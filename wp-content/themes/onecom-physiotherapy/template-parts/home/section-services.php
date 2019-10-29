<?php
$page_id                   = get_the_ID();
$home_courses_switch       = get_post_meta( $page_id, 'home_courses_switch', true );
$courses_section_title     = get_post_meta( $page_id, 'courses_section_title', true );
$courses_section_content   = get_post_meta( $page_id, 'courses_section_content', true );
$home_courses_posts_switch = get_post_meta( $page_id, 'home_courses_posts_switch', true );
$home_courses_ids          = get_post_meta( $page_id, 'home_courses_ids', true );
$home_courses_btn_title    = get_post_meta( $page_id, 'home_courses_btn_label', true );
$home_read_more            = get_post_meta( $page_id, 'home_readmore_button', true );
$home_all_button_text      = get_post_meta( $page_id, 'home_all_service_button_text', true );
$home_all_button_link      = get_post_meta( $page_id, 'home_all_service_button_link', true );
if ( $home_courses_switch != 'off' && ( strlen( $courses_section_title ) || strlen( $courses_section_content ) || ! empty( $home_courses_ids ) ) ): ?>
    <section class="section solid white-bg text-center home-therapies">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-head-wrap align-center">
                        <div class="section-title">
                            <h2 class="textheading2 oversized">
								<?php echo $courses_section_title; ?>
								<?php onecom_edit_icon( 'page_meta', '#setting_home_courses', get_the_ID() ); ?>

                            </h2>
                        </div>
                        <div class="section-content font-larger">
							<?php echo $courses_section_content; ?>
                        </div>
                    </div>
					<?php if ( $home_courses_posts_switch != 'off' ): ?>
                        <div class="section-columns">
							<?php
							$args = array(
								'post_type'      => array( 'therapy' ),
								'post_status'    => array( 'publish' ),
								'posts_per_page' => '4',
							);
							// The Query
							$therepies = new WP_Query( $args );

							// The Loop
							if ( $therepies->have_posts() ) {
								?>
                                <div class="row justify-content-center no-gutters">
									<?php
									$count = 1;
									while ( $therepies->have_posts() ):
										$therepies->the_post();
										?>
                                        <div class="col-md-4 col-sm-12 cpt-col mt-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="cta-block">
														<?php if ( ( $count ++ ) % 2 === 0 ): ?>
                                                            <div class="cta-content">
                                                                <h3 class="cursive-font my-5"><?php the_title(); ?></h3>
                                                                <div class="cta-text-wrap"><?php the_excerpt(); ?></div>
                                                                <div class="mt-5">
                                                                    <a class="button-prime"
                                                                       href="<?php echo get_permalink( $post->ID ); ?>">
																		<?php echo $home_read_more; ?>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
                                                                <div class="cta-banner d-none d-md-block"
                                                                     style="background-image:url('<?php echo get_the_post_thumbnail_url( $post->ID, 'medium_featured' ) ?>')"></div>
															<?php endif; ?>
														<?php else: ?>
															<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
                                                                <div class="cta-banner d-none d-md-block"
                                                                     style="background-image:url('<?php echo get_the_post_thumbnail_url( $post->ID, 'medium_featured' ) ?>')"></div>
															<?php endif; ?>
                                                            <div class="cta-content">
                                                                <h3 class="cursive-font my-5"><?php the_title(); ?></h3>
                                                                <div class="cta-text-wrap"><?php the_excerpt(); ?></div>
                                                                <div class="mt-5">
                                                                    <a class="button-prime"
                                                                       href="<?php echo get_permalink( $post->ID ); ?>">
																		<?php echo $home_read_more; ?>
                                                                    </a>
                                                                </div>
                                                            </div>
															<?php onecom_edit_icon( 'page_meta', '', get_the_ID() ); ?>
														<?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
									<?php endwhile; ?>
                                    <div class="col-12 all-button-wrap">
                                        <a class="button-alt " href="<?php echo $home_all_button_link?>"><?php echo $home_all_button_text?></a>
                                    </div>
                                </div>
								<?php
							} else { /* no posts found */
							}
							// Restore original Post Data
							wp_reset_postdata();
							?>
                        </div>
					<?php endif; ?>

                </div>

            </div>
        </div>
    </section>

<?php endif; ?>