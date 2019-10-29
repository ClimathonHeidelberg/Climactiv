<?php
if( !defined('ABSPATH') ) exit;
if( !defined('STONEHENGE') ) define('STONEHENGE', 'https://www.stonehengecreations.nl/');


if( !class_exists('Stonehenge_License') ) {
	Class Stonehenge_License {

		public static $plugin;
		public static $instance;


		#===============================================
		public static function init( $plugin ) {
			$plugin['text'] 		= isset($plugin['text']) ? $plugin['text'] : str_replace('_', '-', $plugin['slug']);
			$plugin['slug'] 		= isset($plugin['slug']) ? $plugin['slug'] : str_replace('_', '-', $plugin['text']);
			$plugin['icon'] 		= isset($plugin['icon']) ? trim($plugin['icon']).'&nbsp;' : '';
			$plugin['version_slug'] = $plugin['base'].'_version';
			$plugin['license_slug']	= $plugin['base'].'_license';
			$plugin['license'] 		= get_site_option( $plugin['license_slug'] );
			self::$plugin = $plugin;

			if( @self::$instance->plugin['base'] != $plugin['base'] ) {
				self::$instance = new Stonehenge_Plugin($plugin);
			}
			return self::$instance;
		}


		#===============================================
		public static function is_licensed( $plugin ) {
			$license = get_site_option( $plugin['base'].'_license' );

			if( !isset($license) || empty($license) ) {
				$response 	= json_decode( file_get_contents( STONEHENGE . '?api=edd&base='. $plugin['base'] ) );
				$check 		= @$response->licensed ? $response->ID : 'no';

				if( !add_site_option( $plugin['base'].'_license', $check, '', 'no') ) {
					update_site_option( $plugin['base'].'_license', $check, 'no');
				}
				$is_licensed = $response->licensed;
			}
			$is_licensed = is_array($license) ? true : ($license != 'no' ? true : false);
			return $is_licensed;
		}


		#===============================================
		public static function is_valid( $plugin ) {
			$license 		= get_site_option( $plugin['base'].'_license' );
			$is_licensed 	= self::is_licensed( $plugin );
			$is_valid 		= !$is_licensed ? true : ( is_array($license) && isset($license['license']) && $license['license'] === 'valid' ? true : false );

			if( $is_valid ) {
				do_action('stonehenge_loaded');
			}

			return $is_valid;
		}


		#===============================================
		public static function license_server( $license_key, $method, $plugin ) {
			$license 	= $plugin['license'];
			$plugin_id 	= is_array($license) ? $license['item_id'] : $license;
			$parameters = array(
				'edd_action'	=> $method,
				'item_id' 		=> $plugin_id,
				'license' 		=> trim($license_key),
				'url'			=> $_SERVER['SERVER_NAME'],
			);
			$response 		= wp_remote_post( STONEHENGE, array( 'timeout' => 25, 'sslverify' => false, 'body' => $parameters ) );
		    $license_data 	= json_decode( wp_remote_retrieve_body( $response) , true );
			return $license_data;
		}


		#===============================================
		public static function activate( $license_key, $plugin ) {
			$license_data 	= self::license_server( $license_key, 'activate_license', $plugin );
			$check_data 	= self::check( $license_key, $plugin );
			return;
		}


		#===============================================
		public static function deactivate( $license_key, $plugin ) {
			$license_data 	= self::license_server( $license_key, 'deactivate_license', $plugin );
			delete_site_option( $plugin['license_slug'] );
			return;
		}


		#===============================================
		public static function check( $license_key, $plugin ) {
			$license_data 	= self::license_server( $license_key, 'check_license', $plugin );
			$save_data 		= self::process_license_data( $license_data, $license_key, $plugin );
			return $license_data;
		}


		#===============================================
		public static function validate( $plugin ) {
			// Fallback, because this function is called from the plugin base file.
			$plugin['license_slug'] = $plugin['base'].'_license';
			$plugin['license']		= get_site_option( $plugin['license_slug'] );

			// Double check.
			if( self::is_licensed($plugin) && is_array($plugin['license']) ) {
				$license_key	= $plugin['license']['license_key'];
				$license_data 	= self::license_server( $license_key, 'check_license', $plugin );
				$save_data 		= self::process_license_data( $license_data, $license_key, $plugin );
			}
			return;
		}


		#===============================================
		private static function process_license_data( $license_data, $license_key, $plugin ) {
			if( !$license_data['success'] ) {
				return;
			}
			$new_data 	= array();
			$data 		= array( 'success' => '', 'license' => '', 'expires' => '', 'customer_name' => '', 'customer_email' => '', 'license_limit' => '', 'site_count' => '', 'activations_left' => '', 'item_id' => '', 'error' => '');

			foreach($license_data as $key => $value ) {
				if( array_key_exists($key, $data) ) {
					$new_data[$key] = $value;
				}
			}
			$new_data['license_key'] = $license_key;

			if( !add_site_option( $plugin['license_slug'] , $new_data, '', 'no' ) ) {
				update_site_option( $plugin['license_slug'] , $new_data, 'no' );
			}
			return $new_data;
		}


		#===============================================
		public static function mask($input) {
			$masked =  str_repeat("*", strlen($input)-6) . substr($input, -6);
			return $masked;
		}


		#===============================================
		public static function renewal_url( $plugin ) {
			$url = STONEHENGE .'checkout/?edd_license_key='. $plugin['license']['license_key'] . '&download_id=' . $plugin['license']['item_id'];
			return $url;
		}


		#===============================================
		public static function show_license( $plugin ) {
			$text 			= $plugin['text'];
			$license 		= get_site_option( $plugin['license_slug'] );
			$show 			= self::is_valid($plugin) ? 'closed' : '';
			$is_allowed 	= current_user_can('install_plugins') ? true : false;
			$not_allowed	= '<span class="stonehenge-error">Sorry, you do not have the correct permissions to manage this license key.</span>';

			if( isset($_POST['activate_license']) ) {
				$license_key = sanitize_text_field( $_POST['license_key'] );
				self::activate( $license_key, $plugin );
				echo '<meta http-equiv="refresh" content="0">';
			}

			if( isset($_POST['check_license']) ) {
				self::check( esc_attr($license['license_key']), $plugin );
				echo '<meta http-equiv="refresh" content="0">';
			}

			if( isset($_POST['deactivate_license']) ) {
				self::deactivate( esc_attr($license['license_key']), $plugin );
				echo '<meta http-equiv="refresh" content="0">';
			}
			?>
			<div class="stonehenge-section" id="license-key">
				<div class"stonehenge-section-header">
					<h3 class="handle"><?php echo $plugin['short'] .' - '. esc_html__('License Key', $text); ?></h3>
				</div>
				<div class="stonehenge-section-content">
					<br>
					<table class="form-table stonehenge-table">
						<form action="" method="post">
							<?php
							// New install.
							if( !is_array($license) ) {
								echo '<tr>';
								echo 	'<th>License Status</th>';
								echo 	'<td>Please enter your license key.</td>';
								echo '</tr><tr>';
								echo 	'<th>License Key</th>';
								echo 	'<td><input type="text" name="license_key" value="" class="regular-text"></td>';
								echo '</tr><tr><th></th><td>';
								echo $is_allowed ? '<input type="submit" name="activate_license" value="Activate License Key" class="stonehenge-button">' : $not_allowed;
								echo '</td></tr>';
							}
							else {
								$status 	= $license['license'];
								$state 		= str_replace('_', ' ' , ucfirst($license['license']));
								$class 		= $status != 'valid' ? 'stonehenge-error' : 'stonehenge-success';
								$type 		= $license['license_limit'] === 0 ? 'Unlimited ' : $license['license_limit'] .'-';

								$format 	= get_option('date_format');
								$current 	= current_time('mysql');		// Y-m-d H:i:s
								$expiry 	= date_i18n( $format, strtotime( $license['expires'], current_time('timestamp') ) );

								$expires 	= isset($license['expires']) && !empty($license['expires']) ? $license['expires'] : null;
								$diff 		= abs( strtotime($expires) - strtotime($current) );
								$days 		= intval($diff / 86400);
								$days_left 	= intval( abs( strtotime($expires) - strtotime(current_time('mysql')) ) / 86400 );
								$renew_url 	= esc_url_raw( self::renewal_url($plugin) );
								$profile  	= esc_url_raw( STONEHENGE . 'account' );

								echo '<tr>';
								echo 	'<th>License Status</th>';
								echo 	'<td><span class="'. esc_attr($class, ENT_QUOTES) .'">'. $state .'</span></td>';
								echo '</tr><tr>';
								echo 	'<th>License Key</th>';
								echo 	'<td>';

								switch( $status ) {
									case 'valid':
										echo '<strong>'. self::mask($license['license_key']) .'</strong></td></tr>';
										if( $is_allowed ) {
											echo '<tr><th>Licensed to:</strong</th>';
											echo '<td>'. esc_html($license['customer_name']) .'<br>'. esc_html($license['customer_email']) .'</td></tr>';
											echo '<tr><th>License Type:</th><td>'. esc_html($type) .'Install License ';
											echo '('. esc_html($license['site_count']) .' used)</td></tr>';
											echo '<tr><th>Expires on:</th>';
											echo '<td>'. esc_html($expiry) .' ('. esc_html($days_left) .' days left)</td>';
											echo '</tr><tr><th></th>';
											echo 	'<td><input type="submit" name="check_license" value="&#8634; Sync Data" class="stonehenge-button">&nbsp;&nbsp;<a href="'. $profile .'" target="_blank" class="button-secondary">Manage Account</a>&nbsp;&nbsp;<input type="submit" name="deactivate_license" value="Deactivate License" class="button-secondary">';
											echo 	'</td>';
										}
										else {
											echo '<td colspan=2">' . $not_allowed . '</td>';
										}
										echo '</tr>';
									break;

									case 'expired':
										echo '<span class="red">Expired on '. esc_html($expiry) .'.</span></td></tr>';
										echo '<tr><th></th><td>';
										echo $is_allowed ? '<a href="'. $url .'" target="_blank"><button type="button" class="stonehenge-button">Renew License</button></a>' : $not_allowed;
										echo '</td></tr>';
									break;

									case 'disabled':
										echo sprintf('You need to buy a <a href=%s target="blank">new license key</a> or delete this plugin.', STONEHENGE .'creations/') .'</td></tr>';
										echo '<tr><th></th><td>';
										echo $is_allowed ? '<input type="submit" name="deactivate_license" value="Deactivate License" class="stonehenge-button">' : $not_allowed;
										echo '</td></tr>';
									break;

									case 'invalid':
									default:
										echo '<input type="text" name="license_key" id="license_key" value="'. esc_attr(@$license['license_key']) .'" class="regular-text"></td></tr>';
										echo '<tr><th></th><td>';
										echo $is_allowed ? '<input type="submit" name="activate_license" value="Activate" class="stonehenge-button">' : $not_allowed;
										echo '</td></tr>';
									break;
								} // End switch.
							}
							?>
						</form>
					</table>
				</div>
			</div>
			<br style="clear:both;">
			<?php
		}
	}
}


#===============================================
if( !function_exists( 'start_stonehenge' ) ) {
	function start_stonehenge($plugin) {
		$start 	= Stonehenge_License::init($plugin);
		$init 	= $start->plugin;
		$check 	= Stonehenge_License::is_valid($init);
		return $check;
	}
}

