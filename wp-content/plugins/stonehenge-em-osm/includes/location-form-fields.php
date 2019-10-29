<?php
/* This file creates the input form fields for EM Locations. */
?>
	<tr class="osm-location-data-address">
		<th><?php _e ('Address:', 'events-manager')?> <?php echo $required; ?>&nbsp;</th>
		<td><input id="location-address" type="text" name="location_address" value="<?php echo esc_attr($EM_Location->output('#_LOCATIONADDRESS'), ENT_QUOTES); ?>"> </td>
	</tr>
	<tr class="osm-location-data-town">
		<th><?php _e('City/Town:', 'events-manager')?> <?php echo $required; ?>&nbsp;</th>
		<td><input id="location-town" type="text" name="location_town" value="<?php echo esc_attr($EM_Location->output('#_LOCATIONTOWN'), ENT_QUOTES); ?>"> </td>
	</tr>
	<tr class="osm-location-data-state">
		<th><?php _e('State/County:', 'events-manager')?>&nbsp;</th>
		<td><input id="location-state" type="text" name="location_state" value="<?php echo esc_attr($EM_Location->output('#_LOCATIONSTATE'), ENT_QUOTES); ?>">	</td>
	</tr>
	<tr class="osm-location-data-postcode">
		<th><?php _e('Postcode:', 'events-manager')?>&nbsp;</th>
		<td><input id="location-postcode" type="text" name="location_postcode" value="<?php echo esc_attr($EM_Location->output('#_LOCATIONPOSTCODE'), ENT_QUOTES); ?>"></td>
	</tr>
	<tr class="osm-location-data-region">
		<th><?php _e('Region:', 'events-manager')?>&nbsp;</th>
		<td><input id="location-region" type="text" name="location_region" value="<?php echo esc_attr($EM_Location->output('#_LOCATIONREGION'), ENT_QUOTES); ?>">
			<input id="location-region-wpnonce" type="hidden" value="<?php echo wp_create_nonce('search_regions'); ?>">
		</td>
	</tr>
	<tr class="osm-location-data-country">
		<th><?php _e('Country:', 'events-manager')?> <?php echo $required; ?>&nbsp;</th>
		<td><select id="location-country" name="location_country">
				<?php
				foreach( em_get_countries(__('none selected','events-manager')) as $country_key => $country_name) {
					$selected = ($EM_Location->location_country === $country_key || ($EM_Location->location_country == '' && $EM_Location->location_id == '' && get_option('dbem_location_default_country')==$country_key)) ? 'selected="selected"' : '';

					?>
					<option value="<?php echo esc_attr($country_key, ENT_QUOTES); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($country_name); ?></option>
					<?php
					;
				} ?>
			</select>
		</td>
	</tr>
	<?php echo $EM_OSM->show_per_location_select_dropdowns( $EM_Location ); ?>
</table>
	<?php echo Stonehenge_EM_OSM_Maps::admin_map($EM_Location); ?>
	<br style="clear:both;">
	<div id="osm-button">
		<?php echo $EM_OSM->show_search_tip(); ?>
	</div>
