<div class="wrap">
	<div class="loading-overlay fullscreen-loader">
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
				<?php _e( 'Presenting some of our favorite WordPress plugins, curated for quality.', 'onecom-wp' ); ?>
			</span>
		</div>
		<div class="textright">
			<img src="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo.png' ?>" alt="One.com" srcset="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo@2x.png 2x' ?>" /> 
		</div>
	</h2>
	<!-- <hr class="one-hr" /> -->
	<div class="wrap_inner inner one_wrap">
		<div class="nav-tab-wrapper">
			<a href="<?php echo admin_url( 'admin.php?page=onecom-wp-plugins' ); ?>" class="nav-tab"><?php _e( 'One.com plugins', 'onecom-wp' ); ?></a>
		    <a href="<?php echo admin_url( 'admin.php?page=onecom-wp-recommended-plugins' ); ?>" class="nav-tab nav-tab-active"><?php _e( 'Recommended plugins', 'onecom-wp' ); ?></a>
		    <a href="<?php echo admin_url( 'admin.php?page=onecom-wp-discouraged-plugins' ); ?>" class="nav-tab"><?php _e( 'Discouraged plugins', 'onecom-wp' ); ?></a>
		</div>
		<div id="recommended" class="tab active-tab">

			<!-- <div class="theme-filters">
				<div class="filter-search">
					<input type="text" name="s" placeholder="<?php //_e( 'Enter keyword', 'onecom-wp' ); ?>" />
					<button name="search-theme-button"><span class="dashicons dashicons-search"></span></button>
				</div> --> <!-- filter-search -->
			<!-- </div> --> <!-- theme-filters -->

			<div class="plugin-browser widefat">
				<?php $recommended_plugins = onecom_fetch_plugins( $recommended = true ); ?>
				<?php if( ! is_wp_error( $recommended_plugins ) ) : ?>
					<?php foreach( $recommended_plugins as $recommended_plugin ) : ?>
						<?php
							$plugin = $recommended_plugin;

							if( $plugin->slug == '' ) {
								continue;
							}
							$plugin_installed = $plugin_activated = false;
							if ( is_dir( WP_PLUGIN_DIR . '/' . $plugin->slug ) ) {
								$plugin_installed = true;

								$plugin_infos = get_plugins( '/'.$plugin->slug );
								if( ! empty( $plugin_infos ) ) {
									foreach ($plugin_infos as $file => $info) :
										$is_inactivate = is_plugin_inactive( $plugin->slug.'/'.$file );
										if ( $is_inactivate == false ) {
											$plugin_activated = true;
										} else {
											$activateUrl = add_query_arg( array(
												'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin->slug.'/'.$file ),
												'action'   => 'activate',
												'plugin'   => $plugin->slug.'/'.$file,
											), admin_url( 'plugins.php' ) );
										}
									endforeach;
								}
							}
						?>
						<div class="one-plugin-card <?php echo ( count( $recommended_plugins )  == 1 ) ? 'single-plugin' : ''; ?> <?php echo ( $plugin_installed ) ? 'installed' : ''; ?>">
							<div class="plugin-card-top">
								<div class="name column-name">
									<h3>
										<?php echo $plugin->name; ?>
										<?php 
											$icon_url = $plugin->thumbnail;
											if( $icon_url != '' ) :
												?>
													<span class="plugin-icon-wrapper icon-available">
														<span class="plugin-category"><?php echo $recommended_plugin->category; ?></span>
														<img src="<?php echo $icon_url; ?>" alt="<?php echo $plugin->name; ?>" />
													</span>
												<?php
											else :
												$acronym = onecom_string_acronym( $plugin->name );
												$style = 'background-color:'.onecom_random_color( $key );
												?>
													<span class="plugin-icon-wrapper" style="<?php echo $style; ?>">
														<span class="onecom-acronym"><?php echo $acronym; ?></span>
													</span>
												<?php
											endif;
										?>
									</h3>
								</div>
								<div class="action-links">
									<ul class="plugin-action-buttons">
										<li>
											<?php if( $plugin_installed && $plugin_activated ) : ?>
												<a class="installed-plugin button" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" disabled="true" ><?php _e( 'Active', 'onecom-wp' ); ?></a>
											<?php elseif ( $plugin_installed && ( ! $plugin_activated ) ) : ?>
												<a class="activate-plugin button button-primary" href="<?php echo $activateUrl ?>"><?php _e( 'Activate', 'onecom-wp' ); ?></a>
											<?php else : ?>
												<a class="install-now button" href="javascript:void(0)" data-slug="<?php echo $plugin->slug; ?>" data-name="<?php echo $plugin->name ?>" aria-label="Install <?php echo $plugin->name ?> now" data-download_url="<?php echo $plugin->download_link; ?>" data-action="onecom_install_plugin" data-plugin_type="recommended"><?php _e( 'Install now', 'onecom-wp' ); ?></a>
											<?php endif; ?>
										</li>
										<li>
											<?php
												$info_url = ( is_multisite() ) ? network_admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' ) : admin_url( 'plugin-install.php?tab=plugin-information&plugin='.$plugin->slug.'&TB_iframe=true&width=772&height=521' );
												//$info_url = $plugin->url.'?TB_iframe=true&width=772&height=521';
											?>
											<a href="<?php echo $info_url; ?>" class="thickbox open-plugin-details-modal" title="<?php _e( 'More details', 'onecom-wp' ); ?>"><?php _e( 'More details', 'onecom-wp' ); ?></a>
										</li> 
									</ul>
								</div>
								<div class="desc column-description">
									<p><?php echo $plugin->short_description; ?></p>
									<p class="authors">
										<cite><?php _e( 'By', 'onecom-wp' ) ?> <?php echo $plugin->author; ?></cite>
									</p>
								</div>
							</div>
						</div> <!-- one-plugin-card -->
					<?php endforeach; ?>
				<?php else : ?>
						<p><?php echo $recommended_plugins->get_error_message(); ?></p>
				<?php endif; ?>

			</div> <!-- plugin-browser -->
		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->
<?php add_thickbox(); ?> 

<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>