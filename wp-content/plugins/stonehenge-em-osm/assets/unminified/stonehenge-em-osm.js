jQuery.noConflict();
(function($) {
$(document).ready(function(){
	// Settings Page.
	if( $('#per_location_no').length > 0 ) {
		if( $('#per_location_no').is(':checked') ) {
			$('.per-admin').hide();
		} else {
			$('.per-admin').show();
		}

		$('[name="stonehenge_em_osm[per_location]"]').click(function() {
			$('.per-admin').toggle();
		});
	}
/*
	// Is marker draggable?
	if( $('#location-address').length > 0 ) {
		setTimeout( function() {
			if( $('#location-address').attr('disabled') ) {
				marker.dragging.disable();
			} else {
				marker.dragging.enable();
				marker.on('dragend', function(e) {
					jQuery('#location-latitude').val( marker.getLatLng().lat );
					jQuery('#location-longitude').val( marker.getLatLng().lng );
				});
			}
		}, 1800);
	}
*/
	// Location Select
	if( $('#location-select-id').length > 0 ) {
		$('#location-select-id').change( function() {
			var	LocID 		= jQuery("#location-select-id option:selected").val(),
				Coords	 	= jQuery("#location-select-id option:selected").attr('title').split(","),
				Lat 		= Coords[0],
				Lng 		= Coords[1],
				osmCustom	= jQuery("#location-select-id option:selected").attr('osm').split(","),
				iconColor	= osmCustom[0] ,
				markerUrl 	= pluginUrl + iconColor + '.png',
				mapUrl 		= osmCustom[1],
				balloon 	= jQuery("#location-select-id option:selected").attr('balloon');

			$('#location-id').val( LocID );
			$('#location-latitude').val( Lat );
			$('#location-longitude').val( Lng );
			$('#location-marker').val( iconColor );
			$('#location-map').val( mapUrl );

			if( LocID === '0' ) {
				new L.tileLayer( defaultMap ).addTo(map);
				marker.setLatLng( [0,0] ).setIcon(new LeafIcon({iconUrl: defaultMarker})).bindPopup(balloon).openPopup();
				map.setView( [0,0], 1 );
			}
			else {
				new L.tileLayer( mapUrl ).addTo(map);
				marker.setLatLng( [Lat,Lng]).setIcon(new LeafIcon({iconUrl: markerUrl})).bindPopup(balloon).openPopup();
				map.setView( [Lat,Lng], zoomLevel );
			}
		}).trigger('change');
	}

	// Location Checkbox
	if( $('#no-location').length > 0 ) {
		if( $('#no-location').is(':checked') ) {
			$('.location-form-where').hide();
		}
		$('#no-location').click( function() {
			$('.location-form-where').toggle(250);
			setTimeout(function(){ map.invalidateSize()}, 500);
		});
	}


	// Form Fields
		$('#osm-location-reset a').click( function(){
			$('#osm-location-table input, #osm-location-table select').not(':input[type=button]').attr('disabled', false).val('');
			$('#osm-button').show(150);
			$('#osm-location-reset').hide(150);

			// Reset map.
			new L.tileLayer( defaultMap ).addTo(map);
			marker.setLatLng([0,0]).setIcon(new LeafIcon({iconUrl: defaultMarker})).bindPopup(' ').closePopup();
			map.setView([0,0],1);
			marker.dragging.enable();
			marker.on('dragend', function(e) {
				jQuery('#location-latitude').val( marker.getLatLng().lat );
				jQuery('#location-longitude').val( marker.getLatLng().lng );
			});
			return false;
		});

/*		// Disable on Pageload?
		if( $('input#location-id').val() != '0' && $('input#location-id').val() != '' ) {
			$('#osm-location-table input, #osm-location-table select').not(':input[type=button]').attr('disabled', true);
			$('#osm-button').hide();
			$('#osm-location-reset').show();
		} else {
			// Reset Map.
			new L.tileLayer( defaultMap ).addTo(map);
			marker.setLatLng([0,0]).setIcon(new LeafIcon({iconUrl: defaultMarker})).bindPopup(' ').closePopup();
			map.setView([0,0],1);
			marker.dragging.enable();
			marker.on('dragend', function(e) {
				jQuery('#location-latitude').val( marker.getLatLng().lat );
				jQuery('#location-longitude').val( marker.getLatLng().lng );
			});
		}
/*
		// Autocomplete Ajax Search.
		$('.location-form-where input#location-name').autocomplete({
			source: EM.locationajaxurl,
			minLength: 3,
			focus: function( event, ui ){
				jQuery('input#location-id').val( ui.item.value );
				return false;
			},
			select: function( event, ui ){
				$('#location-id').val(ui.item.id);
				$('#location-latitude').val(ui.item.latitude);
				$('#location-longitude').val(ui.item.longitude);
				$("#location-name" ).val(ui.item.value);
				$('#location-address').val(ui.item.address);
				$('#location-town').val(ui.item.town);
				$('#location-state').val(ui.item.state);
				$('#location-region').val(ui.item.region);
				$('#location-postcode').val(ui.item.postcode);
				$('#location-country').val(ui.item.country);
				$('#location_marker_color').val(ui.item.marker);
				$('#location_map_type').val(ui.item.maptype);
				$('#location-icon').val(ui.item.marker);
				$('#location-map').val(ui.item.maptype);
				$('#osm-location-table input, #osm-location-table select').not(':input[type=button]').attr('disabled', true);
				$('#osm-button').hide();
				$('#osm-location-reset').show();

				var	LocID 		= ui.item.id,
					Lat 		= ui.item.latitude,
					Lng 		= ui.item.longitude,
					iconColor	= ui.item.marker || defaultColor,
					markerUrl 	= pluginUrl + iconColor + '.png',
					mapUrl 		= ui.item.maptype || defaultMap,
					balloon 	= '<b>'+ ui.item.value +'</b><br>'+ ui.item.address +', '+ ui.item.town;

				// Move the Map.
				marker.setLatLng( [Lat,Lng]).setIcon(new LeafIcon({iconUrl: markerUrl})).bindPopup(balloon).openPopup();
				map.setView( [Lat,Lng], zoomLevel );
				return false;
			}
		}).data('ui-autocomplete')._renderItem = function( ul, item ) {
			html_val = "<a>" + em_esc_attr(item.label) + '<br><span style="font-size:11px"><em>'+ em_esc_attr(item.address) + ', ' + em_esc_attr(item.town)+"</em></span></a>";
			return $('<li></li>').data('item.autocomplete', item).append(html_val).appendTo(ul);
		};
*/
	}
});
})
(jQuery);

// OSM OpenCage API Search
function apiSearch() {
	geoAddress( jQuery('#location-address').val() +', '+ jQuery('#location-town').val() +', '+ jQuery('#location-postcode').val() +', '+ jQuery('#location-state').val() );
}

function geoAddress(query) {
    jQuery.ajax({
        url: 'https://api.opencagedata.com/geocode/v1/json',
        method: 'GET',
        data: {
            'key': OPCapi,
            'q': query,
            'no_annotations': 1,
            'add_request': 1,
            'pretty': 1,
            'language': osmLocale,
        },
        dataType: 'json',
        statusCode: {
            200: function(response) {
                var result = response.results[0];
				console.log(result);
				var formatted = result.formatted;
				var components = result['components']
				if( components.city ) 		{ jQuery('#location-town').val( components.city );}
				if( components.town ) 		{ jQuery('#location-town').val( components.town );}
				if( components.village ) 	{ jQuery('#location-town').val( components.village );}
				if( components.state ) 		{ jQuery('#location-state').val( components.state );}
				if( components.county ) 	{ jQuery('#location-region').val( components.county );}
				if( components.postcode ) 	{ jQuery('#location-postcode').val( components.postcode );}
				if( components.country_code ) {
					var countryCode = components.country_code.toUpperCase();
					jQuery('#location-country').val( countryCode );
				}
				var newLat = result['geometry']['lat'];
				var newLng = result['geometry']['lng'];
				var newLatLng = new L.LatLng(newLat, newLng);
				jQuery('#location-latitude').val( newLat );
				jQuery('#location-longitude').val( newLng );

				marker.setLatLng(newLatLng).bindPopup(formatted).openPopup();
				map.setView(newLatLng, zoomLevel);
				},
            402: function() {
                console.log('hit free-trial daily limit');
                console.log('become a customer: https://opencagedata.com/pricing');
            }
        }
    });
}

