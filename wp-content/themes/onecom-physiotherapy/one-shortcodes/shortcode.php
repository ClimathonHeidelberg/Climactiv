<?php
/*
* Create all the Short Code Function here
*
*/

add_action( 'wp_enqueue_scripts', 'one_shortcodes_scripts' );
function one_shortcodes_scripts() {

	$resource_extension = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min'; // Adding .min extension if SCRIPT_DEBUG is enabled
	$resource_min_dir = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-'; // Adding min- as a minified directory of resources if SCRIPT_DEBUG is enabled

	wp_register_style( 'one-shortcode-styles', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'css/shortcode'.$resource_extension.'.css' );
	wp_register_style( 'slick-style', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'css/slick'.$resource_extension.'.css' );
	wp_register_style( 'slick-style-theme', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'css/slick-theme'.$resource_extension.'.css' );
	wp_register_style( 'one-shinybox', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'css/shinybox'.$resource_extension.'.css' );
	wp_register_style( 'one-shortcode-css', get_template_directory_uri() . '/one-shortcodes/min-css/one-shortcodes.min.css' );

    wp_register_script( 'one-shinybox-ios-fix', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'js/ios-orientationchange-fix'.$resource_extension.'.js', array('jquery'), null, true );
	wp_register_script( 'one-shinybox', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'js/shinybox'.$resource_extension.'.js', array('jquery'), null, true );
	wp_register_script( 'slick-slider', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'js/slick'.$resource_extension.'.js', array('jquery'), null, true );
	wp_register_script( 'one-shortcode-scripts', get_template_directory_uri() . '/one-shortcodes/'.$resource_min_dir.'js/shortcode'.$resource_extension.'.js', array('jquery'), null, true );
	wp_register_script( 'one-shortcode-js', get_template_directory_uri() . '/one-shortcodes/min-js/one-shortcodes.min.js', array('jquery'), null, true );
}


/**
* Carousel 
**/
function oc_carousel_callback( $atts, $content = null ){
	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_script( 'one-shortcode-js' );
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_style( 'slick-style' );
		wp_enqueue_style( 'slick-style-theme' );
		wp_enqueue_script( 'slick-slider' );
		wp_enqueue_script( 'one-shortcode-scripts' );
	}

	$atts = shortcode_atts(
		array(
			'adaptive_height' => 'true',
			'autoplay'=>'false',
			'dots'=>'false',
			'infinite'=>'true',
			'pause_on_hover'=>'true',
			'slides_to_show'=> '3',
			'slides_to_scroll'=>'1',
			'speed' => '500',
			'arrows'=>'true'
		),
		$atts
	);

	$data_attrs = '';

	if( $atts[ 'adaptive_height' ] != '' ) {
		$data_attrs .= ' data-adaptive_height="'.$atts[ 'adaptive_height' ].'" ';
	}
	if( $atts[ 'autoplay' ] != '' ) {
		$data_attrs .= ' data-autoplay="'.$atts[ 'autoplay' ].'" ';
	}
	if( $atts[ 'dots' ] != '' ) {
		$data_attrs .= ' data-dots="'.$atts[ 'dots' ].'" ';
	}
	if( $atts[ 'infinite' ] != '' ) {
		$data_attrs .= ' data-infinite="'.$atts[ 'infinite' ].'" ';
	}
	if( $atts[ 'pause_on_hover' ] != '' ) {
		$data_attrs .= ' data-pause_on_hover="'.$atts[ 'pause_on_hover' ].'" ';
	}
	if( $atts[ 'slides_to_show' ] != '' ) {
		$data_attrs .= ' data-slides_to_show="'.$atts[ 'slides_to_show' ].'" ';
	}
	if( $atts[ 'slides_to_scroll' ] != '' ) {
		$data_attrs .= ' data-slides_to_scroll="'.$atts[ 'slides_to_scroll' ].'" ';
	}
	if( $atts[ 'speed' ] != '' ) {
		$data_attrs .= ' data-speed="'.$atts[ 'speed' ].'" ';
	}
	if( $atts[ 'arrows' ] != '' ) {
		$data_attrs .= ' data-arrows="'.$atts[ 'arrows' ].'" ';
	}

	$output = '<div class="one-carousel" '.$data_attrs.'>';
	$output .= do_shortcode($content);
	$output .=  '</div>';
    return $output;	
}
add_shortcode( 'oc_carousel', 'oc_carousel_callback' );

/**
* Vimeo Video
**/
function vimeo_video( $atts ){
	$atts = shortcode_atts(
		array(
			'height'=>'360px',
			'width'=>'100%',
			'url'=>'',
			'mute'=>'',
			'autoplay'=>'',
			'loop'=>'',    
			/*'controls'=>'',  */
		),
		$atts
	);
	$url = $atts['url'];
	$id = (int) substr(parse_url($url, PHP_URL_PATH), 1);

	if( $atts['autoplay'] == 'true' ){
		$autoplay = 1;
	} else {
		$autoplay = 0;
	}

	if( $atts['loop'] == 'true' ){
		$loop = 1;
	} else {
		$loop = 0;
	}
	
	/*if( $atts['controls'] == 'true' || $atts['controls'] == true ) {
		$controls = '1';
	} else {
		$controls = '0';
	}*/
	
	if( $atts['mute'] == 'true' || $atts['mute'] == true ){
		$mute = 1;
	} else {
		$mute = 0;
	}

	$atts[ 'width' ] = one_check_unit( $atts[ 'width' ] );
	$atts[ 'height' ] = one_check_unit( $atts[ 'height' ] );
		 
	$output1 = '<div class="clear"></div><div class="oc-video-container">';
	$output1.='<iframe id= player_' .$id  .'" src="//player.vimeo.com/video/'.$id.'?api=1&player_id=player_' .$id  .'&autoplay='.$autoplay.'&color=ffffff&portrait=0&loop='.$loop.'" width="'.$atts['width'].'" height="'.$atts['height'].'" frameborder="0"webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
	return $output1;
}
add_shortcode( 'oc_vimeo_video', 'vimeo_video' );

/**
* YouTube video
**/
function youtube_video($atts){
	
	$atts = shortcode_atts(
		array(
			'height' => '360',
			'width' => '100%',
			'autoplay' => '',
			'controls' => 'true',
			'url'=>'',
			'mute'=>'',
			'loop'=>''
		),
		$atts
	);
	$url = $atts['url'];
	preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
    $id = isset($matches[1]) ? $matches[1] : null;

    

	if( $atts['autoplay'] == 'true' ){
		$autoplay1 = 1;
	} else {
		$autoplay1 = 0;
	}
	if( $atts['controls'] == 'true' ){
		$controls = 1;
	} else {
		$controls = 0;
	}
	if( $atts['mute'] == 'true' ){
		$mute = 1;
	} else {
		$mute = 0;
	}
	if( $atts['loop'] == 'true' ) {
		$loop = 1;
	} else {
		$loop = 0;			
	}

	$atts[ 'width' ] = one_check_unit( $atts[ 'width' ] );
	$atts[ 'height' ] = one_check_unit( $atts[ 'height' ] );

	$output = '<div class="clear"></div><div class="oc-video-container"><iframe id="player_'.$id.'" width="'.$atts['width'].'" height="'.$atts['height'].'" src="//www.youtube.com/embed/'.$id.'?rel=0&amp;playlist='.$id.'&amp;controls='.$controls.'&amp;enablejsapi='.$mute.'&amp;loop='.$loop.'&amp;autoplay='.$autoplay1.'"> </iframe></div>';
	$output .= "<script>
		var tag = document.createElement('script');
		var tag = document.createElement('script');
		tag.src = \"//www.youtube.com/iframe_api\";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		var player;
		function onYouTubeIframeAPIReady() {
			player = new YT.Player('player_" .$id ."', {
				events: {
					'onReady': onPlayerReady
				}
			});
		}
		function onPlayerReady(event) {
			//player.playVideo();
			event.target.mute();
		}
		</script>";
	return $output;
}
add_shortcode( 'oc_youtube_video', 'youtube_video' );

add_shortcode('oc_video','oc_video');
function oc_video($atts){
	$atts = shortcode_atts(
		array(
			'height' => '360px',
			'width' => '100%',
			'autoplay' => '',
			'controls' => '',
			'mp4_url'=>'',
			'ogv_url'=>'',
			'webm_url'=>'',
			'mute'=>'',
			'loop'=>''
		),
		$atts
	);
	if( $atts['mute'] == 'true' || $atts['mute'] == true ):
		$mute = 'muted';
	else:
		$mute = '';
	endif;
	if( $atts['controls'] == 'true' || $atts['controls'] == true ):
		$controls = 'controls';
	else:
		$controls = '';
	endif;
	if( $atts['loop'] == 'true' || $atts['loop'] == true ):
		$loop = 'loop';
	else:
		$loop = '';
	endif;
	if( $atts['autoplay'] == 'true' || $atts['autoplay'] == true ):       
		$autoplay = 'autoplay'	;
	else:
		$autoplay = '';
	endif;	 
	if( $atts['mp4_url'] !=='' || $atts['ogv_url'] !=='' || $atts['webm_url'] !=='' ) { 
		$output = '<video  class="" width="'.$atts['width'].'" height="'.$atts['height'].'" '.$atts['mute'].' '.$atts['controls'].' '.$atts['loop'].' '.$atts['autoplay'].'>';
			$output .= '<source src="'.esc_url($atts['mp4_url']).'" type="video/mp4">
		    <source src="'.esc_url($atts['ogv_url']).'" type="video/mp4">
			<source src="'.esc_url($atts['webm_url']).'" type="video/mp4">';      
         $output .= '</video>';
	}
	return $output;
}

/*Column shortcode*/

function one_column_callback( $atts, $content ) {
	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_style( 'one-shortcode-styles' );
	}

	$atts = shortcode_atts(
		array(
			'class'=>'',
			'text_align'=>'',
			'background_color'=>'',
			'color'=>'',
			'size' => '',
			'last' => '',
			'min_height' => '',
			'padding' => ''
		),
		$atts
	);

	$class = ( isset( $atts[ 'class' ] ) && $atts[ 'class' ] != '' ) ? $atts[ 'class' ].' ' : '';

	switch ( $atts[ 'size' ] ) {
		case '1/1':
			$class .= 'twelve';
		break;
		case '1/2':
			$class .= 'six';
		break;
		case '1/3':
			$class .= 'four';
		break;
		case '1/4':
			$class .= 'three';
		break;
		case '1/6':
			$class .= 'two';
		break;
		case '2/3':
			$class .= 'eight';
		break;
		case '3/4':
			$class .= 'nine';
		break;
		case '5/6':
			$class .= 'ten';
		break;
		case '1/12':
			$class .= 'one';
		break;
		default:
			$class .= 'twelve';
		break;
	}

	$col_style = '';

	if( $atts['last'] == true || $atts['last'] == 'true' ) {
		$class .= ' last';
	}

	if( isset( $atts['background_color'] ) && $atts['background_color'] != '' ) {
		$col_style .= 'background-color:'.$atts['background_color'].';';
	}
	if( isset( $atts['text_align'] ) && $atts['text_align'] != '' ) {
		$col_style .= 'text-align:'.$atts['text_align'].';';
	}
	if( isset( $atts['color'] ) && $atts['color'] != '' ) {
		$col_style .= 'color:'.$atts['color'].';';
	}
	if( isset( $atts['padding'] ) && $atts['padding'] != '' ) {
		$col_style .= 'padding:'.$atts['padding'].';';
	}
	if( isset( $atts['min_height'] ) && $atts['min_height'] != '' ) {
		$atts[ 'min_height' ] = one_check_unit( $atts['min_height'] );
		$col_style .= 'min-height:'.$atts['min_height'].';';
	}

	return '<div class="one-column '.$class.'" style="'.$col_style.'">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'oc_column', 'one_column_callback' );

function oc_spacer($atts){
	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_style( 'one-shortcode-styles' );
	}

	$atts = shortcode_atts(
		array(
			'height'=>'10'
		),
		$atts
	);
	
	$atts[ 'height' ] = one_check_unit( $atts[ 'height' ] );
	
	return '<span class="one-spacer" style="height:'.$atts['height'].'"></span>';
}
add_shortcode( 'oc_spacer','oc_spacer' );

function oc_fullwidth( $atts, $content = null ){
	
	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_script( 'one-shortcode-js' );
	} else {
		wp_enqueue_script( 'one-shortcode-scripts' );
	}
	$atts = shortcode_atts(
		array(
			'background_color' => '',
			'background_image' => ''
		),
		$atts
	);

	$output = '<div class="one-fullwidth-section" data-background_color="'.$atts[ 'background_color' ].'" data-background_image="'.$atts[ 'background_image' ].'" >'.do_shortcode($content).'</div>';
	return $output;
}
add_shortcode('oc_fullwidth_section','oc_fullwidth');

function oc_title_subtitle($atts){

	$atts = shortcode_atts(
		array(
			'title_tag'=>'h2',
			'subtitle_tag'=>'h4',
			'title'=>'',
			'subtitle'=>'',
			'title_color'=>'',
			'title_font_size'=>'',
			'title_line_height'=>'',
			'subtitle_color'=>'',
			'subtitle_font_size'=>'',
			'subtitle_line_height'=>'',
			'text_align' => 'center',
			'class' => '',
		),
		$atts
	);

	$title_style = $subtitle_style = $title_class = '';

	if( $atts[ 'title_color' ] != '' ) {
		$title_style .= 'color:'.$atts['title_color'].';';
	}
	if( $atts['title_font_size'] != '' ) {
		$atts[ 'title_font_size' ] = one_check_unit( $atts['title_font_size'] );
		$title_style .= 'font-size:'.$atts['title_font_size'].';';
	}
	if( $atts['title_line_height'] != '' ) {
		$atts[ 'title_line_height' ] = one_check_unit( $atts['title_line_height'] );
		$title_style .= 'line-height:'.$atts['title_line_height'].';';
	}

	if( $atts[ 'subtitle_color' ] != '' ) {
		$subtitle_style .= 'color:'.$atts['subtitle_color'].';';
	}
	if( $atts['subtitle_font_size'] != '' ) {
		$atts[ 'subtitle_font_size' ] = one_check_unit( $atts['subtitle_font_size'] );
		$subtitle_style .= 'font-size:'.$atts['subtitle_font_size'].';';
	}
	if( $atts['subtitle_line_height'] != '' ) {
		$atts[ 'subtitle_line_height' ] = one_check_unit( $atts['subtitle_line_height'] );
		$title_style .= 'line-height:'.$atts['subtitle_line_height'].';';
	}

	if( $atts['class'] != '' ) {
		$title_class = $atts['class'];
	}	

	$output = '<div class="one-title" style="text-align:'.$atts[ 'text_align' ].'">';
	if( $atts['title'] != '' ) :
		$output .= '<'.$atts['title_tag'].' style="'.$title_style.'" class="'.$title_class.'">'.$atts['title'].'</'.$atts['title_tag'].'>';
	endif;
	if( $atts['subtitle'] != '' ) :
		$output .= '<'.$atts['subtitle_tag'].' style="'.$subtitle_style.'">'.$atts['subtitle'].'</'.$atts['subtitle_tag'].'>';
	endif;
	$output .= '</div>';
	return $output;
}
add_shortcode('oc_title','oc_title_subtitle');

function one_map_callback($atts) {
	extract( shortcode_atts( array(
		'api_key' => false,
		'id' => 'map-canvas-1',
		'class' => '',
		'zoom' => '18',
		'latitude' => '53.339381',
		'longitude' => '-6.260405',
		'type' => 'roadmap',
		'width' => '100%',
		'height' => '360px'
		
	), $atts ) );
	
	wp_enqueue_script( 'one-map-js', "https://maps.googleapis.com/maps/api/js?key=" . $api_key . "&sensor=true", "jquery");
	
	$return = "";
	
	$map_type_id = "google.maps.MapTypeId.ROADMAP";
	
	switch ($type) {
		case "roadmap":
			$map_type_id = "google.maps.MapTypeId.ROADMAP";
			break;
		case "satellite":
			$map_type_id = "google.maps.MapTypeId.SATELLITE";
			break;
		case "hybrid":
			$map_type_id = "google.maps.MapTypeId.HYBRID";
			break;
		case "terrain":
			$map_type_id = "google.maps.MapTypeId.TERRAIN";
			break;
	}
	
	if($api_key) {
		$map_style = '';

		if( $width != '' ) {
			$map_style .= 'width:'.$width.';';
		}
		if( $height != '' ) {
			$height = one_check_unit( $height );
			$map_style .= 'height:'.$height.';';
		}

		$return = '<div id="'.$id.'" class="map-canvas '.$class.'" style="'.$map_style.'" ></div>';
		
		$return .= '<script type="text/javascript">';
		$return .= 'jQuery(document).on("ready", function(){ ';
		$return .= 'var options = { center: new google.maps.LatLng('.$latitude.','.$longitude.'),';
		$return .= 'zoom: ' . $zoom . ', mapTypeId: ' . $map_type_id . ' };';
		$return .= 'var map = new google.maps.Map(document.getElementById("'.$id.'"), options);';
		$return .= 'var marker = new google.maps.Marker({ position: new google.maps.LatLng('.$latitude.','.$longitude.'), map: map });';
		$return .= '});</script>';
		
	} else {
		$return = "<div><p>Please specify your Google Maps API key</p></div>";
	}
	
	return $return;		
}
add_shortcode('oc_map', 'one_map_callback');


function oc_button_callback($atts){

	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_script( 'one-shortcode-js' );
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_script( 'one-shortcode-scripts' );
		wp_enqueue_style( 'one-shortcode-styles' );
	}

	$atts = shortcode_atts(
		array(
			'text' => 'Click Here',
			'link' => '',
			'width' => '',
			'height' => '',
			'font_size' => '',
			'line_height' => '',
			'background_color' => '',
			'background_hover_color' => '',
			'color' => '',
			'color_hover' => '',
			'border_color' => '',
			'border_hover_color' => '',
			'border_width' => '',
			'border_radius' => '0',
			'icon' => '',
			'icon_align' => 'left',
			'icon_size' => '20',
			'class' => ''
		),
		$atts
	);

	$button_style = $icon_style = $data_attrs = $icon = '';

	if( $atts['font_size'] != '' ) {
		$atts['font_size'] = one_check_unit( $atts[ 'font_size' ] );
		$button_style .= 'font-size:'.$atts['font_size'].';';
	}
	if( $atts['line_height'] != '' ) {
		$atts['line_height'] = one_check_unit( $atts[ 'line_height' ] );
		$button_style .= 'line-height:'.$atts['line_height'].';';
	}
	if( $atts['background_color'] != '' ) {
		$button_style .= 'background-color:'.$atts['background_color'].';';
	}
	if( $atts['color'] != '' ) {
		$button_style .= 'color:'.$atts['color'].';';
	}
	if( $atts[ 'border_width' ] != '' ) {
		$atts['border_width'] = one_check_unit( $atts['border_width'] );
		
		$button_style .= 'border-width:'.$atts['border_width'].';';
		$button_style .= 'border-color:'.$atts['border_color'].';';
	}
	if( $atts[ 'width' ] != '' ) { 
		$atts['width'] = one_check_unit( $atts['width'] );
		$button_style .= 'width:'.$atts['width'].';';
	}
	if( $atts[ 'height' ] != '' ) { 
		$atts['height'] = one_check_unit( $atts['height'] );
		$button_style .= 'height:'.$atts['height'].';';
	}

	$data_attrs .= ' data-background_color = "'.$atts[ 'background_color' ].'" ';
	$data_attrs .= ' data-background_hover_color = "'.$atts[ 'background_hover_color' ].'" ';
	$data_attrs .= ' data-color = "'.$atts[ 'color' ].'" ';
	$data_attrs .= ' data-color_hover = "'.$atts[ 'color_hover' ].'" ';
	$data_attrs .= ' data-border_color = "'.$atts[ 'border_color' ].'" ';
	$data_attrs .= ' data-border_hover_color = "'.$atts[ 'border_hover_color' ].'" ';

	if( $atts[ 'icon' ] != '' ) {
		if( one_is_url_image( $atts[ 'icon' ] ) ) {
			if( $atts[ 'icon_size' ] != '' ) { 
				$atts['icon_size'] = one_check_unit( $atts['icon_size'] );
				$icon_style .= 'height:'.$atts['icon_size'].';';
			}
			$icon = '<img src="'.$atts[ 'icon' ].'" class="one-button-icon-image" alt="icon" style="'. $icon_style.'" />';
		} else {
			if( $atts[ 'icon_size' ] != '' ) { 
				$atts['icon_size'] = one_check_unit( $atts['icon_size'] );
				$icon_style .= 'font-size:'.$atts['icon_size'].';';
			}
			$icon = '<i class="'.$atts[ 'icon' ].' one-button-icon" style="'.$icon_style.'"></i>';
		}
	}

	if( $atts[ 'link' ] != '' ) {
		$output = '<a href="'.$atts['link'].'" class="one-button '.$atts['class'].' one-button-'.$atts[ 'icon_align' ].'-align " style="'.$button_style.'" '.$data_attrs.'>';
			if( $atts[ 'icon_align' ] == 'left' ) {
				$output .= $icon;
			}
		$output .= '<span class="one-button-text">'.$atts['text'].'</span>';
			if( $atts[ 'icon_align' ] == 'right' ) {
				$output .= $icon;
			}
		$output .= '</a>';	
	} else {
		$output = '<button class="one-button '.$atts['class'].' one-button-'.$atts[ 'icon_align' ].'-align" style="'.$button_style.'" '.$data_attrs.'>'.$atts[ 'text' ].'</button>';
	}
	
	return $output;
}
add_shortcode( 'oc_button','oc_button_callback' );

/**
* Icon Box
**/
function oc_icon_box_callback( $atts, $content ) {

	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_script( 'one-shortcode-js' );
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_script( 'one-shortcode-scripts' );
		wp_enqueue_style( 'one-shortcode-styles' );
	}

	$atts = shortcode_atts(
		array(
			'title' => '',
			//'link' => '',
			'title_font_size' => '',
			'title_line_height' => '',
			'title_color' => '',
			'desc_font_size' => '',
			'desc_line_height' => '',
			'desc_color' => '',
			'icon' => '',
			'icon_align' => 'top',
			'icon_size' => '50',
			'icon_color' => '',
			'icon_hover_color' => '',
			'icon_border_color' => '',
			'icon_border_hover_color' => '',
			'icon_border_width' => '',
			'icon_border_radius' => '0',
			'icon_background_color' => '',
			'icon_background_hover_color' => '',
			'class' => ''
		),
		$atts
	);

	$output = $icon = $icon_box_style = $title_style = $desc_style = $icon_style = $data_attrs = '';

	if( $atts[ 'title_font_size' ] != '' ) {
		$atts['title_font_size'] = one_check_unit( $atts['title_font_size'] );
		$title_style .= 'font-size: '.$atts['title_font_size'].';';
	}
	if( $atts[ 'title_line_height' ] != '' ) {
		$atts['title_line_height'] = one_check_unit( $atts['title_line_height'] );
		$title_style .= 'line-height: '.$atts['title_line_height'].';';
	}
	if( $atts[ 'title_color' ] != '' ) { 
		$title_style .= 'color:'.$atts['title_color'].';';
	}

	if( $atts[ 'desc_font_size' ] != '' ) {
		$atts['desc_font_size'] = one_check_unit( $atts['desc_font_size'] );
		$desc_style .= 'font-size: '.$atts['desc_font_size'].';';
	}
	if( $atts[ 'desc_line_height' ] != '' ) {
		$atts['desc_line_height'] = one_check_unit( $atts['desc_line_height'] );
		$desc_style .= 'line-height: '.$atts['desc_line_height'].';';
	}
	if( $atts[ 'desc_color' ] != '' ) { 
		$desc_style .= 'color:'.$atts['desc_color'].';';
	}

	if( $atts[ 'icon_color' ] != '' ) { 
		$icon_style .= 'color:'.$atts['icon_color'].';';
	}
	if( $atts[ 'icon_border_color' ] != '' ) { 
		$icon_style .= 'border-color:'.$atts['icon_border_color'].';';
	}
	if( $atts[ 'icon_border_width' ] != '' ) { 
		$atts['icon_border_width'] = one_check_unit( $atts['icon_border_width'] );
		$icon_style .= 'border-width:'.$atts['icon_border_width'].';';
	}
	if( $atts[ 'icon_border_radius' ] != '' ) { 
		$atts['icon_border_radius'] = one_check_unit( $atts['icon_border_radius'] );
		$icon_style .= 'border-radius:'.$atts['icon_border_radius'].';';
	}
	if( $atts[ 'icon_background_color' ] != '' ) {
		$atts[ 'class' ] .= ' one-box-icon-with-background ';
		$icon_style .= 'background-color:'.$atts['icon_background_color'].';';
	}

	$data_attrs .= ' data-icon_color="'.$atts[ 'icon_color' ].'" ';
	$data_attrs .= ' data-icon_hover_color="'.$atts[ 'icon_hover_color' ].'" ';
	$data_attrs .= ' data-icon_border_color="'.$atts[ 'icon_border_color' ].'" ';
	$data_attrs .= ' data-icon_border_hover_color="'.$atts[ 'icon_border_hover_color' ].'" ';
	$data_attrs .= ' data-icon_background_color="'.$atts[ 'icon_background_color' ].'" ';
	$data_attrs .= ' data-icon_background_hover_color="'.$atts[ 'icon_background_hover_color' ].'" ';

	if( $atts[ 'icon' ] != '' ) {
		if( one_is_url_image( $atts[ 'icon' ] ) ) {
			if( $atts[ 'icon_size' ] != '' ) { 
				$atts['icon_size'] = one_check_unit( $atts['icon_size'] );
				$icon_style .= 'width:'.$atts['icon_size'].';';
			}
			$icon = '<div class="one-icon-box-main-icon"><img src="'.$atts[ 'icon' ].'" class="one-icon-box-image" alt="icon" style="'. $icon_style.'" /></div>';
		} else {
			if( $atts[ 'icon_size' ] != '' ) { 
				$atts['icon_size'] = one_check_unit( $atts['icon_size'] );
				$icon_style .= 'font-size:'.$atts['icon_size'].';';
			}
			$icon = '<i class="'.$atts[ 'icon' ].' one-icon-box-icon one-icon-box-main-icon" style="'.$icon_style.'"></i>';
		}
	}

	$output = '<div class="one-icon-box '.$atts[ 'class' ].'" '.$data_attrs.'>';
		$output .= '<div class="one-icon-box-top icon-align-'.$atts[ 'icon_align' ].'">';
			if( $atts[ 'icon_align' ] == 'top' || $atts[ 'icon_align' ] == 'left' ) {
				$output .= $icon;
			}
			$output .= '<div class="one-icon-box-description">';
			if( trim( $atts[ 'title' ] ) != '' ) :
				$output .= '<h3 class="one-icon-box-title" style="'.$title_style.'">';
					$output .= $atts[ 'title' ];
				$output .= '</h3>';
			endif;
			if( trim( $content ) != '' ) :
				$output .= '<div class="one-icon-box-description-inner" style="'.$desc_style.'">';
					$output .= do_shortcode( $content );
				$output .= '</div>';
			endif;
			$output .= '</div>';
			if( $atts[ 'icon_align' ] == 'right' ) {
				$output .= $icon;
			}
		$output .= '</div>';
	$output .= '</div>';

	return $output;

}
add_shortcode( 'oc_icon_box','oc_icon_box_callback' );

/**
* Function 
**/

/**
* Function to check if input has unit or not. If not default px will be set
**/
function one_check_unit( $input ) {
	$last_two_char = substr($input, -2);
	if( is_numeric( $last_two_char ) ) {
		$input .= 'px';
	}
	return $input;
}

/**
*  Function to open gallery image into shinybox
**/
add_filter("post_gallery", "one_gallery_shinybox",10,2);
function one_gallery_shinybox($output, $attr){
	if( (WP_DEBUG != true || WP_DEBUG != 'true' ) && (SCRIPT_DEBUG != true || SCRIPT_DEBUG != 'true' ) ) {
		wp_enqueue_script( 'one-shortcode-js' );
		wp_enqueue_style( 'one-shortcode-css' );
	} else {
		wp_enqueue_script( 'one-shinybox-ios-fix' );
		wp_enqueue_script( 'one-shinybox' );
		wp_enqueue_style( 'one-shinybox' );
		wp_enqueue_script( 'one-shortcode-scripts' );
		wp_enqueue_style( 'one-shortcode-styles' );
	}
}

/**
* Function to change type of gallery to always open in shinybox
**/
function file_gallery_shortcode( $atts ) {
    $atts['link'] = 'file';
	$atts['size'] = 'medium_featured';
    return gallery_shortcode( $atts );
}
add_shortcode( 'gallery', 'file_gallery_shortcode' );

/**
* Function to check if URL is image
**/
function one_is_url_image($filename) {
   $regex = '/\.(jpe?g|bmp|png|JPE?G|BMP|PNG)(?:[\?\#].*)?$/';
   return preg_match($regex, $filename);
}