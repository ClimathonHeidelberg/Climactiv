<?php
define( 'WP_INSTALLING', true );
define( 'DOING_AJAX', true );
/** Load WordPress Bootstrap */
require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/wp-load.php' );
nocache_headers();
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
/**
* Filter to override any theme exists 
**/
add_filter( 'upgrader_package_options', 'oci_upgrader_package_options_callback', 10, 1 );
if( ! function_exists( 'oci_upgrader_package_options_callback' ) ) {
	function oci_upgrader_package_options_callback( $options ) {
		$options[ 'abort_if_destination_exists' ] = false;
		return $options;
	}
}
/**
* Function to handle HTTP requests to GO API
**/
if( ! function_exists( 'onecom_http_requests_filter' ) ) {
	function onecom_http_requests_filter( $allow, $host, $url ) {
		$check_host = '';
		if( isset( $_SERVER[ 'ONECOM_WP_ADDONS_API' ] ) && $_SERVER[ 'ONECOM_WP_ADDONS_API' ] != '' ) {
            $check_host = rtrim( $_SERVER[ 'ONECOM_WP_ADDONS_API' ], '/' );
        } elseif( defined( 'ONECOM_WP_ADDONS_API' ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API != false ) {
            $check_host = rtrim( ONECOM_WP_ADDONS_API, '/' );
        }

        $urlParts = parse_url( $check_host );
        $check_host = preg_replace('/^www\./', '', $urlParts[ 'host' ]);

        if ( $host === $check_host ) {
            $allow = true;
            add_filter('http_request_reject_unsafe_urls', '__return_false' );
        }
        return $allow;
	}
}
/**
* Install theme callback function
**/
if( ! function_exists( 'oci_install_theme' ) ) {
	function oci_install_theme( $download_url, $theme_slug, $redirect = '', $retry = 0 ) {

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );

		add_filter( 'http_request_host_is_external', 'onecom_http_requests_filter', 10, 3 );

		$response = array();

		$title = sprintf( __( 'Installing theme', 'oci' ) );
		$nonce = 'theme-install';
		$url = add_query_arg(
			array(
				'package' => basename( $download_url ), 
				'action' => 'install',
			), 
			admin_url() 
		);

		$type = 'web'; //Install plugin type, From Web or an Upload.

		$skin     = new WP_Ajax_Upgrader_Skin( compact('type', 'title', 'nonce', 'url') );
		$upgrader = new Theme_Upgrader( $skin );

		$result   = $upgrader->install( $download_url );

		$status = array(
			'slug' => $theme_slug
		);

		// retry attempt
		$response[ 'attempt' ] = $retry+1;
		if( $retry == 2 ) {
			$default_retry_error_message = __( 'WordPress is being upgraded. Please try again later.', 'oci' );
		}

		$default_error_message = __( 'Something went wrong. Please contact the support at One.com.', 'oci' );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = ( $retry == 2 ) ? $default_retry_error_message : $result->get_error_message();
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = ( $retry == 2 ) ? $default_retry_error_message : $skin->result->get_error_message();
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = ( $retry == 2 ) ? $default_retry_error_message : $skin->get_error_messages();
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the file system. Please contact the support at One.com.', 'oci' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}
		}

		$status['themeName'] = wp_get_theme( $theme_slug )->get( 'Name' );

		$response[ 'type' ] = 'error';
		$response[ 'message' ] = ( isset( $status[ 'errorMessage' ] ) ) ? $status[ 'errorMessage' ] : $default_error_message ;

		if( $result == true ) {
			$response[ 'type' ] = 'redirect';
			$response[ 'message' ] = __( 'Theme installed successfully.', 'oci' );
			$response[ 'url' ] = admin_url( $redirect );

			unlink( WP_CONTENT_DIR.'/install.php' );
			
			$switched = switch_theme( $theme_slug );
			if( $switched ) {
				$status[ 'themeSwitch' ] = __( 'Theme activated successfully.', 'oci' );
			} else {
				$status[ 'themeSwitch' ] = __( 'Theme cannot be activated.', 'oci' );
			}
		}

		$response[ 'status' ] = $status;

		return $response;
	}
}
/**
* Install single plugin callback function
**/
if( ! function_exists( 'oci_install_plugin' ) ) {
	function oci_install_plugin( $slug ) {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		add_filter( 'http_request_host_is_external', 'onecom_http_requests_filter', 10, 3 );

		$title = sprintf( __( 'Installing plugin', 'oci' ) );
		$nonce = 'plugin-install';

		$download_url = MIDDLEWARE_URL.'/plugins/'.$slug.'/download';

		$url = add_query_arg(
			array(
				'package' => basename( $download_url ), 
				'action' => 'install',
				//'page' => 'page',
				//'step' => 'theme'
			), 
			''
		);

		$type = 'web'; //Install plugin type, From Web or an Upload.

		$skin     = new WP_Ajax_Upgrader_Skin( compact('type', 'title', 'nonce', 'url') );
		$upgrader2 = new Plugin_Upgrader( $skin );
		$result   = $upgrader2->install( $download_url );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
			if( $result->get_error_code() == 'folder_exists' ) {
				$plugin_infos = get_plugins( '/'.$slug );
				if( ! empty( $plugin_infos ) ) {
					foreach ($plugin_infos as $file => $info) :
						$is_activate = activate_plugin( $slug.'/'.$file );
						if ( is_wp_error( $is_activate ) ) {
							$status[ 'activated' ] = $is_activate->get_error_message();
						} else {
							$status[ 'activated' ] = true;
						}
					endforeach;
				}
			}
			//wp_send_json_error( $status );
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
			//wp_send_json_error( $status );
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = $skin->get_error_messages();
			//wp_send_json_error( $status );
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the file system. Please contact the support at One.com.', 'oci' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			//wp_send_json_error( $status );
		}

		if( $result == true ) {
			$status = install_plugin_install_status( array( 'slug' => $slug ) );
			
			$response[ 'type' ] = 'success';
			$response[ 'message' ] = __( 'Plugin installed successfully.', 'oci' );
		}

		$response[ 'status' ] = $status;
		return $response;
	}
}
/**
* Install multiple plugins callback function, parameter array of plugins 
* [Not in use]
**/
if( ! function_exists( 'oci_install_plugins' ) ) {
	function oci_install_plugins( $plugins ) {
		$plugins_response = array();
		if( ! empty( $plugins ) ) :
			
			foreach( $plugins as $key => $plugin ) :
				if( is_dir( WP_PLUGIN_DIR.'/'.$plugin[ 'slug' ] ) ) {
					$temp[ $plugin[ 'slug' ] ] = 'Destination folder already exists.';
					array_push( $plugins_response , $temp );
					continue;
				}
				$temp = $status = array();
				
				$result = oci_install_plugin( $plugin[ 'slug' ] );

				$temp[ 'status' ] = $result;
				array_push( $plugins_response , $temp );

			endforeach;
		endif;

		return $plugins_response;
	}
}
/**
* Activate all plugins after installation or already installed. 
**/
if( ! function_exists( 'oci_activate_plugins' ) ) {
	function oci_activate_plugins() {
		$plugins = get_plugins();

		// Add entry here if you want to keep deactivated a plugin
		$inactivate_plugins = array(
			'akismet/akismet.php',
			'hello.php',
			'visualcomposer/plugin-wordpress.php'
		);
		$response = array();
		if( ! empty( $plugins ) ) :
			foreach( $plugins as $file => $plugin ) :
				if( in_array( $file ,$inactivate_plugins ) ) {
					continue;
				}
				$temp = array();
				$temp[ 'plugin' ] = $file;
				$is_activate = activate_plugin( $file );
				if ( is_wp_error( $is_activate ) ) {
					$temp[ 'activated' ] = $is_activate->get_error_message();
				} else {
					$temp[ 'activated' ] = true;
				}
				array_push( $response ,$temp );
			endforeach;
		endif;
		return $response;
	}
}
/** 
* Handling all ajax actions here
**/
if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] != '' ) {
	$action 	= $_POST[ 'action' ];
	$next_step 		= isset( $_POST[ 'next_step' ] ) ? $_POST[ 'next_step' ] : '';
	$language 	= isset ( $_POST[ 'oci_language' ] ) ? $_POST[ 'oci_language' ] : 'en_US' ;
	
	$response 	= array();

	load_default_textdomain( $language );

	if( $action == 'oci_install_wp' ) : // action to install WP 

		$site_title 	= ( isset( $_POST[ 'site_title' ] ) && $_POST[ 'site_title' ] != '' ) ? wp_unslash( $_POST[ 'site_title' ] ) : 'A WordPress Site';
		$site_tagline 	= ( isset( $_POST[ 'site_tagline' ] ) && $_POST[ 'site_tagline' ] != '' ) ? wp_unslash( $_POST[ 'site_tagline' ] ) : 'Just another WordPress site';
		$public       	= ( isset( $_POST['blog_public'] ) ) ? (int) wp_unslash( $_POST['blog_public'] ) : 1;
		$username 		= ( isset($_POST['username']) ) ? wp_unslash( $_POST['username'] ) : '';
		$email 			= ( isset($_POST['email']) ) ? wp_unslash( $_POST['email'] ) : '';
		$password 		= ( isset($_POST['password']) ) ? wp_unslash( $_POST['password'] ) : '';

		$terms_and_conditions = ( isset($_POST['terms_and_conditions']) ) ? wp_unslash( $_POST['terms_and_conditions'] ) : true;

		if( $username != sanitize_user( $username, true ) ) {
			$response[ 'type' ] = 'error';
			$response[ 'message' ] = 'The username you provided has invalid characters.';
			echo json_encode( $response );
			die();
		}
		if( ! is_email( $email ) ) {
			$response[ 'type' ] = 'error';
			$response[ 'message' ] = 'Sorry, that isn\'t a valid email address. Email addresses look like <code>username@example.com</code>.';
			echo json_encode( $response );
			die();
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$result = wp_install( $site_title, $username, $email, $public, $deprecated = '', $password, $language );

		update_option( 'blogdescription', $site_tagline );

		$response[ 'type' ] = 'success';
		$response[ 'install' ] = 'true';
		$response[ 'message' ] = __( 'WordPress installed successfully!', 'oci' );
		$response[ 'result' ] = $result[ 'user_id' ];
		$response[ 'user' ] = array(
			'log' => $username,
			'pwd' => $password
		);
		$response[ 'install_dependancy' ] = true;

		if( ! isset( $result[ 'user_id' ] ) || $result[ 'user_id' ] == '' ) {
			$response[ 'message' ] = 'Not installed';
		} else {
			wp_set_auth_cookie( $result['user_id'] );
		}

		/**
		* Since 4.7.4, auto login was not working. Adding following code snippet to fix the issue
		**/
		$secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
		setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
		if ( SITECOOKIEPATH != COOKIEPATH ) {
			setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
		}
		
		echo json_encode( $response );
		die();

	elseif ( $action == 'oci_install_theme' ) : // action to install a theme

		require_once ( ABSPATH.'wp-admin/includes/file.php' );
		
		$download_url = $_POST[ 'download_url' ];
		$theme_slug = $_POST[ 'theme_slug' ];
		$redirect = isset( $_POST[ 'redirect' ] ) ? $_POST[ 'redirect' ] : '';

		$retry = isset( $_POST[ 'retry' ] ) ? $_POST[ 'retry' ] : 0;

		$result = oci_install_theme( $download_url, $theme_slug, $redirect, $retry );

		if( isset( $_COOKIE[ 'OnecomWPAuth' ] ) ) { // unset auth cookie
			unset( $_COOKIE[ 'OnecomWPAuth' ] );
  			setcookie( 'OnecomWPAuth', '', time() - ( 15 * 60 ), '/wp-admin/install.php', '.'.$_SERVER[ 'ONECOM_DOMAIN_NAME' ] );
		} 

		echo json_encode( $result );
		die();

	elseif( $action == 'oci_install_dependancy' ) : // action to install plugins [Not in use]


		require_once ( ABSPATH.'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$dependant_plugins = array(
			array(
				'slug' => 'onecom-themes-plugins'
			),
		);

		$response_temp = oci_install_plugins( $dependant_plugins );
		//$response = oci_install_plugin( 'one-com-wp-webshop' );
		$response[ 'type' ] = 'console';
		$response[ 'plugins' ] = $response_temp;

		$activate_plugins = oci_activate_plugins();
		$response[ 'activate' ] = $activate_plugins;

		echo json_encode( $response );
		die();

	elseif( $action == 'oci_activate_plugins' ) : // action to activate plugins 

		$plugins = oci_activate_plugins();
		$response[ 'type' ] = 'console';
		$response[ 'message' ] = $plugins;

		echo json_encode( $response );
		die();
	elseif( $action == 'oci_check_if_busy' ):
		$response = [];
		if ( 
			get_option( 'auto_updater.lock' ) // else if auto updater lock present
			|| get_option( 'core_updater.lock' ) // else if core updater lock present
		){
			$response[ 'type' ] = 'error';
			$response[ 'message' ] = __( 'WordPress is being upgraded. Please try again later.', 'onecom-wp' );
			$response[ 'code' ] = '1';
			
		}else{
			$response[ 'type' ] = 'success';
			$response[ 'message' ] = __( 'All ok', 'onecom-wp' );
			$response[ 'code' ] = '0';			
		}
		echo json_encode( $response );
		wp_die();
	endif;
}