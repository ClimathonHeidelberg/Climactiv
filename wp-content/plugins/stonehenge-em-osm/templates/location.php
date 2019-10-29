<?php
#===============================================
# This file is part of the Events Manager - OpenStreetMaps plugin by Stonehenge Creations.
# https://wordpress.org/plugins/stonehenge-em-osm/
# It is used by Events Manager to show the front-end submission form for events.
# DO NOT DELETE!
# Copy to: your-theme-folder/plugins/events-manager/forms/event/location.php
# VERSION: 1.8.3
#===============================================

global $EM_Event;
return Stonehenge_EM_OSM::show_edit_event_box( $EM_Event );
