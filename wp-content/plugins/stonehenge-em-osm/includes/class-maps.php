<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if( class_exists('Stonehenge_EM_OSM') ) {
	new Stonehenge_EM_OSM_Maps();
}

class Stonehenge_EM_OSM_Maps {


	#===============================================
	public function __construct() {
		add_filter('em_event_output_placeholder', array( 'Stonehenge_EM_OSM_Maps', 'replace_location_map_placeholder'), 1, 3);
		add_filter('em_location_output_placeholder', array( 'Stonehenge_EM_OSM_Maps', 'replace_location_map_placeholder'), 1, 3);

		remove_shortcode('locations_map');
		add_shortcode('locations_map', array('Stonehenge_EM_OSM_Maps', 'shortcode_locations_map'));

		remove_shortcode('events_map');
		add_shortcode('events_map', array('Stonehenge_EM_OSM_Maps', 'shortcode_events_map'));

		// Deprecated, but here for backward compatibility with older versions of Events Manager.
		remove_shortcode('locations-map');
		add_shortcode('locations-map', array('Stonehenge_EM_OSM_Maps', 'shortcode_locations_map'));
		remove_shortcode('events-map');
		add_shortcode('events-map', array('Stonehenge_EM_OSM_Maps', 'shortcode_events_map'));
	}


	#===============================================
	public static function get_plugin_data() {
		$plugin = Stonehenge_EM_OSM::get_plugin_data();
		return $plugin;
	}


	#===============================================
	public static function replace_location_map_placeholder( $replacement, $input, $result ) {
		// $input is used so both $EM_Location and $EM_Event can be processed.
		if( $result === '#_LOCATIONMAP' ) {
			$replacement = ($input->location_id) ? self::single_map($input->location_id) : '';
		}
		return $replacement;
	}


	#===============================================
	public static function admin_map($EM_Location) {
		global $EM_OSM;
		$plugin 	= self::get_plugin_data();
		$text 		= $plugin['text'];
		$options 	= get_option( $plugin['slug'] );
		$api 		= !empty($saved['api']) ? $saved['api'] : '';
		$locale 	= strtolower(substr( get_bloginfo ( 'language' ), 0, 2 ));
		$balloon 	= !empty($EM_Location->location_id) ? $EM_Location->output("<strong>#_LOCATIONNAME</strong><br>#_LOCATIONADDRESS, #_LOCATIONTOWN") : ucwords( esc_html__('no default location', 'events-manager'));

		ob_start();
		?>
		<div id="em-osm-admin-map-container" class="osm-location-map-container">
			<link rel="stylesheet" href="<?php echo  plugins_url('assets/em-osm-leaflet.min.css', __DIR__); ?>">
			<script src="<?php echo plugins_url('assets/em-osm-leaflet.min.js', __DIR__); ?>"></script>
			<div id="em-osm-map" style="height:300px; width:400px; max-width: 98%;" class="em-osm-map"></div>
			<?php echo self::map_settings(); ?>
			<script>
				var OPCapi		= '<?php echo esc_js($options['api']); ?>',
					osmLocale 	= '<?php echo esc_js($locale); ?>',
					balloon 	= '<?php echo html_entity_decode( esc_js($balloon, ENT_QUOTES) ); ?>',
					Lat 		= jQuery('#location-latitude').val(),
					Lng			= jQuery('#location-longitude').val(),
					mapUrl	 	= jQuery('#location-map').val(),
					iconColor 	= jQuery('#location-marker').val(),
					markerUrl 	= pluginUrl + iconColor + '.png',
					thisIcon 	= new LeafIcon({iconUrl: markerUrl}),
					map 		= L.map('em-osm-map', mapOptions );

				if( mapUrl.indexOf("stamen") >= 0 ) { setMaxZoom = 18; setNatZoom = 16; }
				else { setMaxZoom = 20; setNatZoom = 18; }

				map.setView([Lat, Lng]);

				L.tileLayer( mapUrl, {
					attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">OpenStreetMap</a>',
					reuseTiles: true,
					detectRetina: true,
					minZoom: 1,
					maxZoom: L.Browser.retina ? setMaxZoom : setMaxZoom - 1,
					maxNativeZoom: L.Browser.retina ? setNatZoom : setNatZoom + 1
				}).addTo(map);

				if( !jQuery('#location-id').val() ) {
					map.setView([Lat, Lng], 1);
				}
				marker = L.marker([Lat, Lng], {icon: thisIcon}).addTo(map).bindPopup(balloon).openPopup();

				if( showFullscreen == 'yes' ) 	{ map.addControl(new L.Control.Fullscreen({ position: 'topright', })); }
				if( showScale == 'yes' ) 		{ L.control.scale().addTo(map); }

				setTimeout(function(){ map.invalidateSize()}, 400);
			</script>
		</div>
		<?php
		$output = ob_get_clean();
		$output = Stonehenge_Plugin::minify_js($output);
		return $output;
	}


	#===============================================
	public static function single_map( $location_id ) {
		global $EM_Event, $EM_Location, $EM_OSM;
		$plugin 		= self::get_plugin_data();
		$saved 			= get_option( $plugin['slug'] );
		$EM_Location 	= new EM_Location( $location_id );

		// Set Map Size.
		$width 			= get_option('dbem_map_default_width') ? get_option('dbem_map_default_width') : '400px';
		$width 			= preg_match('/(px)|%/', $width) ? $width : $width.'px';
		$height 		= get_option('dbem_map_default_height') ? get_option('dbem_map_default_height') : '300px';
		$height 		= preg_match('/(px)|%/', $height) ? $height:$height.'px';

		// Get the right information of the correct Object.
		if( is_object($EM_Event) ) {
			$id 			= 'L' . $EM_Event->location_id . 'E' . $EM_Event->event_id;
			$balloon 		= trim(preg_replace('/\s\s+/', '<br>', get_option('dbem_location_baloon_format')));
			$balloon 		= $EM_Event->output($balloon);
		} else {
			$id 			= 'L' . $EM_Location->location_id;
			$balloon 		= trim(preg_replace('/\s\s+/', '<br>', get_option('dbem_map_text_format')));
			$balloon 		= $EM_Location->output($balloon);
		}
		$balloon 		= addslashes($balloon);
		$latitude		= $EM_Location->output("#_LOCATIONLATITUDE");
		$longitude		= $EM_Location->output("#_LOCATIONLONGITUDE");

		// Determine Map Tiles & Marker Color.
		$per_location 	= isset($saved['per_location']) && ($saved['per_location'] != 'no') ? true : false;
		$defaultMarker 	= isset($saved['marker']) && !empty($saved['marker']) ? $saved['marker'] : 'blue';
		$markerColor	= $defaultMarker;

		$defaultMap 	= isset($saved['type']) && !empty($saved['type']) ? $saved['type'] : "//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
		$mapType 		= $defaultMap;
		if( $per_location ) {
			$thisMarker 	= get_post_meta( $EM_Location->post_id, '_location_marker_color', true);
			$markerColor 	= isset($thisMarker) && !empty($thisMarker) ? $thisMarker : $defaultMarker;
			$thisMap 		= get_post_meta( $EM_Location->post_id, '_location_map_type', true);
			$mapType 		= isset($thisMap) && !empty($thisMap) ? $thisMap : $defaultMap;
		}

		// Start output.
		ob_start();
		?>
		<div id="em-osm-single-map-container-<?php echo esc_attr($id); ?>" class="em-osm-container">
			<link rel="stylesheet" href="<?php echo  plugins_url('assets/em-osm-leaflet.min.css', __DIR__); ?>">
			<script src="<?php echo plugins_url('assets/em-osm-leaflet.min.js', __DIR__); ?>"></script>
			<div id="map<?php echo esc_attr($id); ?>" class="em-osm-map" style="width: <?php echo esc_attr($width); ?>; height: <?php echo esc_attr($height); ?>;"></div>
			<?php echo self::map_settings(); ?>
			<script>
				var	Lat 		= <?php echo esc_attr($latitude); ?>,
					Lng 		= <?php echo esc_attr($longitude);?>,
					thisIcon 	= new LeafIcon({iconUrl: pluginUrl + '<?php echo esc_attr($markerColor);?>' + '.png'}),
					thisMapTile = '<?php echo esc_attr($mapType); ?>',
					thisBalloon = '<?php echo $balloon; ?>',
					thisMap 	= L.map('map<?php echo esc_attr($id); ?>', mapOptions );

				if( thisMapTile.indexOf("stamen") >= 0 ) { setMaxZoom = 18; setNatZoom = 16; }
				else { setMaxZoom = 20; setNatZoom = 18; }

				thisMap.setView([Lat, Lng]);

				L.tileLayer( thisMapTile, {
					attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">OpenStreetMap</a>',
					reuseTiles: true,
					detectRetina: true,
					minZoom: 1,
					maxZoom: L.Browser.retina ? setMaxZoom : setMaxZoom - 1,
					maxNativeZoom: L.Browser.retina ? setNatZoom : setNatZoom + 1
					}).addTo(thisMap);

				marker = L.marker([Lat, Lng], {icon: thisIcon}).addTo(thisMap).bindPopup( thisBalloon).openPopup().dragging.disable();

				if( showFullscreen == 'yes' ) 	{ thisMap.addControl(new L.Control.Fullscreen({ position: 'topright', })); }
				if( showScale == 'yes' ) 		{ L.control.scale().addTo(thisMap); }

				setTimeout(function(){ thisMap.invalidateSize()}, 400);
			</script>
		</div>
		<?php
		$output = ob_get_clean();
		$output = Stonehenge_Plugin::minify_js($output);
		return $output;
	}


	#===============================================
	public static function shortcode_locations_map( $args ) {
		global $EM_Event, $EM_Location, $EM_Object, $EM_OSM;
		$plugin 	= self::get_plugin_data();
		$saved 		= get_option( $plugin['slug'] );

		// Create fallback to prevent errors.
		if(empty($args)) {
			$args = array();
		}

		if( isset($args['country']) ) {
			$args['country'] = strtoupper($args['country']);
		}

		// Create an unique ID to allow multiple maps per page.
		$id = rand(1,100);

		// Set Map Size.
		$width 		= isset($args['width']) && !empty($args['width']) ? $args['width'] :get_option('dbem_map_default_width');
		$width 		= preg_match('/(px)|%/', $width) ? $width : $width.'px';
		$height 	= isset($args['height']) && !empty($args['height']) ? $args['height'] : get_option('dbem_map_default_height');
		$height 	= preg_match('/(px)|%/', $height) ? $height : $height.'px';
		$padding 	= isset($args['padding']) ? (int) $args['padding'] : 10;

		// Start fetching Locations from the Database.
		$EM_Locations 	= EM_Locations::get( $args, $count=false );
		if( count($EM_Locations) === 0 ) {
			echo '<p><em>'. esc_html__('No Locations Found', 'events-manager') .'.</em></p>';
			return;
		}

		// Always use default MapTile for Multiple Locations Maps.
		$mapTile	= isset($saved['type']) && !empty($saved['type']) ? $saved['type'] : "//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";

		// Process result to prepare for the map.
		$marker 		= array();
		$lats 			= array();
		$lngs 			= array();
		$per_location 	= isset($saved['per_location']) && ($saved['per_location'] != 'no') ? true : false;
		$defaultMarker 	= isset($saved['marker']) && !empty($saved['marker']) ? $saved['marker'] : 'blue';
		$markerColor	= $defaultMarker;

		// Process individual locations.
		foreach( $EM_Locations as $EM_Location ) {

			// Determine the marker color per location.
			if( $per_location ) {
				$thisMarker 	= get_post_meta( $EM_Location->post_id, '_location_marker_color', true);
				$markerColor 	= isset($thisMarker) && !empty($thisMarker) ? $thisMarker : $defaultMarker;
				$markerUrl 		= addslashes(plugins_url("assets/images/marker-icon-2x-{$markerColor}.png", __DIR__));
			}
			$latitude		= $EM_Location->output("#_LOCATIONLATITUDE");
			$longitude		= $EM_Location->output("#_LOCATIONLONGITUDE");
			$lats[]			= $EM_Location->output("#_LOCATIONLATITUDE");
			$lngs[]			= $EM_Location->output("#_LOCATIONLONGITUDE");
			$balloon 		= trim(preg_replace('/\s\s+/', '<br>', get_option('dbem_map_text_format')));
			$balloon 		= addslashes( $EM_Location->output($balloon) );
			$marker[] 		= "[\"{$balloon}\", {$latitude}, {$longitude}, '{$markerColor}']";
		}
		$markers 	= implode(", ", $marker);
		$high_lat 	= max($lats);
		$high_lng 	= max($lngs);
		$low_lat 	= min($lats);
		$low_lng 	= min($lngs);
		$avg_lat 	= array_sum($lats)/count($lats);
		$avg_lng 	= array_sum($lngs)/count($lngs);
		$mapbounds 	= (count( (array) $EM_Locations) === 1) ? sprintf('setView([%1$s, %2$s], %$3s);', esc_attr($high_lat), esc_attr($high_lng), (int) $saved['zoom']) : sprintf('fitBounds([[%1$s, %2$s], [%3$s, %4$s]], {padding: [%5$s, %5$s]});', esc_attr($high_lat), esc_attr($high_lng), esc_attr($low_lat), esc_attr($low_lng), $padding);

		// Start the output.
		ob_start();
		?>
		<div id="em-osm-locations-map-container-<?php echo $id; ?>" class="em-osm-container">
			<link rel="stylesheet" href="<?php echo  plugins_url('assets/em-osm-leaflet.min.css', __DIR__); ?>">
			<script src="<?php echo plugins_url('assets/em-osm-leaflet.min.js', __DIR__); ?>"></script>
			<div id="em-osm-map-<?php echo $id; ?>" class="em-osm-map-multiple" style="width: <?php echo $width;?>; height: <?php echo $height; ?>;"></div>
			<?php echo self::map_settings(); ?>
			<script>
				var locations 	= [<?php echo $markers; ?>],
					thisMapTile = '<?php echo esc_attr($mapTile); ?>',
					thisMap 	= L.map('em-osm-map-<?php echo esc_attr($id); ?>', mapOptions );

				if( thisMapTile.indexOf("stamen") >= 0 ) { setMaxZoom = 18; setNatZoom = 16; }
				else { setMaxZoom = 20; setNatZoom = 18; }

				thisMap.setView([<?php echo esc_attr($avg_lat); ?>, <?php echo esc_attr($avg_lng); ?>]);

				L.tileLayer( thisMapTile, {
					attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">OpenStreetMap</a>',
					reuseTiles: true,
					detectRetina: true,
					minZoom: 1,
					maxZoom: L.Browser.retina ? setMaxZoom : setMaxZoom - 1,
					maxNativeZoom: L.Browser.retina ? setNatZoom : setNatZoom + 1
					}).addTo(thisMap);

				thisMap.<?php echo $mapbounds; ?>;

				for (var i = 0; i < locations.length; i++) {
					var thisIcon = new LeafIcon({iconUrl: pluginUrl + locations[i][3] + '.png'});
					var	marker = new L.marker([locations[i][1],locations[i][2]], {icon: thisIcon},).addTo(thisMap).bindPopup(locations[i][0]).dragging.disable();
				}

				function clickZoom(e) {
					thisMap.setView(e.target.getLatLng(),zoomLevel);
				}

				if( showFullscreen == 'yes' ) 	{ thisMap.addControl(new L.Control.Fullscreen({ position: 'topright', })); }
				if( showScale == 'yes' ) 		{ L.control.scale().addTo(thisMap); }

				setTimeout(function(){ thisMap.invalidateSize()}, 400);
			</script>
		</div>
		<?php
		$script = ob_get_clean();
		$script = Stonehenge_Plugin::minify_js($script);
		return $script;
	}


	#===============================================
	public static function shortcode_events_map( $args ) {
		global $EM_Event, $EM_Location, $EM_Object, $EM_OSM;
		$plugin 	= self::get_plugin_data();
		$saved 		= get_option( $plugin['slug'] );

		// Create fallback to prevent errors.
		if(empty($args)) {
			$args = array();
		}

		if( isset($args['country']) ) {
			$args['country'] = strtoupper($args['country']);
		}

		// Create an unique ID to allow multiple maps per page.
		$id = rand(1,100);

		// Set Map Size.
		$width 		= isset($args['width']) && !empty($args['width']) ? $args['width'] :get_option('dbem_map_default_width');
		$width 		= preg_match('/(px)|%/', $width) ? $width : $width.'px';
		$height 	= isset($args['height']) && !empty($args['height']) ? $args['height'] : get_option('dbem_map_default_height');
		$height 	= preg_match('/(px)|%/', $height) ? $height : $height.'px';
		$padding 	= isset($args['padding']) ? (int) $args['padding'] : 10;

		$EM_Events 		= EM_Events::get( $args, $count = false );
		$location_ids 	= array();
		foreach( $EM_Events as $EM_Event ) {
			// Filter out Events without  location.
			if( 0 != (int) $EM_Event->location_id ) {
				$location_ids[] = $EM_Event->location_id;
			}
		}

		// Clean the array.
		$location_ids 	= array_unique( $location_ids );

		// Is there anything to process?
		if( count( (array) $location_ids) === 0 ) {
			echo '<p><em>'. esc_html__('No Events Found', 'events-manager') .'.</em></p>';
			return;
		}

		// Always use default MapTile for Multiple Locations Maps.
		$mapTile		= isset($saved['type']) && !empty($saved['type']) ? $saved['type'] : "//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";

		// Process result to prepare for the map.
		$marker 		= array();
		$lats 			= array();
		$lngs 			= array();
		$per_location 	= isset($saved['per_location']) && ($saved['per_location'] != 'no') ? true : false;
		$defaultMarker 	= isset($saved['marker']) && !empty($saved['marker']) ? $saved['marker'] : 'blue';
		$markerColor	= $defaultMarker;

		foreach( $location_ids as $location_id ) {
			$EM_Location 	= new EM_Location($location_id);

			// Determine the marker color per location.
			if( $per_location ) {
				$thisMarker 	= get_post_meta( $EM_Location->post_id, '_location_marker_color', true);
				$markerColor 	= isset($thisMarker) && !empty($thisMarker) ? $thisMarker : $defaultMarker;
				$markerUrl 		= addslashes(plugins_url("assets/images/marker-icon-2x-{$markerColor}.png", __DIR__));
			}
			$latitude		= $EM_Location->output("#_LOCATIONLATITUDE");
			$longitude		= $EM_Location->output("#_LOCATIONLONGITUDE");
			$lats[]			= $EM_Location->output("#_LOCATIONLATITUDE");
			$lngs[]			= $EM_Location->output("#_LOCATIONLONGITUDE");
			$balloon 		= trim(preg_replace('/\s\s+/', '<br>', get_option('dbem_map_text_format')));
			$balloon 		= addslashes( $EM_Location->output($balloon) );
			$marker[] 		= "[\"{$balloon}\", {$latitude}, {$longitude}, '{$markerColor}']";
		}

		$markers 	= implode(", ", $marker);
		$high_lat 	= max($lats);
		$high_lng 	= max($lngs);
		$low_lat 	= min($lats);
		$low_lng 	= min($lngs);
		$avg_lat 	= array_sum($lats)/count($lats);
		$avg_lng 	= array_sum($lngs)/count($lngs);
		$mapbounds 	= (count( (array) $location_ids) === 1) ? sprintf('setView([%s, %s], %s);', esc_attr($high_lat), esc_attr($high_lng), (int) $saved['zoom']) : sprintf('fitBounds([[%s, %s], [%s, %s]], {padding: [10, 10]});', esc_attr($high_lat), esc_attr($high_lng), esc_attr($low_lat), esc_attr($low_lng));

		// Start the output.
		ob_start();
		?>
		<div id="em-osm-events-map-container-<?php echo $id; ?>" class="em-osm-container">
			<link rel="stylesheet" href="<?php echo  plugins_url('assets/em-osm-leaflet.min.css', __DIR__); ?>">
			<script src="<?php echo plugins_url('assets/em-osm-leaflet.min.js', __DIR__); ?>"></script>
			<div id="em-osm-map-<?php echo $id; ?>" class="em-osm-map-multiple" style="width: <?php echo $width;?>; height: <?php echo $height; ?>;"></div>
			<?php echo self::map_settings(); ?>
			<script>
				var locations 	= [<?php echo $markers; ?>],
					thisMapTile = '<?php echo esc_attr($mapTile); ?>',
					thisMap 	= L.map('em-osm-map-<?php echo esc_attr($id); ?>', mapOptions );

				if( thisMapTile.indexOf("stamen") >= 0 ) { setMaxZoom = 18; setNatZoom = 16; }
				else { setMaxZoom = 20; setNatZoom = 18; }

				thisMap.setView([<?php echo esc_attr($avg_lat); ?>, <?php echo esc_attr($avg_lng); ?>]);

				L.tileLayer( thisMapTile, {
					attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\">OpenStreetMap</a>',
					reuseTiles: true,
					detectRetina: true,
					minZoom: 1,
					maxZoom: L.Browser.retina ? setMaxZoom : setMaxZoom - 1,
					maxNativeZoom: L.Browser.retina ? setNatZoom : setNatZoom + 1
					}).addTo(thisMap);

				thisMap.<?php echo $mapbounds; ?>;
				for (var i = 0; i < locations.length; i++) {
					var thisIcon = new LeafIcon({iconUrl: pluginUrl + locations[i][3] + '.png'}),
					marker = new L.marker([locations[i][1],locations[i][2]], {icon: thisIcon},).addTo(thisMap).bindPopup(locations[i][0]).dragging.disable();
				}

				function clickZoom(e) {
					thisMap.setView(e.target.getLatLng(),zoomLevel);
				}

				if( showFullscreen == 'yes' ) 	{ thisMap.addControl(new L.Control.Fullscreen({ position: 'topright', })); }
				if( showScale == 'yes' ) 		{ L.control.scale().addTo(thisMap); }

				setTimeout(function(){ thisMap.invalidateSize()}, 400);
			</script>
		</div>
		<?php
		$script = ob_get_clean();
		$script = Stonehenge_Plugin::minify_js($script);
		return $script;
	}


	#===============================================
	public static function map_settings() {
		$plugin 		= self::get_plugin_data();
		$text 			= $plugin['text'];
		$options 		= get_option( $plugin['slug'] );
		$showZoom 		= isset($options['showlevel']) && $options['showlevel'] != 'no' ? 'true' : 'false';
		$zoomControls	= isset($options['zoomcontrols']) && $options['zoomcontrols'] != 'no' ? 'true' : 'false';
		$showFullscreen	= isset($options['fullscreen']) && $options['fullscreen'] != 'no' ? 'yes' : 'no';
		$showScale 		= isset($options['scale']) && $options['scale'] != 'no' ? 'yes' : 'no';

		ob_start();
		?><script>
		// Check for Mobile
		if(L.Browser.mobile) {
			var mobileDrag = false;
			var mobileZoom = false;
		}
		else {
			var mobileDrag = true;
			var mobileZoom = true;
		}

		// Set general options.
		var pluginUrl 		= '<?php echo plugins_url('assets/images/marker-icon-2x-', __DIR__); ?>',
			defaultMap 		= '<?php echo $options['type']; ?>',
			defaultColor 	= '<?php echo esc_attr($options['marker']); ?>',
			defaultMarker	= pluginUrl + defaultColor + '.png',
			zoomLevel 		= <?php echo (int) $options['zoom']; ?>,
			zoomButtons 	= '<?php echo esc_attr($zoomControls); ?>',
			mapOptions 		= {
				zoom: zoomLevel,
				zoomSnap: 0.25,
				zoomControl: zoomButtons,
				zoomDisplayControl: <?php echo $showZoom; ?>,
				scrollWheelZoom: mobileZoom,
				dragging: mobileDrag,
			},
			mapIcon 		= new L.Icon({});
			LeafIcon 		= L.Icon.extend({
				options: {
	//			    shadowUrl: '<?php echo plugins_url('assets/images/marker-shadow.png', __DIR__); ?>',
				    iconSize: [25, 41],
				    iconAnchor: [12, 41],
				    popupAnchor: [1, -34],
				    shadowSize: [41, 41]
				}
			}),
			showFullscreen 	= '<?php echo esc_attr($showFullscreen); ?>',
			showScale 		= '<?php echo esc_attr($showScale); ?>';
		</script><?php
		$output = ob_get_clean();
		$output = Stonehenge_Plugin::minify_js($output);
		return $output;
	}

} // End class.
