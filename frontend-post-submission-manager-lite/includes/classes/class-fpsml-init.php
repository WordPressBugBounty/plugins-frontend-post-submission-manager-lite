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
            $custom_field_type_list = array(
                'textfield' => array('label' => esc_html__('Texfield', 'frontend-post-submission-manager-lite'), 'icon' => 'fas fa-edit'),
                'textarea' => array('label' => esc_html__('Textarea', 'frontend-post-submission-manager-lite'), 'icon' => 'fas fa-expand'),
            );
            /**
             * Filters custom field type list
             *
             * @param array $custom_field_type_list
             *
             * @since 1.0.0
             */
            $custom_field_type_list = apply_filters('fpsml_custom_field_type_list', $custom_field_type_list);
            defined('FPSML_CUSTOM_FIELD_TYPE_LIST') or define('FPSML_CUSTOM_FIELD_TYPE_LIST', $custom_field_type_list);
        }

        

    }

    new FPSML_Init();
}
