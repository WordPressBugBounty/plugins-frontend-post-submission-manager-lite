<?php

defined('ABSPATH') or die('No script kiddies please!!');

if (!class_exists('FPSML_Measurement')) {

    /**
     * Stores privacy-safe, local-only activation milestones.
     */
    class FPSML_Measurement {

        const OPTION_NAME = 'fpsml_measurement_v1';
        const SCHEMA_VERSION = 1;
        const RETENTION_DAYS = 90;

        function __construct() {
            add_action('fpsml_form_submission_success', array($this, 'record_first_submission'), 10, 3);
            add_action('admin_post_fpsml_measurement_enable', array($this, 'enable'));
            add_action('admin_post_fpsml_measurement_disable', array($this, 'disable'));
            add_action('admin_post_fpsml_measurement_reset', array($this, 'reset'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        }

        /**
         * Enqueues measurement UI assets only on the FPSM Settings screen.
         *
         * @param string $hook_suffix Current admin page hook.
         */
        function enqueue_assets($hook_suffix) {
            unset($hook_suffix);

            if (!isset($_GET['page']) || 'fpsml-settings' !== sanitize_key(wp_unslash($_GET['page']))) {
                return;
            }

            wp_enqueue_style(
                'fpsml-measurement',
                FPSML_URL . '/assets/css/fpsml-measurement.css',
                array('fpsml-fonts'),
                FPSML_VERSION
            );
            wp_enqueue_script(
                'fpsml-measurement',
                FPSML_URL . '/assets/js/fpsml-measurement.js',
                array(),
                FPSML_VERSION,
                true
            );
        }

        /**
         * Returns a validated state. Invalid, missing and expired state is off.
         *
         * @return array
         */
        function get_state() {
            $state = get_option(self::OPTION_NAME, false);

            if (!$this->is_valid_state($state)) {
                return $this->off_state();
            }

            if ($state['expires_on'] < gmdate('Y-m-d')) {
                delete_option(self::OPTION_NAME);
                return $this->off_state(true);
            }

            return $state;
        }

        /**
         * Renders the local measurement settings card.
         */
        function render_settings_card() {
            if (!current_user_can('manage_options')) {
                return;
            }

            $measurement_state = $this->get_state();
            $measurement_notice = '';
            if (isset($_GET['fpsml_measurement_status'])) {
                $measurement_notice = sanitize_key(wp_unslash($_GET['fpsml_measurement_status']));
            }

            $measurement_retry = '';
            if (isset($_GET['fpsml_measurement_retry'])) {
                $candidate_retry = sanitize_key(wp_unslash($_GET['fpsml_measurement_retry']));
                if (in_array($candidate_retry, array('enable', 'disable', 'reset'), true)) {
                    $measurement_retry = $candidate_retry;
                }
            }

            include FPSML_PATH . '/includes/views/backend/measurement-settings.php';
        }

        /**
         * Enables a new local measurement epoch.
         */
        function enable() {
            $this->authorize_admin_action('fpsml_measurement_enable');
            delete_option(self::OPTION_NAME);
            $saved = add_option(self::OPTION_NAME, $this->new_state(), '', 'no');
            $this->redirect($saved ? 'enabled' : 'error', $saved ? '' : 'enable');
        }

        /**
         * Disables measurement and removes its local state.
         */
        function disable() {
            $this->authorize_admin_action('fpsml_measurement_disable');
            $deleted = delete_option(self::OPTION_NAME);
            $disabled = $deleted || false === get_option(self::OPTION_NAME, false);
            $this->redirect($disabled ? 'disabled' : 'error', $disabled ? '' : 'disable');
        }

        /**
         * Resets measurement and leaves it off.
         */
        function reset() {
            $this->authorize_admin_action('fpsml_measurement_reset');
            $deleted = delete_option(self::OPTION_NAME);
            $reset = $deleted || false === get_option(self::OPTION_NAME, false);
            $this->redirect($reset ? 'reset' : 'error', $reset ? '' : 'reset');
        }

        /**
         * Records only a verified new post created by the existing success hook.
         *
         * @param int    $post_id  Persisted post ID.
         * @param object $form_row Existing form row; deliberately not stored.
         * @param string $action   Existing insert/update action.
         */
        function record_first_submission($post_id, $form_row, $action) {
            unset($form_row);
            $post_id = absint($post_id);
            if ('insert' !== $action || !$post_id || !get_post($post_id)) {
                return;
            }

            $this->mark_milestone('first_submission_succeeded');
        }

        /**
         * Atomically marks an allowlisted milestone in the current epoch.
         *
         * @param string $milestone Milestone key.
         * @return bool
         */
        private function mark_milestone($milestone) {
            $allowed = array('first_submission_succeeded');
            if (!in_array($milestone, $allowed, true)) {
                return false;
            }

            global $wpdb;
            for ($attempt = 0; $attempt < 3; $attempt++) {
                wp_cache_delete(self::OPTION_NAME, 'options');
                $state = get_option(self::OPTION_NAME, false);
                if (!$this->is_valid_state($state) || $state['expires_on'] < gmdate('Y-m-d')) {
                    return false;
                }
                if (!empty($state['milestones'][$milestone])) {
                    return true;
                }

                $expected_token = $state['epoch_token'];
                $updated_state = $state;
                $updated_state['milestones'][$milestone] = true;

                $updated = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s",
                        maybe_serialize($updated_state),
                        self::OPTION_NAME,
                        maybe_serialize($state)
                    )
                );
                wp_cache_delete(self::OPTION_NAME, 'options');

                if (1 === $updated) {
                    return true;
                }

                $current = get_option(self::OPTION_NAME, false);
                if (!$this->is_valid_state($current) || !hash_equals($expected_token, $current['epoch_token'])) {
                    return false;
                }
            }

            return false;
        }

        /**
         * Builds a fresh enabled state with a unique local generation token.
         *
         * @return array
         */
        private function new_state() {
            $started = gmdate('Y-m-d');
            return array(
                'schema_version' => self::SCHEMA_VERSION,
                'enabled' => true,
                'epoch_token' => $this->generate_epoch_token(),
                'started_on' => $started,
                'expires_on' => gmdate('Y-m-d', strtotime('+' . self::RETENTION_DAYS . ' days', strtotime($started . ' 00:00:00 UTC'))),
                'cohort' => 'unknown',
                'milestones' => array(
                    'measurement_started' => true,
                    'quick_start_seen' => null,
                    'quick_start_dismissed' => null,
                    'form_configured' => null,
                    'shortcode_published' => null,
                    'first_submission_succeeded' => false,
                    'pro_cta_seen' => null,
                    'pro_cta_clicked' => null,
                ),
            );
        }

        /**
         * Generates a 256-bit local synchronization token.
         *
         * @return string
         */
        private function generate_epoch_token() {
            try {
                return bin2hex(random_bytes(32));
            } catch (Exception $exception) {
                return hash('sha256', wp_generate_password(64, true, true));
            }
        }

        /**
         * Validates the enabled-state schema without accepting extra keys.
         *
         * @param mixed $state Stored value.
         * @return bool
         */
        private function is_valid_state($state) {
            if (!is_array($state)) {
                return false;
            }
            $expected_keys = array('schema_version', 'enabled', 'epoch_token', 'started_on', 'expires_on', 'cohort', 'milestones');
            $state_keys = array_keys($state);
            sort($expected_keys);
            sort($state_keys);
            if ($expected_keys !== $state_keys || self::SCHEMA_VERSION !== $state['schema_version'] || true !== $state['enabled']) {
                return false;
            }
            if (!is_string($state['epoch_token']) || !preg_match('/^[a-f0-9]{64}$/', $state['epoch_token'])) {
                return false;
            }
            if (!$this->is_valid_date($state['started_on']) || !$this->is_valid_date($state['expires_on'])) {
                return false;
            }
            if ($state['started_on'] > $state['expires_on']) {
                return false;
            }
            if (!in_array($state['cohort'], array('fresh_verified', 'existing', 'unknown'), true) || !is_array($state['milestones'])) {
                return false;
            }
            $expected_milestones = array('measurement_started', 'quick_start_seen', 'quick_start_dismissed', 'form_configured', 'shortcode_published', 'first_submission_succeeded', 'pro_cta_seen', 'pro_cta_clicked');
            $milestone_keys = array_keys($state['milestones']);
            sort($expected_milestones);
            sort($milestone_keys);
            return $expected_milestones === $milestone_keys
                && true === $state['milestones']['measurement_started']
                && is_bool($state['milestones']['first_submission_succeeded']);
        }

        /**
         * Validates an ISO calendar date without coercing malformed values.
         *
         * @param mixed $date Candidate date.
         * @return bool
         */
        private function is_valid_date($date) {
            if (!is_string($date) || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) {
                return false;
            }

            $parts = array_map('intval', explode('-', $date));
            return checkdate($parts[1], $parts[2], $parts[0]);
        }

        /**
         * Returns the public off-state view model.
         *
         * @param bool $expired Whether an expired state was lazily cleared.
         * @return array
         */
        private function off_state($expired = false) {
            return array(
                'schema_version' => self::SCHEMA_VERSION,
                'enabled' => false,
                'expired' => (bool) $expired,
            );
        }

        /**
         * Enforces capability and an action-specific nonce.
         *
         * @param string $action Nonce/action name.
         */
        private function authorize_admin_action($action) {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You are not allowed to manage local measurement.', 'frontend-post-submission-manager-lite'));
            }
            check_admin_referer($action);
        }

        /**
         * Redirects back to the settings screen with an allowlisted notice.
         *
         * @param string $status       Result status.
         * @param string $retry_action Failed action that may be retried.
         */
        private function redirect($status, $retry_action = '') {
            $allowed = array('enabled', 'disabled', 'reset', 'error');
            if (!in_array($status, $allowed, true)) {
                $status = 'error';
            }

            $query_args = array(
                'page' => 'fpsml-settings',
                'fpsml_measurement_status' => $status,
            );
            if ('error' === $status && in_array($retry_action, array('enable', 'disable', 'reset'), true)) {
                $query_args['fpsml_measurement_retry'] = $retry_action;
            }

            wp_safe_redirect(add_query_arg($query_args, admin_url('admin.php')));
            exit;
        }
    }

    $GLOBALS['fpsml_measurement_obj'] = new FPSML_Measurement();
}
