<?php
/************************************************************
* Plugin Name:	Events Manager - OpenStreetMaps
* Description:	OpenStreetMap replacement for Events Manager.
* Version:		2.9.7
* Author:  		Stonehenge Creations
* Author URI: 	https://www.stonehengecreations.nl/
* Plugin URI: 	https://www.stonehengecreations.nl/creations/stonehenge-em-osm/
* License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: 	stonehenge-em-osm
* Domain Path: 	/languages
************************************************************/
if( !defined('ABSPATH') ) exit;
include_once(ABSPATH.'wp-admin/includes/plugin.php');


Class Stonehenge_EM_OSM {

	#===============================================
	public function __construct() {
		$plugin = $this->get_plugin_data();
		$base 	= $plugin['base'];
		add_action( $base.'_loaded', array($this, 'init'));

		if( start_stonehenge( $plugin ) ) {
			do_action( $base.'_loaded' );
		}
	}


	#===============================================
	public static function get_plugin_data() {
		$wp 	= get_plugin_data( __FILE__ );
		$plugin = array(
			'name' 		=> $wp['Name'],
			'short' 	=> 'OpenStreetMaps',
			'icon' 		=> '&#x1F5FA;',
			'slug' 		=> 'stonehenge_em_osm',
			'version' 	=> $wp['Version'],
			'text' 		=> $wp['TextDomain'],
			'class' 	=> __CLASS__,
			'base' 		=> plugin_basename(__DIR__),
			'prio' 		=> 40,
		);
		$plugin['url'] 	= admin_url().'admin.php?page='.$plugin['slug'];
		return $plugin;
	}


	#===============================================
	public static function init() {
		add_action('init', array($this, 'load_translations'));
		include('includes/class-functions.php');
		include('includes/class-metabox.php');
		include('includes/class-maps.php');
	}


	#===============================================
	public static function dependency() {
		$check = is_plugin_active('events-manager/events-manager.php') ? true : false;
		return $check;
	}


	#===============================================
	public static function add_defaults() {
		$old_values = get_option('stonehenge-em-osm');
		if( $old_values && !empty($old_values) && is_array($old_values) ) {
			delete_option('stonehenge-em-osm');
			return $old_values;
		}
		return;
	}


	#===============================================
	public static function update_this_plugin() {
		global $EM_OSM;
		$plugin = self::get_plugin_data();

		// Do stuff.
		return;
	}


	#===============================================
	public static function sanitize_options( $input ) {
		$sections 	= self::define_options();
		$clean 		= Stonehenge_Plugin::sanitize_options( $input, $sections );
		return $clean;
	}


	#===============================================
	public static function register_assets() {
		$plugin 	= self::get_plugin_data();
		$base 		= $plugin['base'];
		$version 	= $plugin['version'];
		$url 		= plugins_url('/assets/', __FILE__);
		$path 		= plugin_dir_path(__FILE__).'assets/';

		// Admin Assets.
		if( file_exists( "{$path}{$base}.min.css" ) ) {
			wp_register_style( "{$base}-css", "{$url}{$base}.min.css", '', $version, 'all' );
		}
		if( file_exists( "{$path}{$base}.min.js" ) ) {
			wp_register_script( "{$base}-js", "{$url}{$base}.min.js", array('jquery'), $version, true );
		}
		if( method_exists(__CLASS__, 'localize_assets') ) {
			wp_localize_script( "{$base}-js", 'OSM', self::localize_assets() );
		}

		// Public Assets.
		if( file_exists( "public-{$path}{$base}.min.css" ) ) {
			wp_register_style( "public-{$base}-css", "{$url}public-{$base}.min.css", '', $version, 'all' );
		}
		if( file_exists( "public-{$path}{$base}.min.js" ) ) {
			wp_register_script( "public-{$base}-js", "{$url}public-{$base}.min.js", array('jquery'), $version, true );
		}
	}


	#===============================================
	public static function load_admin_assets() {
		$plugin = self::get_plugin_data();
		$base 	= $plugin['base'];
		wp_enqueue_style( "{$base}-css" );
		wp_enqueue_script( "{$base}-js" );
	}


	#===============================================
	public static function load_public_assets() {
		$plugin = self::get_plugin_data();
		$base 	= $plugin['base'];
		wp_enqueue_style( array("public-{$base}-css", 'stonehenge-css'));
		wp_enqueue_script( array("public-{$base}-js", 'parsley-validation', 'parsley-locale', 'parsley-locale-extra'));
	}


	#===============================================
	public static function localize_assets()  {
		$plugin 	= self::get_plugin_data();
		$text 		= $plugin['text'];
		$options 	= get_option( $plugin['slug'] );
		$localize	= array(
			'AutoComplete' 	=> admin_url('admin-ajax.php') .'?action=osm_search_location',
		);
		return $localize;
	}


	#===============================================
	public static function load_translations() {
		$plugin = self::get_plugin_data();
		$text 	= $plugin['text'];
		$locale = apply_filters( 'plugin_locale', function_exists( 'determine_locale' ) ? determine_locale() : get_locale(), $text );
		$mofile = dirname( __FILE__ ) . '/languages/'. $text . '-' . $locale . '.mo';
		$loaded = load_textdomain( $text, $mofile );
		if( !$loaded ) { $loaded = load_plugin_textdomain( $text, false, '/languages/' ); }
		if( !$loaded ) { $loaded = load_muplugin_textdomain( $text, '/languages/' ); }
	}


	#===============================================
	public static function define_options() {
		global $EM_OSM;
		$plugin		= self::get_plugin_data();
		$text  		= $plugin['text'];
		$sections[] = array(
			'id' 		=> 'osm',
			'label'		=> __('Settings'),
			'fields' 	=> array(
				array(
					'id' 		=> 'intro',
					'label' 	=> 'Intro',
					'type'		=> 'intro',
					'default' 	=> __('This plugin completely replaces the built-in Google Maps with open source OpenStreetMap.', $text) .'<br>'.
__('It uses OpenCage for geocoding.', $text),
				),
				array(
					'id' 		=> 'api',
					'label' 	=> __('OpenCage API Key', $text),
					'type' 		=> 'text',
					'required' 	=> true,
					'size'		=> 'regular-text',
					'after' 	=> sprintf( '<a href=%s target="_blank">%s</a>', 'https://opencagedata.com/pricing', '<button type="button" class="stonehenge-button">'. __('Get your free key', $text) .'</button>'),
					'helper' 	=> __('A free OpenCage API Key has a daily limit of 2500 calls per day.', $text) .'<br>'. __('This plugin only calls the OpenCage API when you click the "Search Address" button.', $text),
				),
				array(
					'id' 		=> 'zoom',
					'label' 	=> __('Default Zoom Level', $text),
					'type' 		=> 'number',
					'required'	=> true,
					'min' 		=> '0',
					'max' 		=> '19',
					'default'	=> '15',
					'helper' 	=> __('Enter a number between 1 and 19. (Default is 15)', $text),
				),
				array(
					'id' 		=> 'showlevel',
					'label' 	=> __('Show Zoom Level', $text),
					'type' 		=> 'toggle',
					'default' 	=> 'no',
					'required' 	=> true,
				),
				array(
					'id' 		=> 'zoomcontrols',
					'label' 	=> __('Show Zoom Controls', $text),
					'type' 		=> 'toggle',
					'default' 	=> 'no',
					'required' 	=> true,
				),
				array(
					'id' 		=> 'fullscreen',
					'label' 	=> __('Show Fullscreen', $text),
					'type' 		=> 'toggle',
					'default' 	=> 'no',
					'required' 	=> true,
				),
				array(
					'id' 		=> 'scale',
					'label' 	=> __('Show Scale', $text),
					'type' 		=> 'toggle',
					'default' 	=> 'no',
					'required' 	=> true,
				),
				array(
					'id' 		=> 'marker',
					'label'		=> __('Default Marker Color', $text),
					'required' 	=> true,
					'type' 		=> 'select',
					'choices' 	=> $EM_OSM->marker_color_options(),
					'helper' 	=> sprintf( __('The default marker color will be used as a fallback, if not set in the <a href=%s>Edit Location Page</a>.', $text), admin_url('edit.php?post_type=location') ),
				),
				array(
					'id' 		=> 'type',
					'label'		=> __('Default Map Style', $text),
					'type' 		=> 'select',
					'choices' 	=> $EM_OSM->map_type_options(),
					'helper' 	=> __('This map style will be shown on <code>[events_map]</code> and <code>[locations_map]</code>. It will also be used as a fallback for single location maps using <code>#_LOCATIONMAP</code>.', $text),
					'required' 	=> true,
				),
				array(
					'id' 		=> 'per_location',
					'label' 	=> __('Enable per Location', $text),
					'type' 		=> 'toggle',
					'default' 	=> 'no',
					'helper' 	=> sprintf( __('If set to "Yes", you can set the marker color and map style per location in the <a href=%s>Edit Location Page</a> or when creating a new location in the <a href=%s>Edit Event Page</a>.', $text), admin_url('edit.php?post_type=location'), admin_url('edit.php?post_type=event') ),
					'required' 	=> true,
				),
				array(
					'id' 		=> 'per_admin',
					'label' 	=> __('Admin only', $text),
					'type' 		=> 'toggle',
					'default'	=> 'yes',
					'helper' 	=> __('If set to "Yes", the marker color and map style options will <u>not</u> be shown in front-end submission forms.', $text),
				),
				array(
					'id' 		=> 'delete',
					'label' 	=> __('Delete Data', $text),
					'type' 		=> 'toggle',
					'required'	=> true,
					'helper' 	=> __('Automatically delete all data from your database when you uninstall this plugin?', $text),
					'default' 	=> 'yes',
				),
			),
		);
		return $sections;
	}


	#===============================================
	public static function show_osm_meta_box() {
		return Stonehenge_EM_OSM_Metabox::edit_location();
	}


	#===============================================
	public static function construct_admin_map() {
		global $EM_Event;
		return Stonehenge_EM_OSM_Metabox::edit_event( $EM_Event );
	}


	#===============================================
	public static function show_edit_event_box( $EM_Event ) {
		global $EM_Event;
		return Stonehenge_EM_OSM_Metabox::edit_event( $EM_Event );
	}

} // End Class.


#===============================================
register_deactivation_hook(__FILE__, array('Stonehenge_EM_OSM_Functions', 'remove_template_files'));


#===============================================
add_action('plugins_loaded', function() {
	require_once 'config/init.php';
	new Stonehenge_EM_OSM();
}, 26);


#===============================================
function osm_exclude_from_autoptimize( $exclude ) {
	return $exclude . ", wp-content/plugins/stonehenge-em-osm/";
}
add_filter('autoptimize_filter_js_exclude', 'osm_exclude_from_autoptimize', 15, 1);
add_filter('autoptimize_filter_css_exclude', 'osm_exclude_from_autoptimize', 15, 1);

