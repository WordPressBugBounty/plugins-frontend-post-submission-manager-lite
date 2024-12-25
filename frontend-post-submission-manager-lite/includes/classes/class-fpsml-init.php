<?php

defined('ABSPATH') or die('No script kiddies please!!');
if (!class_exists('FPSML_Init')) {

    class FPSML_Init {

        function __construct() {
            //All tasks needed to be executed in init hooks are placed here
            add_action('init', array($this, 'init_tasks'));
            
        }

        function init_tasks() {
            /**
             * Fires on init hook
             *
             * @since 1.0.0
             */
            do_action('fpsml_init');
            load_plugin_textdomain('frontend-post-submission-manager-lite', false, FPSML_LANGAUGE_PATH);
        }

        

    }

    new FPSML_Init();
}
