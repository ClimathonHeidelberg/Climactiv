<?php
/* This file creates the Drowp Down Select box for EM Locations. */
?>
<div id="osm-location-data" class="osm-location-data">
	<table id="osm-location-table" class="osm-location-table form-table">
		<tr class="em-location-data-select">
			<th><?php esc_html_e('Location:','events-manager'); ?></th>
			<td>
				<select name="location_id" id='location-select-id' size="1">
					<?php
					// Set first option.
					$no_location 	= esc_html__('No Location', 'events-manager');
					$disabled 		= get_option('dbem_require_location', true) ? 'disabled' : '';
					$first			= get_option('dbem_require_location', true) ? '- '. esc_html__('Select') .' -' : $no_location;
					?>
					<option value="0" title="0,0" balloon="<?php echo esc_attr($no_location, ENT_QUOTES); ?>" osm="<?php echo esc_attr($options['marker'], ENT_QUOTES); ?>, <?php echo esc_attr($options['type'], ENT_QUOTES); ?>" <?php echo esc_attr($disabled, ENT_QUOTES); ?>><?php echo esc_attr($first, ENT_QUOTES); ?></option>
					<?php
					// Fetch Locations from the database.
					$ddm_args 			= array( 'private' => $EM_Event->can_manage('read_private_locations') );
					$ddm_args['owner']	= is_user_logged_in() && !current_user_can('read_others_locations') ? get_current_user_id() : false;
					$EM_Locations 		= EM_Locations::get( $ddm_args );
					$saved 				= !empty($EM_Event->location_id) || !empty($EM_Event->event_id) ? $location_id : get_option('dbem_default_location');

					foreach( $EM_Locations as $EM_Location ) {
						// Use output() to correctly process html entities (Let EM do all the work)
						$id 			= $EM_Location->output("#_LOCATIONID");
						$latitude		= $EM_Location->output("#_LOCATIONLATITUDE");
						$longitude		= $EM_Location->output("#_LOCATIONLONGITUDE");
						$name 			= $EM_Location->output("#_LOCATIONNAME");
						$balloon 		= $EM_Location->output("<strong>#_LOCATIONNAME</strong><br>#_LOCATIONADDRESS, #_LOCATIONTOWN");
						$selected 		= ($id === $saved) ? "selected='selected'" : "";
						$marker 		= get_post_meta( $EM_Location->post_id, '_location_marker_color', true);
						$marker 	 	= isset($marker) && !empty($marker) ? $marker : $options['marker'];
						$maptype 		= get_post_meta( $EM_Location->post_id, '_location_map_type', true);
						$maptype 		= isset($maptype) && !empty($maptype) ? $maptype : $options['type'];
						?>
						<option value="<?php echo esc_attr($id, ENT_QUOTES); ?>" title="<?php echo esc_attr($latitude, ENT_QUOTES); ?>, <?php echo esc_attr($longitude, ENT_QUOTES); ?>" balloon="<?php echo esc_attr($balloon, ENT_QUOTES); ?>" osm="<?php echo esc_attr($marker, ENT_QUOTES); ?>, <?php echo esc_attr($maptype, ENT_QUOTES); ?>" <?php echo esc_attr($selected, ENT_QUOTES); ?>><?php echo esc_attr($name, ENT_QUOTES); ?></option>
						<?php ;
					} // End foreach()
					?>
				</select>
			</td>
		</tr>
	</table>
	<?php echo Stonehenge_EM_OSM_Maps::admin_map($EM_Location); ?>
	<br style="clear:both;">
</div>
