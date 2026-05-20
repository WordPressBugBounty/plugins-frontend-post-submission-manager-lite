<div class="fpsml-settings-each-section fpsml-display-none" data-tab="security">
    <?php
    $captcha_provider = (!empty($form_details['security']['captcha_provider'])) ? $form_details['security']['captcha_provider'] : 'recaptcha';
    ?>
    <div class="fpsml-field-wrap">
        <label><?php _e('Captcha Provider', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <select name="form_details[security][captcha_provider]" class="fpsml-toggle-trigger" data-toggle-class="fpsml-captcha-provider-ref">
                <option value="recaptcha" <?php selected($captcha_provider, 'recaptcha'); ?>><?php esc_html_e('Google reCAPTCHA', 'frontend-post-submission-manager-lite'); ?></option>
                <option value="turnstile" <?php selected($captcha_provider, 'turnstile'); ?>><?php esc_html_e('Cloudflare Turnstile', 'frontend-post-submission-manager-lite'); ?></option>
            </select>
        </div>
    </div>
    <div class="fpsml-captcha-provider-ref <?php echo ($captcha_provider !== 'recaptcha') ? 'fpsml-display-none' : ''; ?>" data-toggle-ref="recaptcha">
    <div class="fpsml-field-wrap">
        <label><?php _e('ReCaptcha Site Key', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][site_key]" value="<?php echo (!empty($form_details['security']['site_key'])) ? esc_attr($form_details['security']['site_key']) : ''; ?>"/>
            <p class="description"><?php _e('Please go <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a> to get the google reCaptcha site key.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
    </div>
    <div class="fpsml-field-wrap">
        <label><?php _e('ReCaptcha Secret Key', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][secret_key]" value="<?php echo (!empty($form_details['security']['secret_key'])) ? esc_attr($form_details['security']['secret_key']) : ''; ?>"/>
            <p class="description"><?php _e('Please go <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a> to get the google reCaptcha secret key.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
    </div>
    </div>
    <div class="fpsml-captcha-provider-ref <?php echo ($captcha_provider !== 'turnstile') ? 'fpsml-display-none' : ''; ?>" data-toggle-ref="turnstile">
    <div class="fpsml-field-wrap">
        <label><?php _e('Turnstile Site Key', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][turnstile_site_key]" value="<?php echo (!empty($form_details['security']['turnstile_site_key'])) ? esc_attr($form_details['security']['turnstile_site_key']) : ''; ?>"/>
            <p class="description"><?php _e('Please go <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">here</a> to get the Cloudflare Turnstile site key.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
    </div>
    <div class="fpsml-field-wrap">
        <label><?php _e('Turnstile Secret Key', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][turnstile_secret_key]" value="<?php echo (!empty($form_details['security']['turnstile_secret_key'])) ? esc_attr($form_details['security']['turnstile_secret_key']) : ''; ?>"/>
            <p class="description"><?php _e('Please go <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">here</a> to get the Cloudflare Turnstile secret key.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
    </div>
    </div>
    <div class="fpsml-field-wrap">
        <label><?php _e('Enable Captcha in frontend form', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <?php
            $frontend_form_captcha = (!empty($form_details['security']['frontend_form_captcha'])) ? 1 : 0;
            ?>
            <input type="checkbox" name="form_details[security][frontend_form_captcha]" value="1" <?php checked($frontend_form_captcha, true); ?>/>
            <p class="description"><?php _e('Please check to enable captcha in frontend form.', 'frontend-post-submission-manager-lite'); ?></p>
        </div>
    </div>
    <?php if ($form_row->form_type == 'login_require') { ?>
        <div class="fpsml-field-wrap">
            <label><?php _e('Enable Captcha in login form', 'frontend-post-submission-manager-lite'); ?></label>
            <div class="fpsml-field">
                <?php
                $login_form_captcha = (!empty($form_details['security']['login_form_captcha'])) ? 1 : 0;
                ?>
                <input type="checkbox" name="form_details[security][login_form_captcha]" value="1" <?php checked($login_form_captcha, true); ?>/>
                <p class="description"><?php _e('Please check to enable captcha in login form.', 'frontend-post-submission-manager-lite'); ?></p>
            </div>
        </div>
    <?php } ?>
    <div class="fpsml-field-wrap">
        <label><?php _e('Captcha Label', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][captcha_label]" value="<?php echo (!empty($form_details['security']['captcha_label'])) ? esc_attr($form_details['security']['captcha_label']) : ''; ?>"/>
        </div>
    </div>
    <div class="fpsml-field-wrap">
        <label><?php _e('Captcha Error Message', 'frontend-post-submission-manager-lite'); ?></label>
        <div class="fpsml-field">
            <input type="text" name="form_details[security][error_message]" value="<?php echo (!empty($form_details['security']['error_message'])) ? esc_attr($form_details['security']['error_message']) : ''; ?>"/>
        </div>
    </div>
</div>
