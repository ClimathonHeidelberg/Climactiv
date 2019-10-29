<?php
/*@header("Content-type: text/css");*/
function ot_check_css_prop($prop, $val, $val2 = '')
{
    if (!(isset($prop) && strlen($prop))) return;

    if (isset($val) && !is_array($val) && strlen($val)) {
        if (isset($val2) && strlen($val2)) {
            return sprintf($prop, $val, $val2);
        }
        return sprintf($prop, $val);
    }
    return;
}

function ot_get_bg_image_src($id)
{

    if (!isset($id) && !strlen($id)) return;
    if (is_numeric($id)) {
        $img = wp_get_attachment_image_src($id, 'large');
        if (!empty($img)) {
            $img_src = $img[0];
            return $img_src;
        }
        return;
    }
    return $id;
}

function ot_check_bg_css_prop($pairs)
{
    if (!(isset($pairs) && is_array($pairs))) return;
    $pairs = array_filter($pairs, 'strlen');
    $css = '';
    foreach ($pairs as $key => $prop) {
        $css .= ('background-image' === $key) ? sprintf('%s:url(%s);', $key, ot_get_bg_image_src($prop)) : sprintf('%s:%s;', $key, $prop);
    }
    return $css;
}

function ot_check_font_css_prop($pairs)
{
    if (!(isset($pairs) && is_array($pairs))) return;
    $pairs = array_filter($pairs, 'strlen');
    $css = '';
    foreach ($pairs as $key => $prop) {
        $css .= ('font-family' === $key) ? sprintf('%s:%s;', $key, ot_google_font_family($prop)) : sprintf('%s:%s;', $key, $prop);
    }
    return $css;
} ?>
<style type="text/css">

    <?php

    ################################
    ########  Skin Styles  #########
    ################################

    $skin_primary = ot_get_option('skin_primary');
    $skin_secondary = ot_get_option('skin_secondary');
    $skin_switch = ot_get_option('custom_skin_switch');
    if(!empty($skin_primary) && !empty($skin_secondary) && $skin_switch != 'off'){?>
    /* Primary Skin Color */
    .site-logo a,
    .site-logo h1,
    #primary-nav ul,
    .footer-widgets .widget_calendar table thead,
    .footer-widgets .widget_calendar table th,
    .comment-reply-link:hover,
    .button, .button:visited,
    a.page-numbers,
    .primary_color {
        color: <?php echo $skin_primary; ?>;
    }

    #primary-nav ul li.current_page_item a,
    #primary-nav ul li ul li a,
    #primary-nav ul li:hover > a,
    .page-numbers.current,
    .page-numbers:hover,
    .next.page-numbers:hover,
    .searchform input[type=submit],
    .comment-reply-link,
    .footer-widgets,
    .widget_calendar table thead,
    .primary_bgcolor {
        background-color: <?php echo $skin_primary; ?>;
    }


    .prev.page-numbers, .next.page-numbers,
    .post-comments textarea,
    .comment-reply-link:hover,
    .comment-reply-link,
    .button.border:hover,
    .button,
    .button.dark,
    .button.border {
        border-color: <?php echo $skin_primary; ?>;
    }

    .social-icons ul li a svg * {
        fill: <?php echo $skin_primary; ?>;
    }


    /* Secondary Skin Color */
    .button.dark,
    .footer-widgets .widget_calendar table thead,
    .secondary_color {
        color: <?php echo $skin_secondary; ?>;
    }

    .footer-widgets .social-icons ul li a svg * {
        /* fill:



    <?php echo $skin_secondary; ?>    ;*/
    }

    .footer-widgets .widget_calendar table thead,
    .secondary_bgcolor {
        background-color: <?php echo $skin_secondary; ?>;
    }


    .footer-widgets .widget_calendar table,
    .footer-widgets .widget_calendar table tfoot,
    .secondary_bordercolor {
        border-color: <?php echo $skin_secondary; ?>;
    }

    <?php }


        /* Header Height */
        $header_height = ot_get_option('header_height');
        if(!empty($header_height)){

            echo 'header#site-header{'.
                ot_check_css_prop('min-height:%s%s;', $header_height[0], $header_height[1]).
            '}';

        }

        /* Home Banner Height */
        $homepage_id = get_option('page_on_front');
        if(isset($homepage_id) && strlen($homepage_id)){
            /* Apply this css only if custom height selected */
            $hbanner_height_mode = get_post_meta($homepage_id, 'banner_height', true);
            if(isset($hbanner_height_mode) && 'custom_height' === $hbanner_height_mode){

                $hbanner_height = get_post_meta($homepage_id, 'hbanner_height', true);

                if(!empty($hbanner_height)){

                echo '.banner.home-banner{'.
                    ot_check_css_prop('height:%s%s;', $hbanner_height[0], $hbanner_height[1]).
                '}';

                }
            }
        }


        /* Internal Banner Height */
        $intbanner_height_mode = get_post_meta(get_the_ID(), 'int_banner_height', true);
        if(isset($intbanner_height_mode) && 'custom_height' === $intbanner_height_mode){
            $intbanner_height = get_post_meta(get_the_ID(), 'intbanner_height', true);
            if(!empty($intbanner_height)){

                echo '.banner.internal-banner{'.
                    ot_check_css_prop('height:%s%s;', $intbanner_height[0], $intbanner_height[1]).
                '}';
            }
        }

/* ################################## Header Logo Options ################################# */
/* Header Height */
$header_logo_height = ot_get_option('header_logo_height');
if (!empty($header_logo_height)) {
	echo '.oct-site-logo img{' .
	ot_check_css_prop('max-height:%s%s;', $header_logo_height[0], $header_logo_height[1]) .
	'}';
	}
        /* Logo Font Style */
        $logo_typo = ot_get_option('logo_typo');
        if(!empty($logo_typo)) printf(".oct-site-logo a, h1.site-title a{%s}", ot_check_font_css_prop($logo_typo));

        /* Header Menu Font Style */
        $menu_typo = ot_get_option('menu_typo');
        if(!empty($menu_typo)){
            echo '#primary-nav ul li a{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($menu_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $menu_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $menu_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $menu_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $menu_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $menu_typo['text-decoration']).
            '}';
        }


        /* Body Font Style */
        $body_typo = ot_get_option('body_typo');
        if(!empty($body_typo)){
            echo 'body, p, .section-content p, .post-content,.post-content p, .page-content, .page-content p, .cpt-excerpt, .cpt-excerpt p, .cpt-content, .cpt-content:not(.dashicons), .cpt-content p, .sidebar, .textnormal, .textwidget p, .benefit-content p, .testimonial_text_wrap p{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($body_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $body_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $body_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $body_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $body_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $body_typo['text-decoration']).
            '}';
        }

        /* Secondary Font Style */
        $secondary_typo = ot_get_option('secondf_typo');
        if(!empty($secondary_typo)){
            echo '.cursive-font, .post-comments #respond h3, .footer-widgets .widget-title, .banner-caption .sub-title{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($secondary_typo['font-family'])).
            '}';
        }

        /* H1 = Heading Font Style */
        $h1_typo = ot_get_option('h1_typo');
        if(!empty($h1_typo)){
            echo 'h1, .page-content h1, .post-content h1, cpt-content h1{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h1_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h1_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h1_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h1_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h1_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h1_typo['text-decoration']).
            '}';
        }

        /* H2 = Heading Font Style */
        $h2_typo = ot_get_option('h2_typo');
        if(!empty($h2_typo)){
            echo 'h2, .page-content h2, .post-content h2, cpt-content h2,  .textheading2.oversized, .oversized{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h2_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h2_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h2_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h2_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h2_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h2_typo['text-decoration']).
            '}';
        }

        /* H3 = Heading Font Style */
        $h3_typo = ot_get_option('h3_typo');
        if(!empty($h3_typo)){
            echo 'h3, .page-content h3, .post-content h3, cpt-content h3, .textheading3, .single-therapy-content h3{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h3_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h3_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h3_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h3_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h3_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h3_typo['text-decoration']).
            '}';
        }

        /* H4 = Heading Font Style */
        $h4_typo = ot_get_option('h4_typo');
        if(!empty($h4_typo)){
            echo 'h4, .page-content h4, .post-content h4, cpt-content h4{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h4_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h4_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h4_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h4_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h4_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h4_typo['text-decoration']).
            '}';
        }

        /* H5 = Heading Font Style */
        $h5_typo = ot_get_option('h5_typo');
        if(!empty($h5_typo)){
            echo 'h5, .page-content h5, .post-content h5, cpt-content h5{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h5_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h5_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h5_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h5_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h5_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h5_typo['text-decoration']).
            '}';
        }

        /* H6 = Heading Font Style */
        $h6_typo = ot_get_option('h6_typo');
        if(!empty($h6_typo)){
            echo 'h6, .page-content h6, .post-content h6, cpt-content h6{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($h6_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $h6_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $h6_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $h6_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $h6_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $h6_typo['text-decoration']).
            '}';
        }
        /* Homepage Banner Font Style */
        $banner_htypo = ot_get_option('banner_htypo');
        if(!empty($banner_htypo)){
            echo '.home-banner .banner-caption h2{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($banner_htypo['font-family'])).
                ot_check_css_prop('font-size:%s;', $banner_htypo['font-size']).
                ot_check_css_prop('font-style:%s;', $banner_htypo['font-style']).
                ot_check_css_prop('font-weight:%s;', $banner_htypo['font-weight']).
                ot_check_css_prop('line-height:%s;', $banner_htypo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $banner_htypo['text-decoration']).
            '}';
        }

        /* Homepage Banner Text Shadow */
        $banner_hshadow = ot_get_option('banner_hshadow');
        if(!empty($banner_hshadow)){
            echo '.home-banner .banner-caption h2{'.
                sprintf('text-shadow:%spx %spx %spx %s;', $banner_hshadow['offset-x'], $banner_hshadow['offset-y'], $banner_hshadow['blur-radius'], $banner_hshadow['color']).
            '}';
        }


        /* Homepage Banner - Subtext Font Style */
        $banner_stypo = ot_get_option('banner_stypo');
        if(!empty($banner_stypo)){
            echo '.banner-caption .sub-title{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($banner_stypo['font-family'])).
                ot_check_css_prop('font-size:%s;', $banner_stypo['font-size']).
                ot_check_css_prop('font-style:%s;', $banner_stypo['font-style']).
                ot_check_css_prop('font-weight:%s;', $banner_stypo['font-weight']).
                ot_check_css_prop('line-height:%s;', $banner_stypo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $banner_stypo['text-decoration']).
            '}';
        }



        /* Internal Banner - Font Style */
        $ibanner_typo = ot_get_option('ibanner_typo');
        if(!empty($ibanner_typo)){
            echo '.internal-banner h1, .internal-banner h2 {'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($ibanner_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $ibanner_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $ibanner_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $ibanner_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $ibanner_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $ibanner_typo['text-decoration']).
            '}';
        }


        /* Internal Banner - Font Style */
        $sectionsh_typo = ot_get_option('sectionsh_typo');
        if(!empty($sectionsh_typo)){
            echo '.section-title h2, .section.background .section-title h2 {'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($sectionsh_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $sectionsh_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $sectionsh_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $sectionsh_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $sectionsh_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $sectionsh_typo['text-decoration']).
            '}';
        }


        /* Internal Banner Text Shadow */
        $ibanner_hshadow = ot_get_option('ibanner_hshadow');
        if(!empty($ibanner_hshadow)){
            echo '.internal-banner .banner-caption h2{'.
                sprintf('text-shadow:%spx %spx %spx %s;', $ibanner_hshadow['offset-x'], $ibanner_hshadow['offset-y'], $ibanner_hshadow['blur-radius'], $ibanner_hshadow['color']).
            '}';
        }


        /* Buttons - Font Style */
        $buttonsh_typo = ot_get_option('content_button_typo');
        if(!empty($buttonsh_typo)){
            echo '.button, .banner-button a, .button1, .button-prime, .button-alt, #booking_form .submit {'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($buttonsh_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $buttonsh_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $buttonsh_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $buttonsh_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $buttonsh_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $buttonsh_typo['text-decoration']).
            '}';
        }

        /* Footer -  Font Style */
        $footerh_typo = ot_get_option('footer_heading_typo');
        if(!empty($footerh_typo)){
            echo '#oct-site-footer .textheading3{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($footerh_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $footerh_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $footerh_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $footerh_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $footerh_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $footerh_typo['text-decoration']).
            '}';
        }
        $footert_typo = ot_get_option('footer_text_typo');
        if(!empty($footert_typo)){
            echo '#oct-site-footer p{'.
                ot_check_css_prop('font-family:%s;', ot_google_font_family($footert_typo['font-family'])).
                ot_check_css_prop('font-size:%s;', $footert_typo['font-size']).
                ot_check_css_prop('font-style:%s;', $footert_typo['font-style']).
                ot_check_css_prop('font-weight:%s;', $footert_typo['font-weight']).
                ot_check_css_prop('line-height:%s;', $footert_typo['line-height']).
                ot_check_css_prop('text-decoration:%s;', $footert_typo['text-decoration']).
            '}';
        }



        /* Body CSS */
        $body_css_val = ot_get_option('body_bg');
        if(!empty($body_css_val) && $skin_switch !== 'off') printf("#page{%s}", ot_check_bg_css_prop($body_css_val));


        /* Links colors  */
        $links_colors = ot_get_option('body_link_color');
        if(!empty($links_colors)){
            echo '.section-content a:not(.button), .post-content a:not(.button), .sidebar .widget a:not(.button, .comment-reply-link){'.
                ot_check_css_prop('color:%s;', $links_colors['link']).
            '}';
            echo '.section-content a:not(.button):active, .post-content a:not(.button):active, .sidebar .widget a:not(.button, .comment-reply-link):active{'.
                ot_check_css_prop('color:%s;', $links_colors['active']).
            '}';
            echo '.section-content a:not(.button):visited, .post-content a:not(.button):visited, .sidebar .widget a:not(.button, .comment-reply-link):visited{'.
                ot_check_css_prop('color:%s;', $links_colors['visited']).
            '}';
            echo '.section-content a:not(.button):hover, .post-content a:not(.button):hover, .sidebar .widget a:not(.button, .comment-reply-link):hover{'.
                ot_check_css_prop('color:%s;', $links_colors['hover']).
            '}';
        }

        /* Headings Colors */
        $headings_colors = ot_get_option('headings_colors');
        if(!empty($headings_colors)){
            echo '.post-content h1, .cpt-content h1{'.
                ot_check_css_prop('color:%s;', $headings_colors['h1']).
            '}';
            echo '.post-content h2, .cpt-content h2{'.
                ot_check_css_prop('color:%s;', $headings_colors['h2']).
            '}';
            echo '.post-content h3, .cpt-content h3{'.
                ot_check_css_prop('color:%s;', $headings_colors['h3']).
            '}';
            echo '.post-content h4, .cpt-content h4{'.
                ot_check_css_prop('color:%s;', $headings_colors['h4']).
            '}';
            echo '.post-content h5, .cpt-content h5{'.
                ot_check_css_prop('color:%s;', $headings_colors['h5']).
            '}';
            echo '.post-content h6, .cpt-content h6{'.
                ot_check_css_prop('color:%s;', $headings_colors['h6']).
            '}';
        }

       /* Header CSS */
        $header_bg = ot_get_option('header_bg');
        if(!empty($header_bg)) printf("header#site-header{%s}", ot_check_bg_css_prop($header_bg));


        $logo_color = ot_get_option('logo_color');
        if(!empty($logo_color)){

            echo '.site-logo a{'.
                ot_check_css_prop('color:%s;', $logo_color['link']).
            '}';

            echo '.site-logo a:hover{'.
                ot_check_css_prop('color:%s;', $logo_color['hover']).
            '}';

        }

        /* Menu item color */
        $menu_link_color = ot_get_option('menu_link_color');

        if(!empty($menu_link_color) && $skin_switch !== 'off'){
            echo '#primary-nav ul li a, #sticky_menu li a{'.
                ot_check_css_prop('color:%s;', $menu_link_color['link']).
            '}';

            echo '#primary-nav ul li:hover > a, #sticky_menu li:hover > a{'.
                ot_check_css_prop('color:%s;', $menu_link_color['hover']).
            '}';

            echo '#primary-nav ul li.current_page_item a, #primary-nav ul li.current-menu-parent a, 
                  #sticky_menu li.current_page_item a, #sticky_menu li.current-menu-parent a{'.
                ot_check_css_prop('color:%s;', $menu_link_color['active']).
            '}';
        }

        /* Menu Item Background */
        $menu_typo_bg = ot_get_option('menu_typo_bg');
        if(!empty($menu_typo_bg) && $skin_switch !== 'off'){
            echo '#primary-nav ul li a, #sticky_menu li a{'.
                ot_check_css_prop('background-color:%s;', $menu_typo_bg['link']).
            '}';

            echo '#primary-nav ul li:hover > a, #sticky_menu li:hover > a{'.
                ot_check_css_prop('background-color:%s;', $menu_typo_bg['hover']).
            '}';

            echo '#primary-nav ul li.current_page_item a, #primary-nav ul li.current-menu-parent a, 
                  #sticky_menu li.current_page_item a, #sticky_menu li.current-menu-parent a{'.
                ot_check_css_prop('background-color:%s;', $menu_typo_bg['active']).
            '}';
        }


        /* Submenu item color */
        $submenu_link_color = ot_get_option('submenu_link_color');

        if(!empty($submenu_link_color) && $skin_switch !== 'off'){
            echo '#primary-nav ul.sub-menu li a, #sticky_menu ul.sub-menu li a{'.
                ot_check_css_prop('color:%s;', $submenu_link_color['link']).
            '}';

            echo '#primary-nav ul.sub-menu li:hover > a, #sticky_menu ul.sub-menu li:hover > a{'.
                ot_check_css_prop('color:%s;', $submenu_link_color['hover']).
            '}';

            echo '#primary-nav ul.sub-menu li.current_page_item a, #primary-nav ul.sub-menu li.current-menu-item a, 
                 #sticky_menu ul.sub-menu li.current_page_item a, #sticky_menu ul.sub-menu li.current-menu-item a{'.
                ot_check_css_prop('color:%s;', $submenu_link_color['active']).
            '}';

        }

        /* Submenu Item Background */
        $submenu_typo_bg = ot_get_option('submenu_typo_bg');
        if(!empty($submenu_typo_bg) && $skin_switch !== 'off'){
            echo '#primary-nav ul.sub-menu li a, #sticky_menu ul.sub-menu li a{'.
                ot_check_css_prop('background-color:%s;', $submenu_typo_bg['link']).
            '}';

            echo '#primary-nav ul.sub-menu li:hover > a, #sticky_menu ul.sub-menu li:hover > a{'.
                ot_check_css_prop('background-color:%s;', $submenu_typo_bg['hover']).
            '}';

            echo '#primary-nav ul.sub-menu li.current_page_item a, #primary-nav ul.sub-menu li.current-menu-item a,
                 #sticky_menu ul.sub-menu li.current_page_item a, #sticky_menu ul.sub-menu li.current-menu-item a{'.
                ot_check_css_prop('background-color:%s;', $submenu_typo_bg['active']).
            '}';
        }


        /* Banner Text Color */
        /* Homepage Banner */
        $hbanner_text_color = ot_get_option('hbanner_text_color');
        if(!empty($hbanner_text_color) && $skin_switch !== 'off'){
            echo '.home-banner .banner-caption h1, .home-banner .banner-caption h2{'.
                ot_check_css_prop('color:%s;', $hbanner_text_color['link']).
            '}';

            echo '.home-banner .banner-caption .sub-title p{'.
                ot_check_css_prop('color:%s;', $hbanner_text_color['hover']).
            '}';

            echo '.banner.home-banner{'.
                ot_check_css_prop('background-color:%s;', $hbanner_text_color['active']).
            '}';
        }


        /* Internal Banner */
        $intbanner_text_color = ot_get_option('intbanner_text_color');
        if(!empty($intbanner_text_color)){
            echo '.internal-banner .banner-caption h1, .internal-banner .banner-caption h2{'.
                ot_check_css_prop('color:%s;', $intbanner_text_color['link']).
            '}';

            echo '.internal-banner .banner-caption .sub-title p, .internal-banner .archive-description{'.
                ot_check_css_prop('color:%s;', $intbanner_text_color['hover']).
            '}';

            echo '.banner.internal-banner{'.
                ot_check_css_prop('background-color:%s;', $intbanner_text_color['active']).
            '}';
        }


        /* Page Section Colors */
        $page_section_text_color = ot_get_option('page_section_text_color');
        if(!empty($page_section_text_color)){
            echo '.section-title h2{'.
                ot_check_css_prop('color:%s;', $page_section_text_color).
            '}';
        }

        /* Section Content Color */
        $bg_page_section_text_color = ot_get_option('bg_page_section_text_color');
        if(!empty($bg_page_section_text_color)){
            echo '.section.background .section-title h2{'.
                ot_check_css_prop('color:%s;', $bg_page_section_text_color).
            '}';
        }

        /* Footer BG Color */
        $footer_bg = ot_get_option('footer_bg');
        if(!empty($footer_bg)) printf(".footer-widgets{%s}", ot_check_bg_css_prop($footer_bg));

        /* Footer Text Color */
        $footer_tcolor = ot_get_option('footer_tcolor');
        if(!empty($footer_tcolor)){
            echo '.footer-widgets, .footer-widgets p, .footer-widgets .widget p, .footer-widgets .footer-logo.site-logo .site-title{'.
                ot_check_css_prop('color:%s;', $footer_tcolor).
            '}';
            echo '.footer-widgets .widget_calendar table thead{'.
                ot_check_css_prop('background-color:%s;', $footer_tcolor).
            '}';
            echo '.footer-widgets .widget_calendar table, .footer-widgets .widget_calendar table tfoot{'.
                ot_check_css_prop('border-color:%s;', $footer_tcolor).
            '}';
        }

        /* Banner Buttons - Text Color */
        $ban_buttons_text_color = ot_get_option('ban_buttons_text_color');
        if(!empty($ban_buttons_text_color)){
            echo '.banner .button, .widget_cta_banner .button {'.
                ot_check_css_prop('color:%s;', $ban_buttons_text_color['link']).
                ot_check_css_prop('background-color:%s;', $ban_buttons_text_color['active']).
            '}';

            echo '.banner .button:hover, .widget_cta_banner .button:hover {'.
                ot_check_css_prop('color:%s;', $ban_buttons_text_color['hover']).
                ot_check_css_prop('background-color:%s;', $ban_buttons_text_color['visited']).
            '}';
        }

        /* Banner Buttons Borders */
        $ban_buttons_border_sw = ot_get_option('ban_buttons_border_sw');
        if('off' != $ban_buttons_border_sw){

            $ban_buttons_border = ot_get_option('ban_buttons_border');
            $ban_buttons_border_rad = ot_get_option('ban_buttons_border_rad');
            if(!empty($ban_buttons_border)){

                echo '.banner .button, .widget_cta_banner .button{'.
                    ot_check_css_prop('border-width:%s%s;', $ban_buttons_border['width'],$ban_buttons_border['unit']).
                    ot_check_css_prop('border-style:%s;', $ban_buttons_border['style']).
                    ot_check_css_prop('border-color:%s;', $ban_buttons_border['color']).
                    ot_check_css_prop('border-radius:%s;', $ban_buttons_border_rad.'px').
                '}';
            }

        }


        /* Content Buttons Color */
        $ont_buttons_text_color = ot_get_option('content_buttons_text_color');
        if(!empty($ont_buttons_text_color) && $skin_switch !== 'off'){
            echo '.button1, .button-prime, .button-alt, .banner-button, .cta-content .button, .section-button .button:not(.dark), .cpt-button .button, .cpt-buttons .button, #commentform input[type=submit], #booking_form .submit {'.
                ot_check_css_prop('color:%s;', $ont_buttons_text_color['link']).
                ot_check_css_prop('background-color:%s;', $ont_buttons_text_color['active']).
            '}';

            echo '.button1:hover, .button-prime:hover, .button-alt:hover, .banner-button:hover,.cta-content .button:hover, .section-button .button:hover, .cpt-button .button:hover, .cpt-buttons .button:hover, #commentform input[type=submit]:hover, #booking_form .submit:hover {'.
                ot_check_css_prop('color:%s;', $ont_buttons_text_color['hover']).
                ot_check_css_prop('background-color:%s;', $ont_buttons_text_color['visited']).
            '}';
        }

        /* Content Buttons Borders */
        $cont_buttons_border_sw = ot_get_option('cont_buttons_border_sw');
        if('off' != $cont_buttons_border_sw){

            $cont_buttons_border = ot_get_option('cont_buttons_border');
            $cont_buttons_border_rad = ot_get_option('cont_buttons_border_rad');
            if(!empty($cont_buttons_border)){

                echo '.cta-content .button, .section-button .button, .cpt-button .button, .cpt-buttons .button, #commentform input[type=submit]{'.
                    ot_check_css_prop('border-width:%s%s;', $cont_buttons_border['width'],$cont_buttons_border['unit']).
                    ot_check_css_prop('border-style:%s;', $cont_buttons_border['style']).
                    ot_check_css_prop('border-color:%s;', $cont_buttons_border['color']).
                    ot_check_css_prop('border-radius:%s;', $cont_buttons_border_rad.'px').
                '}';
            }

        }


        /* Form Submit Buttons Borders */
        $form_buttons_text_color = ot_get_option('form_buttons_text_color');
        if(!empty($form_buttons_text_color)){
            echo '.comment-reply-link, .form input[type=submit], section.background input[type=submit]{'.
                ot_check_css_prop('color:%s;', $form_buttons_text_color['link']).
                ot_check_css_prop('background-color:%s;', $form_buttons_text_color['active']).
            '}';

            echo '.comment-reply-link:hover, .form input[type=submit]:hover, section.background input[type=submit]:hover {'.
                ot_check_css_prop('color:%s;', $form_buttons_text_color['hover']).
                ot_check_css_prop('background-color:%s;', $form_buttons_text_color['visited']).
            '}';
        }

        /* Form Submit Buttons Borders */
        $form_buttons_border_sw = ot_get_option('form_buttons_border_sw');
        if('on' == $form_buttons_border_sw){

            $form_buttons_border = ot_get_option('form_buttons_border');
            $form_buttons_border_rad = ot_get_option('form_buttons_border_rad');
            if(!empty($form_buttons_border)){

                echo '.comment-reply-link, .form input[type=submit], section.background input[type=submit]{'.
                    ot_check_css_prop('border-width:%s%s;', $form_buttons_border['width'],$form_buttons_border['unit']).
                    ot_check_css_prop('border-style:%s;', $form_buttons_border['style']).
                    ot_check_css_prop('border-color:%s;', $form_buttons_border['color']).
                    ot_check_css_prop('border-radius:%s;', $form_buttons_border_rad.'px').
                '}';
            }

        }


        /* Social Icons Colors */
        $social_icons_color_invert = ot_get_option('social_icons_color_invert');
        if('on' === $social_icons_color_invert){
            echo '.social-icons ul li a svg *{ fill: 0; }';
        }
        elseif('off' === $social_icons_color_invert){

            $social_icons_color = ot_get_option('social_icons_color');
            if(!empty($social_icons_color)){
                echo '.social-icons ul li a svg *{'.
                    ot_check_css_prop('fill:%s;', $social_icons_color['link']).
                '}';
                echo '.social-icons ul li a:hover svg *{'.
                    ot_check_css_prop('fill:%s;', $social_icons_color['hover']).
                '}';
            }

        }


        /* Custom CSS  */
        echo ot_get_option('custom_css');




    ?>
</style>