<?php
defined("WPINC") or die(); // No Direct Access
define('OC_OPEN', "1");
define('OC_RESOLVED', "0");

require_once 'inc' . DIRECTORY_SEPARATOR . 'functions.php';
require_once 'inc' . DIRECTORY_SEPARATOR . 'ajax.php';
require_once 'inc' . DIRECTORY_SEPARATOR . 'widgets.php';

add_action('admin_menu', 'oc_sh_report_page');
add_action( 'network_admin_menu', 'oc_sh_report_page');
add_action('admin_enqueue_scripts', 'oc_sh_scripts');