<?php
/**
 * Plugin Name: 	One.com themes and plugins
 * Plugin URI:  https://help.one.com/hc/en-us/articles/115005593945
 * Plugin Info:  https://one.com
 * Version: 		0.4.6
 * Text Domain: 	onecom-wp
 * Domain Path: 	/languages
 * Description: 	Personalize your website with custom made themes and plugins exclusive to One.com customers. You can also find a curated list of plugins that we recommend.
 * Network: true
 * Author: 		One.com
 * Author URI: 	https://one.com/
 * License:     	GPL v2 or later
 * 
 * 	Copyright 2017 One.com
 * 
 * 	This program is free software; you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation; either version 2 of the License, or
 * 	(at your option) any later version.
 * 
 * 	This program is distributed in the hope that it will be useful,
 * 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 	GNU General Public License for more details.
 */
defined( 'ABSPATH' ) or die( 'Cheating Huh!' ); // Security

if( ! defined( 'ONECOM_WP_VERSION' ) ) {
	define( 'ONECOM_WP_VERSION', '0.4.5' );
}
if( ! defined( 'ONECOM_WP_PATH' ) ) {
	define( 'ONECOM_WP_PATH', plugin_dir_path( __FILE__ ) );
}
if( ! defined( 'ONECOM_WP_URL' ) ) {
	define( 'ONECOM_WP_URL', plugin_dir_url( __FILE__ ) );
}
if( !defined( 'MIDDLEWARE_URL' ) ) {
	$api_version = 'v1.0';
	if( isset( $_SERVER[ 'ONECOM_WP_ADDONS_API' ] ) && $_SERVER[ 'ONECOM_WP_ADDONS_API' ] != '' ) {
		$ONECOM_WP_ADDONS_API = $_SERVER[ 'ONECOM_WP_ADDONS_API' ];
	} elseif( defined( 'ONECOM_WP_ADDONS_API' ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API != false ) {
		$ONECOM_WP_ADDONS_API = ONECOM_WP_ADDONS_API;
	} else {
		$ONECOM_WP_ADDONS_API = 'http://wpapi.one.com/';
	}
	$ONECOM_WP_ADDONS_API = rtrim( $ONECOM_WP_ADDONS_API, '/' );
	define( 'MIDDLEWARE_URL', $ONECOM_WP_ADDONS_API.'/api/'.$api_version );
}
if( !defined( 'WP_API_URL' ) ) {
	$api_version = '1.0';
	define( 'WP_API_URL', 'https://api.wordpress.org/plugins/info/'.$api_version.'/' );
}
if( !defined( 'ONECOM_WP_CORE_VERSION' ) ) {
	global $wp_version;
	define( 'ONECOM_WP_CORE_VERSION' , $wp_version );
}
if( !defined( 'ONECOM_PHP_VERSION' ) ) {
	define( 'ONECOM_PHP_VERSION' , phpversion() );
}
/**
 * Include API hook file
 **/
include_once ONECOM_WP_PATH.'/inc/api-hooks.php';
/**
 * Plugin activation hook
 **/
if( ! function_exists( 'onecom_plugin_activation' ) ) {
	function onecom_plugin_activation() {
		onecom_trigger_log( $request = 'plugins', $slug = dirname( plugin_basename( __FILE__ ) ), $action = 'activate' );
		// Fix for VC v2.3 update, should be removed after few updates
		onecom_check_vc_uploads_rule();
	}
}
register_activation_hook( __FILE__, 'onecom_plugin_activation' );
/**
 * Plugin deactivation hook
 **/
if( ! function_exists( 'onecom_plugin_deactivation' ) ) {
	function onecom_plugin_deactivation() {
		onecom_trigger_log( $request = 'plugins', $slug = dirname( plugin_basename( __FILE__ ) ), $action = 'deactivate' );
	}
}
register_deactivation_hook( __FILE__, 'onecom_plugin_deactivation' );
/**
 * Plugin upgradation hook
 */
if( ! function_exists( 'onecom_plugin_upgradation' ) ) {
	function onecom_plugin_upgradation($upgrader_object, $options) {
		if('onecom-themes-plugins.php' !== plugin_basename( __FILE__)){
			return ;
		}
		if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
			foreach($options['plugins'] as $plugin){
				if ($plugin==plugin_basename( __FILE__)){
					// Fix for VC v2.3 update, should be remove after few updates
					onecom_check_vc_uploads_rule();
				}
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'onecom_plugin_upgradation',10, 2);

if( ! function_exists( 'onecom_update_htaccess' ) ) {
	function onecom_update_htaccess() {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir[ 'basedir' ];
		$htaccess = $upload_dir . DIRECTORY_SEPARATOR . '.htaccess';

		if( is_file( $htaccess ) ) {

			//Check if htaccess already having exception for visualcomposer
			$old_rules = @file_get_contents($htaccess);
			$find = 'RewriteCond %{REQUEST_URI} !^.*wp-content/uploads/visualcomposer-assets/.*\.js$';
			if ( strpos( $old_rules, $find ) ) {
				return true;
			}

			$data = '# Block executables
<FilesMatch "\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|html|htm|shtml|sh|cgi|suspected)$">
    deny from all
</FilesMatch>

# Block javascript except for visualcomposer (VC) plugin
RewriteEngine On
RewriteCond %{REQUEST_URI} !^.*wp-content/uploads/visualcomposer-assets/.*\.js$
RewriteRule ^(.*\.js)$ - [F,L]';

			$written = @file_put_contents($htaccess, $data);
			if(!$written){
				onecom_generic_log( 'vc_htaccess_update', 'Could not update the htaccess.', 'htaccess_error');
				return false;
			}

			update_site_option('vc_uploads_dir_allow_rule', true);
			onecom_trigger_log( $request = 'plugins', $slug = dirname( plugin_basename( __FILE__ ) ), $action = 'upgrade' );
			onecom_generic_log( 'vc_htaccess_update', 'Updated htaccess to allow VC.');
			onecom_write_log( "VC V2.3 FIX: ".$written );
		}
		else{
			//echo "Could not update the HTACCESS file";
			//onecom_generic_log( 'vc_htaccess_update', 'Failed to update htaccess.', true);
		}
	}
}


// Check if exception present
function onecom_check_vc_uploads_rule() {
	if (true !== (bool) get_option('vc_uploads_dir_allow_rule'))
		(function_exists( 'onecom_update_htaccess')) ? onecom_update_htaccess() : '';
}
add_action('plugins_loaded', 'onecom_check_vc_uploads_rule');


add_action( 'admin_init', 'onecom_check_for_get_request', -1 );
if( ! function_exists( 'onecom_check_for_get_request' ) ) {
	function onecom_check_for_get_request() {
		/**
		 * Deactivate plugin
		 **/
		if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'deactivate_plugin' ) {
			if( isset( $_POST[ 'plugin' ] ) && trim( $_POST[ 'plugin' ] ) != '' ) {
				$network_wide = false;
				$silent = false;
				if( is_multisite() && is_network_admin() ) {
					$network_wide = true;
				}
				$is = deactivate_plugins( $_POST[ 'plugin' ], $silent, $network_wide );
				wp_safe_redirect( wp_get_referer() );
			}
		}

		/**
		 * Delete site transient
		 **/
		if( isset( $_GET[ 'request' ] ) && $_GET[ 'request' ] != '' ) {
			delete_site_transient( 'onecom_'.$_GET[ 'request' ] );
			$url = ( is_network_admin() && is_multisite() ) ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' );
			$url = add_query_arg( array(
				'page' => 'onecom-wp-themes'
			), $url );
			wp_safe_redirect( $url );
			die();
		}
		return;
	}
}

add_action( 'plugins_loaded', 'onecom_wp_load_textdomain', -1);
if( ! function_exists( 'onecom_wp_load_textdomain' ) ) {
	function onecom_wp_load_textdomain() {
		
		// load english tranlsations [as] if any unsupported language is selected in WP-Admin
		if(strpos(get_locale(), 'en_') === 0){
			load_textdomain( 'onecom-wp', dirname( __FILE__ ) . '/languages/onecom-wp-en_US.mo' );
		}
		else{
			load_plugin_textdomain( 'onecom-wp', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
	}
}

/**
 * Limit load of resources on only specific admin pages to optimize loading time
 */
/* Add hook to following array where you want to enquque your resources */
global $load_onecom_wp_resources_slugs;
$load_onecom_wp_resources_slugs = array(
	'toplevel_page_onecom-wp',
	'one-com_page_onecom-wp-themes',
	'one-com_page_onecom-wp-plugins',
	'one-com_page_onecom-wp-recommended-plugins',
	'admin_page_onecom-wp-recommended-plugins',
	'admin_page_onecom-wp-discouraged-plugins',
	'one-com_page_onecom-wp-staging',
	'one-com_page_onecom-wp-staging-blocked',
	'one-com_page_onecom-wp-cookie-banner'
	//'one-com_page_onecom-wp-images',
);
$load_onecom_wp_resources_slugs = apply_filters( 'load_onecom_wp_resources_slugs', $load_onecom_wp_resources_slugs );

add_action( 'limit_enqueue_resources', 'limit_enqueue_resources_callback', 10, 3 );
if( ! function_exists( 'limit_enqueue_resources_callback' ) ) {
	function limit_enqueue_resources_callback( $handle, $hook, $type ) {
		global $load_onecom_wp_resources_slugs;
		if( in_array( $hook, $load_onecom_wp_resources_slugs) ) { // checking hook with provided array to be allowed
			if( $type == 'style' ) {
				wp_enqueue_style( $handle ); // if allowed, enqueue the style
			} else if( $type == 'script' ) {
				wp_enqueue_script( $handle ); // if allowed, enqueue the script
			}
		}
	}
}

add_action( 'admin_enqueue_scripts', 'register_one_core_resources' );
if( ! function_exists( 'register_one_core_resources' ) ) {
	function register_one_core_resources( $hook ) {
		$resource_extension = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min'; // Adding .min extension if SCRIPT_DEBUG is enabled
		$resource_min_dir = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-'; // Adding min- as a minified directory of resources if SCRIPT_DEBUG is enabled

		wp_register_style(
			$handle = 'one-font-icon',
			$src = ONECOM_WP_URL.'assets/fonts/onecom/style.css',
			$deps = null,
			$ver = ONECOM_WP_VERSION,
			$media = 'all'
		);
		wp_enqueue_style( 'one-font-icon' );
		wp_register_style(
			$handle = 'onecom-wp',
			$src = ONECOM_WP_URL.'assets/'.$resource_min_dir.'css/style'.$resource_extension.'.css',
			$deps = null,
			$ver = ONECOM_WP_VERSION,
			$media = 'all'
		);

		wp_register_script(
			$handle = 'onecom-wp',
			$src = ONECOM_WP_URL.'assets/'.$resource_min_dir.'js/script'.$resource_extension.'.js',
			$deps = array( 'jquery', 'thickbox', 'jquery-ui-dialog' ),
			$ver = ONECOM_WP_VERSION
		);
		wp_localize_script( 'onecom-wp', 'onecom_vars',
			array(
				'network' => ( is_network_admin() && is_multisite() ) ? true : false
			)
		);

		wp_register_style(
			$handle = 'onecom-promo',
			$src = ONECOM_WP_URL.'assets/'.$resource_min_dir.'css/promo'.$resource_extension.'.css',
			$deps = null,
			$ver = ONECOM_WP_VERSION,
			$media = 'all'
		);

		wp_register_script(
			$handle = 'onecom-promo',
			$src = ONECOM_WP_URL.'assets/'.$resource_min_dir.'js/promo'.$resource_extension.'.js',
			$deps = array( 'jquery' ),
			$ver = ONECOM_WP_VERSION
		);

		/**
		 * Hooking resource into limit utilization
		 **/
		do_action( 'limit_enqueue_resources', $handle = 'onecom-wp', $hook, $type = 'style' );
		do_action( 'limit_enqueue_resources', $handle = 'onecom-wp', $hook, $type = 'script' );

		/* Google fonts */
		wp_register_style(
			$handle = 'onecom-wp-google-fonts',
			$src = '//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800',
			$deps = null,
			$ver = null,
			$media = 'all'
		);
		do_action( 'limit_enqueue_resources', $handle = 'onecom-wp-google-fonts', $hook, $type = 'style' );
	}
}

add_action( 'admin_menu', 'one_core_admin', -1 );
add_action( 'network_admin_menu', 'one_core_admin', -1 );
if( ! function_exists( 'one_core_admin' ) ) {
	function one_core_admin() {
		if( ! is_network_admin() && is_multisite() ) {
			return false;
		}
		$position = onecom_get_free_menu_position( '2.1' );

		// save for other One.com plugins and themes
		global $onecom_generic_menu_position;
		$onecom_generic_menu_position = $position;

		add_menu_page(
			$page_title = __( 'One.com', 'onecom-wp' ),
			$menu_title = __( 'One.com', 'onecom-wp' ),
			$capability = 'manage_options',
			$menu_slug = 'onecom-wp',
			$function = 'one_core_admin_callback',
			$icon_url = 'dashicons-admin-generic',
			$position
		);
		add_submenu_page(
			$parent_slug = 'onecom-wp',
			$page_title = __( 'Themes', 'onecom-wp' ),
			$menu_title = __( 'Themes', 'onecom-wp' ),
			$capability = 'manage_options',
			$menu_slug = 'onecom-wp-themes',
			$function = 'one_core_theme_listing_callback'
		);
		add_submenu_page(
			$parent_slug = 'onecom-wp',
			$page_title = __( 'Plugins', 'onecom-wp' ),
			$menu_title = __( 'Plugins', 'onecom-wp' ),
			$capability = 'manage_options',
			$menu_slug = 'onecom-wp-plugins',
			$function = 'one_core_plugin_listing_callback'
		);
		add_submenu_page(
			$parent_slug = null, // adding null to hide from submenu
			$page_title = __( 'Plugins', 'onecom-wp' ),
			$menu_title = __( 'Plugins', 'onecom-wp' ),
			$capability = 'manage_options',
			$menu_slug = 'onecom-wp-recommended-plugins',
			$function = 'one_core_recommended_plugin_listing_callback'
		);
		add_submenu_page(
			$parent_slug = null, // adding null to hide from submenu
			$page_title = __( 'Plugins', 'onecom-wp' ),
			$menu_title = __( 'Plugins', 'onecom-wp' ),
			$capability = 'manage_options',
			$menu_slug = 'onecom-wp-discouraged-plugins',
			$function = 'one_core_discouraged_plugin_listing_callback'
		);
		remove_submenu_page('onecom-wp','onecom-wp'); // remove admin duplicate menu item 		
	}
}

add_action( 'admin_bar_menu', 'add_one_bar_items', 100, 100 );
if( ! function_exists( 'add_one_bar_items' ) ) {
	function add_one_bar_items( $admin_bar ) {
		if( ! is_network_admin() && is_multisite() ) {
			return false;
		}
		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$args = array(
			'id'    => 'onecom-wp',
			//'parent' => 'top-secondary',
			'title' => __( 'One.com', 'onecom-wp' ),
			'href'  => ( is_multisite() && is_network_admin() ) ?  network_admin_url( 'admin.php?page=onecom-wp' ) : admin_url( 'admin.php?page=onecom-wp' ),
			'meta'  => array(
				'title' => __( 'One.com', 'onecom-wp' ),
				'class' => 'onecom-wp-admin-bar-item'
			),
		);
		$admin_bar->add_menu( $args );

		$args = array(
			'id'    => 'onecom-wp-themes',
			'parent' => 'onecom-wp',
			'title' => __( 'Themes', 'onecom-wp' ),
			'href'  => ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-themes' ) : admin_url( 'admin.php?page=onecom-wp-themes' ),
			'meta'  => array(
				'title' => __( 'Themes', 'onecom-wp' ),
			),
		);
		$admin_bar->add_menu( $args );

		$args = array(
			'id'    => 'onecom-wp-plugins',
			'parent' => 'onecom-wp',
			'title' => __( 'Plugins', 'onecom-wp' ),
			'href'  => ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-plugins' ) : admin_url( 'admin.php?page=onecom-wp-plugins' ),
			'meta'  => array(
				'title' => __( 'Plugins', 'onecom-wp' ),
			),
		);
		$admin_bar->add_menu( $args );

		$args = array(
			'id'    => 'onecom-wp-staging',
			'parent' => 'onecom-wp',
			'title' => __( 'Staging', 'onecom-wp' ),
			'href'  => ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-staging' ) : admin_url( 'admin.php?page=onecom-wp-staging' ),
			'meta'  => array(
				'title' => __( 'Plugins', 'onecom-wp' ),
			),
		);
		$admin_bar->add_menu( $args );

		/*
		* Account link to Control Panel
		*/
		$args = array(
			'id'    => 'one-cp',
			'parent' => 'onecom-wp',
			'title' => __( 'One.com Control Panel', 'onecom-wp' ),
			'href'  => 'https://www.one.com/admin/wp-overview.do',
			'meta'  => array(
				'title' => __( 'Go to Control Panel at One.com', 'onecom-wp' ),
				'target' => '_blank'
			),
		);
		$admin_bar->add_menu( $args );

		/*
		* WordPress support
		*/
		$locale = get_locale();

		$args = array(
			'id'    => 'one-wp-support',
			'parent' => 'onecom-wp',
			'title' => __( 'One.com Guides & FAQ', 'onecom-wp' ),
			'href'  => onecom_generic_locale_link( $request = 'main_guide', $locale ),
			'meta'  => array(
				'title' => __( 'Go to Guides & FAQ at One.com', 'onecom-wp' ),
				'target' => '_blank'
			),
		);
		$admin_bar->add_menu( $args );
	}
}

if( ! function_exists( 'one_core_admin_callback' ) ) {
	function one_core_admin_callback() {
		$network = ( is_network_admin() && is_multisite() ) ? 'network/' : '';
		include_once 'templates/'.$network.'theme-listing.php';
	}
}

if( ! function_exists( 'one_core_theme_listing_callback' ) ) {
	function one_core_theme_listing_callback() {
		$network = ( is_network_admin() && is_multisite() ) ? 'network/' : '';
		include_once 'templates/'.$network.'theme-listing.php';
	}
}

if( ! function_exists( 'one_core_plugin_listing_callback' ) ) {
	function one_core_plugin_listing_callback() {
		$network = ( is_network_admin() && is_multisite() ) ? 'network/' : '';
		include_once 'templates/'.$network.'plugin-listing.php';
	}
}

if( ! function_exists( 'one_core_recommended_plugin_listing_callback' ) ) {
	function one_core_recommended_plugin_listing_callback() {
		$network = ( is_network_admin() && is_multisite() ) ? 'network/' : '';
		include_once 'templates/'.$network.'recommended-plugin-listing.php';
	}
}

if( ! function_exists( 'one_core_discouraged_plugin_listing_callback' ) ) {
	function one_core_discouraged_plugin_listing_callback() {
		$network = (is_network_admin() && is_multisite() ) ? 'network/' : '';
		include_once 'templates/'.$network.'discouraged-plugin-listing.php';
	}
}
/**
 * Function to get free position for menu
 **/
if( ! function_exists( 'onecom_get_free_menu_position' ) ) {
	function onecom_get_free_menu_position($start, $increment = 0.3) {
		foreach ($GLOBALS['menu'] as $key => $menu) {
			$menus_positions[] = $key;
		}

		if (!in_array($start, $menus_positions)) return $start;

		/* the position is already reserved find the closet one */
		while (in_array($start, $menus_positions)) {
			$start += $increment;
		}

		return (string) $start;
	}
}

/**
 * One.com updater
 **/
if( ! class_exists( 'ONECOMUPDATER' ) ) {
	require_once ONECOM_WP_PATH.'/inc/update.php';
}

/**
 * General functions
 **/
add_action( 'admin_init', 'onecom_admin_init_callback' );
if( ! function_exists( 'onecom_admin_init_callback' ) ) {
	function onecom_admin_init_callback() {
		require_once ONECOM_WP_PATH.'/inc/functions.php';
	}
}

/**
 * One.com staging
 **/
if(!class_exists('OneStaging\\OneStaging') && is_admin()){
	include_once 'staging/one_staging.php';
	\OneStaging\OneStaging::getInstance()->run();
}

/**
 * Dynamic guide links
 **/
global $onecom_global_links;
$onecom_global_links = array();
$onecom_global_links[ 'en' ] = array(
	'main_guide' => 'https://help.one.com/hc/en-us/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/en-us/articles/115005586029-Discouraged-WordPress-plugins',
	'staging_guide' => 'https://help.one.com/hc/en-us/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/en-us/articles/360001472758'
);
$onecom_global_links[ 'cs_CZ' ] = array(
	'main_guide' => 'https://help.one.com/hc/cs/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/cs/articles/115005586029-Nedoporu%C4%8Dovan%C3%A9-moduly-plug-in-ve-WordPressu',
	'staging_guide' => 'https://help.one.com/hc/cs/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/cs/articles/360001472758'
);
$onecom_global_links[ 'da_DK' ] = array(
	'main_guide' => 'https://help.one.com/hc/da/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/da/articles/115005586029-Frar%C3%A5dede-WordPress-plugins',
	'staging_guide' => 'https://help.one.com/hc/da/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/da/articles/360001472758'
);
$onecom_global_links[ 'de_DE' ] = array(
	'main_guide' => 'https://help.one.com/hc/de/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/de/articles/115005586029-Nicht-empfohlene-Plugins',
	'staging_guide' => 'https://help.one.com/hc/de/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/de/articles/360001472758'
);
$onecom_global_links[ 'es_ES' ] = array(
	'main_guide' => 'https://help.one.com/hc/es/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/es/articles/115005586029-Plugins-de-WordPress-no-recomendados',
	'staging_guide' => 'https://help.one.com/hc/es/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/es/articles/360001472758'
);
$onecom_global_links[ 'fr_FR' ] = array(
	'main_guide' => 'https://help.one.com/hc/fr/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/fr/articles/115005586029-Les-plugins-WordPress-d%C3%A9conseill%C3%A9s',
	'staging_guide' => 'https://help.one.com/hc/fr/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/fr/articles/360001472758'
);
$onecom_global_links[ 'it_IT' ] = array(
	'main_guide' => 'https://help.one.com/hc/it/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/it/articles/115005586029-Plugin-per-WordPress-sconsigliati',
	'staging_guide' => 'https://help.one.com/hc/it/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/it/articles/360001472758'
);
$onecom_global_links[ 'nb_NO' ] = array(
	'main_guide' => 'https://help.one.com/hc/no/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/no/articles/115005586029-Ikke-anbefalte-WordPress-plugins',
	'staging_guide' => 'https://help.one.com/hc/no/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/no/articles/360001472758'
);
$onecom_global_links[ 'nl_NL' ] = array(
	'main_guide' => 'https://help.one.com/hc/nl/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/nl/articles/115005586029-Niet-aanbevolen-WordPress-plugins',
	'staging_guide' => 'https://help.one.com/hc/nl/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/nl/articles/360001472758'
);
$onecom_global_links[ 'pl_PL' ] = array(
	'main_guide' => 'https://help.one.com/hc/pl/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/pl/articles/115005586029-Niezalecane-wtyczki-WordPress',
	'staging_guide' => 'https://help.one.com/hc/pl/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/pl/articles/360001472758'
);
$onecom_global_links[ 'pt_PT' ] = array(
	'main_guide' => 'https://help.one.com/hc/pt/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/pt/articles/115005586029-Plugins-para-o-WordPress-desaconselh%C3%A1veis',
	'staging_guide' => 'https://help.one.com/hc/pt/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/pt/articles/360001472758'
);
$onecom_global_links[ 'fi' ] = array(
	'main_guide' => 'https://help.one.com/hc/fi/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/fi/articles/115005586029-WordPress-lis%C3%A4osat-joiden-k%C3%A4ytt%C3%B6%C3%A4-ei-suositella',
	'staging_guide' => 'https://help.one.com/hc/fi/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/fi/articles/360001472758'
);
$onecom_global_links[ 'sv_SE' ] = array(
	'main_guide' => 'https://help.one.com/hc/sv/sections/115001491649-WordPress',
	'discouraged_guide' => 'https://help.one.com/hc/sv/articles/115005586029-WordPress-till%C3%A4gg-som-vi-avr%C3%A5der-fr%C3%A5n',
	'staging_guide' => 'https://help.one.com/hc/sv/articles/360000020617',
	'cookie_guide' => 'https://help.one.com/hc/sv/articles/360001472758'
);

if( ! function_exists( 'onecom_generic_locale_link' ) ) {
	function onecom_generic_locale_link( $request, $locale, $lang_only=0 ) {
		global $onecom_global_links;
		if( ! empty( $onecom_global_links )  && array_key_exists( $locale, $onecom_global_links ) ) {

			if($lang_only != 0){ return strstr($locale, '_', true); }

			if( ! empty( $onecom_global_links[ $locale ][ $request ] ) ) {
				return $onecom_global_links[ $locale ][ $request ];
			}
		}

		if($lang_only != 0){ return 'en'; }

		return $onecom_global_links[ 'en' ][ $request ];
	}
}

if(file_exists(ONECOM_WP_PATH.'/modules/health-monitor/health-monitor.php')){
	require_once ONECOM_WP_PATH.'/modules/health-monitor/health-monitor.php';
}

if(file_exists(ONECOM_WP_PATH.'/modules/cookie-banner/cookie-banner.php')){
	require_once ONECOM_WP_PATH.'/modules/cookie-banner/cookie-banner.php';
}
  
/* Add "View Details" link for all One.com plugins, if not already exist */
add_filter( 'plugin_row_meta', 'onecom_generic_plugin_row_meta', 20, 2 );
function onecom_generic_plugin_row_meta( $links, $file ) {

	// skip all non-one.com plugin entries
	if($file != plugin_basename( __FILE__ )){
		return $links;
	}

	$health_url = ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-health-monitor' ) : admin_url( 'admin.php?page=onecom-wp-health-monitor' );

	$stg_url = ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-staging' ) : admin_url( 'admin.php?page=onecom-wp-staging' );

	$themes_url = ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-themes' ) : admin_url( 'admin.php?page=onecom-wp-themes' );

	$plugin_url = ( is_multisite() && is_network_admin() ) ? network_admin_url( 'admin.php?page=onecom-wp-plugins' ) : admin_url( 'admin.php?page=onecom-wp-plugins' );

	// add new link - "View Details"
	$anchor = '<a href="%s">%s</a>';

	$new_links = array(
		'oc-health' => sprintf($anchor,$health_url, __('Health Monitor', 'onecom-wp')),
		'oc-staging' => sprintf($anchor,$stg_url, __('Staging', 'onecom-wp')),
		'oc-themes' => sprintf($anchor,$themes_url, __('Themes')),
		'oc-plugins' => sprintf($anchor,$plugin_url, __('Plugins'))
	);
	
	// club the new link with existing links
	return array_merge( $links, $new_links );
}