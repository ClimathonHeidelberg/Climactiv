<?php
/*
 * Template Name: Services
 */
?>
<?php get_header();
get_template_part( 'template-parts/internal', 'banner' ); ?>
    <!-- START Page Content -->
    <section class="page-content" role="main">
        <div class="container">

			<?php $read_more_title = get_post_meta( get_the_ID(), 'info_btn_title', true ); ?>
			<?php
			/* Query Lessons */
			$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			$args  = array(
				'post_type'   => array( 'therapy' ),
				'post_status' => array( 'publish' ),
				'nopaging'    => true,
			);

			$therapies = new WP_Query( $args );

			if ( $therapies->have_posts() ):
				?>
                <div class="therapy-listing">
					<?php
					while ( $therapies->have_posts() ):
						$therapies->the_post();
						$subtitle = get_post_meta( $post->ID, 'therapy_subtitle', true );
						$intro    = get_post_meta( $post->ID, 'therapy_introduction', true );
						?>

                        <!-- START Single CPT -->
                        <article id="therapy-<?php the_ID(); ?>" <?php post_class( 'therapy-wrapper' ); ?>
                                 style="background-image:url(<?php echo get_the_post_thumbnail_url( $post ) ?>)">

                            <div class="row">
                                <!-- CPT Content -->
                                <div class="therapy-content  col-md-7">
                                    <div class="text-center therapy-content-inner">
                                        <div class="cpt-title">
											<?php the_title( '<h2 class="textheading2 oversized text-white">', '</h2>' ); ?>
                                        </div>

                                        <div class="cpt-excerpt">
											<?php if ( $subtitle ): ?>
                                                &nbsp;&nbsp;
                                                <h3 class="textheading3"><?php echo $subtitle ?></h3>
											<?php
											endif;
											if ( $intro ):
												?> &nbsp;&nbsp;
                                                <p class="textnormal"><?php echo $intro ?></p>
											<?php endif; ?>
                                        </div>
										<?php
										$first_button_text = get_post_meta( $post->ID, 'therapy_first_button_text', true );
										$first_button_link = get_post_meta( $post->ID, 'therapy_first_button_link', true );
										if (!trim($first_button_link) || trim($first_button_link) === '#') {
											$first_button_link = get_permalink( get_page_by_path( 'contact' ) );
										}
										$second_button_text = get_post_meta( $post->ID, 'therapy_second_button_text', true );
										$second_button_link = get_permalink( $post->ID );
										?>


                                        <div class="row cpt-buttons">
                                            <div class="col-md-6 col-sm-12 cpt-button">

												<?php if ( strlen( $first_button_text ) ): ?>
                                                    <a class="button-alt d-block float-md-right" href="<?php echo $first_button_link; ?>"
                                                       ><?php echo $first_button_text; ?></a>
												<?php endif; ?>

                                            </div>
                                            <div class="col-md-6 col-sm-12 cpt-button last-button-wrap">
												<?php if ( strlen( $second_button_text ) ): ?>
                                                    <a href="<?php echo $second_button_link; ?>"
                                                       class="button-alt d-block last-button float-md-left"><?php echo $second_button_text; ?></a>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <!-- END Single CPT -->

					<?php endwhile; ?>

                </div>

			<?php else: ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>

			<?php
			/* Restore original Post Data */
			wp_reset_postdata();
			?>

        </div>
    </section>
<?php get_template_part( 'template-parts/home/section', 'gallery' ); ?>
    <!-- END Page Content -->


<?php get_footer(); ?>