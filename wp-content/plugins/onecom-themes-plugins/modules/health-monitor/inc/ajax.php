<?php
//Handle AJAX calls
add_action('wp_ajax_ocsh_check_php_updates', 'ocsh_check_php_updates_cb');
function ocsh_check_php_updates_cb()
{
    $result = oc_sh_check_php_updates();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_plugin_updates', 'ocsh_check_plugin_updates_cb');
function ocsh_check_plugin_updates_cb()
{
    $result = oc_sh_check_plugin_updates();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_theme_updates', 'ocsh_theme_updates_cb');
function ocsh_theme_updates_cb()
{
    $result = oc_sh_check_theme_updates();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_wp_updates', 'ocsh_wp_updates_cb');
function ocsh_wp_updates_cb()
{
    $result = oc_sh_check_wp_updates();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_wp_connection', 'ocsh_wp_connection');
function ocsh_wp_connection()
{
    $result = oc_sh_wp_connection();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_core_updates', 'ocsh_check_core_updates_cb');
function ocsh_check_core_updates_cb()
{
    $result = oc_sh_check_auto_updates();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_ssl', 'ocsh_check_ssl_cb');
function ocsh_check_ssl_cb()
{
    $result = oc_sh_check_ssl();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_file_execution', 'ocsh_check_file_execution_cb');
function ocsh_check_file_execution_cb()
{
    $result = oc_sh_check_execution();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_file_permissions', 'ocsh_check_file_permissions_cb');
function ocsh_check_file_permissions_cb()
{
    $result = oc_sh_check_permission();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_DB', 'ocsh_DB_cb');
function ocsh_DB_cb()
{
    $result = oc_sh_check_db_security();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_file_edit', 'ocsh_check_file_edit_cb');
function ocsh_check_file_edit_cb()
{
    $result = oc_sh_check_file_editing();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_usernames', 'ocsh_check_usernames_cb');
function ocsh_check_usernames_cb()
{
    $result = oc_sh_check_usernames();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_check_dis_plugin', 'ocsh_check_dis_plugin_cb');
function ocsh_check_dis_plugin_cb()
{
    $result = oc_sh_check_plugins();
    wp_send_json($result);
}

add_action('wp_ajax_ocsh_save_result', 'ocsh_save_result_cb');  
function ocsh_save_result_cb()
{
    $result = floatval($_POST['osch_Result']);
    // set_site_transient( 'ocsh_site_scan_result', $result, 4 * HOUR_IN_SECONDS );
}