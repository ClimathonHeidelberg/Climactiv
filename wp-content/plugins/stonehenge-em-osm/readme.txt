=== Events Manager - OpenStreetMaps ===
Plugin Name: 		Events Manager - OpenStreetMaps
Contributors: 		DuisterDenHaag
Tags: 				Events Manager, Maps, Open, Street, free, map
Donate link: 		https://useplink.com/payment/VRR7Ty32FJ5mSJe8nFSx
Requires at least: 	5.2
Tested up to: 		5.2.4
Requires PHP: 		7.1
Stable tag: 		trunk
License: 			GPLv2 or later
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html


OpenStreetMaps for Events Manager. Replace Google Maps with OpenStreetMap. 100% free.


== Description ==
**0% Google, 100% open source.**
Use the free and open source OpenStreetMap to show your Events Manager Location Maps.

Existing locations work right out-of-the-box.

This plugin completely replaces the original Google Maps API (paid) with OpenStreetMap (open source). Once installed and activated, this plugin will automatically disable the Google Maps integration in Events Manager for you and replace them with OpenStreetMaps.


**Available Options:**
- Set Marker Color per location.
- Set Map Type per location.
- Set default Marker Color.
- Set default Map Type.
- Set default Zoom Level.
- Show/hide Zoom Controls.
- Show/hide current Zoom Level.
- Show/hide Full Screen Control.
- Show/hide Map Scale (metric & imperial).

There are currently 12 different map tile types available.

**Please note:**
Multiple Location Maps will apply the custom marker colors per location, but will always use the default map type.

**Geolocation Search** is currently not available when using this plugin.

== Localisation ==
* US English (default)
* Dutch (included)
* French (included)
* German (included)

The plugin is ready to be translated, all texts are defined in the POT file. Any contributions to localize this plugin are very welcome!


== Feedback ==
I am open to your suggestions and feedback!
[Please also check out my other plugins.](https://www.stonehengecreations.n/creations/)


== Frequently Asked Questions ==
= Are you part of the Events Manager team? =
**No, I am not!**
I am not associated with [Events Manager](https://wordpress.org/plugins/events-manager/) or its developer, [Marcus Sykes](http://netweblogic.com/), in <em>any</em> way.

= What is the big benefit of OpenStreetMap? =
**1)** OpenStreetMap uses a free, open source platform.
Check their website for more information: [OpenStreetMap.org](https://www.openstreetmap.org/about).

**2)** This plugin with OpenStreetMap will <b><i>not</i></b> request any visitor (location) info to display the map. So, that makes it easier to include in your own GDPR compliance.

= Why are Map Sizes different? =
The maps shown in the meta boxes <em>(Add/Edit Location & Event in the back-end and the front-end submission forms)</em> have fixed dimensions of 400px X 300px (EM default) to ensure a correct display of the meta boxes.

You can set the dimensions for #_LOCATIONMAP (single location) in Events &rarr; Settings &rarr; Formatting &rarr; Maps.
These will also be the default dimension for the [locations_map]. If you wish to display the [locations_map] differently, you can set those dimensions from within the shortcode: `[locations_map width="500px" height="500px"]`

= My maps won't load / Map tiles all over the screen =
All EM OSM scripts and styles need to be loaded in a very specific order. Caching & optimizing plugins tend to combine multiple files into one. Please exclude the '/wp-content/plugins/stonehenge-em-osm/' folder in the settings of your optimization plugin to prevent these errors.

= Why is my map not visible? =
You probably have set your map dimension in percentages (100%). Please check Events &rarr; Settings &rarr; Formatting &rarr; Single Event Page &rarr; Single Event Page format.
Replace: `<div style="float:right; margin:0px 0px 15px 15px;">#_LOCATIONMAP</div>`
With: `#_LOCATIONMAP`
Because the div has no width set, it is automatically scaled to 0px. Therefore your map is filling 100% of 0px.

If you are using a caching plugin and/or optimizer plugin, please exclude wp-content/plugins/stonehenge-em-osm/ in the settings of that plugin. OSM Leaflet assets have to be loaded in a very specific order and such optimizers break that. All included assets are fully optimized already.

**All EM OpenStreetMaps are being wrapped in a div.**
You can target that with custom css in your stylesheet to best suit your theme's responsiveness. "#em-osm-map-container {}"


== Installation ==
1. First make sure the original Events Manager plugin is installed and activated.
2. Install and activate this plugin.
3. Upon activation this plugin will automatically disable the Google Maps integration in Events Manager for you.
4. Enter your free OpenCage API key and preferred settings in the options page.
5. Enjoy the free OpenStreetMaps on your website.


== Screenshots ==
1. Single Location Map.
2. Multiple Locations Map.
3. Select Map Style and Marker Color.
4. Edit Event Page (Front-End Submission).
5. Add padding to the map.


== Upgrade Notice ==
You can now add extra padding inside the multi-marker maps. `[locations_map padding="50"]`.


== Changelog ==
= 2.9.7 =
- Disabled unavailable Geo Search options in the Events Manager settings to avoid confusion. This plugin does not support Geo Search (yet).

= 2.9.6 =
**User Requested:**
Added the argument for extra padding (in pixels) inside a multi-marker map.

- Map Bounds and Zoom Level are always automatically calculated, but this additional argument will allow you to zoom out a little.
- Can be applied to [locations_map] and [events_map].
- If not used, the map will (still) default to 10.
- Usage: [locations_map padding="50"].


= 2.9.5 =
- **User Requested:** Added four additional Stamen map tiles (Toner, Toner Lite, Terrain & Water Color).
	Their maximum zoom level is 18. Max zoom is automatically adjusted if one of these servers is selected, to prevent gray screens.
- Minor bug fix in responsive layout of the location select dropdown admin page.
- Updated readme.txt file.
- Confirmed compatibility with WordPress 5.2.4.
