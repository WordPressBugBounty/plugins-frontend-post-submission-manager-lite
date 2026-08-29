<?php

defined('ABSPATH') or die('No script kiddies please!!');
if (!class_exists('FPSML_Admin')) {

    class FPSML_Admin {

        function __construct() {
            add_action('admin_menu', array($this, 'add_admin_menus'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_first_success_assets'));
            add_action('admin_post_fpsml_dismiss_first_success', array($this, 'dismiss_first_success'));
            add_action('admin_footer', array($this, 'add_extra_html'));
            add_action('admin_footer', array($this, 'compare_plugin_html'));
        }

        /**
         * Loads onboarding styles only on the FPSM form-list screen.
         *
         * @param string $hook_suffix Current admin page hook.
         */
        function enqueue_first_success_assets($hook_suffix) {
            if ('toplevel_page_fpsm' !== $hook_suffix || !empty($_GET['action'])) {
                return;
            }

            wp_enqueue_style(
                'fpsml-first-success',
                FPSML_URL . '/assets/css/fpsml-first-success.css',
                array('fpsml-fonts'),
                FPSML_VERSION
            );
        }

        function add_admin_menus() {
            if (!empty($_GET['action']) && $_GET['action'] == 'edit_form') {
                $page_title = esc_html__('Edit Form', 'frontend-post-submission-manager-lite');
            } else {
                $page_title = esc_html__('All forms', 'frontend-post-submission-manager-lite');
            }
            add_menu_page(esc_html__('Frontend Post Submission', 'frontend-post-submission-manager-lite'), esc_html__('Frontend Post Submission', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsm', array($this, 'form_lists'), 'dashicons-format-aside');
            add_submenu_page('fpsm', $page_title, esc_html__('All Forms', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsm', array($this, 'form_lists'));
            add_submenu_page('fpsm', esc_html__('Setting', 'frontend-post-submission-manager-lite'), esc_html__('Settings', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsml-settings', array($this, 'render_form_settings_page'));
            add_submenu_page('fpsm', esc_html__('Help', 'frontend-post-submission-manager-lite'), esc_html__('Help', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsml-help', array($this, 'render_form_help_page'));
            add_submenu_page('fpsm', esc_html__('About', 'frontend-post-submission-manager-lite'), esc_html__('About', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsml-about', array($this, 'render_form_about_page'));
            add_submenu_page('fpsm', esc_html__('Upgrade to PRO', 'frontend-post-submission-manager-lite'), esc_html__('Upgrade to PRO', 'frontend-post-submission-manager-lite'), 'manage_options', 'fpsml-upgrade', array($this, 'render_upgrade_page'));
        }

        function form_lists() {
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                switch ($action) {
                    case 'edit_form':
                        include(FPSML_PATH . '/includes/views/backend/forms/form-edit.php');
                        break;
                }
            } else {
                include(FPSML_PATH . '/includes/views/backend/forms/form-list.php');
            }
        }

        /**
         * Renders the first-success guide until the current user dismisses it.
         */
        function render_first_success_panel() {
            if (!current_user_can('manage_options') || get_user_meta(get_current_user_id(), 'fpsml_first_success_dismissed', true)) {
                return;
            }

            global $wpdb;
            $form_table = FPSML_FORM_TABLE;
            $primary_form = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT form_id, form_title, form_alias FROM {$form_table} WHERE form_alias = %s LIMIT 1",
                    'guest_post_form'
                )
            );

            if (!$primary_form) {
                $primary_form = $wpdb->get_row("SELECT form_id, form_title, form_alias FROM {$form_table} ORDER BY form_id ASC LIMIT 1");
            }

            include(FPSML_PATH . '/includes/views/backend/first-success.php');
        }

        /**
         * Stores a per-user dismissal for the first-success guide.
         */
        function dismiss_first_success() {
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You are not allowed to dismiss this guide.', 'frontend-post-submission-manager-lite'));
            }

            check_admin_referer('fpsml_dismiss_first_success');

            $updated = update_user_meta(get_current_user_id(), 'fpsml_first_success_dismissed', 1);
            $redirect_args = array('page' => 'fpsm');

            if (false === $updated) {
                $redirect_args['fpsml_first_success_error'] = 'dismiss';
            }

            wp_safe_redirect(add_query_arg($redirect_args, admin_url('admin.php')));
            exit;
        }

        function compare_plugin_html() {
            include(FPSML_PATH . '/includes/views/backend/compare_plugin_html.php');
        }

        function render_form_settings_page() {
            include(FPSML_PATH . '/includes/views/backend/settings.php');
        }

        function render_form_help_page() {
            include(FPSML_PATH . '/includes/views/backend/help.php');
        }

        function render_form_about_page() {
            include(FPSML_PATH . '/includes/views/backend/about.php');
        }

        function render_upgrade_page() {
            include(FPSML_PATH . '/includes/views/backend/upgrade.php');
        }

        function add_extra_html() {
            include(FPSML_PATH . '/includes/views/backend/admin-footer.php');
        }

    }

    new FPSML_Admin();
}
