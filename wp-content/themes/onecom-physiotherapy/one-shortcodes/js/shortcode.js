(function( $ ){

	$( document ).on( 'resize_one_fullwidth_section', function(){
		var widndow_width = $( window ).width();
		$( '.one-fullwidth-section' ).each( function( i, section ) {
			$( section ).css( { 'margin-left' : '0' } );
			var section_offset = $( section ).offset().left;
			$( section ).css( { 'width' : widndow_width, 'margin-left' : -section_offset } );
		} );
	} );

	$( document ).ready( function() {

		/* 
			On document ready resize full width section
		*/
		$( document ).trigger( 'resize_one_fullwidth_section' );

		$( '.one-fullwidth-section' ).each( function( i, section ) {
			var section_bgcolor = $( section ).attr( 'data-background_color' );
			var section_bgimage = $( section ).attr( 'data-background_image' );
			if( typeof section_bgcolor != 'undefined' ) {
				$( section ).css( { 'background-color' : section_bgcolor } );
			}
			if( typeof section_bgimage != 'undefined' || section_bgimage != '' ) {
				$( section ).css( { 'background-image' : 'url( '+section_bgimage+' )' } );
			}
		} );

		/*
			On button hover
		*/
		$( '.one-button' ).hover(
			function(){
				var background_hover_color = $( this ).attr( 'data-background_hover_color' );
				var color_hover = $( this ).attr( 'data-color_hover' );
				var border_hover_color = $( this ).attr( 'data-border_hover_color' );

				if( typeof background_hover_color != 'undefined' ) {
					$( this ).css( { 'background-color' : background_hover_color });
				}
				if( typeof color_hover != 'undefined' ) {
					$( this ).css( { 'color' : color_hover } );
				}
				if( typeof border_hover_color != 'undefined' ) {
					$( this ).css( { 'border-color' : border_hover_color } );
				}
			},
			function(){
				var background_color = $( this ).attr( 'data-background_color' );
				var color = $( this ).attr( 'data-color' );
				var border_color = $( this ).attr( 'data-border_color' );

				if( typeof background_color != 'undefined' ) {
					$( this ).css( { 'background-color' : background_color });
				}
				if( typeof color != 'undefined' ) {
					$( this ).css( { 'color' : color } );
				}
				if( typeof border_color != 'undefined' ) {
					$( this ).css( { 'border-color' : border_color } );
				}
			}
		);

		/*
			Carousel init
		*/
		$( '.one-carousel' ).each( function( i, carousel ){
			var adaptive_height = $( carousel ).attr( 'data-adaptive_height' );
			var autoplay = $( carousel ).attr( 'data-autoplay' );
			var dots = $( carousel ).attr( 'data-dots' );
			var infinite = $( carousel ).attr( 'data-infinite' );
			var pause_on_hover = $( carousel ).attr( 'data-pause_on_hover' );
			var slides_to_show = $( carousel ).attr( 'data-slides_to_show' );
			var slides_to_scroll = $( carousel ).attr( 'data-slides_to_scroll' );
			var speed = $( carousel ).attr( 'data-speed' );
			var arrows = $( carousel ).attr( 'data-arrows' );

			$(carousel).slick({
				adaptiveHeight : JSON.parse( adaptive_height ), /*  JSON.parse used to convert true / false string to boolean value */
				autoplay: JSON.parse( autoplay ),
				dots: JSON.parse( dots ),
		        infinite: JSON.parse( infinite ),
				speed: parseInt( speed ),
				slidesToShow: parseInt( slides_to_show ),
				slidesToScroll: parseInt( slides_to_scroll ),
				arrows: JSON.parse( arrows ),
				pauseOnHover: JSON.parse( pause_on_hover ),
				responsive: [
					{
					  	breakpoint: 768,
					  	settings: {
							arrows: false,
							centerMode: true,
							centerPadding: "40px",
							autoplay : true,
							slidesToShow: 1
					  	}
					}, 
					{
						breakpoint: 480,
					  	settings: {
							arrows: false,
							centerMode: true,
							centerPadding: "40px",
							autoplay : true,
							slidesToShow: 1
					  	}
					}
				]
		    });
		} );

		/*
			Icon box 
		*/
		$( '.one-icon-box' ).each( function( i, icon_box ){
			var icon_color = $( icon_box ).attr( 'data-icon_color' );
			var icon_hover_color = $( icon_box ).attr( 'data-icon_hover_color' );
			var icon_border_color = $( icon_box ).attr( 'data-icon_border_color' );
			var icon_border_hover_color = $( icon_box ).attr( 'data-icon_border_hover_color' );
			var icon_background_color = $( icon_box ).attr( 'data-icon_background_color' );
			var icon_background_hover_color = $( icon_box ).attr( 'data-icon_background_hover_color' );

			var icon = $( icon_box ).find( '.one-icon-box-main-icon' );

			$( icon_box ).hover(
				function(){
					$( icon ).addClass( 'icon-active' );
					if( typeof icon_hover_color != 'undefined' && icon_hover_color != '' ) {
						$( icon ).css( { 'color' : icon_hover_color } );
					}
					if( typeof icon_border_hover_color != 'undefined' && icon_border_hover_color != '' ) {
						$( icon ).css( { 'border-color' : icon_border_hover_color } );
					}
					if( typeof icon_background_hover_color != 'undefined' && icon_background_hover_color != '' ) {
						$( icon ).css( { 'background-color' : icon_background_hover_color } );
					}
				},
				function(){
					$( icon ).removeClass( 'icon-active' );
					if( typeof icon_color != 'undefined' && icon_color != '' ) {
						$( icon ).css( { 'color' : icon_color } );
					}
					if( typeof icon_border_color != 'undefined' && icon_border_color != '' ) {
						$( icon ).css( { 'border-color' : icon_border_color } );
					}
					if( typeof icon_background_color != 'undefined' && icon_background_color != '' ) {
						$( icon ).css( { 'background-color' : icon_background_color } );
					}
				}
			);
		} );

		/* 
			Fancybox init
		*/
		try {

        	if($("figure.wp-caption").length){
        		$("figure.wp-caption").each(function(i, v){
					var anchor = $(this).find('a');
					var caption = $(this).find('figcaption').text();
					$(anchor).attr('title', caption);
				});
			}
            $(".gallery-item .gallery-icon a, figure.wp-caption a").addClass('shinybox');
			$(".shinybox").unbind('click').shinybox();
	    } catch(e) {}
	} );

	$( window ).load( function() {

		/* 
			On window load resize full width section
		*/
		$( document ).trigger( 'resize_one_fullwidth_section' );

	} );

	$( window ).resize( function() {

		/* 
			On window resize, resize full width section
		*/
		$( document ).trigger( 'resize_one_fullwidth_section' );

	} );

})( jQuery );