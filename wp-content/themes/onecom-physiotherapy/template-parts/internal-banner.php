<?php
$id = null;
if ( is_home() || is_category() || is_author() || is_tag() || is_archive() || is_month() || is_year() || is_search() || (is_single() && !is_singular('therapy'))) {
	$id = get_option( 'page_for_posts' );
	$margin_class = 'mt-0 mb-md-5';
} else {
	$id = get_the_ID();
	$margin_class = '';
}
$page_subtitle = get_post_meta( $id, 'page_subtitle', true );
$page_banner   = get_post_meta( $id, 'banner_image', true );

$banner_bg     = [];
if ( $page_banner ) {
	$banner = wp_get_attachment_image_src( $page_banner, 'large_featured' );
	if ( isset( $banner[0] ) ) {
		$banner_bg['background-image'] = $banner[0];
	}
}

?>
<style>
    <?php
if (!empty($banner_bg)) {
printf(".internal-banner{%s}", ot_check_bg_css_prop($banner_bg));
}
?>
</style>
<!-- START Page Content -->
<?php
$banner_switch = get_post_meta($id, 'internal_banner_switch', true);
if ($banner_switch !== 'off'):
?>
<section class="page-content internal-banner <?php echo $margin_class?>" role="main">
    <!-- START Single CPT -->
    <article id="page-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
        <div class="container">
            <div class="row">
                <!-- Content -->
                <div class="col-md-7 about-us-text">
                    <div class="post-content" role="main">
                        <h2 class="textheading2 oversized"><?php echo get_the_title($id) ?></h2>
                        <h3 class="textheading3"><?php echo $page_subtitle ?></h3>
                    </div>
                </div>

            </div>
        </div>
    </article>
    <!-- END Single CPT -->
</section>
<?php endif;?>