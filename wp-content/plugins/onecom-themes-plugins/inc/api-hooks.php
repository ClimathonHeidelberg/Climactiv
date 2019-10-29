<?php
/**
 * @Version 1.5
 **/
if(!defined('OC_ACTIVATE_STR')){
	define('OC_ACTIVATE_STR','activate');
}
if(!defined('OC_DEACTIVATE_STR')){
	define('OC_DEACTIVATE_STR','deactivate');
}
if(!defined('OC_DM_NAME')){
	define('OC_DM_NAME','ONECOM_DOMAIN_NAME');
}
/**
 * WordPress action to trigger after activating the theme
 **/
add_action( 'after_switch_theme', 'onecom_activate_theme_trigger' );
/**
 * WordPress action to trigger after deactivating the theme
 **/
add_action( 'switch_theme', 'onecom_deactivate_theme_trigger', 10, 3 );
/**
 * Function after activating the theme
 **/
if( ! function_exists( 'onecom_activate_theme_trigger' ) ) {
	function onecom_activate_theme_trigger() {
		$theme = wp_get_theme();
		if( 'one.com' !== strtolower( $theme->display( 'Author', FALSE ) ) ) {
			return false;
		}
		onecom_trigger_log('themes', $theme->stylesheet, OC_ACTIVATE_STR);
	}
}
/**
 * Function after dectivating the theme
 **/
if( ! function_exists( 'onecom_deactivate_theme_trigger' ) ) {
	function onecom_deactivate_theme_trigger( $new_name, $new_theme, $old_theme ) {
		@$new_name; @$new_theme;
		if( 'one.com' !== strtolower( $old_theme->display( 'Author', FALSE ) ) ) {
			return false;
		}
		onecom_trigger_log('themes', $old_theme->stylesheet, OC_DEACTIVATE_STR);
	}
}

/**
 * Function to get the client ip address
 **/
if( ! function_exists( 'onecom_get_client_ip_env' ) ) {
	function onecom_get_client_ip_env() {
	    if (getenv('HTTP_CLIENT_IP')){
			$ipaddress = getenv('HTTP_CLIENT_IP');
		}
	    else if(getenv('REMOTE_ADDR')){
			$ipaddress = getenv('REMOTE_ADDR');
		}   
	    else{
			$ipaddress = '0.0.0.0';
		}	 
	    return $ipaddress;
	}
}


/**
 * Generic log to WP API
 * @param string action
 * @param string message
 * @param string error
 * @return void
 **/
if (!function_exists('onecom_generic_log')) {
    function onecom_generic_log($action, $message = '', $error = null)
    {
        if (!isset($action) && strlen($action)) {
            return '';
        }

        global $wp_version;

        // if message is not an array
		if(!is_array($message)){

			@json_decode($message);

			// if message is neither an array nor a JSON
			if(json_last_error() != JSON_ERROR_NONE){
				$message = filter_var($message, FILTER_SANITIZE_STRING);
			}			
		}
		else{
			$message = json_encode($message);
		}
        
        $log_entry = json_encode(
			array(
				'action_type' => filter_var($action ,FILTER_SANITIZE_STRING),
				'message' => $message,
				'error' => $error
			)
        );
        
        $log_url = MIDDLEWARE_URL . '/log';
        $client_ip = onecom_get_client_ip_env();
        $client_domain = (isset($_SERVER[OC_DM_NAME]) && !empty($_SERVER[OC_DM_NAME])) ? $_SERVER[OC_DM_NAME] : 'localhost';

        $save_log = wp_safe_remote_post($log_url, array(
            'method' => 'POST',
            'timeout' => 10,
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'body' => $log_entry,
            'headers' => array(
                'X-ONECOM-CLIENT-IP' => $client_ip,
                'X-ONECOM-CLIENT-DOMAIN' => $client_domain,
            ),
        )
        );

        // For Debugging
        if (!is_wp_error($save_log)) {
            return true;
		}
		
        return false;
    }
}

/**
 * Trigger log to WP API
 * @param string category
 * @param string item_slug
 * @param string item_action
 * @param string is_ajax
 **/
if( ! function_exists( 'onecom_trigger_log' ) ) {
	function onecom_trigger_log( $request, $slug, $action, $is_ajax = false ) {
		global $onecom_log_url, $onecom_log_request, $onecom_log_slug, $onecom_log_action;

		$url = MIDDLEWARE_URL.'/stats/'.$request.'/'.$slug.'/'.$action;

		$onecom_log_url 	= $url;
		$onecom_log_action	= $action;
		$onecom_log_request	= $request;
		$onecom_log_slug 	= $slug;

		if( OC_DEACTIVATE_STR === $action || 'plugins' === $request || $is_ajax === true ) {
			onecom_trigger_log_by_wp();
		} else {
			add_action( 'admin_head', 'onecom_trigger_log_by_js' );
		}
		return false;
	}
}

/**
 * Function to call log API Synchronously (deactivate not working with Asynchronusly)
 **/
if( ! function_exists( 'onecom_trigger_log_by_wp' ) ) {
	function onecom_trigger_log_by_wp() {
		global $onecom_log_url, $onecom_log_request, $onecom_log_slug, $onecom_log_action;

		$option = 'onecom_activation_'.$onecom_log_request.'_'.$onecom_log_slug;

		if( $onecom_log_action === OC_ACTIVATE_STR && true === get_site_option( $option )) {
			return false;
		}

		$ip = onecom_get_client_ip_env();
		$domain = ( isset( $_SERVER[ OC_DM_NAME ] ) && ! empty( $_SERVER[ OC_DM_NAME ] ) ) ? $_SERVER[ OC_DM_NAME ] : 'localhost';
		onecom_write_log( $onecom_log_url );

		global $wp_version;

		$args = array(
			'method' => 'GET',
			'timeout'       => 10,
			'httpversion'   => '1.0',
			'user-agent'    => 'WordPress/' . $wp_version . '; ' . home_url(),
			'compress'      => false,
			'decompress'    => true,
			'sslverify'     => true,
			'stream'        => false,
			'headers'       => array(
				'X-ONECOM-CLIENT-IP' => $ip,
				'X-ONECOM-CLIENT-DOMAIN' => $domain
			)
		);

		if( $onecom_log_action === OC_ACTIVATE_STR ) {
			update_site_option( $option, true );
		} else if( $onecom_log_action === OC_DEACTIVATE_STR ) {
			delete_site_option( $option );
		}

		wp_safe_remote_get( $onecom_log_url, $args );
		return false;
	}
}

/**
 * Function to call log API Asynchronously
 **/
if( ! function_exists( 'onecom_trigger_log_by_js' ) ) {
	function onecom_trigger_log_by_js() {
		global $onecom_log_url, $onecom_log_request, $onecom_log_slug, $onecom_log_action;
		?>
        <script type="text/javascript">
            ( function( $ ) {
                $( document ).ready( function() {
                    var data = {
                        'action'		: 'onecom_log_activate_ajax',
                        'request'		: '<?php echo $onecom_log_request; ?>',
                        'slug'			: '<?php echo $onecom_log_slug; ?>',
                        'log_action'	: '<?php echo $onecom_log_action; ?>',
                    };

                    $.post(ajaxurl, data, function(response) {
                        console.log(response);
                    });
                } );
            } )( jQuery );
        </script>
		<?php
	}
}

/**
 * Ajax action to call trigger
 **/
if( ! function_exists( 'onecom_log_activate_ajax_callback' ) ) {
	function onecom_log_activate_ajax_callback() {
		$request 	= $_POST[ 'request' ];
		$action 	= $_POST[ 'log_action' ];
		$slug 		= $_POST[ 'slug' ];
		onecom_trigger_log( $request , $slug, $action, true );
		wp_die();
	}
}
add_action( 'wp_ajax_onecom_log_activate_ajax', 'onecom_log_activate_ajax_callback' );

/**
 * Function to write logs
 **/
if ( ! function_exists( 'onecom_write_log' ) ) {
	function onecom_write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}
