<div class="fpsml-form-message"></div>
<?php
$custom_field_type_list = FPSML_CUSTOM_FIELD_TYPE_LIST;
if (!empty($custom_field_type_list)) {
    foreach ($custom_field_type_list as $custom_field_type => $custom_field_details) {
        $custom_field_type_label = $custom_field_details['label'];
        $field_name_prefix = 'form_details[form][fields][{{data.field_key}}]';
        $show_hide_toggle_class = '{{data.meta_key}}';
        $field_details['field_label'] = '{{data.label}}';
        $field_type = $custom_field_type;
        $field_key = '{{data.field_key}}';
        ?>
        <script type="text/html" id="tmpl-custom-<?php echo esc_attr($custom_field_type); ?>">
            <?php include(FPSML_PATH . '/includes/views/backend/js-templates/tmpl-custom-field-holder.php'); ?>
        </script>
        <?php
    }
}
?>

<?php
global $pagenow;
if ('plugins.php' === $pagenow) {
    ?>
    <div class="fpsml-deactivation-feedback-overlay" style="display:none;">
        <div class="fpsml-deactivation-feedback-modal" role="dialog" aria-modal="true" aria-labelledby="fpsml-deactivation-feedback-title">
            <h2 id="fpsml-deactivation-feedback-title"><?php esc_html_e('Quick feedback before deactivating', 'frontend-post-submission-manager-lite'); ?></h2>
            <p><?php esc_html_e('Would you like to share why you are deactivating Frontend Post Submission Manager Lite?', 'frontend-post-submission-manager-lite'); ?></p>
            <p class="fpsml-deactivation-feedback-note"><?php esc_html_e('This is optional. If submitted, your selected reason, optional message, plugin version, WordPress version, and PHP version will be emailed to the plugin team to help improve the plugin.', 'frontend-post-submission-manager-lite'); ?></p>
            <form class="fpsml-deactivation-feedback-form">
                <label><input type="radio" name="reason" value="I no longer need the plugin"> <?php esc_html_e('I no longer need the plugin', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Temporarily deactivating for troubleshooting"> <?php esc_html_e('Temporarily deactivating for troubleshooting', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="I could not set it up"> <?php esc_html_e('I could not set it up', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Missing feature"> <?php esc_html_e('Missing feature', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Found another plugin"> <?php esc_html_e('Found another plugin', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Plugin conflict or error"> <?php esc_html_e('Plugin conflict or error', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Free version is too limited"> <?php esc_html_e('Free version is too limited', 'frontend-post-submission-manager-lite'); ?></label>
                <label><input type="radio" name="reason" value="Other"> <?php esc_html_e('Other', 'frontend-post-submission-manager-lite'); ?></label>
                <textarea name="message" rows="4" placeholder="<?php esc_attr_e('Additional feedback (optional)', 'frontend-post-submission-manager-lite'); ?>"></textarea>
                <div class="fpsml-deactivation-feedback-actions">
                    <button type="submit" class="button button-primary"><?php esc_html_e('Submit Feedback & Deactivate', 'frontend-post-submission-manager-lite'); ?></button>
                    <button type="button" class="button fpsml-deactivation-skip"><?php esc_html_e('Skip & Deactivate', 'frontend-post-submission-manager-lite'); ?></button>
                    <button type="button" class="button-link fpsml-deactivation-cancel"><?php esc_html_e('Cancel', 'frontend-post-submission-manager-lite'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
