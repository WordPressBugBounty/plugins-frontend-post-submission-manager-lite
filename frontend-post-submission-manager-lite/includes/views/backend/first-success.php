<?php
defined('ABSPATH') or die('No script kiddies please!!');

$has_form = !empty($primary_form);
$form_title = $has_form ? $primary_form->form_title : esc_html__('your form', 'frontend-post-submission-manager-lite');
$form_alias = $has_form ? $primary_form->form_alias : '';
$help_url = 'https://wpshuffle.com/docs/frontend-post-submission-manager-lite/';
$edit_url = $has_form
    ? add_query_arg(
        array(
            'page' => 'fpsm',
            'form_id' => absint($primary_form->form_id),
            'action' => 'edit_form',
        ),
        admin_url('admin.php')
    )
    : $help_url;
?>
<section class="fpsml-first-success" aria-labelledby="fpsml-first-success-title">
    <div class="fpsml-first-success__topbar">
        <div>
            <span class="fpsml-first-success__eyebrow"><?php esc_html_e('Quick start', 'frontend-post-submission-manager-lite'); ?></span>
            <h2 id="fpsml-first-success-title"><?php esc_html_e('Publish your first frontend submission form', 'frontend-post-submission-manager-lite'); ?></h2>
            <p><?php esc_html_e('Two ready-to-use forms are already installed. Configure one, publish its shortcode, and submit a test entry.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="fpsml_dismiss_first_success">
            <?php wp_nonce_field('fpsml_dismiss_first_success'); ?>
            <button type="submit" class="fpsml-first-success__dismiss">
                <span aria-hidden="true">&times;</span>
                <span class="screen-reader-text"><?php esc_html_e('Dismiss quick start guide', 'frontend-post-submission-manager-lite'); ?></span>
            </button>
        </form>
    </div>

    <?php if (isset($_GET['fpsml_first_success_error']) && 'dismiss' === sanitize_key(wp_unslash($_GET['fpsml_first_success_error']))) { ?>
        <div class="fpsml-first-success__error" role="alert">
            <?php esc_html_e('The guide could not be dismissed. Please try again.', 'frontend-post-submission-manager-lite'); ?>
        </div>
    <?php } ?>

    <ol class="fpsml-first-success__steps">
        <li>
            <span class="fpsml-first-success__number" aria-hidden="true">1</span>
            <div><strong><?php esc_html_e('Choose a form', 'frontend-post-submission-manager-lite'); ?></strong><span><?php esc_html_e('Start with Guest Post Form or Login Require Form.', 'frontend-post-submission-manager-lite'); ?></span></div>
        </li>
        <li>
            <span class="fpsml-first-success__number" aria-hidden="true">2</span>
            <div><strong><?php esc_html_e('Configure fields', 'frontend-post-submission-manager-lite'); ?></strong><span><?php esc_html_e('Review labels, required fields, post status, notifications, and spam protection.', 'frontend-post-submission-manager-lite'); ?></span></div>
        </li>
        <li>
            <span class="fpsml-first-success__number" aria-hidden="true">3</span>
            <div>
                <strong><?php esc_html_e('Publish the shortcode', 'frontend-post-submission-manager-lite'); ?></strong>
                <?php if ($has_form) { ?>
                    <code>[fpsm alias="<?php echo esc_attr($form_alias); ?>"]</code>
                <?php } else { ?>
                    <span><?php esc_html_e('Copy a form shortcode into a page or post.', 'frontend-post-submission-manager-lite'); ?></span>
                <?php } ?>
            </div>
        </li>
        <li>
            <span class="fpsml-first-success__number" aria-hidden="true">4</span>
            <div><strong><?php esc_html_e('Test a submission', 'frontend-post-submission-manager-lite'); ?></strong><span><?php esc_html_e('Open the published page and confirm that a new post reaches the expected status.', 'frontend-post-submission-manager-lite'); ?></span></div>
        </li>
    </ol>

    <div class="fpsml-first-success__actions">
        <a class="fpsml-first-success__primary" href="<?php echo esc_url($edit_url); ?>">
            <?php
            echo $has_form
                ? esc_html(sprintf(__('Configure %s', 'frontend-post-submission-manager-lite'), $form_title))
                : esc_html__('Open setup help', 'frontend-post-submission-manager-lite');
            ?>
        </a>
        <a class="fpsml-first-success__secondary" href="<?php echo esc_url($help_url); ?>">
            <?php esc_html_e('View setup help', 'frontend-post-submission-manager-lite'); ?>
        </a>
    </div>
</section>
