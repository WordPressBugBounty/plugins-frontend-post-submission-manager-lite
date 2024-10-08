<div class="wrap fpsml-wrap">
    <div class="fpsml-header fpsml-clearfix">
        <h1 class="fpsml-floatLeft">
            <?php esc_html_e('Frontend Post Submission Manager', 'frontend-post-submission-manager-lite'); ?>
            <span><?php esc_html_e('Lite', 'frontend-post-submission-manager-lite'); ?></span>
        </h1>
        <div class="fpsml-add-wrap">
            <a href="<?php echo esc_url(FPSML_UPGRADE_LINK); ?>" target="_blank"><input type="button" class="fpsml-button-primary" value="<?php esc_html_e('Upgrade to PRO', 'frontend-post-submission-manager-lite'); ?>"></a>
        </div>
    </div>
<?php include(FPSML_PATH.'/includes/views/backend/upgrade-banner.php');?>
    <div class="fpsml-block-wrap">
        <?php include(FPSML_PATH . '/includes/views/backend/upgrade-to-pro-section.php'); ?>
    </div>
</div>
