<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (is_multisite()) {
    $site_ids = get_sites(array('fields' => 'ids', 'number' => 0));
    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        delete_option('fpsml_measurement_v1');
        restore_current_blog();
    }
} else {
    delete_option('fpsml_measurement_v1');
}
