<?php
// No Direct Access
defined( "WPINC" ) or die(); // No Direct Access

/*
 * Discouraged Plugins notice
 * */
if( ! function_exists( 'onecom_discouraged_plugins_notice' ) ){
	function onecom_discouraged_plugins_notice(){
		$screen = get_current_screen();

		$screens = array(
			'dashboard',
			'plugins',
		);

		// return if screen not allowed
		if(! in_array($screen->base, $screens))
			return;

		// get installed plugins
		$active_plugins = get_site_option('active_plugins');

		if(empty($active_plugins))
			return;

		$active_plugins_slug=[];
		foreach ($active_plugins as $plg){
			$active_plugins_slug[] = explode( '/', $plg)[0];
		}

		// get discouraged plugins list
		$plugins = get_site_transient( 'onecom_discouraged_plugins' );
		$fetch_plugins_url = MIDDLEWARE_URL.'/discouraged-plugins';

		$fetch_plugins_url = onecom_query_check( $fetch_plugins_url );

		$ip = onecom_get_client_ip_env();
		$domain = ( isset( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) && ! empty( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) ) ? $_SERVER[ 'ONECOM_DOMAIN_NAME' ] : 'localhost';

		if ( ( ! $plugins ) || empty( $plugins ) ) {
			global $wp_version;
			$args = array(
				'timeout'     => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'body'        => null,
				'compress'    => false,
				'decompress'  => true,
				'sslverify'   => true,
				'stream'      => false,
				'headers'       => array(
					'X-ONECOM-CLIENT-IP' => $ip,
					'X-ONECOM-CLIENT-DOMAIN' => $domain
				)
			);
			$response = wp_remote_get( $fetch_plugins_url, $args );

			if( is_wp_error( $response ) ) {
				if( isset( $response->errors[ 'http_request_failed' ] ) ) {
					$errorMessage = __( 'Connection timed out', 'onecom-wp' );
				} else {
					$errorMessage = $response->get_error_message();
				}
			} else {
				if( wp_remote_retrieve_response_code( $response ) != 200 ) {
					$errorMessage = '('.wp_remote_retrieve_response_code( $response ).') '.wp_remote_retrieve_response_message( $response );
				} else {
					$body = wp_remote_retrieve_body( $response );
					$body = json_decode( $body );

					if( ! empty($body) && $body->success ) {
							$plugins = $body->data;
					} elseif ( $body->success == false ) {
						if( $body->error == 'RESOURCE NOT FOUND' ) {
							$args = array(
								'request' => 'discouraged_plugins',
							);
						} else {
							echo $body->error;
						}
					}
				}

				if( empty($plugins) )
					return;

				set_site_transient( 'onecom_discouraged_plugins', $plugins, 3 * HOUR_IN_SECONDS );
			}
		}
		$disc_plugins = [];
		foreach ( $plugins as $plugin ) {
			$disc_plugins[] =$plugin->slug;
		}

		$active_disc_plugins = array_intersect( $disc_plugins, $active_plugins_slug);

		if(empty($active_disc_plugins))
			return;

		$text =  __('You are using one or more plugins from the list of plugins we discourage. We recommend that you deactivate them, to ensure the best performance of your website. ', 'onecom-wp');

		echo $message = sprintf(
			'<div class="notice notice-error is-dismissible"><p>%s&nbsp;<a href="%s">%s</a></p></div>',
					$text, admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' ), __('View discouraged plugins', 'onecom-wp')
		);

	}
}
add_action( 'admin_notices', 'onecom_discouraged_plugins_notice', 2 );
?>