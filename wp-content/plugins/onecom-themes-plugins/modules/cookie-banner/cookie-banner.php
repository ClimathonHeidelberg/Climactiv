<?php
defined("WPINC") or die(); // No Direct Access

require_once 'inc' . DIRECTORY_SEPARATOR . 'functions.php';

add_action('admin_menu', 'oc_cb_config_page');
add_action('network_admin_menu', 'oc_cb_config_page');
add_action('admin_enqueue_scripts', 'oc_cb_scripts_admin');
add_action('wp_enqueue_scripts', 'oc_cb_scripts_frontend');