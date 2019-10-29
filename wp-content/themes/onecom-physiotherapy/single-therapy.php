<?php
/*
 * Single Therapy Page 
 */
get_header();
get_template_part( 'template-parts/internal', 'banner' );
$layout = get_post_meta(get_the_ID(), 'single_post_page_layout', true);
if (!$layout) {
	$layout = ot_get_option('single_post_page_layout');
}
$main_class = 'col-md-7 col-lg-8';
$sidebar_class = 'col-md-5 col-lg-4';
$content_class = 'mr-md-5';
$sidebar_inner_margin = 'ml-md-4';
$sidebar_button_class = 'float-md-right';
if ($layout === 'one-column-left-sidebar') {
	$main_class = 'col-md-7 col-lg-8 order-md-last mr-0';
	$sidebar_class = 'col-md-5 col-lg-4 ml-0';
	$content_class = 'ml-md-5';
	$sidebar_inner_margin = 'mr-md-4';
	$sidebar_button_class = 'float-md-left';
} elseif ($layout === 'one-column-no-sidebar') {
	$main_class = 'col-12';
	$sidebar_class = 'd-none';
	$content_class = '';
}
$info_blocks      = get_post_meta( get_the_ID(), 'single_therapy_benefit', true );
$book_link        = get_post_meta( get_the_ID(), 'therapy_first_button_link', true );
$booking_title    = get_post_meta( get_the_ID(), 'therapy_first_button_text', true );
$info_block_count = $info_blocks ? count( $info_blocks ) : 0;
?>
<section class="page-content" role="main">
	<?php
	if ( have_posts() ):
		while ( have_posts() ):
			the_post();
			$subtitle      = get_post_meta( $post->ID, 'therapy_subtitle', true );
			$slider_images = get_post_meta( $post->ID, 'slider_images', true );
			?>
            <article id="lesson-<?php $post->ID; ?>" <?php post_class( 'cpt-single' ); ?> role="article">
                <div class="container">
                    <div class="row">
                        <div class="col-12 order-first">
							<?php if ( $slider_images && ot_get_option('show_blog_thumb') === 'on'): ?>
                                <div class="cpt-thumb therapy-slider">
									<?php
									foreach ( $slider_images as $slide ):
										if ( $slide['therapy_slide'] ):
											$img_url = wp_get_attachment_image_src( $slide['therapy_slide'], 'slider_featured' )[0];
											$img_url_full = wp_get_attachment_image_src( $slide['therapy_slide'], 'full' )[0];
											if ( $img_url && $img_url_full ):
											?>
                                            <a class="shinybox" rel="g1" href="<?php echo $img_url_full ?>"><img
                                                        src="<?php echo $img_url ?>" alt="<?php echo $slide['title'] ?>"
                                                        class="featured-image img-fluid"></a>
										<?php
										endif;
										endif;
									endforeach;
									?>
                                </div>
							<?php endif;
							?>
                        </div>
                        <div class="<?php echo $main_class?>">
                            <div class="single-therapy-content">
                                <header class="cpt-title <?php echo $content_class?> mb-5">
									<?php the_title( '<h2 class="textheading2 d-md-inline-block oversized">', '</h2>' ); ?>
	                                <?php if ($layout === 'one-column-no-sidebar'):?>
		                                <?php if ($book_link && $booking_title): ?>
                                            <a class="d-none d-md-block button-alt text-white <?php echo $sidebar_button_class?>"
                                               href="<?php echo $book_link; ?>"><?php echo $booking_title ?></a>
		                                <?php endif; ?>
	                                <?php endif;?>
                                </header>
                                <!-- CPT Text -->
                                <div class="post-content" role="main">
									<?php add_filter( 'the_content', 'wpautop' ) ?>
                                    <div class="textnormal <?php echo $content_class?>"><?php the_content(); ?></div>
                                </div>
	                            <?php if ($layout === 'one-column-no-sidebar'):?>
		                            <?php if ($book_link && $booking_title): ?>
                                        <a class="d-block d-md-none text-center text-white button-alt <?php echo $sidebar_button_class?>"
                                           href="<?php echo $book_link; ?>"><?php echo $booking_title ?></a>
		                            <?php endif; ?>
	                            <?php endif;?>
                            </div>
                        </div>
                        <!-- Sidebar --->
                        <aside class="<?php echo $sidebar_class?> sidebar primary" role="complementary">
                            <!-- CPT Custom Fields -->

                            <div class="cpt-custom-fields">
                                <div class="row">
                                    <div class="col-12 single-therapy-book text-center">
										<?php if ( $book_link && $booking_title ): ?>
                                            <a class="button-alt <?php echo $sidebar_button_class?>"
                                               href="<?php echo $book_link; ?>"><?php echo $booking_title ?></a>
										<?php endif; ?>
                                    </div>
                                </div>
								<?php
								if ( ! empty( $info_blocks ) ) {
									$current_block = 1;
									?>
									<?php foreach ( $info_blocks as $info_block ) { ?>
										<?php if ( ! empty( $info_block ) ) { ?>
                                            <div class="row no-gutters info-block-wrap">
												<?php
												$info_icon = wp_get_attachment_url( $info_block['single_therapy_ben_icon'] );
												if ( $info_icon ):
													?>
                                                    <div class="col-2 d-flex align-items-center">
                                                        <img src="<?php echo $info_icon ?>"
                                                             alt="<?php echo $info_block['title'] ?>"
                                                             class="img-fluid"/>
                                                    </div>
												<?php endif; ?>

                                                <div class="col-10 d-flex align-items-center">
                                                    <h3 class="textheading3 mb-0"><?php echo $info_block['title'] ?></h3>
                                                </div>
												<?php if ( $info_block['single_therapy_ben_text'] ): ?>
                                                    <div class="col-12 textnormal">
                                                        <p class="info-content-text"><?php echo $info_block['single_therapy_ben_text']; ?></p>
                                                    </div>
												<?php
												endif;
												if ( $current_block < $info_block_count ):
												endif;
												$current_block ++;
												?>

                                            </div>
										<?php } ?>
									<?php } ?>
								<?php } ?>
                            </div>

                            <!-- Sidebar Widgets -->
							<?php dynamic_sidebar( 'content_sidebar' ); ?>
                        </aside>
                    </div>
                </div>
            </article>
		<?php
		endwhile;
	endif;
	get_template_part( 'template-parts/home/section', 'gallery' );
	?>

</section>

<?php get_footer(); ?>
