<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
	//Fade Images MainMenue
	
	$("#HexEnergiewende").click(function () {
		$(".infoText2").hide();
		$("#InfoKlimaschutz, #InfoMottoEnergiewende").fadeIn();

		$("#HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	$("#HexEnergiewende").mouseover(function () {
		$(".infoText2").hide();
		$("#HexOSM, #InfoMottoEnergiewende").fadeIn();

		$("#HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	
	$("#HexOSM, #showTut").click(function () {
		$(".infoText2").hide();
		$("#InfoTutorial").fadeIn();

		$("#HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoKlimaschutz, #InfoBeteiligung").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	$("#HexOSM").mouseover(function () {
		$(".infoText2").hide();
		$("#InfoMottoEnergiewende, #InfoBeteiligung").fadeIn();

		$("#HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoKlimaschutz").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	
	
	$("#HexVersorgung").mouseover(function () {
		$(".infoText2").hide();
		$("#InfoVersorgung, #InfoMottoVersorgung").fadeIn();
		$("#HexVersorgungFilter").fadeOut();
		
		$("#InfoMottoEnergiewende, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexErnaehrungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	$("#HexVersorgungFoto").mouseover(function () {
		$("#InfoVersorgung, .infoText, .infoText2").hide();
		$("#InfoMottoEnergiewende, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexVersorgungMap, #InfoHandlbereicheV").fadeIn();
	});
	
	$("#HexErnaehrung").mouseover(function () {
		$(".infoText2").hide();
		$("#InfoErnaehrung, #InfoMottoErnaehrung").fadeIn();
		$("#HexErnaehrungFilter").fadeOut();
		
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexVersorgungFilter, #HexMobilitaetFilter, #HexWohnenFilter").fadeIn();
	});
	$("#HexErnaehrungFoto").mouseover(function () {
		$("#InfoErnaehrung, .infoText, .infoText2").hide();
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexErnaehrungMap, #InfoHandlbereicheE").fadeIn();
	});
	
	
	$("#HexMobilitaet").mouseover(function () {
		$(".infoText2").hide();
		$("#InfoMobilitaet, #InfoMottoMobilitaet").fadeIn();
		$("#HexMobilitaetFilter").fadeOut();
		
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexWohnenFilter").fadeIn();
	});
	$("#HexMobilitaetFoto").mouseover(function () {
		$("#InfoMobilitaet, .infoText, .infoText2").hide();
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexWohnenMap, #InfoWohnen, #InfoMottoWohnen, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexMobilitaetMap, #InfoHandlbereicheM").fadeIn();
	});
	
	$("#HexWohnen").mouseover(function () {
		$(".infoText2").hide();
		$("#InfoWohnen, #InfoMottoWohnen").fadeIn();
		$("#HexWohnenFilter").fadeOut();
		
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexVersorgungFilter, #HexErnaehrungFilter, #HexMobilitaetFilter").fadeIn();
	});
	$("#HexWohnenFoto").mouseover(function () {
		$("#InfoWohnen, .infoText, .infoText2").hide();
		$("#InfoMottoEnergiewende, #HexVersorgungMap, #InfoVersorgung, #InfoMottoVersorgung, #HexErnaehrungMap, #InfoErnaehrung, #InfoMottoErnaehrung, #HexMobilitaetMap, #InfoMobilitaet, #InfoMottoMobilitaet, #InfoBeteiligung, #InfoKlimaschutz").fadeOut();
		$("#HexWohnenMap, #InfoHandlbereicheW").fadeIn();
	});
	
	$(".hexagon").click(function () {
  		if(this.id == "HexVersorgung"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 2)
  		}
  		if(this.id == "HexMobilitaet"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 4)
    	}
  		if(this.id == "HexErnaehrung"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 5)
    	}
  		if(this.id == "HexWohnen"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 3)
  		}
	});
	
	$(".hexagonMap").click(function () {
  		if(this.id == "HexVersorgungMap"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 2)
  		}
  		if(this.id == "HexMobilitaetMap"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 4)
    	}
  		if(this.id == "HexErnaehrungMap"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 5)
    	}
  		if(this.id == "HexWohnenMap"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 3)
  		}
	});

	$(".hexagonFoto").click(function () {
  		if(this.id == "HexVersorgungFoto"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 2)
  		}
  		if(this.id == "HexMobilitaetFoto"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 4)
    	}
  		if(this.id == "HexErnaehrungFoto"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 5)
    	}
  		if(this.id == "HexWohnenFoto"){
  			$("#MainMenue").fadeOut();
  			$(".layer-switcher .panel").accordion("option","active", 3)
  		}
	});
	
	$(".back").click(function () {
		$("#MainMenue").fadeIn();
	});
	$(".backTut").click(function () {
		$("#InfoTutorial").fadeOut();
	});
		
	$("#HideCenter").click(function () {
		$("#Center").fadeOut();
		$("#AboutContent").fadeIn();
		$("#HideCenter").fadeOut();
		$("#BackCenter").fadeIn();
	});
	$("#BackCenter").click(function () {
		$("#AboutContent").fadeOut();
		$("#Center").fadeIn();
		$("#BackCenter").fadeOut();
		$("#HideCenter").fadeIn();
	});
	
//$("#accordion").accordion();

//GeoJSON reader/writer
var geoJsonFormat = new ol.format.GeoJSON({defaultDataProjection:"EPSG:3857", geometryName: "way"});

//Create vector layer 
// var source = new ol.source.Vector();
// var vector = new ol.layer.Vector({
 // // title:'Benutzer Zeichnung',	
  // source: source,
  // style: new ol.style.Style({
    // fill: new ol.style.Fill({
      // color: 'rgba(255, 255, 255, 0.2)'
    // }),
    // stroke: new ol.style.Stroke({
      // color: '#ffcc33',
      // width: 2
    // }),
    // image: new ol.style.Circle({
      // radius: 7,
      // fill: new ol.style.Fill({
        // color: '#ffcc33'
      // })
    // })
  // })
// });

//Add map
var view = new ol.View({
    center: ol.proj.transform([8.71, 49.412222], 'EPSG:4326', 'EPSG:3857'),
    zoom: 14,
    //maxZoom: 18,
    //minZoom: 12
}); 

//osm source
var attribution = new ol.control.Attribution({
    collapsible: false
});

var map = new ol.Map({

	target: 'map',
    controls: ol.control.defaults({attribution: false}).extend([attribution]),
    view: view
});

//osm source
function checkSize() {
    var small = map.getSize()[0] < 600;
    attribution.setCollapsible(small);
    attribution.setCollapsed(small);
  }

window.addEventListener('resize', checkSize);
checkSize();

//-----------geolocation-----------------
var geolocation = new ol.Geolocation({
    projection: view.getProjection()
});

function el(id) {
    return document.getElementById(id);
}

  el('track').addEventListener('change', function() {
    geolocation.setTracking(this.checked);
  });

  // update the HTML page when the position changes.
  geolocation.on('change', function() {
    el('accuracy').innerText = geolocation.getAccuracy() + ' [m]';
    el('altitude').innerText = geolocation.getAltitude() + ' [m]';
    el('altitudeAccuracy').innerText = geolocation.getAltitudeAccuracy() + ' [m]';
    el('heading').innerText = geolocation.getHeading() + ' [rad]';
    el('speed').innerText = geolocation.getSpeed() + ' [m/s]';
  });

  // handle geolocation error.
  geolocation.on('error', function(error) {
    var info = document.getElementById('info');
    info.innerHTML = error.message;
    info.style.display = '';
  });

  var accuracyFeature = new ol.Feature();
  geolocation.on('change:accuracyGeometry', function() {
    accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
  });

  var positionFeature = new ol.Feature();
  positionFeature.setStyle(new ol.style.Style({
    image: new ol.style.Circle({
      radius: 6,
      fill: new ol.style.Fill({
        color: '#3399CC'
      }),
      stroke: new ol.style.Stroke({
        color: '#fff',
        width: 2
      })
    })
  }));

  geolocation.on('change:position', function() {
    var coordinates = geolocation.getPosition();
    positionFeature.setGeometry(coordinates ?
        new ol.geom.Point(coordinates) : null);
  });

  new ol.layer.Vector({
    map: map,
    source: new ol.source.Vector({
      features: [accuracyFeature, positionFeature]
    })
  });

//-----------------


// map.addLayer(vector);

//Draw features 	   
// var typeSelect = document.getElementById('type');
// var draw; 
// function addInteraction() {
  // var value = typeSelect.value;
  // if (value !== 'None') {
    // draw = new ol.interaction.Draw({
      // source: source,
      // type: /** @type {ol.geom.GeometryType} */ (value)
    // });
    // map.addInteraction(draw);
  // }
// }
// typeSelect.onchange = function(e) {
  // map.removeInteraction(draw);
  // addInteraction();
// };
// addInteraction();

//Highlighting Layer
var hlSource = new ol.source.Vector();
var hlVector = new ol.layer.Vector({
  	
  source: hlSource,
  style: new ol.style.Style({
    fill: new ol.style.Fill({
      color: 'rgba(255, 255, 255, 0.5)'
    }),
    stroke: new ol.style.Stroke({
      color: '#ffff00',
      width: 4
    }),
    image: new ol.style.Circle({
      radius: 7,
      fill: new ol.style.Fill({
        color: 'rgba(255,255,0,0.7)' //'#ffff00'
       }) ,
      stroke: new ol.style.Stroke({
      	color: '#333333',
      	width: 2
      })
      
    })
  })
});



var highlightFeature = function(geojson) {
	
	var features = geoJsonFormat.readFeatures(geojson);
	
	hlSource.addFeatures(features);
	
};

var clearHighlightLayer = function() {
	
	hlSource.clear(true);
	
};

var getOsmGeomType = function(geojson) {
	var isOsmIdNegative = geojson.features[0].properties.osm_id < 0;
	//var wktType = geojson.features[0].geometry.type;
	var sourceType = (
		geojson.features &&
		geojson.features[0].properties &&
		geojson.features[0].properties.sourcetype
		) ? geojson.features[0].properties.sourcetype : null;
	
	var osmType = (isOsmIdNegative)? "relation" : (sourceType==null)? null :(sourceType == 'point')? "node" : "way";
	
	return osmType;
	
};

var getOsmLink = function(id, geojson) {

	var osmType = getOsmGeomType(geojson);

	if (osmType == null) return '';

	var osmURL = "https://www.openstreetmap.org/" + osmType + "/" + Math.abs(id);
	
	var osmLink = "<a class='osm_link' target='_blank' href='"+ osmURL +"' rel="noopener noreferrer">"+ Math.abs(id) +" auf OpenStreetMap.org bearbeiten</a>";
	
	return osmLink;
	
};



//German-English

var switchLegendTo = function(titleProp){
	map.removeControl(lyrSwitcher);
	lyrSwitcher = new ol.control.LayerSwitcher({titleProp:titleProp});
	map.addControl(lyrSwitcher);
	lyrSwitcher.showPanel();
	
	//outer accordion
	$(".layer-switcher .panel").accordion({header:'>ul>li.group>label', heightStyle: "content", collapsible:true});

	//inner accordion
	$(".layer-switcher .panel>ul>li.group").accordion({header:'>ul>li.group>label', heightStyle: "content", collapsible:true});
}

$(".sprachelink_de").click(function () { 
	switchLegendTo('title_en');
	$(".HeaderKarte_de").hide();
	$(".HeaderKarte_en").show();
});

$(".sprachelink_en").click(function () { 
	switchLegendTo('title');
	$(".HeaderKarte_en").hide();
	$(".HeaderKarte_de").show();
});



/*var getWebsite = function(website) {
	var websiteLink = "<a href='" + website + "</a>";
	return websiteLink;
};*/

//LayerGroups
var lyrGrp_Basemaps = new ol.layer.Group({title:'Hintergrundkarten',title_en:'Basemaps'});
var lyrGrp_Energiewende = new ol.layer.Group({title:'Energiewende',title_en:'Energy Policy'});
var lyrGrp_Versorgung = new ol.layer.Group({title:'Versorgung & Energie',title_en:'Energy Supply'});	
var lyrGrp_Wohnen = new ol.layer.Group({title:'Wohnen & Gebäude',title_en:'Living & Construction'});		
var lyrGrp_Mobilitaet = new ol.layer.Group({title:'Mobilität & Verkehr',title_en:'Mobility & Traffic'});
var lyrGrp_Konsum = new ol.layer.Group({title:'Konsum & Ernährung',title_en:'Food & Consumption'});

//Subgroups
var lyrGrp_Versorgung_Strom = new ol.layer.Group({title:'Anlagen für Erneuerbaren Strom',title_en:'Facilities for renewable Power'});	
var lyrGrp_Versorgung_Waerme = new ol.layer.Group({title:'Anlagen für Erneuerbare Wärme',title_en:'Facilities for renewable Heat'});	
var lyrGrp_Versorgung_KWK = new ol.layer.Group({title:'Anlagen für Kraft-Wärme-Kopplung',title_en:'Facilities for Power-Heat Couplers'});	
var lyrGrp_Versorgung_NetzeStrom = new ol.layer.Group({title:'Netze zur Versorgung mit Strom',title_en:'Power Supply Network'});
var lyrGrp_Versorgung_Speicher = new ol.layer.Group({title:'Speicher',title_en:'Storages'});	
var lyrGrp_Versorgung_AllgemKW = new ol.layer.Group({title:'Kraftwerke allgemein',title_en:'General Power Facilities'});	

lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_Strom);
lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_Waerme);
lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_KWK);
lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_NetzeStrom);
lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_Speicher);
lyrGrp_Versorgung.getLayers().push(lyrGrp_Versorgung_AllgemKW);

var lyrGrp_Konsum_Saisonal = new ol.layer.Group({title:'Saisonal und regional einkaufen',title_en:'Seasonal and Regional Shopping'});
var lyrGrp_Konsum_Biolog = new ol.layer.Group({title:'Biologische Lebensmittel einkaufen',title_en:'Organic Food Shopping'});
var lyrGrp_Konsum_Veg = new ol.layer.Group({title:'Vegetarisch / Vegan ernähren',title_en:'live vegetarian or vegan'});
var lyrGrp_Konsum_Reparieren = new ol.layer.Group({title:'Gegenstände reparieren (statt wegwerfen)',title_en:'Repair Items'});
var lyrGrp_Konsum_Gebraucht = new ol.layer.Group({title:'gebrauchte Gegenstände nutzen/kaufen',title_en:'Use scound-hand Products'});

lyrGrp_Konsum.getLayers().push(lyrGrp_Konsum_Saisonal);
lyrGrp_Konsum.getLayers().push(lyrGrp_Konsum_Biolog);
lyrGrp_Konsum.getLayers().push(lyrGrp_Konsum_Veg);
lyrGrp_Konsum.getLayers().push(lyrGrp_Konsum_Reparieren);
lyrGrp_Konsum.getLayers().push(lyrGrp_Konsum_Gebraucht);

var lyrGrp_Mobilitaet_Fahrrad = new ol.layer.Group({title:'Fahrrad fahren',title_en:'Ride a Bicycle'});	
var lyrGrp_Mobilitaet_Elektro = new ol.layer.Group({title:'auf Elektromobilität umsteigen',title_en:'Change to Electromobility'});	
var lyrGrp_Mobilitaet_Autoteilen = new ol.layer.Group({title:'Auto teilen',title_en:'Share your Car'});	
var lyrGrp_Mobilitaet_Freizeit = new ol.layer.Group({title:'Freizeit CO2-arm gestalten',title_en:'Spend your free time CO2-neutral'});	

lyrGrp_Mobilitaet.getLayers().push(lyrGrp_Mobilitaet_Fahrrad);
lyrGrp_Mobilitaet.getLayers().push(lyrGrp_Mobilitaet_Elektro);
lyrGrp_Mobilitaet.getLayers().push(lyrGrp_Mobilitaet_Autoteilen);
lyrGrp_Mobilitaet.getLayers().push(lyrGrp_Mobilitaet_Freizeit);
	
map.addLayer(lyrGrp_Basemaps);
map.addLayer(lyrGrp_Energiewende);
map.addLayer(lyrGrp_Versorgung);
map.addLayer(lyrGrp_Wohnen);
map.addLayer(lyrGrp_Mobilitaet);
map.addLayer(lyrGrp_Konsum);



map.addLayer(hlVector);	//??

//var sourceOSM = new ol.layer.Tile({source: new ol.source.OSM()});

//Base Layer
var lyrBase_Mapnik = new ol.layer.Tile({
	title: 'OSM Mapnik',
	title_en: 'OSM Mapnik',
	type : 'base',
	visible: false,
	source: new ol.source.OSM()
});

//var lyrBase_Transport = new ol.layer.Tile({
//	title: 'Transport',
//	title_en: 'Transport',
//	type : 'base',
//	visible: false,
//	source: new ol.source.XYZ({
//		url:"https://a.tile2.opencyclemap.org/transport/{z}/{x}/{y}.png"
//	})
//});
//
//var lyrBase_Landscape = new ol.layer.Tile({ 
//	title: 'Topographie',
//	title_en: 'Landscape',
//	type : 'base',
//	visible: false,
//	source: new ol.source.XYZ({
//		url:"httpss://a.tile.thunderforest.com/landscape/{z}/{x}/{y}.png" //tlw. kostenpflichtig, evt. ersetzen
//	})
//});

var lyrBase_Mapsurfer = new ol.layer.Tile({ 
	title: 'OSM Mapsurfer Strassen',
	title_en: 'OSM Mapsurfer Roads',
	type : 'base',
	visible: true, 
	source: new ol.source.XYZ({
		//url:"https://korona.geog.uni-heidelberg.de/tiles/roads/x={x}&y={y}&z={z}"
		url:"https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png"
	})
});
var lyrBase_Mapsurfer_Hillshade = new ol.layer.Tile({ 
	title: 'Hillshade',
	title_en: 'Hillshade',
	//type : 'base',
	visible: true,
	opacity: 0.6,
	source: new ol.source.XYZ({
		transition: 0,
		//url:"https://korona.geog.uni-heidelberg.de/tiles/asterh/x={x}&y={y}&z={z}"
		url:"https://maps.heigit.org/openmapsurfer/tiles/asterh/webmercator/{z}/{x}/{y}.png"
	})
});
	
lyrGrp_Basemaps.getLayers().push(lyrBase_Mapnik);
//lyrGrp_Basemaps.getLayers().push(lyrBase_Transport);
//lyrGrp_Basemaps.getLayers().push(lyrBase_Landscape);
lyrGrp_Basemaps.getLayers().push(lyrBase_Mapsurfer);
lyrGrp_Basemaps.getLayers().push(lyrBase_Mapsurfer_Hillshade);

//Layer
//Kategorie Energiewende
var lyr_initiatives = new ol.layer.Tile({title: "Vereine & Initiativen",title_en:"Initiatives and Clubs ", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:initiatives', STYLES: '',}})});

//Kategorie Energieversorgung
//Anlagen für erneuerbaren Strom
var lyr_vers_biogaskw = new ol.layer.Tile({title: "Biogas-Kraftwerke",title_en: "Biogas-Powerplants",visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biogaskw', STYLES: 'A_Klimaschutzk_Biogaskraft',}})});
var lyr_vers_biomasskw = new ol.layer.Tile({title: "Biomasse-Kraftwerke",title_en:"Biomass-Powerplants", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biomasskw', STYLES: 'A_Klimaschutzk_biomasskraft',}})});
var lyr_vers_biooelkw = new ol.layer.Tile({title: "Bioöl-Kraftwerke",title_en:"Biooil-Powerplants", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biooelkw', STYLES: 'A_Klimaschutzk_biooelkraft',}})});
var lyr_vers_photovoltaic = new ol.layer.Tile({title: "Solar Photovoltaik",title_en:"Solar Photovoltaic-Powerplants", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_solar_photovoltaic', STYLES: 'A_Klimaschutzk_SolarAnlage',}})});
var lyr_vers_windkraft = new ol.layer.Tile({title: "Windkraftanlagen",title_en:"Wind Turbines", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_windkraft', STYLES: 'A_Klimaschutzk_Windkraft',}})});
var lyr_vers_geothmkw = new ol.layer.Tile({title: "Geothermie-Kraftwerke",title_en:"Geothermal-Powerplants", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_geothmkw', STYLES: 'A_Klimaschutzk_Geothermkraft',}})});
var lyr_vers_wasserkw = new ol.layer.Tile({title: "Wasserkraftwerke",title_en:"Water Power Stations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_wasserkw', STYLES: 'A_Klimaschutzk_Wasserkraft',}})});
var lyr_vers_gezeitenkw = new ol.layer.Tile({title: "Gezeitenkraftwerke",title_en:"Tidal Power Stations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_gezeitkw', STYLES: 'A_Klimaschutzk_Wasserkraft',}})});
//Anlagen für erneuerbare Wärme
var lyr_vers_giogas = new ol.layer.Tile({title: "Biogas",title_en:"Biogas", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biogas', STYLES: 'A_Klimaschutzk_BioGas',}})});
var lyr_vers_biomasse = new ol.layer.Tile({title: "Biomasse",title_en:"Biomass", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biomasse', STYLES: 'A_Klimaschutzk_BioMasse',}})});
var lyr_vers_biooel = new ol.layer.Tile({title: "Bioöl",title_en:"Biooil", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_biooel', STYLES: 'A_Klimaschutzk_BioOel',}})});
var lyr_vers_soltherm = new ol.layer.Tile({title: "Solarthermie",title_en:"Solar Thermal Energy", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_solarthermie', STYLES: 'A_Klimaschutzk_SolarAnlage',}})});
var lyr_vers_geothermie = new ol.layer.Tile({title: "Geothermie",title_en:"Geothermal", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_geothermie', STYLES: 'A_Klimaschutzk_Geothermie',}})});
//Anlagen für Kraft-Wärme-Kopplung
var lyr_vers_kwkanlagen = new ol.layer.Tile({title: "KWK-Anlagen",title_en:"Cogeneration Plants", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_kwk-anlagen', STYLES: 'A_Klimaschutzk_Transformator',}})});
//Netze zur Versorgung mit Strom
var lyr_vers_powerline = new ol.layer.Tile({title: "Stromleitungen",title_en:"Powerlines", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_power_line', STYLES: '',}})});
//var lyr_power_tower = new ol.layer.Tile({title: "Strommasten",visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
//    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:power_tower', STYLES: '',}})});
var lyr_vers_powertransformer = new ol.layer.Tile({title: "Transformatoren",title_en:"Transformers", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_power_transformer', STYLES: 'A_Klimaschutzk_Transformator',}})});
var lyr_vers_substation = new ol.layer.Tile({title: "Schaltanlage",title_en:"Substations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_substation', STYLES: 'A_Klimaschutzk_SchaltAnlage',}})});
//Speicher
var lyr_vers_stromsp = new ol.layer.Tile({title: "Stromspeicher",title_en:"Energy Storage", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_stromspeicher', STYLES: 'A_Klimaschutzk_StromSpeicher',}})});
var lyr_vers_waermesp = new ol.layer.Tile({title: "Wärmespeicher",title_en:"Heat Storage", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_waermespeicher', STYLES: 'A_Klimaschutzk_WaermeSpeicher',}})});
var lyr_vers_gassp = new ol.layer.Tile({title: "Gasspeicher",title_en:"Gas Storage", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_gasspeicher', STYLES: 'A_Klimaschutzk_WaermeSpeicher',}})});
//Verschiedenes
var lyr_vers_powergenerator = new ol.layer.Tile({title: "Kraftwerke & Generatoren",title_en:"Power Stations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vers_energ_power_generator', STYLES: 'A_Klimaschutzk_KraftwerGenerator',}})});
//Kategorie Konsum und Ernährung
var lyr_marketplace = new ol.layer.Tile({title: "Wochenmärkte",title_en:"Weekly Markets", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:marketplace', STYLES: 'A_Klimaschutzk_WochenMarkt',}})});
var lyr_bio_supermarket = new ol.layer.Tile({title: "Bio-Supermärkte",title_en:"Organic Supermarkets", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bio_supermarket', STYLES: 'A_Klimaschutzk_BioSupermarkt',}})});
var lyr_bio_butcher = new ol.layer.Tile({title: "Bio-Metzgereien",title_en:"Organic Buchery", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bio_butcher', STYLES: 'A_Klimaschutzk_BioMetzger',}})});
var lyr_bio_bakery = new ol.layer.Tile({title: "Bio-Bäckereien",title_en:"Organic Bakery", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bio_bakery', STYLES: 'A_Klimaschutzk_BioBackery',}})});
var lyr_vegetarian = new ol.layer.Tile({title: "Vegetarische Restaurants/Cafés",title_en:"Vegetarian Restaurants/Cafes ", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vegetarian', STYLES: 'A_Klimaschutzk_RestaurantVegan',}})});
var lyr_vegan = new ol.layer.Tile({title: "Vegane Restaurants/Cafés",title_en:"Vegan Restaurants/Cafes ", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:vegan', STYLES: 'A_Klimaschutzk_RestaurantVegan',}})});
var lyr_repair_electronics = new ol.layer.Tile({title: "Elektronik-Reparatur",title_en:"Repair Electronic Devices", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:repair_electronics', STYLES: 'A_Klimaschutzk_ElektroReperatur',}})});
var lyr_second_hand = new ol.layer.Tile({title: "Gebrauchtwarenladen",title_en:"Second-Hand Shops", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:second_hand', STYLES: 'A_Klimaschutzk_GebrauchtwarenLaden',}})});
var lyr_farm = new ol.layer.Tile({title: "Hofladen",title_en:"Farm Shops", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:farm', STYLES: 'A_Klimaschutzk_HofLaden',}})});
//Kategorie Mobilität 
var lyr_viewpoint = new ol.layer.Tile({title: "Aussichtspunkte",title_en:"Viewpoints", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:viewpoint', STYLES: 'A_Klimaschutzk_ViewPoint',}})});
var lyr_shop_bicycle = new ol.layer.Tile({title: "Fahrradladen",title_en:"Bicycle Shops", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:shop_bicycle', STYLES: 'A_Klimaschutzk_BicycleShop'}})});
var lyr_bicycle_rent = new ol.layer.Tile({title: "Fahrradmietstationen",title_en:"Rent-a-Bike-Stations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bicycle_rental', STYLES: 'A_Klimaschutzk_BicycleRental',}})});
var lyr_car_sharing = new ol.layer.Tile({title: "CarSharing",title_en:"Car Sharing Places", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:car_sharing', STYLES: 'A_Klimaschutzk_CarShareing',}})});
var lyr_bicycle_parking = new ol.layer.Tile({title: "Fahrrad-Stellplätze",title_en:"Bicycle Parkings", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bicycle_parking', STYLES: 'A_Klimaschutzk_BicycleParking',}})});
var lyr_hiking = new ol.layer.Tile({title: "Wanderwege",title_en:"Hiking Tours", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:route_hiking', STYLES: '',}})});
var lyr_route_bicycle = new ol.layer.Tile({title: "Fahrradtouren",title_en:"Bicycle Tours", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:route_bicycle', STYLES: '',}})});
var lyr_bicycle_designated = new ol.layer.Tile({title: "Fahrradwege",title_en:"Cycleways", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:bicycle_way', STYLES: '',}})});
var lyr_charging_station = new ol.layer.Tile({title: "Ladestationen",title_en:"Charging Stations", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:charging_station', STYLES: 'A_Klimaschutzk_ChargingStation',}})});
//Kategorie Wohnen und Gebäude 	
//var lyr_building_av = new ol.layer.Tile({title: "A/V-Index",title_en:"Surface-Area-to-Volume Ratio", visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
//    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'hlorei:hd_buildings_av', STYLES: '',}})});
//=======
//    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:farm', STYLES: '',}})});
//var lyr_building_av = new ol.layer.Tile({title: "A/V-Index",visible: false,source: new ol.source.TileWMS({url: 'https://osmatrix.geog.uni-heidelberg.de/geoserver/hlorei/wms',
//    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'hlorei:hd_buildings_av', STYLES: '',}})});
// >>>>>>> bcab227a893523d4ef9a9ded92a9fd080b25e8e6


var geojsonFormat = new ol.format.GeoJSON();

var lyr_building_av_heatmap_source = new ol.source.Vector({
  format: geojsonFormat,
  
  url: function(extent, resolution, projection) {
    return 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/wfs?service=WFS&' +
        'version=1.1.0&request=GetFeature&typename=urbanoffice:buildings_hd_subtract_centroids&' +
        'outputFormat=application/json' +
        '&srsname=EPSG:3857&bbox=' + extent.join(',') + ',EPSG:3857';
    
      },
      strategy: ol.loadingstrategy.tile(ol.tilegrid.createXYZ({
	    maxZoom: 19
	  }))
        
       });
       
var lyr_building_av_heatmap = new ol.layer.Heatmap({
			title: "Heatmap",
			source:  lyr_building_av_heatmap_source, 
			radius: 40 / map.getView().getResolution(), // ca. 2 bei zoom 14
			blur: 14,
			weight: 'a_v',
			visible:false
       });

// AV Durhschnitt       
var lyr_avavg = new ol.layer.Tile({title: "Durchschnittl. Gebäude- Oberfl./Vol. Index für Heidelberg",title_en:"Building Surface-Area to Volume Ratio (only Heidelberg)",visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:buildingavgs', STYLES: '',}})});       

// Fläche Durchschnitt
//var lyr_areaavg = new ol.layer.Tile({title: "Durchschnittl. Gebäudegrundfläche",visible: false,source: new ol.source.TileWMS({url: 'https://urbanoffice.geog.uni-heidelberg.de/geoserver/urbanoffice/wms',
//    params: {'VERSION': '1.1.1', tiled: true, LAYERS: 'urbanoffice:buildingavgs', STYLES: 'urbanoffice:areaavg',}})}); 

map.getView().on('change:resolution', function(changeEvent) {
	
	// bei zoom 14: radius = ca. 2px (ca. 20m) und blur = 7. Radius zund blur passen sich an resolution der zoomestufe jedesmal neu an, um die Darstellung zu fixieren.
	// 
	
 	lyr_building_av_heatmap.setRadius( 40 / map.getView().getResolution() ); 
 	lyr_building_av_heatmap.setBlur( Math.pow(2, map.getView().getZoom()-14) * 14 );
 	
});

// window.loadFeatures = function(response) {
  // lyr_building_av_heatmap_source.addFeatures(geojsonFormat.readFeatures(response));
// };

// var vector = new ol.layer.Heatmap({
  // source: new ol.source.KML({
    // extractStyles: false,
    // projection: 'EPSG:3857',
    // url: 'data/kml/2012_Earthquakes_Mag5.kml'
  // }),
  // radius: 5
// });


lyrGrp_Energiewende.getLayers().push(lyr_initiatives); 

lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_biogaskw); 
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_biomasskw);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_biooelkw);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_photovoltaic);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_windkraft);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_geothmkw);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_wasserkw);
lyrGrp_Versorgung_Strom.getLayers().push(lyr_vers_gezeitenkw);
lyrGrp_Versorgung_Waerme.getLayers().push(lyr_vers_giogas);
lyrGrp_Versorgung_Waerme.getLayers().push(lyr_vers_biomasse);
lyrGrp_Versorgung_Waerme.getLayers().push(lyr_vers_biooel);
lyrGrp_Versorgung_Waerme.getLayers().push(lyr_vers_soltherm);
lyrGrp_Versorgung_Waerme.getLayers().push(lyr_vers_geothermie);
lyrGrp_Versorgung_KWK.getLayers().push(lyr_vers_kwkanlagen);
lyrGrp_Versorgung_NetzeStrom.getLayers().push(lyr_vers_powerline);
lyrGrp_Versorgung_NetzeStrom.getLayers().push(lyr_vers_powertransformer);
lyrGrp_Versorgung_NetzeStrom.getLayers().push(lyr_vers_substation);
lyrGrp_Versorgung_Speicher.getLayers().push(lyr_vers_stromsp);
lyrGrp_Versorgung_Speicher.getLayers().push(lyr_vers_waermesp);
lyrGrp_Versorgung_Speicher.getLayers().push(lyr_vers_gassp);
lyrGrp_Versorgung_AllgemKW.getLayers().push(lyr_vers_powergenerator);

lyrGrp_Konsum_Saisonal.getLayers().push(lyr_marketplace); 
lyrGrp_Konsum_Saisonal.getLayers().push(lyr_farm); 
lyrGrp_Konsum_Biolog.getLayers().push(lyr_bio_supermarket); 
lyrGrp_Konsum_Biolog.getLayers().push(lyr_bio_butcher); 
lyrGrp_Konsum_Biolog.getLayers().push(lyr_bio_bakery); 
//Vegetarisch/Vegan ernähren
lyrGrp_Konsum_Veg.getLayers().push(lyr_vegetarian); 
lyrGrp_Konsum_Veg.getLayers().push(lyr_vegan); 
lyrGrp_Konsum_Reparieren.getLayers().push(lyr_repair_electronics); 
lyrGrp_Konsum_Gebraucht.getLayers().push(lyr_second_hand); 

lyrGrp_Mobilitaet_Fahrrad.getLayers().push(lyr_route_bicycle);
lyrGrp_Mobilitaet_Fahrrad.getLayers().push(lyr_bicycle_designated);
lyrGrp_Mobilitaet_Fahrrad.getLayers().push(lyr_shop_bicycle);
lyrGrp_Mobilitaet_Fahrrad.getLayers().push(lyr_bicycle_rent);
lyrGrp_Mobilitaet_Fahrrad.getLayers().push(lyr_bicycle_parking);
lyrGrp_Mobilitaet_Elektro.getLayers().push(lyr_charging_station);
lyrGrp_Mobilitaet_Autoteilen.getLayers().push(lyr_car_sharing);
lyrGrp_Mobilitaet_Freizeit.getLayers().push(lyr_viewpoint);
lyrGrp_Mobilitaet_Freizeit.getLayers().push(lyr_hiking);
lyrGrp_Mobilitaet_Freizeit.getLayers().push(lyr_route_bicycle);

//lyrGrp_Wohnen.getLayers().push(lyr_building_av);
//lyrGrp_Wohnen.getLayers().push(lyr_building_av_heatmap);
lyrGrp_Wohnen.getLayers().push(lyr_avavg);
//lyrGrp_Wohnen.getLayers().push(lyr_areaavg);

//Layerswitcher
var lyrSwitcher =  new ol.control.LayerSwitcher({layerProp:'title'});
map.addControl(lyrSwitcher);
lyrSwitcher.showPanel();

$(".layer-switcher .panel").accordion({header:'>ul>li.group>label', heightStyle: "content", collapsible:true});
//inner accordion
$(".layer-switcher .panel>ul>li.group").accordion({header:'>ul>li.group>label', heightStyle: "content", collapsible:true});

$("#ui-id-8").mouseover(function () {
	//alert("saff");
	
});


//Popup
var popup = new ol.Overlay({element: document.getElementById('popup'), offset:[20,0]});
map.addOverlay(popup);
//open/close popup on click
map.on('singleclick', function(evt) {
	
	//hide popup
	popup.setPosition(undefined);
	//clear highlighted features
    clearHighlightLayer();
	
	var viewResolution = /** @type {number} */ (map.getView().getResolution());

	// get all visible WMS layers
	var visibleWMSLayers = [];
    var root = map.getLayerGroup();
    var traverse = function(layer){
    	//console.log((layer instanceof ol.layer.Group)?"group "+layer.get('title') : layer.get('title'));
    	if( layer instanceof ol.layer.Tile && layer.getSource() instanceof ol.source.TileWMS && layer.getVisible() ){
    		visibleWMSLayers.push(layer);
    	}
    	if(layer instanceof ol.layer.Group){
    		var children = layer.getLayers().forEach(function(child){traverse(child);});
    	}
    };
    traverse(root);
    
    // get list of visible layers names
    var visibleWMSLayerNames = [];
    visibleWMSLayers.forEach(function(layer,i,a){visibleWMSLayerNames.push(layer.getSource().getParams().LAYERS);});
    
    //console.log(visibleWMSLayerNames.join(","));
    
    // Stop here if no layers are visible
    if (visibleWMSLayers.length == 0 ) return;
    
    
    var url = visibleWMSLayers[0].getSource().getGetFeatureInfoUrl(evt.coordinate, viewResolution, 'EPSG:3857',
            {'INFO_FORMAT': 'application/json', 'BUFFER':'6', 'LAYERS': visibleWMSLayerNames.join(","), 'QUERY_LAYERS': visibleWMSLayerNames.join(",")});
            
    console.log(url);
    
    if (url) {
    	//AJAX Request to fetch feature info
    	
    	$.getJSON(url, function(data){
    		//console.log(data);
    		
    		// if response is feature info
    		if("type" in data && data.type == "FeatureCollection"){
    			
    			// //clear highlighted features
    			// clearHighlightLayer();
    			
    			//find out layer name
    			if (data.features.length == 0) return;
    			
    			//highlight features
    			highlightFeature(data);
    			
    			//create popup content
    			var layername = data.features[0].id.replace(/(.+)(\..+)/, "$1"); //enthält zB substation oder power_tower etc.
   			
    			//console.log(layername);
    			
    			var featureProperties = data.features[0].properties;
    			var attributes={};
    			var content = ""; //HTML string to be added to popup
    			
    			switch (layername) {
    				case 'power_line':  content += "<h3>Stromleitung</h3>";
    									attributes["Name"] = featureProperties.name 										|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator 								|| "nicht angegeben";
    									attributes["Spannung"]	= featureProperties.voltage 								|| "nicht angegeben";
    									attributes["Frequenz"] 	= featureProperties.frequency 								|| "nicht angegeben";
    									attributes["Anzahl der Leiter"] 	= featureProperties.cables 						|| "nicht angegeben";
    									attributes["Referenznummer"] = featureProperties.ref								|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
					case 'buildingavgs': content +=  "<h3>Gebäude_HD</h3>";
										attributes["Durchschnittl. A/V Index"] =featureProperties.meana_v 					|| "nicht angegeben";
										attributes["Durchschnittl. Gebäude Grundfl."] = featureProperties.meanarea 			|| "nicht angegeben"; 
										break;																		
//    				case 'power_tower':  content += "<h3>Strommasten</h3>";
//    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
//    									attributes["Betreiber"] = featureProperties.operator 	|| "nicht angegeben";
//    									attributes["Typ"] 	= featureProperties.tower_type 		|| "nicht angegeben";
//    									attributes["Design"] 	= featureProperties.design 		|| "nicht angegeben";
//    									attributes["Höhe"] 	= featureProperties.height 			|| "nicht angegeben";
//    									attributes["Material"] 	= featureProperties.material 	|| "nicht angegeben";
//    									attributes["Referenznummer"] = featureProperties.ref 	|| "nicht angegeben";
//    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
//    									break;
    				case 'substation':  content += "<h3>Schaltanlage</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator 	|| "nicht angegeben";
    									attributes["Typ"]		= featureProperties.substation 	|| "nicht angegeben";
    									attributes["Spannung"] 	= featureProperties.voltage 	|| "nicht angegeben";
    									attributes["Referenznummer"] = featureProperties.ref    || "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'power_transformer': 
    									content += "<h3>Transformator</h3>";
    									attributes["Betreiber"] = featureProperties.operator 	|| "nicht angegeben";
    									attributes["Typ"]		= featureProperties.transformer || "nicht angegeben";
    									attributes["Anzahl der Leiter"]  = featureProperties.cables || "nicht angegeben";
    									attributes["Spannung"] 	= featureProperties.voltage 	|| "nicht angegeben";
    									attributes["Frequenz"] 	= featureProperties.frequency 	|| "nicht angegeben";
    									attributes["Referenz Nummer"]	= featureProperties.ref || "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'power_generator': 
    									content += "<h3>Generator</h3>";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator 										|| "nicht angegeben";
    									attributes["Energiequelle"]		= featureProperties.generator_source					    || "nicht angegeben";
    									attributes["Umwandlungsmethode"] 	= featureProperties.generator_method 					|| "nicht angegeben";
    									attributes["Nennleistung"] 	= featureProperties.generator_output_electricity 				|| "nicht angegeben";
    									attributes["Typ"] = featureProperties.generator_type 										|| "nicht angegeben";
    									attributes["Baujahr"] = featureProperties.start_date										|| "nicht angegeben";
    									attributes["OSM ID"] = getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'solar_panels': 
    									content += "<h3>Solaranlage</h3>";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator 										|| "nicht angegeben";
    									attributes["Energiequelle"]		= featureProperties.generator_source					    || "nicht angegeben";
    									attributes["Umwandlungsmethode"] 	= featureProperties.generator_method 					|| "nicht angegeben";
    									attributes["Nennleistung"] 	= featureProperties.generator_output_electricity 				|| "nicht angegeben";
    									attributes["Typ"] = featureProperties.generator_type 										|| "nicht angegeben";
    									attributes["Baujahr"] = featureProperties.start_date										|| "nicht angegeben";
    									attributes["OSM ID"] = getOsmLink(featureProperties.osm_id, data);  										
    									break;
    				case 'route_hiking':  
    									content += "<h3>Wanderweg</h3>";
    									attributes["Netzwerk"] = featureProperties.network 											|| "nicht angegeben";
    									attributes["Betreiber"]	= featureProperties.operator 										|| "nicht angegeben";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Referenznummer"] = featureProperties.ref										|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'viewpoint':  
    									content += "<h3>Aussichtspunkt</h3>";
    									attributes["Höhe"] = featureProperties.ele 													|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]	= featureProperties.wheelchair								|| "nicht angegeben";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Beschreibung"] = featureProperties.decription									|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'route_bicycle':  
    									content += "<h3>Fahrradroute</h3>";
    									attributes["Netzwerk"] = featureProperties.network 											|| "nicht angegeben";
    									attributes["Betreiber"]	= featureProperties.operator 										|| "nicht angegeben";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Referenznummer"] = featureProperties.ref										|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'bicycle_way':  
    									content += "<h3>Fahrradweg</h3>";
    									attributes["Untergrund"] = featureProperties.surface 										|| "nicht angegeben"; 
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data); 
    									break;
    				case 'shop_bicycle': 
    									content += "<h3>Fahrradladen</h3>";
    									attributes["Name"] = featureProperties.name 												|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Website"]	= featureProperties.website											|| "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone										|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 							|| "nicht angegeben";
    									attributes["Mieten"]	= featureProperties.service_bicycle_rental 							|| "nicht angegeben";
    									attributes["Reparieren"]		= featureProperties.service_bicycle_repair					|| "nicht angegeben";
    									attributes["DIY-Reparieren"]	= featureProperties.service_bicycle_diy 					|| "nicht angegeben";
    									attributes["Luftpumpe"]		= featureProperties.service_bicycle_pump						|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'bicycle_rental':  
    									content += "<h3>Fahrradmietstation</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator	|| "nicht angegeben";
    									attributes["Stellplätze"] = featureProperties.capacity 	|| "nicht angegeben";
    									attributes["Kreditkarte"] = featureProperties.payment_credit_cards	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'bicycle_parking':  
    									content += "<h3>Fahrrad-Stellplätze</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["überdacht"] = featureProperties.covered		|| "nicht angegeben";
    									attributes["Stellplätze"] = featureProperties.capacity 	|| "nicht angegeben";
    									attributes["Typ"] = featureProperties.bicycle_parking	|| "nicht angegeben";
    									attributes["Zugang"] = featureProperties.access			|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'car_sharing':  
    									content += "<h3>CarSharing</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator	|| "nicht angegeben";
    									attributes["Stellplätze"] = featureProperties.capacity 	|| "nicht angegeben";
    									attributes["Website"] = featureProperties.website   	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'charging_station':  
    									content += "<h3>Ladestationen</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Betreiber"] = featureProperties.operator	|| "nicht angegeben";
    									attributes["Kapazität"] = featureProperties.capacity 	|| "nicht angegeben";
    									attributes["Aufladen von Autos"] = featureProperties.car	|| "nicht angegeben";
    									attributes["Aufladen von Elektrofahrrädern"] = featureProperties.bicycle 	|| "nicht angegeben";
    									attributes["kostenpflichtig"] = featureProperties.fee	|| "nicht angegeben";
    									attributes["Zugang"] = featureProperties.capacity 	|| "nicht angegeben";
    									attributes["Öffnungszeiten"] = featureProperties.access	|| "nicht angegeben";
    									attributes["max. Stromstärke"] = featureProperties.amperage|| "nicht angegeben";
    									attributes["Spannung"] = featureProperties.voltage 	|| "nicht angegeben";
    									attributes["Referenznummer"] = featureProperties.ref 	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'marketplace':  
    									content += "<h3>Wochenmarkt</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Öffnungszeiten"] = featureProperties.opening_hours 	|| "nicht angegeben";
    									attributes["rollstuhlgerecht"] = featureProperties.wheelchair	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  
    									break;
    				case 'bio_supermarket': 
    									content += "<h3>Bio-Supermarkt</h3>";
    									attributes["Name"] = featureProperties.name 						|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone				|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 	|| "nicht angegeben";
    									attributes["Bio-Produkte"]	= featureProperties.organic 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]		= featureProperties.wheelchair	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'bio_butcher': 
    									content += "<h3>Bio-Metzgerei</h3>";
    									attributes["Name"] = featureProperties.name 						|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone				|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 	|| "nicht angegeben";
    									attributes["Bio-Produkte"]	= featureProperties.organic 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]		= featureProperties.wheelchair	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data); 					
    									break;
    				case 'bio_bakery': 
    									content += "<h3>Bio-Bäckerei</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone				|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 	|| "nicht angegeben";
    									attributes["Bio-Produkte"]	= featureProperties.organic 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"] = featureProperties.wheelchair		|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'vegetarian': 
    									content += "<h3>Vegetarisches Restaurant/Café</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone	|| "nicht angegeben";
    									attributes["Website"]		= featureProperties.website	|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 			|| "nicht angegeben";
    									attributes["Küche"]	= featureProperties.cuisine			|| "nicht angegeben";
    									attributes["Kapazität"]	= featureProperties.capacity			|| "nicht angegeben";
    									attributes["Internetzugang"]	= featureProperties.internet_access			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]		= featureProperties.wheelchair	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'vegan': 
    									content += "<h3>Veganes Restaurant/Café</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone	|| "nicht angegeben";
    									attributes["Website"]		= featureProperties.website	|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 			|| "nicht angegeben";
    									attributes["Küche"]	= featureProperties.cuisine			|| "nicht angegeben";
    									attributes["Kapazität"]	= featureProperties.capacity			|| "nicht angegeben";
    									attributes["Internetzugang"]	= featureProperties.internet_access			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]		= featureProperties.wheelchair	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'repair_electronics': 
    									content += "<h3>Elektronik-Reparatur</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Website"]	= featureProperties.website	|| "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone	|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]	= featureProperties.wheelchair 			|| "nicht angegeben";
    									attributes["gebrauchte Produkte"]		= featureProperties.second_hand	|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'second_hand': 
    									content += "<h3>Gebrauchtwarenladen</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Website"]	= featureProperties.website	|| "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone	|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]	= featureProperties.wheelchair 			|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				case 'farm': 
    									content += "<h3>Hofladen</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["Adresse"] = ((featureProperties.addr_street || "") + " " + (featureProperties.addr_housenumber || "") + " (" + (featureProperties.addr_postcode || "") + " " + (featureProperties.addr_city || "") + ")" ) || "nicht angegeben";
    									attributes["Bio-Produkte"]	= featureProperties.organic 			|| "nicht angegeben";
    									attributes["Website"]	= featureProperties.website	|| "nicht angegeben";
    									attributes["Telefon"]		= featureProperties.phone	|| "nicht angegeben";
    									attributes["Öffnungszeiten"]	= featureProperties.opening_hours 			|| "nicht angegeben";
    									attributes["rollstuhlgerecht"]	= featureProperties.wheelchair 			|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data);  					
    									break;
    				
    				
    				default:			content += "<h3>"+ layername +"</h3>";
    									attributes["Name"] = featureProperties.name 			|| "nicht angegeben";
    									attributes["OSM ID"]	= getOsmLink(featureProperties.osm_id, data); 
    			}
    			
    			//make html table from attributes
    			
    			content += "<table>";
    			
    			for(key in attributes){
    				
    				content += "<tr><th>"+ key +"</th><td>"+ attributes[key] +"</td></tr>";
    				
    			}
    			
    			content += "</table>";
    			
    			popup.getElement().innerHTML = content;
    			
    			popup.setPosition(evt.coordinate);
    				
    		}
    		else {
    			//handle error response
    			console.log(data);
    		}	
    	});
    }
    
   
    
    
}.bind(this));









</script>
<!-- end Simple Custom CSS and JS -->
