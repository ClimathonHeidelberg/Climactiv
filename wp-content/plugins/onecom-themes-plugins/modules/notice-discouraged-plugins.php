<?php
// No Direct Access
defined( "WPINC" ) or die(); // No Direct Access

/*
 * Discouraged Plugins notice
 * */
if( ! function_exists( 'onecom_discouraged_plugins_notice' ) ){
	function onecom_discouraged_plugins_notice(){

		if ( ! current_user_can( 'deactivate_plugin' ) )
			return;

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

		$dp_plugins = onecom_fetch_plugins( $recommended = false, $discouraged = true );



		// Get discouraged plugins list from transients

		// Convert transient value to array
		$dp_plugins = json_decode( json_encode($dp_plugins), true);

		// Prepare discouraged plugins array
		$disc_plugins = [];
		foreach ( $dp_plugins as $plugin ) {
			if( is_dir( WP_PLUGIN_DIR . '/' . $plugin['slug'] ) ) {
				$plugin_info = get_plugins('/'.$plugin['slug']);
				$disc_plugins[] = $plugin['slug'].'/'.key($plugin_info);
			}
		}

		// Check if discouraged plugin(s) exist(s)
		$active_disc_plugins = array_intersect( $disc_plugins, $active_plugins);

		// Exit if no discouraged plugin is active
		if(empty($active_disc_plugins))
			return;


		// If discouraged plugins exist, check for critical plugins
		// Check and get the critic degree
		$get_degrees = @array_column($dp_plugins, 'degree');

		// Get critical plugins keys
		$critical_keys = @array_keys($get_degrees, 'high');

		// Prepare critical active plugins array
		$crtical_active = [];
		if(!empty($critical_keys)){
			foreach ($critical_keys as $value){
				if( is_dir( WP_PLUGIN_DIR . '/' . $dp_plugins[$value]['slug'] ) ) {
					$plugin_info = get_plugins('/'.$dp_plugins[$value]['slug']);
					$crtcl_path = $dp_plugins[$value]['slug'].'/'.key($plugin_info);
					if(is_plugin_active($crtcl_path)){
						$crtical_active[] = $crtcl_path;
						$plugin_file = $crtcl_path;
						$plugin_name = reset( $plugin_info)['Name'];
					}
				}
			}
		}

		// Check if any critical plugin active
		if(!empty($crtical_active) && is_array($crtical_active)){

			// Display Critical plugin warning

			$text =  sprintf(__('The plugin <strong>%s</strong>, that you are using, creates a lot of large temporary files causing high load on our servers and possible time-outs on your site.<br/>We strongly advise you to deactivate it. If you keep on using this plugin we may be forced to suspend your site. ', 'onecom-wp'), $plugin_name);


			$link = '<a class="button" href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . urlencode( $plugin_file ) . '&amp;', 'deactivate-plugin_' . $plugin_file ) . '">' . __( 'Deactivate' ) . '</a>';

			echo "<div class='notice notice-error is-dismissible'><p> {$text} &nbsp; {$link} </p></div>";
		}
		else{

			// Display Discouraged plugin warning

			$text =  __('You are using one or more plugins from the list of plugins we discourage. We recommend that you deactivate them, to ensure the best performance of your website. ', 'onecom-wp');

			$link = admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' );
			$title = __('View discouraged plugins', 'onecom-wp');

			echo "<div class='notice notice-error is-dismissible'><p> {$text} &nbsp;<a href='{$link}'>{$title}</a></p></div>";
		}

	}
}

add_action( 'admin_notices', 'onecom_discouraged_plugins_notice', 2 );
?>