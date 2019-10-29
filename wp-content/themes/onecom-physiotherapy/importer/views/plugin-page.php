<?php
/**
 * The plugin page view - the "settings" page of the plugin.
 *
 * @package ocdi
 */

namespace OCDI;

?>

<div class="ocdi  wrap  about-wrap">

	<!--<h4 class="ocdi__title "><i class="el el-download-alt"></i> <?php /*echo __( 'Import demo data', 'oct-physiotherapy' );*/ ?></h4>-->

	<?php

	// Display warrning if PHP safe mode is enabled, since we wont be able to change the max_execution_time.
	/*if ( ini_get( 'safe_mode' ) ) {
		printf(
			esc_html__( '%sWarning: your server is using %sPHP safe mode%s. This means that you might experience server timeout errors.%s', 'oct-physiotherapy' ),
			'<div class="notice  notice-warning  is-dismissible"><p>',
			'<strong>',
			'</strong>',
			'</p></div>'
		);
	}*/

	// Start output buffer for displaying the plugin intro text.
	ob_start();
	?>

	<div class="ocdi__intro-text">
		<p>
			<?php echo __( 'The easiest way to setup your theme is by importing demo data. It includes demo posts, pages, images and theme settings. This will allow you to edit the demo content, instead of creating everything from scratch.', 'oct-physiotherapy' ); ?>
		</p>

		<hr>

		<p><?php echo __( 'When you import demo data, please be aware of the following:', 'oct-physiotherapy' ); ?></p>

		<ul>
			<li><?php echo __( 'No existing data will be deleted or modified, this includes posts, pages, categories, images, custom post types or any other data.', 'oct-physiotherapy' ); ?></li>
			<li><?php echo __( 'Posts, pages, images, widgets, menus and other theme settings will get imported.', 'oct-physiotherapy' ); ?></li>
			<li><?php echo __( 'It can take a couple of minutes for the import to finish, so please click the Import button only once.', 'oct-physiotherapy' ); ?></li>
		</ul>

		<hr>
        <p>
            <strong><?php echo __('Note:', 'option-tree'); ?></strong> <?php echo __('You should only import demo data when making a new website from scratch.', 'option-tree'); ?>
            <br/><?php echo __('Otherwise you might end up mixing the demo data with the existing content on your website.', 'option-tree'); ?>
        </p>
        <hr>
	</div>

	<?php
	$plugin_intro_text = ob_get_clean();

	// Display the plugin intro text (can be replaced with custom text through the filter below).
	echo wp_kses_post( apply_filters( 'pt-ocdi/plugin_intro_text', $plugin_intro_text ) );
	?>


	<?php if ( empty( $this->import_files ) ) : ?>

		<div class="notice  notice-info  is-dismissible">
			<p><?php esc_html_e( 'There are no predefined import files available in this theme. Please upload the import files manually!', 'oct-physiotherapy' ); ?></p>
		</div>

		<div class="ocdi__file-upload-container">
			<h2><?php esc_html_e( 'Manual demo files upload', 'oct-physiotherapy' ); ?></h2>

			<div class="ocdi__file-upload">
				<h3><label for="content-file-upload"><?php esc_html_e( 'Choose a XML file for content import:', 'oct-physiotherapy' ); ?></label></h3>
				<input id="ocdi__content-file-upload" type="file" name="content-file-upload">
			</div>

			<div class="ocdi__file-upload">
				<h3><label for="widget-file-upload"><?php esc_html_e( 'Choose a WIE or JSON file for widget import:', 'oct-physiotherapy' ); ?></label> <span><?php esc_html_e( '(*optional)', 'oct-physiotherapy' ); ?></span></h3>
				<input id="ocdi__widget-file-upload" type="file" name="widget-file-upload">
			</div>

			<div class="ocdi__file-upload">
				<h3><label for="customizer-file-upload"><?php esc_html_e( 'Choose a DAT file for customizer import:', 'oct-physiotherapy' ); ?></label> <span><?php esc_html_e( '(*optional)', 'oct-physiotherapy' ); ?></span></h3>
				<input id="ocdi__customizer-file-upload" type="file" name="customizer-file-upload">
			</div>

			<?php if ( class_exists( 'ReduxFramework' ) ) : ?>
			<div class="ocdi__file-upload">
				<h3><label for="redux-file-upload"><?php esc_html_e( 'Choose a JSON file for Redux import:', 'oct-physiotherapy' ); ?></label> <span><?php esc_html_e( '(*optional)', 'oct-physiotherapy' ); ?></span></h3>
				<input id="ocdi__redux-file-upload" type="file" name="redux-file-upload">
				<div>
					<label for="redux-option-name" class="ocdi__redux-option-name-label"><?php esc_html_e( 'Enter the Redux option name:', 'oct-physiotherapy' ); ?></label>
					<input id="ocdi__redux-option-name" type="text" name="redux-option-name">
				</div>
			</div>
			<?php endif; ?>
		</div>

		<p class="ocdi__button-container">
			<button class="ocdi__button  button  button-primary  js-ocdi-import-data"><?php echo __( 'Import Demo Data', 'oct-physiotherapy' ); ?></button>
		</p>

	<?php elseif ( 1 === count( $this->import_files ) ) : ?>

		<div class="ocdi__demo-import-notice  js-ocdi-demo-import-notice"><?php
			if ( is_array( $this->import_files ) && ! empty( $this->import_files[0]['import_notice'] ) ) {
				echo wp_kses_post( $this->import_files[0]['import_notice'] );
			}
		?></div>

		<p class="ocdi__button-container">
			<button class="ocdi__button  button  button-primary  js-ocdi-import-data"><?php echo __( 'Import Demo Data', 'oct-physiotherapy' ); ?></button>
		</p>

	<?php else : ?>

		<!-- OCDI grid layout -->
		<div class="ocdi__gl  js-ocdi-gl">
		<?php
			// Prepare navigation data.
			$categories = Helpers::get_all_demo_import_categories( $this->import_files );
		?>
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="ocdi__gl-header  js-ocdi-gl-header">
					<nav class="ocdi__gl-navigation">
						<ul>
							<li class="active"><a href="#all" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php esc_html_e( 'All', 'oct-physiotherapy' ); ?></a></li>
							<?php foreach ( $categories as $key => $name ) : ?>
								<li><a href="#<?php echo esc_attr( $key ); ?>" class="ocdi__gl-navigation-link  js-ocdi-nav-link"><?php echo esc_html( $name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</nav>
					<div clas="ocdi__gl-search">
						<input type="search" class="ocdi__gl-search-input  js-ocdi-gl-search" name="ocdi-gl-search" value="" placeholder="<?php esc_html_e( 'Search demos...', 'oct-physiotherapy' ); ?>">
					</div>
				</div>
			<?php endif; ?>
			<div class="ocdi__gl-item-container  wp-clearfix  js-ocdi-gl-item-container">
				<?php foreach ( $this->import_files as $index => $import_file ) : ?>
					<?php
						// Prepare import item display data.
						$img_src = isset( $import_file['import_preview_image_url'] ) ? $import_file['import_preview_image_url'] : '';
						// Default to the theme screenshot, if a custom preview image is not defined.
						if ( empty( $img_src ) ) {
							$theme = wp_get_theme();
							$img_src = $theme->get_screenshot();
						}

					?>
					<div class="ocdi__gl-item js-ocdi-gl-item" data-categories="<?php echo esc_attr( Helpers::get_demo_import_item_categories( $import_file ) ); ?>" data-name="<?php echo esc_attr( strtolower( $import_file['import_file_name'] ) ); ?>">
						<div class="ocdi__gl-item-image-container">
							<?php if ( ! empty( $img_src ) ) : ?>
								<img class="ocdi__gl-item-image" src="<?php echo esc_url( $img_src ) ?>">
							<?php else : ?>
								<div class="ocdi__gl-item-image  ocdi__gl-item-image--no-image"><?php esc_html_e( 'No preview image.', 'oct-physiotherapy' ); ?></div>
							<?php endif; ?>
						</div>
						<div class="ocdi__gl-item-footer">
							<h4 class="ocdi__gl-item-title"><?php echo esc_html( $import_file['import_file_name'] ); ?></h4>
							<button class="ocdi__gl-item-button  button  button-primary  js-ocdi-gl-import-data" value="<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Import', 'oct-physiotherapy' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="js-ocdi-modal-content"></div>

	<?php endif; ?>

	<p class="ocdi__ajax-loader  js-ocdi-ajax-loader">
		<span class="spinner"></span> <?php echo __( 'Importing, please wait!', 'oct-physiotherapy' ); ?>
	</p>

	<div class="ocdi__response  js-ocdi-ajax-response"></div>
</div>
<?php
/* Provision for Auto-trigger Import after site installation */
if(get_option('fresh_site') && isset($_REQUEST['auto-import']) && $_REQUEST['auto-import'] == 1){ ?>
    <script>
        jQuery(document).ready(function(){
            if(jQuery(document).find('a[href="#section_importer_section"]').length){
                jQuery('a[href="#section_importer_section"]').trigger('click');
            }
            jQuery( ".js-ocdi-import-data" ).trigger("click").attr('disabled', 'disabled');
            console.log('Auto import started.');
        });
    </script>
<?php }
elseif(!get_option('fresh_site')){ ?>
    <script>console.log('Did not start auto import because WP is not new.');</script>
<?php } ?>
