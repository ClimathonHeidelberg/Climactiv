<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package blog
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
        <?php wp_head(); ?>
        <?php include(TEMPLATEPATH . '/assets/css/header-css.php'); ?>
        <?php if (is_singular()) wp_enqueue_script('comment-reply'); ?>
    </head>
    <body <?php body_class(); ?>>
        <div id="oct-wrapper">
            <div id="page">
                <?php
                get_template_part('template-parts/header/header', 'logo');
                get_template_part('template-parts/header/header', 'navigation');
                ?>
