<?php //include_once ONECOM_WP_PATH.'inc/functions.php' ?>
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
				<?php _e( 'Exclusive themes specially crafted for One.com customers.', 'onecom-wp' ); ?>
			</span>
		</div>
		<div class="textright">
			<img src="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo.png' ?>" alt="One.com" srcset="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo@2x.png 2x' ?>" /> 
		</div>
	</h2>
	<!-- <hr class="one-hr" /> -->
	<div class="wrap_inner inner one_wrap">
		<!-- <div class="nav-tab-wrapper">
		    <a href="#free" class="nav-tab nav-tab-active"><?php //_e( 'Free Themes', 'onecom-wp' ); ?></a>
		    <a href="#premium" class="nav-tab"><?php //_e( 'Premium Themes', 'onecom-wp' ); ?></a>
		</div> -->
		<div id="free" class="tab active-tab">

			<div class="theme-filters">
				<!-- <div class="filter-search">
					<input type="text" name="s" placeholder="<?php //_e( 'Enter keyword', 'onecom-wp' ); ?>" />
					<button name="search-theme-button"><span class="dashicons dashicons-search"></span></button>
				</div> --> <!-- filter-search -->
				<div class="oc_select_wrapper">
					 	<span><?php echo __('All', 'onecom-wp')?></span>
						<select id="oc_theme_filter_select" class="oc_theme_filter_select">
							<option value="all" class="oc-active-filter"><?php _e( 'All', 'onecom-wp' ); ?></option>
							<option value="blogging" class="oc-active-filter"><?php _e( 'Blog', 'onecom-wp' ); ?></option>
							<option value="business-services"><?php _e( 'Business & Services', 'onecom-wp' ); ?></option>
							<option value="events"><?php _e( 'Events', 'onecom-wp' ); ?></option>
							<option value="family-recreation"><?php _e( 'Family & Recreation', 'onecom-wp' ); ?></option>
							<option value="food-hospitality"><?php _e( 'Food & Hospitality', 'onecom-wp' ); ?></option>
							<option value="music-art"><?php _e( 'Music & Art', 'onecom-wp' ); ?></option>						
							<option value="online-shop"><?php _e( 'Online Shop', 'onecom-wp' ); ?></option>
							<option value="portfolio-cv"><?php _e( 'Portfolio & CV', 'onecom-wp' ); ?></option>
						</select>
				 	</div>
				<ul id="oc_theme_filter" class="oc_theme_filter">
					<li data-filter-key="all" class="oc-active-filter"><?php _e( 'All', 'onecom-wp' ); ?></li>
					<li data-filter-key="blogging"><?php _e( 'Blog', 'onecom-wp' ); ?></li>
					<li data-filter-key="business-services"><?php _e( 'Business & Services', 'onecom-wp' ); ?></li>
					<li data-filter-key="events"><?php _e( 'Events', 'onecom-wp' ); ?></li>
					<li data-filter-key="family-recreation"><?php _e( 'Family & Recreation', 'onecom-wp' ); ?></li>
					<li data-filter-key="food-hospitality"><?php _e( 'Food & Hospitality', 'onecom-wp' ); ?></li>
					<li data-filter-key="music-art"><?php _e( 'Music & Art', 'onecom-wp' ); ?></li>						
					<li data-filter-key="online-shop"><?php _e( 'Online Shop', 'onecom-wp' ); ?></li>
					<li data-filter-key="portfolio-cv"><?php _e( 'Portfolio & CV', 'onecom-wp' ); ?></li>
				</ul>
			</div> <!-- theme-filters -->

			<?php 
				$requsted_paged = ( isset( $_GET[ 'paged' ] ) ) ? $_GET[ 'paged' ] : 1;
				global $theme_data; 
				$theme_data = onecom_fetch_themes( $page = $requsted_paged ); 
				$config = onecom_themes_listing_config();
			?>

			<div class="theme-browser" data-total_themes="<?php echo $config[ 'total' ]; ?>" data-item_count="<?php echo $config[ 'item_count' ]; ?>">
				<?php 
					load_template( dirname( __FILE__ ) . '/theme-listing-loop.php' ); 
				?>
			</div> <!-- theme-browser -->

			<div class="loading-overlay theme-loader">
				<div class="loading-overlay-content">
					<div class="loader"></div>
				</div>
			</div><!-- loader -->
			
			<?php onecom_themes_listing_pagination( $config, $requsted_paged ); ?>
			
		</div> <!-- tab -->

	</div> <!-- wrap_inner -->
</div> <!-- wrap -->

<?php add_thickbox(); ?> 

<div id="thickbox_preview" style="display:none">
   <div id="preview_box">
       <div class="one-theme-listing-bar">
           <span class="dashicons dashicons-wordpress-alt"></span>
       </div>
       <div class="header_btn_bar">
           <div class="left-header">
               	<a href="javascript:void(0)" class="close_btn"><?php _e( 'Back to themes', 'onecom-wp' ); ?></a>
               	<div class="btn btn_arrow previous" data-demo-id=""><span class="dashicons dashicons-arrow-left-alt2"></span></div>
               	<span data-theme-count="" data-active-demo-id="" class="theme-info hide"></span>
               	<div class="btn btn_arrow next" data-demo-id=""><span class="dashicons dashicons-arrow-right-alt2"></span></div>
               	<a href="javascript:void(0)" class="btn button_1 preview-install-button" data-active-demo-id=""><?php _e( 'Install', 'onecom-wp' ); ?></a>
           </div>
           <div class="right-header">
               <div class="btn button_2 current" id="desktop"> <span class="dashicons dashicons-desktop"></span> <?php _e( 'Desktop', 'onecom-wp' ); ?></div>
               <div class="btn button_2" id="mobile"> <span class="dashicons dashicons-smartphone"></span> <?php _e( 'Mobile', 'onecom-wp' ); ?></div>
         </div>
       </div>
       <!-- <hr class="divider" /> -->
       <span class="divider_shadow" > </span>

       <div class="preview-container">
             <div class="desktop-content text-center preview">
                 <iframe src='#'></iframe>
             </div>
       </div>
   </div>
</div>   

<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>