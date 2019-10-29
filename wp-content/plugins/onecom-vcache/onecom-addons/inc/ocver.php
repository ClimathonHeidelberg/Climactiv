<?php
if( ! class_exists( 'OCVer' ) ) {
	class OCVer {
		private $transient = '__onecom_allowed_';
		private $transientDuration = 13; // in hours
		private $access = 'false';

		private $middleware;
		private $middlewareVer = 'v1.0';
		private $middlewareEndpoint = 'prostatus';

		private $pluginStatusURL = 'plugins';
		private $themesStatusURL = 'themes';

		private $isPlugin = true;
		private $itemSlug;

		/**
		* Construnctor to sync premium products
		*
		* @param boolean $is_plugin //True to False
		* @param string $item_slug //Slug of plugin or theme
		* @param number $duration //No of hours of transient check
		*/
		public function __construct( $is_plugin = true, $item_slug = '', $duration = null ) {
			if( '' === $item_slug ) {
				return;
			}

			$api_version = $this->middlewareVer;
			if( isset( $_SERVER[ 'ONECOM_WP_ADDONS_API' ] ) && $_SERVER[ 'ONECOM_WP_ADDONS_API' ] != '' ) {
				$ONECOM_WP_ADDONS_API = $_SERVER[ 'ONECOM_WP_ADDONS_API' ];
			} elseif( defined( 'ONECOM_WP_ADDONS_API' ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API != false ) {
				$ONECOM_WP_ADDONS_API = ONECOM_WP_ADDONS_API;
			} else {
				$ONECOM_WP_ADDONS_API = 'http://wpapi.one.com/';
			}
			$ONECOM_WP_ADDONS_API = rtrim( $ONECOM_WP_ADDONS_API, '/' );
			$this->middleware = $ONECOM_WP_ADDONS_API.'/api/'.$api_version;

			if( TRUE === $is_plugin ) {
				$this->isPlugin = true;
				$this->middlewareEndpoint = $this->pluginStatusURL . '/' . $this->middlewareEndpoint;
			} else {
				$this->isPlugin = false;
				$this->middlewareEndpoint = $this->themesStatusURL . '/' . $this->middlewareEndpoint;
			}
			$this->itemSlug = $item_slug;
			if( $duration != null || $duration != '' ) {
				$this->transientDuration = $duration;
			}

			//delete_site_transient( $this->transient . $this->itemSlug );
			//delete_site_option( $this->transient . $this->itemSlug );

			add_action( 'admin_init', array( $this, 'checkItemStatus' ) );
		}

		/**
		* Function to check item status
		*
		*/
		public function checkItemStatus() {

			if(!is_admin()){
				update_option('onecom_vcache_check', time());
			}

			$name = $this->transient . $this->itemSlug;
	        if ( get_site_transient( $name ) ) {
	            $value = get_site_transient( $name );
	            $access = $this->can_access( $value );
	            $this->access = $access;
	            return;
	        }

	       	$ip = $this->onecom_get_client_ip_env();
			$domain = ( isset( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) && ! empty( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) ) ? $_SERVER[ 'ONECOM_DOMAIN_NAME' ] : 'localhost';

			$data = array(
				'domain' => $domain
			);
			if( TRUE === $this->isPlugin ) {
				$data[ 'plugin' ] = $this->itemSlug;
			} else {
				$data[ 'theme' ] = $this->itemSlug;
			}

			$data = json_encode( array($data) );

			global $wp_version;
			$args = array(
			    'timeout'     => 5,
			    'httpversion' => '1.0',
			    'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
			    'body'        => $data,
			    'compress'    => false,
			    'decompress'  => true,
			    'sslverify'   => true,
			    'stream'      => false,
			    'headers'       => array(
		            'X-ONECOM-CLIENT-IP' => $ip,
		            'X-ONECOM-CLIENT-DOMAIN' => $domain
		        )
			);
			$url = $this->middleware.'/'.$this->middlewareEndpoint;
			$response = wp_remote_post( $url, $args );

			$response_value = $this->access;

			if( ! is_wp_error( $response ) ) {
				if( wp_remote_retrieve_response_code( $response ) == 200 ) {
					$body = wp_remote_retrieve_body( $response );

					$body = json_decode( $body );
					if( ! empty($body) && $body->success ) {
						//$this->access = $body->success;
						//$data = $body->data;
						$data = ( isset( $body->data[0] ) ) ? $body->data[0] : array() ;
						if( ! empty( $data ) ) {
							if( isset( $data->enabled ) && ( null !== $data->enabled || "null" !== $data->enabled ) ) {
								$response_value = (string) trim( $data->enabled );
							} else {
								$response_value = trim( $data->memory_limit );
							}
						}
					}
				}
			}

	        //update_site_option( $name, $response_value );
	        $this->updateTransient( $name, $response_value );
	        $this->access = $this->can_access( $response_value );
	        return $this->access;
	    }

		/**
		 * Function to return access of plugin or theme
		 *
		 * @return boolean
		 */
		public function isVer( $slug = '', $is_admin = true ) {

			if( '' !== $slug ) {
				$transient = $this->transient . $slug;

				if( get_site_transient( $transient ) ) {

					return $this->can_access( get_site_transient( $transient ) );
				} else {
					// set to call WPAPI
					// IF Admin logged-in
					// OR Every 1 hour from frontend traffic
					$time_slot = get_option('onecom_vcache_check', false, 0);

					if( TRUE === $is_admin || (time() - $time_slot) >= 3600) {
						return $this->checkItemStatus();
					}
				}
			}
			return (string) $this->access;
		}

	    /**
	    * Function to check if access available
	    *
	    * @param boolean/number $value
	    */
	    public function can_access( $value ) {
	    	if( 'false' === $value || FALSE === $value ) { // if value is boolean return same
	    		return 'false';
	    	} elseif( 'true' === $value || TRUE === $value ) {
	    		return 'true';
	    	}

    		if( isset( $_SERVER[ 'ONECOM_MEMORYLIMIT' ] ) ) { // memory check
    			if( $_SERVER[ 'ONECOM_MEMORYLIMIT' ] >= $value ) {
    				return 'true';
    			}
    		}
    		return ( 'true' === $this->access ) ? 'true' : 'false';
	    }

	    /**
	    * Function to update transient
	    *
	    * @param string $name //name of transient
	    * @param string $value //value of transient
	    * @param number $duration //duration of transient in hours | default $transientDuration hours
	    */
	    public function updateTransient( $name, $value, $duration = null ) {
	    	if( '' === $name || '' === $value ) {
	    		return false;
	    	}
	    	if( null === $duration ) {
	    		$duration = $this->transientDuration;
	    	}

	    	$set = set_site_transient( $name, $value, $this->transientDuration * HOUR_IN_SECONDS );
	    }

	    /**
         * Function to get the client ip address
         **/
        public function onecom_get_client_ip_env() {
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = '0.0.0.0';

            return $ipaddress;
        }
	}
}
//Initialise new object for new plugin/theme, like below
//$OCVer = new OCVer( $is_plugin = true, $slug = 'onecom-varnish', $duration = 8 );