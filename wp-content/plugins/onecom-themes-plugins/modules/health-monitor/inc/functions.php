<?php
$onecom_wp = 'onecom-wp';
$oc_hm_status = 'status';
$oc_hm_desc = 'desc';
$oc_hm_score = 'score';
$oc_hm_cms_update = "<a href='https://help.one.com/hc/%s/articles/360001621938-How-do-I-update-a-CMS-like-WordPress-and-Joomla-' target='_blank'>";

if (!function_exists('oc_sh_report_page')) {
    function oc_sh_report_page()
    {
        global $onecom_wp;
        add_submenu_page(
            $onecom_wp,
            __('Health Monitor', $onecom_wp),
            __('Health Monitor', $onecom_wp),
            'manage_options',
            'onecom-wp-health-monitor',
            'oc_sh_health_monitor_callback'
        );
    }
}

function oc_sh_health_monitor_callback()
{
    include_once ONECOM_WP_PATH . 'modules' . DIRECTORY_SEPARATOR . 'health-monitor' . DIRECTORY_SEPARATOR . 'templates/oc_sh_health_monitor.php';
}

if (!function_exists('oc_sh_scripts')) {
    function oc_sh_scripts($hook_suffix)
    {
        global $onecom_wp;
        if ($hook_suffix === 'one-com_page_onecom-wp-health-monitor' || $hook_suffix === 'admin_page_onecom-wp-health-monitor') {
            $folder = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-';
            $extenstion = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min';

            wp_enqueue_style('oc_sh_css', ONECOM_WP_URL . 'assets/' . $folder . 'css/site-scanner' . $extenstion . '.css');
            wp_enqueue_script('oc_sh_js', ONECOM_WP_URL . 'assets/' . $folder . 'js/oc_sh_script' . $extenstion . '.js', ['jquery'], null, true);
            wp_localize_script('oc_sh_js', 'oc_constants', [
                'OC_RESOLVED' => OC_RESOLVED,
                'OC_OPEN' => OC_OPEN,
                'ocsh_page_url' => menu_page_url( 'admin_page_onecom-wp-health-monitor', false ),
                'ocsh_scan_btn' => __('Scan again', $onecom_wp)
            ]);
        }
    }
}

if (!function_exists('oc_sh_check_debug_mode')) {
    function oc_sh_check_debug_mode()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Scanning debug mode');
        $display_errors = ini_get('display_errors');
        $result = [];
        if ($display_errors || WP_DEBUG ) {

            $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/115005593705-How-do-I-enable-error-messages-for-PHP-' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $guide_link2 = sprintf("<a href='https://help.one.com/hc/%s/articles/115005594045-How-do-I-enable-debugging-in-WordPress-' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('You can disable PHP error reporting in the One.com control panel and WordPress debugging in the wp.config.php file. Check these two guides for more details on how to manage these settings: %sHow do I enable error messages for PHP?%s and %sHow do I enable debugging in WordPress?%s', $onecom_wp), $guide_link, "</a>", $guide_link2, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('This is configured properly!', $onecom_wp),
            ];
        }
        oc_sh_save_result('debug_mode', $result[$oc_hm_status]);
        oc_sh_log_entry('Finished scanning debug mode');
        return $result;
    }
}

if (!function_exists('oc_sh_check_php_updates')) {
    function oc_sh_check_php_updates()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        $result = [];
        oc_sh_log_entry('Scanning available site updates -- PHP version');
        $php_updates_available = version_compare(PHP_VERSION, '7.3.0', '<');
        oc_sh_log_entry('Finished scaanning available site updates -- PHP version');

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360000449117-How-do-I-update-PHP-for-my-WordPress-site-' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        if ($php_updates_available ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('You are running an outdated version of PHP. We recommend using the latest version of PHP. %sHow do I update PHP for my WordPress site?%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('You have the latest PHP version enabled!', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scan for PHP version');
        oc_sh_save_result('php_updates', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_plugin_updates')) {
    function oc_sh_check_plugin_updates()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc, $oc_hm_cms_update;
        $result = [];
        // plugin updates
        $plugin_updates_available = false;
        $plugin_transients = get_site_transient('update_plugins');
        oc_sh_log_entry('Scanning available site updates -- Plugins');
        if (isset($plugin_transients->response) && count($plugin_transients->response) > 0) {
            $plugin_updates_available = true;
        }
        oc_sh_log_entry('Finished scanning available site updates -- Plugins');

        $guide_link = sprintf($oc_hm_cms_update, onecom_generic_locale_link('', get_locale(), 1));

        if ($plugin_updates_available ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('One or more of your plugins are outdated. Outdated plugins make your site vulnerable to security attacks. %sUpdate your Plugins to the newest version%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('All your plugins are updated', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scan for available plugin updates');
        oc_sh_save_result('plugin_updates', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_theme_updates')) {
    function oc_sh_check_theme_updates()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc, $oc_hm_cms_update;
        $result = [];
        //theme updates
        oc_sh_log_entry('Scanning available site updates -- Themes');
        $theme_update_available = false;
        $theme_transients = get_site_transient('update_themes');
        if (isset($theme_transients->response) && (count($theme_transients->response) > 0)) {
            $theme_update_available = true;
        }
        oc_sh_log_entry('Finished scanning available site updates -- Themes');
        if ($theme_update_available  ) {

            $guide_link = sprintf($oc_hm_cms_update, onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('One or more of your installed themes are outdated. Using outdated themes can break your site and generate potential security risks. %sUpdate your Themes to the newest version%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('All your themes are updated!', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scan for available theme updates');
        oc_sh_save_result('theme_updates', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_wp_updates')) {
    function oc_sh_check_wp_updates()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc, $oc_hm_cms_update;
        //core updates
        oc_sh_log_entry('Scanning available site updates -- Core');
        $core_update_available = false;
        $core_transients = get_site_transient('update_core');
        if ($core_transients->updates && is_array($core_transients->updates)) {
            foreach ($core_transients->updates as $updates) {
                if (isset($updates->response) && $updates->response === 'upgrade') {
                    $core_update_available = true;
                }
            }
        }

        $guide_link = sprintf($oc_hm_cms_update, onecom_generic_locale_link('', get_locale(), 1));

        if ($core_update_available  ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Update WordPress to the latest version, especially minor updates are important because they usually include security fixes. Check this guide for more instructions:  %sHow do I update a CMS like WordPress?%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Your WordPress version is updated', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scaanning available site updates -- Core');
        oc_sh_save_result('core_updates', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_db_security')) {
    function oc_sh_check_db_security()
    {
        global $wpdb, $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Scanning DB security');
        $has_default_prefix = false;
        if ($wpdb->prefix === 'wp_') {
            $has_default_prefix = true;
        }

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360002107438-Change-the-table-prefix-for-WordPress-' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        if ($has_default_prefix  ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('You are using default table prefix. This means that attackers can easily guess your database configuration. %sChange the table prefix for WordPress%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('You are not using default table prefix', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scanning DB security');
        oc_sh_save_result('db_security', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_auto_updates')) {
    function oc_sh_check_auto_updates()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking if CORE updates can be carried out');
        $core_updates_disabled = false;
        if (defined('WP_AUTO_UPDATE_CORE') && WP_AUTO_UPDATE_CORE === false) {
            $core_updates_disabled = true;
        }
        oc_sh_log_entry('Checking connections to wordpress.org');
        $wp_org_connection = oc_sh_check_connection();
        if ($wp_org_connection[$oc_hm_status] == OC_OPEN) {
            oc_sh_log_entry('Could not connect to wordpress.org');
        }

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360000110977-Why-you-should-always-update-WordPress' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        if ($core_updates_disabled  ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Wordpress core updates are disabled. This means that your site cannot be auto updated to latest minor updates of WordPress. %sEnable auto updates%s', $onecom_wp), $guide_link, "</a>"),
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Wordpress core updates are enabled', $onecom_wp),
            ];
        }
        oc_sh_save_result('auto_updates', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_wp_connection')) {
    function oc_sh_wp_connection()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking connections to WordPress.org');
        $wp_org_connection = oc_sh_check_connection();
        if ($wp_org_connection[$oc_hm_status] == OC_OPEN) {
            oc_sh_log_entry('Could not connect to WordPress.org');
            return $wp_org_connection;
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Connection to Wordpress.org was successful', $onecom_wp),
            ];
        }
        oc_sh_save_result('wp_connection', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_file_editing')) {
    function oc_sh_check_file_editing()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking if file editing enabled from admin');
        $file_editing_enabled = true;
        if (defined('DISALLOW_FILE_EDIT') && (DISALLOW_FILE_EDIT)) {
            $file_editing_enabled = false;
        }

        if (!$file_editing_enabled) {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('File editing from WordPress admin is disabled', $onecom_wp),
            ];
        } else {

            $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360002104398' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('File editing from your WordPress dashboard is allowed, meaning users with a role that has this right can edit all the core files of your site. Someone might accidentally break it, or a hacker might get access to a password. %sDisable file editing in WordPress admin%s', $onecom_wp), $guide_link, "</a>"),
                
            ];
        }
        oc_sh_log_entry('Finished checking for file editing enabled from admin');
        oc_sh_save_result('admin_file_edit', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_usernames')) {
    function oc_sh_check_usernames()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking if vulnerable usernames used');
        global $wpdb;
        $logins = [
            'admin',
            'user',
            'usr',
            'wp',
            'wordpress',
        ];
        $login_names = implode("','", $logins);
        $user_count = $wpdb->get_var("SELECT COUNT(user_login) FROM $wpdb->users WHERE user_login IN ('{$login_names}')");

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360002094117-Change-a-WordPress-username-in-PhpMyAdmin' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        if ($user_count > 0 ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('One or more of your usernames is very common, like for example “admin”. These usernames are easy to guess and could be exploited by hackers. %sChange a WordPress username in PhpMyAdmin%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('You are not using any common username. ', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished checking for vulnerable usernames used');
        oc_sh_save_result('common_usernames', $result[$oc_hm_status]);
        return $result;

    }
}

if (!function_exists('oc_sh_check_ssl')) {
    function oc_sh_check_ssl()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking if SSL enabled');
        $result = [];
        $url = str_replace('http://', 'https://', get_site_url());
        $headers = oc_sh_get_curl_header($url);
        if (empty($headers)  ) {

            $guide_link = sprintf("<a href='https://www.one.com/%s/chat' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Your site isn’t using HTTPS. Visitors on your site might get a warning that your website isn’t secure and it can also have a negative effect on your SEO rating. %sContact support%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Your site has valid SSL certificate!', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished checking for SSL');
        oc_sh_save_result('ssl_certificate', $result[$oc_hm_status]);
        return $result;
    }
}

if (!function_exists('oc_sh_check_execution')) {
    function oc_sh_check_execution()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Checking if File Execution is enabled in uploads folder');
        //create a php file in uploads folder and check if it can be executed
        $uploads_dir = wp_get_upload_dir();
        $result = [];
        $time = time();
        $php_file = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . $time . '.php';
        $php_script = '<?php header("X-One-Executable:true");?>';
        oc_sh_log_entry('Creating a dummy php file in uploads');
        file_put_contents($php_file, $php_script);

        //check response of calling the file
        $url = $uploads_dir['baseurl'] . '/' . $time . '.php';
        oc_sh_log_entry('Retriving headers from dummy file');
        $headers = oc_sh_get_curl_header($url);
        oc_sh_log_entry('Deleting dummy file');
        unlink($php_file);
        if (array_key_exists('x-one-executable', $headers)  ) {

            $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360002102258-Disable-file-execution-in-the-WordPress-uploads-folder' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('File execution is allowed in your uploads folder. This means that an attacker can upload malware and execute it by simply trying to access it from their browser. %sDisable file execution in the WordPress uploads folder%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('File execution is blocked in "Uploads" folder.', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished checking for File Execution');
        oc_sh_save_result('file_execution', $result[$oc_hm_status]);
        return $result;
    }
}

 if(!function_exists('oc_sh_discouraged_plugins')){
     function oc_sh_discouraged_plugins($plugins = array()){

        foreach ($plugins as $key => $plugin){
            if (!is_dir(WP_PLUGIN_DIR . '/' . $plugin->slug)) {
                unset($plugins[$key]);
                continue;
            }
            $plugin_infos = get_plugins('/' . $plugin->slug);
            $plugin_activated = false;
            if (!empty($plugin_infos)) {
                foreach ($plugin_infos as $file => $info):
                    $is_inactivate = is_plugin_inactive($plugin->slug . '/' . $file);
                    if (!$is_inactivate) {
                        $plugin_activated = true;
                        $plugins[$key]->file = $file;
                    }
                endforeach;
            }
            if (!$plugin_activated) {
                unset($plugins[$key]);
            }
        }
        return $plugins;
     }
 }

if (!function_exists('oc_sh_check_plugins')) {
    function oc_sh_check_plugins()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        $result = [];
        oc_sh_log_entry('Scanning for discouraged plugins');
        $plugins = onecom_fetch_plugins(false, true);
        if (!is_wp_error($plugins) && !empty($plugins)):
            $plugins = oc_sh_discouraged_plugins($plugins);
        endif;
        if (!empty($plugins)  ) {

            $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/115005586029-Discouraged-WordPress-plugins' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('One or more of your plugins is on the list of plugins we advise against using. Plugins on this list have a negative effect on the performance of your site, or pose a security risk. Deactivate %sDiscouraged WordPress plugins%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('You are doing great! None of your installed plugins, are on our list of discouraged plugins.', $onecom_wp),
            ];
        }
        oc_sh_log_entry('Finished scanning for discouraged plugins');
        oc_sh_save_result('discouraged_plugins', $result[$oc_hm_status], 1);
        return $result;
    }
}

if (!function_exists('oc_sh_check_permission')) {
    function oc_sh_check_permission()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        oc_sh_log_entry('Scanning for WP file permissions.');
        clearstatcache();
        $bad_permission = false;
        $files = array_diff(scandir(ABSPATH), ['.', '..', '.DS_Store', '.tmb']);
        foreach ($files as $file) {
            $valid_permission = 755;
            if (is_dir(ABSPATH . DIRECTORY_SEPARATOR . $file)) {
                $valid_permission = 755;
            }
            if ($valid_permission < decoct(fileperms(ABSPATH . DIRECTORY_SEPARATOR . $file) & 0777)) {
                $bad_permission = true;
            }
        }
        if ($bad_permission  ) {
            
            $guide_link = sprintf("<a href='https://help.one.com/hc/%s/articles/360002087097-Change-the-file-permissions-via-an-FTP-client' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Your file and folder permissions are not set correctly. If they are too strict you get errors on your site, if they are too loose this poses a security risk. %sChange the file permissions via an FTP client%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Correct file permissions.', $onecom_wp),
            ];
        }
        oc_sh_save_result('file_permissions', $result[$oc_hm_status]);
        oc_sh_log_entry('Finished scanning for WP file permissions.');
        return $result;
    }
}

if (!function_exists('oc_sh_get_curl_header')) {
    function oc_sh_get_curl_header($url)
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        $headers = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                @$curl;
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                {
                    return $len;
                }
                $name = strtolower(trim($header[0]));
                if (!array_key_exists($name, $headers)) {
                    $headers[$name] = [trim($header[1])];
                } else {
                    $headers[$name][] = trim($header[1]);
                }
                return $len;
            }
        );
        curl_exec($ch);
        return $headers;
    }
}

if (!function_exists('oc_sh_check_connection')) {
    function oc_sh_check_connection()
    {
        global $onecom_wp, $oc_hm_status, $oc_hm_desc;
        include ABSPATH . WPINC . '/version.php';

        $guide_link = sprintf("<a href='https://help.one.com/hc/%s' target='_blank'>", onecom_generic_locale_link('', get_locale(), 1));

        try {
            $checksums = get_core_checksums($wp_version, 'en_US');
        } catch (Exception $e) {
            return [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Your site could not connect to wordpress.org, which means background updates may not be working properly. %Contact support%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        }
        if (!$checksums  ) {
            $result = [
                $oc_hm_status => OC_OPEN,
                $oc_hm_desc => sprintf(__('Your site could not connect to wordpress.org, which means background updates may not be working properly. %Contact support%s', $onecom_wp), $guide_link, "</a>"),
                
                
            ];
        } else {
            $result = [
                $oc_hm_status => OC_RESOLVED,
                $oc_hm_desc => __('Background updates are working properly.', $onecom_wp),
                
            ];
        }
        return $result;

    }
}

if (!function_exists('oc_sh_log_entry')) {
    function oc_sh_log_entry($message, $single=0)
    {
        if(! ((WP_DEBUG_LOG || WP_DEBUG_LOG == 'true') || $single == 1)){
            return;
        }
        $uploads_dir = wp_upload_dir();
        $log_dir = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'onecom_logs';
        if (!file_exists($log_dir)) {
            mkdir($log_dir);
        }
        $log_file = $log_dir . DIRECTORY_SEPARATOR . 'onecom-health-monitor.log';
        $time = date('Y-m-d H:i:s');
        file_put_contents($log_file, '[' . $time . '] ' . $message . "\n", FILE_APPEND);
    }
}

if (!function_exists('oc_sh_save_result')) {
    function oc_sh_save_result($stage, $oc_hm_status, $finish=0)
    {
        global $oc_hm_score;
        $result = get_site_transient('ocsh_site_scan_result');
        $time = time();
        if (!$result) {
            $result = [];
        }
        $result['time'] = $time;
        $result[$stage] = $oc_hm_status;
        $save = set_site_transient('ocsh_site_scan_result', $result, 4 * HOUR_IN_SECONDS);

        if($finish == 1){
            unset($result['time']);
            $health = [];
            $health['issues'] = $result;
            $health[$oc_hm_score] = round(oc_sh_calculate_score($result)[$oc_hm_score]);

            /* save health monitor result */
            oc_sh_log_entry('== One.com Health Monitor Scan ==');
            oc_sh_log_entry(json_encode($health), 1);
            
            (function_exists( 'onecom_generic_log')? onecom_generic_log( "wp_health_status", $health, NULL ):'');
        }

        return $save; 
    }
}

if (!function_exists('oc_sh_calculate_score')){
    function oc_sh_calculate_score($transient){
        global $oc_hm_score;
        if ( ! $transient || count($transient) === 0 ){
            return 0;
        }
        @$time = $transient['time'];
        unset($transient['time']);
        
        $success = 0;
        $percent = 0;
        
        foreach ($transient as $score){
            if ($score == OC_RESOLVED){
                $success++;
            }
        }
        $percent = round( (($success*100)/count($transient)), 2);
        if ($percent == '100.00'){
            $percent = 100;
        }
        return [
            $oc_hm_score => $percent,
            'time' => $time
        ];        
    }
}