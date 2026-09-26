<?php
defined('ABSPATH') or die('No script kiddies please!!');

$measurement_enabled = !empty($measurement_state['enabled']);
$notice_messages = array(
    'enabled' => array('success', esc_html__('Local measurement is enabled.', 'frontend-post-submission-manager-lite')),
    'disabled' => array('success', esc_html__('Local measurement is disabled and its data was removed.', 'frontend-post-submission-manager-lite')),
    'reset' => array('success', esc_html__('Local measurement data was reset. Measurement is now off.', 'frontend-post-submission-manager-lite')),
    'error' => array('error', esc_html__('Measurement settings could not be saved. Existing plugin settings and forms were not changed.', 'frontend-post-submission-manager-lite')),
);
$retry_actions = array(
    'enable' => array('fpsml_measurement_enable', esc_html__('Try enabling again', 'frontend-post-submission-manager-lite')),
    'disable' => array('fpsml_measurement_disable', esc_html__('Try disabling again', 'frontend-post-submission-manager-lite')),
    'reset' => array('fpsml_measurement_reset', esc_html__('Try resetting again', 'frontend-post-submission-manager-lite')),
);
?>
<section class="fpsml-measurement-card" aria-labelledby="fpsml-measurement-title">
    <?php if (isset($notice_messages[$measurement_notice])) { ?>
        <div class="fpsml-measurement-notice fpsml-measurement-notice-<?php echo esc_attr($notice_messages[$measurement_notice][0]); ?>" role="<?php echo 'error' === $notice_messages[$measurement_notice][0] ? 'alert' : 'status'; ?>">
            <p><?php echo esc_html($notice_messages[$measurement_notice][1]); ?></p>
            <?php if ('error' === $measurement_notice && isset($retry_actions[$measurement_retry])) { ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="<?php echo esc_attr($retry_actions[$measurement_retry][0]); ?>">
                    <?php wp_nonce_field($retry_actions[$measurement_retry][0]); ?>
                    <button type="submit" class="button"><?php echo esc_html($retry_actions[$measurement_retry][1]); ?></button>
                </form>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if (!empty($measurement_state['expired'])) { ?>
        <div class="fpsml-measurement-notice" role="status">
            <?php esc_html_e('Local measurement expired after 90 days and its data was removed.', 'frontend-post-submission-manager-lite'); ?>
        </div>
    <?php } ?>
    <div class="fpsml-measurement-heading">
        <h2 id="fpsml-measurement-title"><?php esc_html_e('Local measurement', 'frontend-post-submission-manager-lite'); ?></h2>
        <span class="fpsml-measurement-status <?php echo $measurement_enabled ? 'is-enabled' : 'is-disabled'; ?>">
            <?php echo $measurement_enabled ? esc_html__('On', 'frontend-post-submission-manager-lite') : esc_html__('Off', 'frontend-post-submission-manager-lite'); ?>
        </span>
    </div>
    <p><?php esc_html_e('Record setup milestones on this WordPress site. Nothing is sent to WP Shuffle. Data expires after 90 days or is removed when you disable or reset measurement.', 'frontend-post-submission-manager-lite'); ?></p>
    <div class="fpsml-measurement-privacy">
        <strong><?php esc_html_e('Stored on this site only', 'frontend-post-submission-manager-lite'); ?></strong>
        <p><?php esc_html_e('No form content, email addresses, user IDs, site URL, IP addresses, license keys, or individual event timestamps are recorded.', 'frontend-post-submission-manager-lite'); ?></p>
    </div>

    <?php if ($measurement_enabled) { ?>
        <p class="fpsml-measurement-summary">
            <?php
            echo esc_html(sprintf(
                __('Enabled %1$s · Expires %2$s', 'frontend-post-submission-manager-lite'),
                date_i18n(get_option('date_format'), strtotime($measurement_state['started_on'])),
                date_i18n(get_option('date_format'), strtotime($measurement_state['expires_on']))
            ));
            ?>
            <br>
            <?php
            echo !empty($measurement_state['milestones']['first_submission_succeeded'])
                ? esc_html__('First successful submission: Reached', 'frontend-post-submission-manager-lite')
                : esc_html__('First successful submission: Not reached', 'frontend-post-submission-manager-lite');
            ?>
        </p>
        <div class="fpsml-measurement-actions">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="fpsml_measurement_disable">
                <?php wp_nonce_field('fpsml_measurement_disable'); ?>
                <button type="submit" class="button"><?php esc_html_e('Disable', 'frontend-post-submission-manager-lite'); ?></button>
            </form>
            <button type="button" class="button fpsml-measurement-reset-toggle" aria-expanded="false" aria-controls="fpsml-measurement-reset-confirm">
                <?php esc_html_e('Reset measurement data', 'frontend-post-submission-manager-lite'); ?>
            </button>
        </div>
        <div id="fpsml-measurement-reset-confirm" class="fpsml-measurement-reset" hidden>
            <strong><?php esc_html_e('Reset local measurement data?', 'frontend-post-submission-manager-lite'); ?></strong>
            <p><?php esc_html_e('This removes measurement history only. Forms and submissions are not changed.', 'frontend-post-submission-manager-lite'); ?></p>
            <div class="fpsml-measurement-actions">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="fpsml_measurement_reset">
                    <?php wp_nonce_field('fpsml_measurement_reset'); ?>
                    <button type="submit" class="button button-primary"><?php esc_html_e('Reset data', 'frontend-post-submission-manager-lite'); ?></button>
                </form>
                <button type="button" class="button fpsml-measurement-reset-cancel"><?php esc_html_e('Cancel', 'frontend-post-submission-manager-lite'); ?></button>
            </div>
        </div>
    <?php } else { ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="fpsml_measurement_enable">
            <?php wp_nonce_field('fpsml_measurement_enable'); ?>
            <button type="submit" class="button button-primary fpsml-measurement-enable"><?php esc_html_e('Enable local measurement', 'frontend-post-submission-manager-lite'); ?></button>
        </form>
    <?php } ?>
    <p class="description"><?php esc_html_e('You can disable or reset this data at any time. Plugin features remain available when measurement is off.', 'frontend-post-submission-manager-lite'); ?></p>
</section>
