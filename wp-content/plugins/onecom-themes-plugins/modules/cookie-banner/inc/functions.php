<?php
if(!defined('OC_TEXTDOMAIN')){
    define('OC_TEXTDOMAIN', 'onecom-wp');
}
if(!defined('OCCB_COOKIE_NAME')){
    define('OCCB_COOKIE_NAME', 'onecom_cookie_consent');
}

if(!defined('OCCB_COOKIE_EXP')){
    define('OCCB_COOKIE_EXP', 31536000);
}

if(!defined('OCCB_OPTION')){
    define('OCCB_OPTION', 'oc_cb_configuration');
}

if (!function_exists('oc_cb_config_page')) {
    function oc_cb_config_page()
    {
        add_submenu_page(
            OC_TEXTDOMAIN,
            __('Cookie Banner', OC_TEXTDOMAIN),
            __('Cookie Banner', OC_TEXTDOMAIN),
            'manage_options',
            'onecom-wp-cookie-banner',
            'oc_cookie_banner_callback'
        );
    }
}

function oc_cookie_banner_callback(){ 
   require dirname(plugin_dir_path( __FILE__ )).'/templates/oc_cookie_banner_admin.php';   
}

if (!function_exists('oc_cb_scripts_admin')) {
    function oc_cb_scripts_admin($hook_suffix)
    {
    
        if ($hook_suffix === 'one-com_page_onecom-wp-cookie-banner' || $hook_suffix === 'admin_page_onecom-wp-cookie-banner') {
            $folder = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-';
            $extenstion = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min';

            wp_enqueue_style('oc_cb_css', ONECOM_WP_URL . 'assets/' . $folder . 'css/cookie-banner-admin' . $extenstion . '.css');
            wp_enqueue_script('oc_cb_js', ONECOM_WP_URL . 'assets/' . $folder . 'js/cookie-banner-admin' . $extenstion . '.js', ['jquery'], null, true);
            wp_localize_script('oc_cb_js', 'oc_constants', [
                'oc_cb_token' => wp_create_nonce( "oc_cb_token" )
            ]);
        }
    }
}

if (!function_exists('oc_cb_scripts_frontend')) {
    function oc_cb_scripts_frontend()
    {   
        $folder = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-';
        $extenstion = (SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min';

        wp_enqueue_style('oc_cb_css_fr', ONECOM_WP_URL . 'assets/' . $folder . 'css/cookie-banner-frontend' . $extenstion . '.css');
        wp_enqueue_script('oc_cb_js_fr', ONECOM_WP_URL . 'assets/' . $folder . 'js/cookie-banner-frontend' . $extenstion . '.js', ['jquery'], null, true);
        wp_localize_script('oc_cb_js_fr', 'oc_constants', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
}

/* Save cookie banner settings */
if( ! function_exists( 'oc_cb_settings' ) ){
    function oc_cb_settings(){
        
        if(!check_ajax_referer( 'oc_cb_token', 'oc_cb_sec' )){
            die(json_encode(array('error'=>true, 'message'=>'unauthenticated request!')));
        }

        //TODO: implement security - CSRF token
        if(empty($_POST) || !array_key_exists('settings', $_POST)){
            die(json_encode(array('error'=>true, 'message'=>'No data provided!')));
        }

        $query = $_POST['settings'];
        
        parse_str($query, $settings);

        $default = array(
            'show' => 0,
            'banner_text' => '',
            'policy_link' => '',
            'policy_link_text' => '',
            'policy_link_url' => '',
            'button_text' => '',
            'banner_style' => 'grey',
        );

        $settings = array_merge($default, $settings);

        $new_settings = [];

        // update the status of showing admin notice
        // if user has enabled the cookie banner then we should not show the admin notice

        $new_settings['show_notice'] = (($settings['show'] == 1) ? false : true);

        $new_settings['config'] = $settings;

        update_site_option('oc_cb_configuration', $new_settings);

        die(json_encode(array('error'=>null, 'message'=>'settings saved!')));
    
    }
}
add_action('wp_ajax_oc_cb_settings', 'oc_cb_settings');


/* Save cookie banner acceptance */
function oc_cb_cookie_consent(){
    //TODO: implement security - CSRF token
    if(empty($_POST)){
        die(json_encode(array('error'=>true, 'message'=>'No data provided!')));
    }

    // set a cookie for 1 year
    if(!isset($_COOKIE[OCCB_COOKIE_NAME])) {
        $time = time();         
        setcookie(OCCB_COOKIE_NAME, $time, $time+OCCB_COOKIE_EXP, COOKIEPATH, COOKIE_DOMAIN, true);
    }

    die(json_encode(array('error'=>null, 'message'=>$_COOKIE['onecom_cookie_consent'])));
}

add_action('wp_ajax_oc_cb_cookie_consent', 'oc_cb_cookie_consent');
add_action('wp_ajax_nopriv_oc_cb_cookie_consent', 'oc_cb_cookie_consent');


/* Display cookie banner notice inside WP-admin */
if( ! function_exists( 'oc_cookie_banner_notice' ) ){
	function oc_cookie_banner_notice(){
		$screen = get_current_screen();

		$skip_screens = array(
			'one-com_page_onecom-wp-cookie-banner',
			/* 'plugins', */
        );

        // return if screen not allowed
		if(in_array($screen->base, $skip_screens)){
            return false;
        }

		// get installed plugins
		$active_plugins = get_site_option('active_plugins');
		$config_status = get_site_option('oc_cb_configuration');
        $flag = false;

        // show banner if this is a fresh install and user hasn't taken any action
        if(empty($config_status)){
            $flag = true;
        }
        // hide banner if the user has intentionally disabled the banner
		else if(isset($config_status['show_notice']) && !$config_status['show_notice']){
			$flag = false;
        }

		// exit if OneCom-web-analytics plugin is not active.
		if(
			empty($active_plugins) || 
			!in_array('OneCom-web-analytics/wp-sbs-analytics.php', $active_plugins) ||
			!$flag

		){
			return false;
		}

        // prepare text for notice
		$text =  __('Since you are using One.com Analytics plugin, we recommend that you enable a cookie banner. This banner tells your visitors about your site storing data about them.', 'onecom-wp');

        // display notice
		echo sprintf(
			'<div class="notice notice-error is-dismissible"><p>%s  <span style="display:block; margin-top:10px"><a class="button button-primary" href="%s">%s</a>&nbsp;&nbsp;<a class="button" href="%s">%s</a></span></p></div>',
					$text, menu_page_url( 'onecom-wp-cookie-banner', false ), __('Setup cookie banner', 'onecom-wp'),
					admin_url('admin-post.php')."?action=oc_cb_notice&data=dismiss", __('Skip setup', 'onecom-wp')
		);

	}
}
add_action( 'admin_notices', 'oc_cookie_banner_notice', 2 );

/* Display cookie banner on website frontend */
function oc_cb_output_banner(){

    /* check if cookie already exists and has not expired */
    $time = time();

    if(
        !isset($_COOKIE[OCCB_COOKIE_NAME]) || 
        (isset($_COOKIE[OCCB_COOKIE_NAME]) && ((int)$time - (int)$_COOKIE[OCCB_COOKIE_NAME] >= OCCB_COOKIE_EXP))
    ){
        include_once ONECOM_WP_PATH . 'modules' . DIRECTORY_SEPARATOR . 'cookie-banner' . DIRECTORY_SEPARATOR . 'templates/oc_cookie_banner_frontend.php';
    }
}
add_action('wp_footer', 'oc_cb_output_banner');


/* Get params from URL */
function oc_cb_dismiss_notice(){
    $config_status = get_site_option('oc_cb_configuration');

    $config_status['show_notice'] = false;

    update_site_option(OCCB_OPTION, $config_status);
    wp_safe_redirect(admin_url());
    
}
add_action( 'admin_post_oc_cb_notice', 'oc_cb_dismiss_notice' );