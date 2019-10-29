<div class="wrap">
	<div class="loading-overlay">
		<div class="loading-overlay-content">
			<div class="loader"></div>
		</div>
	</div><!-- loader -->
	<div class="onecom-notifier"></div>
	<!-- <h2 id="one-com-logo-wrapper" class=""><span id="one-com-icon"></span> one.com</h2> -->
	<h2 class="one-logo"> 
		<div class="textleft">
			<?php //_e( 'Welcome to One.com', 'onecom-wp' ); ?>
			<span>
				<?php _e( 'Discouraged plugins', 'onecom-wp' ); ?>
			</span>
		</div>
		<div class="textright">
			<img src="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo.png' ?>" alt="One.com" srcset="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo@2x.png 2x' ?>" /> 
		</div>
	</h2>
	<!-- <hr class="one-hr" /> -->
	<div class="wrap_inner inner one_wrap">
		<div class="nav-tab-wrapper">
			<a href="<?php echo network_admin_url( 'admin.php?page=onecom-wp-plugins' ); ?>" class="nav-tab"><?php _e( 'One.com plugins', 'onecom-wp' ); ?></a>
		    <a href="<?php echo network_admin_url( 'admin.php?page=onecom-wp-recommended-plugins' ); ?>" class="nav-tab"><?php _e( 'Recommended plugins', 'onecom-wp' ); ?></a>
		    <a href="<?php echo network_admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' ); ?>" class="nav-tab nav-tab-active"><?php _e( 'Discouraged plugins', 'onecom-wp' ); ?></a>
		    <!-- <a href="#premium" class="nav-tab"><?php //_e( 'Premium plugins', 'onecom-wp' ); ?></a> -->
		</div>
		<?php 
			$plugins = onecom_fetch_plugins( $recommended = false, $discouraged = true );
		
			if( ! is_wp_error( $plugins ) && ! empty( $plugins ) ) :
				foreach ( $plugins as $key => $plugin ) :
					if( ! is_dir( WP_PLUGIN_DIR . '/' . $plugin->slug ) ) {
						unset( $plugins[ $key ] );
						continue;
					}
					$plugin_infos = get_plugins( '/'.$plugin->slug );
					$plugin_activated = false;
					if( ! empty( $plugin_infos ) ) {
						foreach ( $plugin_infos as $file => $info ) :
							$is_inactivate = is_plugin_inactive( $plugin->slug.'/'.$file );
							if ( $is_inactivate == false ) {
								$plugin_activated = true;
								$plugins[ $key ]->file = $file;
							}
						endforeach;
					}
					if( $plugin_activated != true ) {
						unset( $plugins[ $key ] );
						continue;
					}
				endforeach;
			endif;
		?>
		<div id="discouraged" class="tab active-tab">
			<div class="tab-description">
				<?php if( empty( $plugins ) ) : ?>
					<?php _e( 'You are doing great! None of your installed plugins, are on our list of discouraged plugins.', 'onecom-wp' ); ?>
				<?php else : ?>
					<?php _e ( 'Your WordPress site should work the best possible way. We checked the plugins on your website, and listed those we don\'t recommended you to use.', 'onecom-wp' ); ?><br/><?php _e ( 'There are also some suggestions for alternative plugins to use instead.', 'onecom-wp' ); ?>
				<?php endif; ?>
				<div class="discouraged-list-button-wrapper">
					<a href="<?php echo onecom_generic_locale_link( $request = 'discouraged_guide', get_locale() ) ?>" target="_blank"><?php _e( 'View full list of discouraged plugins', 'onecom-wp' ); ?></a>
				</div>
			</div>
			<div class="plugin-browser widefat">
				<?php if( ! is_wp_error( $plugins ) ) : ?>
					<?php foreach ( $plugins as $key => $plugin ) : ?>
						<div class="one-plugin-card">
							<div class="plugin-card-top">
								<h3>
									<span class="discouraged-plugin-name">
										<?php echo esc_html( $plugin->name ); ?> 
									</span>
									<span class="discouraged-plugin-action">
										<form method="post" action="">
											<input type="hidden" name="plugin" value="<?php echo $plugin->slug.'/'.$plugin->file; ?>" />
											<input type="hidden" name="action" value="deactivate_plugin" />
											<input type="submit" name="one-deactivate-plugin" value="<?php _e( 'Deactivate', 'onecom-wp' ); ?>" class="button button-primary one-deactivate-plugin" />
										</form>
									</span>
								</h3>
								<div class="column-description">
									<?php echo esc_html( $plugin->reason ); ?>
								</div>
								<?php if( isset( $plugin->alternatives ) && ( ! empty( $plugin->alternatives ) ) ) : ?>
									<div class="plugin-alternatives">
										<?php $string = ( count( $plugin->alternatives ) == 1 ) ? __( 'Suggested alternative', 'onecom-wp' ) : __( 'Suggested alternatives', 'onecom-wp' ); ?>
										<h4><?php echo $string; ?></h4>
										<?php $count = count( $plugin->alternatives ); ?>
										<?php foreach ( $plugin->alternatives as $key => $alternative ) : ?>
											<?php 
												$details_url = ( is_network_admin() && is_multisite() ) ? network_admin_url( 'plugin-install.php' ) : admin_url( 'plugin-install.php' );
												$details_url = add_query_arg(
													array(
													    's' => urlencode( $alternative->name ),
													    'tab' => 'search',
													    'type' => 'term'
													), $details_url 
												);
											?>
											<a href="<?php echo esc_url( $details_url ); ?>" target="_blank"><?php echo esc_html( trim( $alternative->name ) ); ?></a><?php if( ( $count -1 ) != $key ) echo ', '; ?>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						</div><!-- -->
					<?php endforeach; ?>
				<?php else : ?>
						<strong><?php echo $plugins->get_error_message(); ?></strong>
				<?php endif; ?>
			</div> <!-- plugin-browser -->
		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->
<div id="one-confirmation" class="hide" data-yes_string="<?php _e( 'Yes, deactivate plugin', 'onecom-wp' ); ?>" data-no_string="<?php _e( 'Not right now', 'onecom-wp' ); ?>">
	<div class="plugin-card-top">
		<strong><?php _e( 'Are you sure that you want to deactivate this plugin?', 'onecom-wp' ); ?></strong>
		<div class="discouraged-list-button-wrapper" style="display: none">
			<?php _e( 'Deactivating a plugin can break functionality on your website.', 'onecom-wp' ); ?>
		</div>
	</div>
	<span class="dashicons dashicons-no-alt discouraged-modal-close"></span>
</div>