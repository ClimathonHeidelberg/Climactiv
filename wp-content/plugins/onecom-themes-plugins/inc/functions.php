<?php
/* Increase timeout limit for downloading Visual Composer Plugin */
add_filter('http_request_args', 'vc_http_request_args', 100, 1);
function vc_http_request_args($r){
	if(strpos($r['filename'], 'visualcomposer')){
		add_action('http_api_curl', 'vc_http_api_curl', 100, 1);
		$r['timeout'] = 30;
	}
	return $r;
}
function vc_http_api_curl($handle){
	curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt( $handle, CURLOPT_TIMEOUT, 30 );
}
if( ! function_exists( 'onecom_themes_listing_config' ) ) {
	function onecom_themes_listing_config( $request = null, $per_page = 12 ) {
		$config = array();
		$themes = get_site_transient( 'onecom_themes' );

		if (is_array($themes)){
			foreach($themes as $key_top =>  $theme_group){
				if (isset($theme_group->collection)){
					foreach ($theme_group->collection as $key => $theme){
						if($theme->slug === 'onecom-ilotheme'){
							$themes['total'] = ($themes['total'] - 1);
						}
					}
				}
			}
		}

		if( ! $themes ) {
			return false;
		}
		if( $request == null ) {
			// $config[ 'item_count' ] = $themes[ 'item_count' ];
			$config[ 'item_count' ] = $per_page;
			$config[ 'total' ]	= $themes[ 'total' ];
			return $config;
		} else {
			return ( isset( $themes->{$request} ) ) ? $themes->{$request} : false;
		}
	}
}
if( ! function_exists( 'onecom_themes_listing_pagination' ) ) {
	function onecom_themes_listing_pagination( $config, $requsted_page_number = 1 ) {
		?>
			<div class="theme-browser-pagination text-center">
				<?php 
					$total_pages = ( int )ceil( ( $config[ 'total' ] / $config[ 'item_count' ] ) );
					if( $total_pages <= 1 ) {
						return;
					}
					$url = ( is_network_admin() && is_multisite() ) ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' );
					for ( $i = 1; $i <= $total_pages; $i++ ) :
						$page_number = $i;
						$item_class = '';
						if ( $page_number === 1 ) {
					        $item_class = 'first';
					    } else if ( $page_number === $total_pages ) {
					        $item_class = 'last';
					    }
					    if ( $page_number === $requsted_page_number ) {
					        $item_class .= ' current';
					    }
					    $args = array(
					    	'page' => 'onecom-wp-themes',
					    	'page_number' => $page_number
					    ); 
						?>
							<a href="<?php echo add_query_arg( $args, $url ); ?>" class="pagination-item <?php echo $item_class; ?>" data-request_page="<?php echo $page_number; ?>"><?php echo $page_number; ?></a>
						<?php
					endfor;
				?>
			</div>
		<?php
	}
}
if( ! function_exists( 'onecom_fetch_themes' ) ) {
	function onecom_fetch_themes( $page = 1, $exclude_ilotheme = false) {
		$themes = array();

		//delete_site_transient( 'onecom_themes' );
		$themes = get_site_transient( 'onecom_themes' );

		/* Note- simple switch over from previous data to new data structure */
		if( ! isset( $themes[ 'total' ] ) && ! empty( $themes ) ) { 
			delete_site_transient( 'onecom_themes' );
			$themes = get_site_transient( 'onecom_themes' );
		}

		// If requested page already exists in transient, return 
		if( ! empty( $themes ) && $themes['item_count'] >= 1000) {
			if( array_key_exists( $page, $themes ) ) { // page exists in current themes
				$themes = onecom_exclude_themes($themes, $exclude_ilotheme);
				return $themes[ $page ];
			}
		}

		$fetch_themes_url = MIDDLEWARE_URL.'/themes';

		$fetch_themes_url = onecom_query_check( $fetch_themes_url, $page );

		$ip = onecom_get_client_ip_env();
		$domain = ( isset( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) && ! empty( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) ) ? $_SERVER[ 'ONECOM_DOMAIN_NAME' ] : 'localhost';

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
		$response = wp_remote_get( $fetch_themes_url, $args );

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
					$themes[ 'item_count' ] = $body->data->item_count;
					$themes[ 'total' ] = $body->data->total;
					$themes[ $body->data->current_page ] = (object) array();
					$themes[ $body->data->current_page ]->collection = $body->data->collection;
					$themes[ $body->data->current_page ]->page_number = $body->data->current_page;					
				} elseif ( $body->success == false ) {
					if( $body->error == 'RESOURCE NOT FOUND' ) {
						$try_again_url = add_query_arg(
							array(
								'request' => 'themes',
							),
							''
						);
						$try_again_url = wp_nonce_url( $try_again_url, '_wpnonce' );
						$errorMessage = __( 'Sorry, no compatible themes found for your version of WordPress and PHP.', 'onecom-wp' ).'&nbsp;<a href="'.$try_again_url.'">'.__( 'Try again', 'onecom-wp' ).'</a>';
					} else {
						$errorMessage = $body->error;
					}
				}	
			}
			$themes = onecom_exclude_themes($themes, $exclude_ilotheme);
			
			set_site_transient( 'onecom_themes', $themes, 3 * HOUR_IN_SECONDS );
		}

		if( empty( $themes ) || ! isset( $themes[ $page ] ) ) {		
			$themes = new WP_Error( 'message', $errorMessage );
			return $themes;
		} else {
			return $themes[ $page ];	
		}
		
	}
}

if( ! function_exists( 'onecom_get_more_themes_callback' ) ) {
	function onecom_get_more_themes_callback() {
		$page = ( isset( $_POST[ 'page' ] ) && $_POST[ 'page' ] != '' ) ? $_POST[ 'page' ] : 1;
		global $theme_data;
		$theme_data = onecom_fetch_themes( $page, true );
		echo load_template( dirname( dirname( __FILE__ ) ) . '/templates/theme-listing-loop.php' ); 
		wp_die();
	}
}
add_action( 'wp_ajax_onecom_get_more_themes', 'onecom_get_more_themes_callback' );

/**
* Function to handle install a theme
**/
if( ! function_exists( 'onecom_install_theme_callback' ) ) {
	function onecom_install_theme_callback() {

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/theme.php' );

		if ( 
			get_option( 'auto_updater.lock' ) // else if auto updater lock present
			|| get_option( 'core_updater.lock' ) // else if core updater lock present
		) {
			$response[ 'type' ] = 'error';
			$response[ 'message' ] = __( 'WordPress is being upgraded. Please try again later.', 'onecom-wp' );
			echo json_encode( $response );
			wp_die();
		}

		$theme_slug = wp_unslash( $_POST[ 'theme_slug' ] );
		$redirect = ( isset( $_POST[ 'redirect' ] ) ) ? $_POST[ 'redirect' ] : false;
		$network = ( isset( $_POST[ 'network' ] ) ) ? (boolean) $_POST[ 'network' ] : false;

		$theme_info = onecom_get_theme_info( $theme_slug );

		$theme_info->download_link = MIDDLEWARE_URL.'/themes/'.$theme_info->slug.'/download';

		add_filter( 'http_request_host_is_external', 'onecom_http_requests_filter', 10, 3 );

		$title = sprintf( __( 'Installing theme', 'onecom-wp' ) );
		$nonce = 'theme-install';
		$url = add_query_arg(
			array(
				'package' => basename( $theme_info->download_link ), 
				'action' => 'install',
			), 
			admin_url() 
		);

		$type = 'web'; //Install plugin type, From Web or an Upload.

		$skin     = new WP_Ajax_Upgrader_Skin( compact('type', 'title', 'nonce', 'url') );
		$upgrader = new Theme_Upgrader( $skin );
		$result   = $upgrader->install( $theme_info->download_link );

		$status = array(
			'slug' => $theme_info->slug
		);

		$default_error_message = __( 'Something went wrong. Please contact the support at One.com.', 'onecom-wp' );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
		} elseif ( is_wp_error( $skin->result ) ) {
			$status['errorCode']    = $skin->result->get_error_code();
			$status['errorMessage'] = $skin->result->get_error_message();
		} elseif ( $skin->get_errors()->get_error_code() ) {
			$status['errorMessage'] = $skin->get_error_messages();
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$status['errorCode']    = 'unable_to_connect_to_filesystem';
			$status['errorMessage'] = __( 'Unable to connect to the file system. Please contact the support at One.com.', 'onecom-wp' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}
		}

		$status['themeName'] = wp_get_theme( $theme_slug )->get( 'Name' );

		$response[ 'type' ] = 'error';
		$response[ 'message' ] = ( isset( $status[ 'errorMessage' ] ) ) ? $status[ 'errorMessage' ] : $default_error_message ;

		if( $result == true ) {
			$response[ 'type' ] = 'success';
			$response[ 'message' ] = __( 'Theme installed successfully', 'onecom-wp' );

			if( $redirect != false ) {
				$button_html = '<span class="action-text one-activate-theme">'.__( 'Activate', 'onecom-wp' ).'</span>';
			} else {
				if( $network ) {
					$activate_url = add_query_arg( array(
						'action'     => 'enable',
						'_wpnonce'   => wp_create_nonce( 'enable-theme_' . $theme_slug ),
						'theme' => $theme_slug,
					), network_admin_url( 'themes.php' ) );
				} else {
					$activate_url = add_query_arg( array(
						'action'     => 'activate',
						'_wpnonce'   => wp_create_nonce( 'switch-theme_' . $theme_slug ),
						'stylesheet' => $theme_slug,
					), admin_url( 'themes.php' ) ); 
				}
				$button_html = '<a href="'.$activate_url.'">'.__( 'Activate', 'onecom-wp' ).'</a>';
			}
			
			$response[ 'button_html' ] = $button_html;
		}

		$response[ 'status' ] = $status;

		echo json_encode( $response );

		wp_die();
	}
}
add_action( 'wp_ajax_onecom_install_theme', 'onecom_install_theme_callback' );

if( ! function_exists( 'onecom_fetch_plugins' ) ) {
	function onecom_fetch_plugins( $recommended = false, $discouraged = false ) {
		$plugins = array();
		
		//delete_site_transient( 'onecom_plugins' );
		//delete_site_transient( 'onecom_recommended_plugins' );
		//delete_site_transient( 'onecom_discouraged_plugins' );

		if( $recommended ) {
			$plugins = get_site_transient( 'onecom_recommended_plugins' );
			$fetch_plugins_url = MIDDLEWARE_URL.'/recommended-plugins';
		} else if( $discouraged ) {
			$plugins = get_site_transient( 'onecom_discouraged_plugins' );
			$fetch_plugins_url = MIDDLEWARE_URL.'/discouraged-plugins';
		} else {
			$plugins = get_site_transient( 'onecom_plugins' );
			$fetch_plugins_url = MIDDLEWARE_URL.'/plugins';
		}

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
						if( $recommended || $discouraged ) { 
							$plugins = $body->data;
							
						} else {
							$plugins = $body->data->collection;
						}
						
					} elseif ( $body->success == false ) {
						if( $body->error == 'RESOURCE NOT FOUND' ) {
							if( $recommended ) { 
								$args = array(
									'request' => 'recommended_plugins',
								);
							} else if( $discouraged ) {
								$args = array(
									'request' => 'discouraged_plugins',
								);
							} else {
								$args = array(
									'request' => 'plugins',
								);
							}
							$try_again_url = add_query_arg(
								$args, ''
							);
							$try_again_url = wp_nonce_url( $try_again_url, '_wpnonce' );
							$errorMessage = __( 'Sorry, no compatible plugins found with your version of WordPress and PHP.', 'onecom-wp' ).'&nbsp;<a href="'.$try_again_url.'">'.__( 'Try again', 'onecom-wp' ).'</a>';
						} else {
							echo $body->error;
						}
					}
				}

				if( $recommended ) {
					set_site_transient( 'onecom_recommended_plugins', $plugins, 3 * HOUR_IN_SECONDS );
				} else if( $discouraged ){
					set_site_transient( 'onecom_discouraged_plugins', $plugins, 3 * HOUR_IN_SECONDS );
				} else {
					set_site_transient( 'onecom_plugins', $plugins, 3 * HOUR_IN_SECONDS );
				}
			}		
		}

		if( empty( $plugins ) ) {		
			$plugins = new WP_Error( 'message', $errorMessage );
		}

		return $plugins;
	}
}

/**
* Ajax handler to activate theme 
**/
if( ! function_exists( 'onecom_activate_theme_callback' ) ) {
	function onecom_activate_theme_callback() {
		$theme_slug = wp_unslash( $_POST[ 'theme_slug' ] );
		$redirect = $_POST[ 'redirect' ];
		$is_activate = switch_theme( $theme_slug );
		$response = array();
		//$response[ 'activated' ] = $is_activate;
		$response[ 'type' ] = 'redirect';
		$response[ 'url' ] = $redirect;

		echo json_encode( $response );
		wp_die();

	}
}
add_action( 'wp_ajax_onecom_activate_theme', 'onecom_activate_theme_callback' );

/**
*	It will return key of array of objects based on search value and search key 
**/
if( ! function_exists( 'onecom_search_key_in_object' ) ) {
	function onecom_search_key_in_object( $search_value, $array, $search_key ) {
	   foreach ( $array as $key => $val ) {
	       if ( $val->$search_key === $search_value ) {
	           return $key;
	       }
	   }
	   return null;
	}
}

/**
* Function to get theme info
**/
if( ! function_exists( 'onecom_get_theme_info' ) ) {
	function onecom_get_theme_info( $slug ) {
		$found_theme = false;
		if( $slug == '' ) {
			return new WP_Error( 'message', 'Theme slug should not be empty' );
		}

		$themes_pages = get_site_transient( 'onecom_themes' );		
		if( empty( $themes_pages ) ) {
			return new WP_Error( 'message', 'No themes found locally' );
		}

		foreach ( $themes_pages as $page_number_key => $theme_set ) :
			if( empty( $theme_set->collection ) ) {
				continue;
			}

			$collection = $theme_set->collection;

			foreach ( $collection as $key => $theme ) {
				if( $theme->slug == $slug ) {
					$found_theme = $theme;
					break 2;
				}
			}
			
		endforeach;

		if( $found_theme != false ) {
			return $found_theme;
		} else {
			return new WP_Error( 'message', 'Theme not found' );
		}
	}
}

/**
* Function to get theme info
**/
if( ! function_exists( 'onecom_get_plugin_info' ) ) {
	function onecom_get_plugin_info( $slug, $type ) {
		if( $slug == '' ) {
			return new WP_Error( 'message', 'Plugin slug should not be empty' );
		}
		$plugins = ( $type == 'recommended' ) ? get_site_transient( 'onecom_recommended_plugins_meta' ) : get_site_transient( 'onecom_plugins' );
		if( empty( $plugins ) ) {
			if( $type == 'recommended' ) {
				$plugins = onecom_fetch_plugins( $recommended = true );
			} else {
				$plugins = onecom_fetch_plugins();
			}
		    if( empty( $plugins ) ) {
				return new WP_Error( 'message', 'No plugins found locally' );
			}
		}
		$key = onecom_search_key_in_object( $slug, $plugins, 'slug' );
		return $plugins[ $key ];
	}
}

/**
* Check if theme installed
**/
if( ! function_exists( 'onecom_is_theme_installed' ) ) {
	function onecom_is_theme_installed( $theme_slug ) {
		$path = get_theme_root().'/'.$theme_slug.'/';
		if( file_exists($path) ) {
			return true;
		} else {
			return false;
		}
	}
}

/**
* Function to handle plugin installation
**/
if( ! function_exists( 'onecom_install_plugin_callback' ) ) {
	function onecom_install_plugin_callback( $isAjax = true, $pluginSlugParam = '' ) {
		$plugin_type = ( isset( $_POST[ 'plugin_type' ] ) ) ? wp_unslash( $_POST[ 'plugin_type' ] ) : 'normal';
		$download_url = ( isset( $_POST[ 'download_url' ] ) ) ? $_POST[ 'download_url' ] : '';
		$plugin_slug = ( isset( $_POST[ 'plugin_slug' ] ) ) ? wp_unslash( $_POST[ 'plugin_slug' ] ) : $pluginSlugParam;
		$plugin_name = ( isset( $_POST[ 'plugin_name' ] ) ) ? wp_unslash( $_POST[ 'plugin_name' ] ) : '';
		$redirect = ( isset( $_POST[ 'redirect' ] ) ) ? $_POST[ 'redirect' ] : false;

		if ( 
			get_option( 'auto_updater.lock' ) // else if auto updater lock present
			|| get_option( 'core_updater.lock' ) // else if core updater lock present
		) {
			return false;
		}

		$plugin_info = onecom_get_plugin_info( $plugin_slug, $plugin_type );
		$plugin_info->slug = $plugin_slug;

		if( $plugin_type == 'recommended' ) {
			$plugin_info->download_link = $download_url;
		} elseif ( $plugin_type == 'external' ) {
			$plugin_info->download_link = $plugin_info->download;
		} else {
			$plugin_info->download_link = MIDDLEWARE_URL.'/plugins/'.$plugin_info->slug.'/download';
		}
		
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		add_filter( 'http_request_host_is_external', 'onecom_http_requests_filter', 10, 3 );

		$title = sprintf( __( 'Installing plugin', 'onecom-wp' ) );
		$nonce = 'plugin-install';
		$url = add_query_arg(
			array(
				'package' => basename( $plugin_info->download_link ), 
				'action' => 'install',
				//'page' => 'page',
				//'step' => 'theme'
			), 
			admin_url() 
		);

		$type = 'web'; //Install plugin type, From Web or an Upload.

		$skin     = new WP_Ajax_Upgrader_Skin( compact('type', 'title', 'nonce', 'url') );
		//$skin = new Plugin_Installer_Skin( compact('type', 'title', 'nonce', 'url') );
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $plugin_info->download_link );

		$default_error_message = __( 'Something went wrong. Please contact the support at One.com.', 'onecom-wp' );

		if ( is_wp_error( $result ) ) {
			$status['errorCode']    = $result->get_error_code();
			$status['errorMessage'] = $result->get_error_message();
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
			$status['errorMessage'] = __( 'Unable to connect to the file system. Please contact the support at One.com.', 'onecom-wp' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base && is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() ) {
				$status['errorMessage'] = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			//wp_send_json_error( $status );
		}
		$response[ 'type' ] = 'error';
		$response[ 'message' ] = ( isset( $status[ 'errorMessage' ] ) ) ? $status[ 'errorMessage' ] : $default_error_message ;

		if( $result == true ) {
			$status = install_plugin_install_status( $plugin_info );
			$response[ 'type' ] = 'success';
			$response[ 'message' ] = __( 'Plugin installed successfully', 'onecom-wp' );
			$admin_url = ( is_multisite() ) ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
			$activateUrl = add_query_arg( array(
				'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $status['file'] ),
				'action'   => 'activate',
				'plugin'   => $status['file'],
			), $admin_url );
			if( $redirect == false || $redirect == '' || is_multisite() ) {
				$button_html = '<a href="'.$activateUrl.'" class="button button-primary activate-plugin">'.__( 'Activate', 'onecom-wp' ).'</a>';
			} else {
				$button_html = '<a class="activate-plugin activate-plugin-ajax button button-primary" href="javascript:void(0)" data-action="onecom_activate_plugin" data-redirect="'.$redirect.'" data-slug="'.$status['file'].'" data-name="'.$plugin_name.'">'.__( 'Activate', 'onecom-wp' ).'</a>';
			}
			$response[ 'button_html' ] = $button_html;
			$response[ 'info' ] = $plugin_info;
		}

		$response[ 'status' ] = $status;
		
		if( FALSE === $isAjax ) {
			return $response;
		}

		echo json_encode( $response );

		wp_die();
	}
}
add_action( 'wp_ajax_onecom_install_plugin', 'onecom_install_plugin_callback' );

/**
* Ajax handler to activate theme 
**/
if( ! function_exists( 'onecom_activate_plugin_callback' ) ) {
	function onecom_activate_plugin_callback() {
		$plugin_slug = wp_unslash( $_POST[ 'plugin_slug' ] );
		$redirect = $_POST[ 'redirect' ];
		$is_activate = activate_plugin( $plugin_slug );
		$response = array();

		if( is_wp_error( $is_activate ) ) {
			$response[ 'type' ] = 'error';
			$response[ 'message' ] = $is_activate->get_error_message();
		} else {
			$response[ 'type' ] = 'redirect';
			$response[ 'url' ] = $redirect;
		}

		echo json_encode( $response );
		wp_die();

	}
}
add_action( 'wp_ajax_onecom_activate_plugin', 'onecom_activate_plugin_callback' );

/**
* An alternative for thumbnail
**/
if( ! function_exists( 'onecom_string_acronym' ) ) {
	function onecom_string_acronym( $name ) {
		preg_match_all('/\b\w/', $name, $acronym);
		$str = implode( '', $acronym[0] );
		return substr($str, 0, 3);
	}
}

/**
* Pick random flat color 
**/
if( ! function_exists( 'onecom_random_color' ) ) {
	function onecom_random_color( $key = null ) {
		$array = array(
			'#FFC107', //yellow
			'#3498db', // peter river
			'#2ecc71', // emerald
			'#9b59b6', // Amethyst
			'#f1c40f', // sun flower
			'#e74c3c', // alizarin
			'#1abc9c', // turquoise
			'#00BCD4', // cyan,
			'#E91E63', // pink
			'#34495e', // wet asphalt
			'#CDDC39', // lime
			'#03A9F4', // light blue,
			'#8BC34A', // light green
			'#9C27B0', // purple
			'#3F51B5', // indigo
			'#F44336', // red
			'#009688', // teal
			
		);
		if( $key == null ) {
			$key = array_rand( $array );
		} else {
			$array_keys = array_keys( $array );
			if( ! in_array( $key, $array_keys) ) {
				$key = array_rand( $array );
			}
		}
		
		return $array[ $key ];
	}
}

/**
* Function to query update
**/
if( ! function_exists( 'onecom_query_check' ) ) {
	function onecom_query_check( $url, $page = null ) {
		//echo ( function_exists( 'add_query_arg' ) ) ? 'EXISTS' : 'NOT EXISTS';
		if( $page != null || $page != 1 || $page != "1" ) {
			$url = add_query_arg(
				array(
					'page' => $page,
				), $url
			);	
		}
		$url = add_query_arg(
			array(
				'wp' => ONECOM_WP_CORE_VERSION,
				'php' => ONECOM_PHP_VERSION,
				'item_count' => 1000
			), $url
		);
		return $url;
	}
}

/**
* Function which will display admin notices
**/
if( ! function_exists( 'onecom_generic_promo' ) ) {
	function onecom_generic_promo() {
		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $wp_version;

		//delete_site_transient( 'onecom_promo' );
		//delete_site_option( 'onecom_local_promo' );

		$is_transient = get_site_transient( 'onecom_promo' );

		if( ! $is_transient ) {
			$url = MIDDLEWARE_URL.'/promo';
			$args = array(
			    'timeout'     => 10,
			    'httpversion' => '1.0',
			    'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
			    'body'        => null,
			    'compress'    => false,
			    'decompress'  => true,
			    'sslverify'   => true,
			    'stream'      => false,
			); 	

			$x_promo_transient = 48; // default transient value

			$response = wp_remote_get( $url, $args );
			if( ! is_wp_error( $response ) ) {
				$local_promo = array();
				$local_promo = get_site_option( 'onecom_local_promo' );
				$x_promo_check = wp_remote_retrieve_header( $response, $header = 'X-ONECOM-Promo' );
				$x_promo_transient = wp_remote_retrieve_header( $response, $header = 'X-ONECOM-Transient' );

				$x_promo_include = wp_remote_retrieve_header( $response, $header = 'X-ONECOM-Promo-Include' );
				$x_promo_exclude = wp_remote_retrieve_header( $response, $header = 'X-ONECOM-Promo-Exclude' );
	
				$result = wp_remote_retrieve_body( $response );

				$json = json_decode( $result );
 				if (json_last_error() === 0) {
 					$result = '';
 				}

				if( isset( $local_promo[ 'xpromo' ] ) && $local_promo[ 'xpromo' ] == $x_promo_check ) {
					$local_promo[ 'html' ] = $result;
				} else {
					$local_promo[ 'show' ] = true;
					$local_promo[ 'html' ] = $result;
					$local_promo[ 'xpromo' ] = $x_promo_check;
					if( trim( $x_promo_include ) != '' ) {
						$local_promo[ 'include' ] = explode( '|', $x_promo_include);
					}
					if( trim( $x_promo_exclude ) != '' ) {
						$local_promo[ 'exclude' ] = explode( '|', $x_promo_exclude);
					}
				}
				update_site_option( 'onecom_local_promo', $local_promo );
			}

			set_site_transient( 'onecom_promo', true, $x_promo_transient * HOUR_IN_SECONDS );
		}

		$local_promo = get_site_option( 'onecom_local_promo' );

		$screen = get_current_screen();
		$restrict = false;

		// echo '<pre>';
		// print_r($screen);
		// echo "</pre>";

		if( isset( $local_promo[ 'include' ] ) && ! empty( $local_promo[ 'include' ] ) ) {
			$restrict = true;
			if(
				in_array( $screen->base, $local_promo[ 'include' ] )
				|| in_array( $screen->id, $local_promo[ 'include' ] )
				|| in_array( $screen->parent_base, $local_promo[ 'include' ] )
				|| in_array( $screen->parent_file, $local_promo[ 'include' ] )
			) {
				$restrict = false;
			} 
		}
		if( isset( $local_promo[ 'exclude' ] ) && ! empty( $local_promo[ 'exclude' ] ) && $restrict != true ) {
			$restrict = false;
			 if(
				in_array( $screen->base, $local_promo[ 'exclude' ] )
				|| in_array( $screen->id, $local_promo[ 'exclude' ] )
				|| in_array( $screen->parent_base, $local_promo[ 'exclude' ] )
				|| in_array( $screen->parent_file, $local_promo[ 'exclude' ] )
			) {
				$restrict = true;
			}
		}

		if( ( $restrict == false ) && ( isset( $local_promo[ 'show' ] ) && $local_promo[ 'show' ] == true ) && ( isset( $local_promo[ 'html' ] ) && $local_promo[ 'html' ] != '' ) ) {
			wp_enqueue_style( 'onecom-promo' );
			wp_enqueue_script( 'onecom-promo' );
			echo apply_filters( 'onecom_filter_promo_html', $local_promo[ 'html' ] ); 
		}
	}
}
if( ( is_network_admin() && is_multisite() ) ) {
	add_action( 'network_admin_notices', 'onecom_generic_promo' );
} else {
	add_action( 'admin_notices', 'onecom_generic_promo' );
}

if( ! function_exists( 'onecom_filter_promo_html_callback' ) ) {
	function onecom_filter_promo_html_callback( $html ) {
		$admin_url = ( is_network_admin() && is_multisite() ) ? network_admin_url() : admin_url();
		$html = str_replace( '{admin_url}', $admin_url, $html );
		return $html;
	}
}
add_filter( 'onecom_filter_promo_html', 'onecom_filter_promo_html_callback' );

/**
* Ajax handler for dismissable notice request
**/
if( ! function_exists( 'onecom_dismiss_notice_callback' ) ) {
	function onecom_dismiss_notice_callback() {
		$local_promo = get_site_option( 'onecom_local_promo' );
		$local_promo[ 'show' ] = false;
		$is_update = update_site_option( 'onecom_local_promo', $local_promo );
		if( $is_update ) {
			echo 'Notice dismissed';
		} else {
			echo 'Notice cannot dismissed';
		}
		wp_die();
	}
}
add_action( 'wp_ajax_onecom_dismiss_notice', 'onecom_dismiss_notice_callback' );

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

/**
* Function to auto install
*/
if( ! function_exists( 'onecom_auto_install' ) ) {
	function onecom_auto_install() {
		$plugins_to_install = array(
			'onecom-vcache' => 'onecom-vcache/vcaching.php'
		);
		if( ! empty( $plugins_to_install ) ) {
			$plugins_dir_path = dirname( ONECOM_WP_PATH );
			foreach ( $plugins_to_install as $pluginSlug => $pluginFile ) {
				$optionName = '__onecom_auto_install_'.$pluginSlug;
				//delete_option( $optionName );
				if( FALSE === is_dir( $plugins_dir_path . DIRECTORY_SEPARATOR . $pluginSlug ) ) {
					if( get_option( $optionName ) ) {
						continue;
					}
					$response = onecom_install_plugin_callback( $isAjax = false, $pluginSlug );
					if(false !== $response){
						update_option( $optionName, true );
						if( isset( $response[ 'type' ] ) && "success" === $response[ 'type' ] ) {
							activate_plugin( $pluginFile );
						}
                    }
				}
			}
		}
	}
	onecom_auto_install();
}

/**
* Function onecom_exclude_themes
* Remove ILO app theme from Theme listing  in plugin section
* @param array $themes, an array of onecom themes
* @param bool $exclude_themes, weather or not to exclude ilo theme?
* @return array
*/
function onecom_exclude_themes($themes, $exclude_themes){ 
	if ( ! $exclude_themes || ! is_array($themes)){		
		return $themes;
	}
	foreach($themes as $index => $theme_item){
		if (isset($theme_item->collection)){
			foreach ($theme_item->collection as $key => $theme){
				if (isset($theme->slug) && ($theme->slug === 'onecom-ilotheme') ){
					unset($theme_item->collection[$key]);
				}
			}
		}
	}
	return $themes;
}

/*
 * Admin notices
 **/
if(file_exists(ONECOM_WP_PATH.'modules'.DIRECTORY_SEPARATOR.'notice-discouraged-plugins.php')){
	include_once ONECOM_WP_PATH.'modules'.DIRECTORY_SEPARATOR.'notice-discouraged-plugins.php';
}