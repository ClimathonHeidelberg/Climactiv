<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if( class_exists('Stonehenge_EM_OSM') ) {
	global $EM_OSM;
	$EM_OSM = new Stonehenge_EM_OSM_Functions();
}


Class Stonehenge_EM_OSM_Functions {

	#===============================================
	public function __construct() {
		add_action('wp_loaded', array($this, 'restore'), 15, 1);
		add_action('wp_loaded', array($this, 'disable_google_api'), 50);
		add_action('admin_init', array($this, 'alter_locations_table'));
		add_action('wp_loaded', array($this, 'place_template_for_event'), 11, 1);
		add_action('wp_loaded', array($this, 'place_template_for_location'), 12, 1);
		add_action('em_location_save_meta_pre', array($this, 'save_maps_per_location'));
		add_action('admin_notices', array($this, 'create_admin_notices') );

		// Ajax Search.
		add_action('wp_ajax_osm_search_location', array( __CLASS__, 'ajax_search'));
		add_action('wp_ajax_norpiv_osm_search_location', array( __CLASS__, 'ajax_search'));

		add_action('admin_footer', array($this, 'disable_geosearch_options'));
	}


	#===============================================
	public static function get_plugin_data() {
		$plugin = Stonehenge_EM_OSM::get_plugin_data();
		return $plugin;
	}


	#===============================================
	public static function remove_template_files() {
		$theme_location = get_stylesheet_directory().'/plugins/events-manager/forms/event/location.php';
		$theme_event 	= get_stylesheet_directory().'/plugins/events-manager/forms/location/where.php';
		if( file_exists($theme_location) ) {
			wp_delete_file($theme_location);
		}
		if( file_exists($theme_event) ) {
			wp_delete_file($theme_event);
		}
	}


	#===============================================
	public static function restore() {
		$EM_folder 	= WP_PLUGIN_DIR .'/events-manager/';
		$OSM_folder = WP_PLUGIN_DIR .'/stonehenge-em-osm/originals/';

		// Restore events-manager.js
		if( file_exists( $EM_folder.'includes/js/_events-manager.js') ) {
			wp_delete_file( $EM_folder.'includes/js/_events-manager.js' );
			wp_delete_file( $EM_folder.'includes/js/events-manager.js' );
			copy( $OSM_folder.'events-manager.js', $EM_folder.'includes/classes/events-manager.js');
		}

		// Restore em-actions.php
		if( file_exists( $EM_folder.'_em-actions.php') ) {
			wp_delete_file( $EM_folder.'_em-actions.php');
			wp_delete_file( $EM_folder.'em-actions.php');
			copy( $OSM_folder.'em-actions.php', $EM_folder.'em-actions.php');
		}
		return;
	}


	#===============================================
	public static function alter_locations_table() {
		global $wpdb;
		$table 	= EM_LOCATIONS_TABLE;
		$column = $wpdb->query("SHOW COLUMNS FROM `{$table}` LIKE 'location_marker'");
		if( !$column ) {
			$wpdb->query("ALTER TABLE {$table} ADD `location_marker` VARCHAR( 25 ) AFTER `location_private`");
			$wpdb->query("ALTER TABLE {$table} ADD `location_map_type` VARCHAR( 255 ) AFTER `location_marker`");
		}
		return;
	}


	#===============================================
	public static function disable_google_api() {
		if( '0' != get_option('dbem_gmap_is_active') ) {
			update_option( 'dbem_gmap_is_active', '0' );
		}

		// Disable GEO Search.
		if( '0' != get_option('dbem_search_form_geo') ) {
			update_option( 'dbem_search_form_geo', '0');
		}

		return;
	}


	#===============================================
	public static function disable_geosearch_options() {
		$disabled = '<em>This option is not available when using EM - OpenStreetMaps.</em>';
		?><script>
jQuery(document).ready(function(){
	jQuery('#dbem_search_form_geo_row td, #dbem_gmap_is_active_row td, #dbem_search_form_geo_units_row td').html('<?php echo $disabled; ?>');
	jQuery('#em-opt-google-maps p').html('');
	jQuery('#dbem_search_form_geo_units_row th').html('');
	jQuery('#dbem_search_form_geo_units_label_row').hide();
	jQuery('#dbem_search_form_geo_distance_options_row').hide();

});
</script><?php
	}


	#===============================================
	public static function place_template_for_location() {
		$version 		= '1.8.5';
		$file 			= 'location.php';
		$theme_folder 	= get_stylesheet_directory() . '/plugins/events-manager/forms/event/';
		$plugin_folder	= plugin_dir_path(__DIR__, 1) . 'templates/';
		$plugin_file 	= $plugin_folder.$file;
		$theme_file 	= $theme_folder.$file;
		$version_file 	= $theme_folder.'location-'.$version.'.php';

		// Clean install.
		if( !is_dir($theme_folder) && !file_exists($theme_file) && !file_exists($version_file) ) {
			wp_mkdir_p($theme_folder);
			copy($plugin_file, $theme_file);
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder exists, but the files not.
		elseif( is_dir($theme_folder) && !file_exists($theme_file) && !file_exists($version_file) ) {
			copy($plugin_file, $theme_file);
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder and template exist, back-up old template.
		elseif( is_dir($theme_folder) && file_exists($theme_file) && !file_exists($version_file) ) {
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder and back-up exist, but main template is missing.
		elseif( is_dir($theme_folder) && !file_exists($theme_file) && file_exists($version_file) ) {
			copy($plugin_file, $theme_file);
		}
		return;
	}


	#===============================================
	public static function place_template_for_event() {
		$version 		= '1.8.5';
		$file 			= 'where.php';
		$theme_folder 	= get_stylesheet_directory() . '/plugins/events-manager/forms/location/';
		$plugin_folder	= plugin_dir_path(__DIR__, 1) . 'templates/';
		$plugin_file 	= $plugin_folder.$file;
		$theme_file 	= $theme_folder.$file;
		$version_file 	= $theme_folder.'where-'.$version.'.php';

		// Clean install.
		if( !is_dir($theme_folder) && !file_exists($theme_file) && !file_exists($version_file) ) {
			wp_mkdir_p($theme_folder);
			copy($plugin_file, $theme_file);
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder exists, but the files not.
		elseif( is_dir($theme_folder) && !file_exists($theme_file) && !file_exists($version_file) ) {
			copy($plugin_file, $theme_file);
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder and template exist, back-up old template.
		elseif( is_dir($theme_folder) && file_exists($theme_file) && !file_exists($version_file) ) {
			rename($theme_file, $version_file);
			copy($plugin_file, $theme_file);
		}

		// If folder and back-up exist, but main template is missing.
		elseif( is_dir($theme_folder) && !file_exists($theme_file) && file_exists($version_file) ) {
			copy($plugin_file, $theme_file);
		}
		return;
	}


	#===============================================
	public static function ajax_search() {
		global $wpdb;
		$suggestions = array();
		if( is_user_logged_in() || ( get_option('dbem_events_anonymous_submissions') && user_can(get_option('dbem_events_anonymous_user'), 'read_others_locations') ) ) {
			$location_cond = (is_user_logged_in() && !current_user_can('read_others_locations')) ? "AND location_owner=".get_current_user_id() : '';
			if( !is_user_logged_in() && get_option('dbem_events_anonymous_submissions') ) {
				if( !user_can(get_option('dbem_events_anonymous_user'),'read_private_locations') ) {
					$location_cond = " AND location_private=0";
				}
			}
			elseif( is_user_logged_in() && !current_user_can('read_private_locations') ) {
			    $location_cond = " AND location_private=0";
			}
			elseif( !is_user_logged_in() ) {
				$location_cond = " AND location_private=0";
			}

			if( EM_MS_GLOBAL && !get_site_option('dbem_ms_mainblog_locations') ) {
				$location_cond .= " AND blog_id=". absint(get_current_blog_id());
			}

			$location_cond = apply_filters('em_actions_locations_search_cond', $location_cond);
			$term = (isset($_REQUEST['term'])) ? '%'.$wpdb->esc_like(wp_unslash($_REQUEST['term'])).'%' : '%'.$wpdb->esc_like(wp_unslash($_REQUEST['q'])).'%';
			$sql = $wpdb->prepare("SELECT
					location_id AS `id`,
					Concat( location_name )  AS `label`,
					location_name AS `value`,
					location_address AS `address`,
					location_town AS `town`,
					location_state AS `state`,
					location_region AS `region`,
					location_postcode AS `postcode`,
					location_country AS `country`,
					location_latitude AS `latitude`,
					location_longitude AS `longitude`,
					location_marker AS `marker`,
					location_map_type AS `maptype`
				FROM ". EM_LOCATIONS_TABLE ."
				WHERE ( `location_name` LIKE %s ) AND location_status=1 $location_cond LIMIT 10", $term);
			$suggestions = $wpdb->get_results($sql);
		}
		$response = json_encode($suggestions);
		echo $response;
		exit();
	}


	#===============================================
	public static function marker_color_options() {
		$plugin		= self::get_plugin_data();
		$text 		= $plugin['text'];
		$choices 	= array(
			'blue' 		=> __('Blue', $text),
			'red' 		=> __('Red', $text),
			'green' 	=> __('Green', $text),
			'orange' 	=> __('Orange', $text),
			'yellow' 	=> __('Yellow', $text),
			'violet' 	=> __('Purple', $text),
			'grey' 		=> __('Grey', $text),
			'black' 	=> __('Black', $text),
		);
		return $choices;
	}


	#===============================================
	public static function map_type_options() {
		$plugin		= self::get_plugin_data();
		$text 		= $plugin['text'];
		$choices 	= array(
			'//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' 	=> 'OpenStreetMap',
			'//{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png' => 'OpenStreetMap HOT',
			'//server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}' => 'ArcGIS WorldMap',
			'//server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}' => 'ArcGIS TopoMap',
			'//server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}' => 'ArcGIS World Imagery',
			'//{s}.tile.openstreetmap.se/hydda/full/{z}/{x}/{y}.png' => 'Hydda (max zoom = 18)',
			'//maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png' => 'OpenMapSurfer Roads',
			'//maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png' => 'Wikimedia',
			'//stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}.png' => 'Stamen Toner',
			'//stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}.png' => 'Stamen Toner Lite',
			'//stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg' => 'Stamen Terrain',
			'//stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.jpg' => 'Stamen Watercolor'
		);
		return $choices;
	}


	#===============================================
	public static function per_location() {
		$plugin 		= self::get_plugin_data();
		$saved 			= get_option( $plugin['slug'] );
		$per_location 	= isset($saved['per_location']) && ($saved['per_location'] != 'no') ? true : false;
		$admin_only 	= isset($saved['per_admin']) && ($saved['per_admin'] != 'no') ? true : false;
		$is_admin 		= (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) ? true : false;

		if( $per_location ) {
			if( $admin_only ) {
				$show_this = $is_admin ? true : false;
				return $show_this;
			}
			$show_this = true;
			return $show_this;
		}
		return false;
	}


	#===============================================
	public static function show_per_location_select_dropdowns( $EM_Location ) {
		if( self::per_location() ) {
			$plugin 	= self::get_plugin_data();
			$text 		= $plugin['text'];
			$fields 	= array(
				'marker' => array(
					'id' 		=> 'location_marker_color',
					'label' 	=> __('Marker Color', $text),
					'choices'	=> self::marker_color_options(),
				),
				'maptype' => array(
					'id' 		=> 'location_map_type',
					'label' 	=> __('Map Type', $text),
					'choices'	=> self::map_type_options(),
				),
			);
			foreach( $fields as $field ) {
				?>
				<tr class="osm-<?php echo esc_attr($field['id'], ENT_QUOTES); ?>">
					<th><?php echo esc_html($field['label']); ?>:</th>
					<td><select name="_<?php echo esc_attr($field['id'], ENT_QUOTES); ?>" id="<?php echo esc_attr($field['id'], ENT_QUOTES); ?>">
						<option value="" selected disabled>- <?php echo esc_attr__('Default'); ?> -</option>
						<?php foreach( $field['choices'] as $choice => $value ) {
							$saved = get_post_meta($EM_Location->post_id, '_'.$field['id'], true );
							$selected = ($saved != $choice) ? '' : ' selected';
							echo "<option value='{$choice}' {$selected}>{$value}</option>";
						} ?>
					</select></td>
				</tr>
				<?php
			}
		}
		return;
	}


	#===============================================
	public static function save_maps_per_location( $EM_Location ) {
		global $wpdb;
		$table 			= EM_LOCATIONS_TABLE;
		$LocationPostID	= $EM_Location->post_id;

		if( isset($_POST['_location_marker_color']) && !empty($_POST['_location_marker_color']) ) {
			$marker = sanitize_text_field( $_POST['_location_marker_color'] );
			update_post_meta( $LocationPostID, '_location_marker_color', $marker);
			$wpdb->query("UPDATE {$table} SET `location_marker` = '{$marker}' WHERE `post_id` = '{$LocationPostID}'");

		}
		if( isset($_POST['_location_map_type']) && !empty($_POST['_location_map_type']) ) {
			$map = sanitize_text_field( $_POST['_location_map_type'] );
			update_post_meta( $LocationPostID, '_location_map_type', $map);
			$wpdb->query("UPDATE {$table} SET `location_map_type` = '{$map}' WHERE `post_id` = '{$LocationPostID}'");
		}
		return $EM_Location;
	}


	#===============================================
	public static function show_hidden_fields( $location_id ) {
		$plugin			= self::get_plugin_data();
		$options 		= get_option( $plugin['slug'] );

		$EM_Location 	= new EM_Location($location_id);
		$saved_marker 	= metadata_exists('post', $EM_Location->post_id, '_location_marker_color') ? get_post_meta( $EM_Location->post_id, '_location_marker_color', true) : $options['marker'];
		$saved_map 		= metadata_exists('post', $EM_Location->post_id, '_location_map_type') ? get_post_meta( $EM_Location->post_id, '_location_map_type', true) : $options['type'];

		ob_start();
		?>
		<div id="osm-location-info" style="display:none;"><input type="text" size="3" id="location-id" name="location_id" value="<?php echo esc_attr($location_id, ENT_QUOTES); ?>" readonly><input type="text" size="14" id="location-latitude" name="location_latitude" value="<?php echo esc_attr($EM_Location->location_latitude, ENT_QUOTES); ?>" readonly><input type="text" size="14" id="location-longitude" name="location_longitude" value="<?php echo esc_attr($EM_Location->location_longitude, ENT_QUOTES); ?>" readonly><input type="text" size="6" id="location-marker" value="<?php echo esc_attr($saved_marker, ENT_QUOTES); ?>" readonly><input type="text" size="80" id="location-map" value="<?php echo esc_attr($saved_map, ENT_QUOTES); ?>" readonly></div>
		<?php
		$fields = ob_get_clean();
		return $fields;
	}


	#===============================================
	public static function show_search_tip() {
		$plugin 	= self::get_plugin_data();
		$text 		= $plugin['text'];
		$saved 		= get_option( $plugin['slug'] );

		if( !isset($saved['api']) || empty($saved['api']) ) {
			$return 	= '<p style="color:#e14d43; font-weight: bold;">'. sprintf( __('Please enter your OpenCage API Key in your <a href=%s>Plugin Settings</a>.', $plugin['text']), $plugin['url'] ) .'</p>';

		}
		else {
			$button 	= '<button type="button" class="button-primary" onClick="apiSearch()">'. esc_html__('Search Address', $text) .'</button>';
			$expl 		= esc_html__('Hint', $text);
			$tooltip 	= esc_html__('If your location cannot be found, search for one nearby.', $text) .'<br>'. esc_html__('After the marker has been set, you can drag the marker to the preferred position and manually change the address details in the location form fields.', $text);

			$return 	= sprintf( '<div id="osm-search-tip" class="description"><span>%s %s</span></div>%s',
				esc_attr__('The more details you provide, the more accurate the search result will be.', $text),
				'<span class="osm-tooltip">('. $expl .')<span class="osm-tooltiptext">'. $tooltip .'</span></span>', $button
			);
		}
		return $return;
	}


	#===============================================
	public static function create_admin_notices() {
		$plugin 	= self::get_plugin_data();
		$saved 		= get_option( $plugin['slug'] );
		if( !isset($saved['api']) || empty($saved['api']) ) {
			$message = '<div class="notice notice-error"><p>';
			$message .= sprintf( __('Please enter your OpenCage API Key in your <a href=%s>Plugin Settings</a>.', $plugin['text']), $plugin['url'] );
			$message .= '</p></div>';
			echo $message;
		}
		return;
	}


} // End class.

