<?php
/* Template Name: About Us */
get_header();
if ( have_posts() ) {
	the_post();
}
get_template_part('template-parts/internal', 'banner');
?>
<?php
$benefits_switch      = get_post_meta( $id, 'show_benefits_switch', true );
$benefits_title       = get_post_meta( $id, 'benefit_section_title', true );
$benefits_desc        = get_post_meta( $id, 'benefit_section_desc', true );
$benefits             = get_post_meta( $id, 'benefit_list', true );
$benefits_button_text = get_post_meta( $id, 'benefit_button_text', true );
$benefits_button_link = get_post_meta( $id, 'benefit_button_url', true );
if ( $benefits_switch != 'off' ):
	?>
    <!-- Background Section -->
    <section class="section background about-us-bg">
        <div class="container about-us-benefits">
            <div class="row">
				<?php if ( $benefits_title ): ?>
                    <div class="col-md-6 about-title-wrap">
                        <div class="section_title">
                            <h2 class="textheading2 oversized text-white"><?php echo $benefits_title ?></h2>
                        </div>
                        <div class="textnormal mt-4">
							<?php echo $benefits_desc; ?>
                        </div>
                    </div>
                    <div class="w-100 mb-5 d-none d-md-block"></div>
				<?php endif;
				$count = 1;
				foreach ( $benefits as $benefit ): ?>
                    <div class="col-sm-12 col-md-4">
                        <div class="benefit-content text-left">
                            <div class="row benefit-content-wrap">
                                <div class="col-12 d-flex mt-md-5 mb-md-2 ben-title">
                                    <span class="ben-no textheading2 mr-1"><?php echo str_pad( $count ++, 2, '0', STR_PAD_LEFT ); ?></span>
                                    <h3 class="textheading2 text-white align-self-center"><?php echo $benefit['title'] ?></h3>
                                </div>
                                <div class="col-12">
									<?php echo $benefit['benefits_content'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
            </div>
			<?php if ( $benefits_button_text ): ?>
                <div class="row benefit-cta">
                    <div class="col-12 d-flex justify-content-center">
                        <a href="<?php echo esc_url( $benefits_button_link ) ?>"
                           class="button-alt"><?php echo $benefits_button_text ?></a>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </section>
<?php endif; ?>
<?php
$home_newsletter_switch     = get_post_meta( get_the_ID(), 'home_newsletter_switch', true );
$newsletter_section_title   = get_post_meta( get_the_ID(), 'newsletter_section_title', true );
$newsletter_section_content = get_post_meta( get_the_ID(), 'newsletter_section_content', true );
$newsletter_form_switch     = get_post_meta( get_the_ID(), 'newsletter_form_switch', true );
$newsletter_embed_code      = get_post_meta( get_the_ID(), 'newsletter_embed_code', true );
$newsletter_btn_title       = get_post_meta( get_the_ID(), 'newsletter_btn_title', true );
?>

<?php if ( $home_newsletter_switch != 'off' && ( strlen( $newsletter_section_title ) || strlen( $newsletter_section_content ) ) ): ?>
    <section class="section solid white newsletter-section">
        <div class="container">
            <div class="row  newsletter-form-wrap">
                <div class="col-md-6 d-flex align-items-center justify-content-start newsletter-text-section order-md-last">
                    <div class="section-content">
                        <h2><?php echo $newsletter_section_title; ?></h2>
						<?php onecom_edit_icon( 'page_meta', '#setting_home_newsletter', get_the_ID() ); ?>
						<?php echo wpautop( $newsletter_section_content ); ?>
                    </div>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center newsletter-form-section order-md-first">
		            <?php if ( $newsletter_form_switch != 'off' ): ?>
                        <div class="form-container newsletter-form">
				            <?php if ( strlen( trim( $newsletter_embed_code ) ) ) { ?>
					            <?php echo do_shortcode( $newsletter_embed_code ); ?>
				            <?php } else { ?>
                                <form id="subscribe_form" class="form text-center">
                                    <fieldset>
                                        <input type="email" name="email" class="input sub_email form-control"
                                               placeholder="<?php echo __( 'Please enter your email.', 'oct-physiotherapy' ); ?>"
                                               required/>
                                    </fieldset>
                                    <fieldset>
							            <?php wp_nonce_field( 'subscribe_form', 'validate_nonce' ); ?>
							            <?php if ( strlen( $newsletter_btn_title ) ): ?>
                                            <input type="submit"
                                                   class="submit button small dark float-right mt-4 button-prime"
                                                   value="<?php echo $newsletter_btn_title; ?>"
                                                   name="newsletter-submit"/>
							            <?php endif; ?>
                                    </fieldset>
                                    <div class="form_message"></div>
                                </form>
				            <?php } ?>
                        </div>

		            <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
$testimonials_switch = get_post_meta( $id, 'show_testimonial_switch', true );
$testimonial_title   = get_post_meta( $id, 'testimonial_section_title', true );
$testimonial_content = get_post_meta( $id, 'testimonial_section_content', true );
$therapists          = get_post_meta( $id, 'testimonials', true );
?>
    <!--Upcoming Events -->
<?php if ( $testimonials_switch != 'off' && ( strlen( $testimonial_title ) || strlen( $testimonial_content ) ) ): ?>
    <section class="section solid white text-center testimonial-section" role="region">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="textheading2 oversized section_title"><?php echo $testimonial_title; ?></h2>
                </div>
				<?php if ( $testimonial_content ): ?>
                    <div class="col-12 textnormal">
                        <div class="testimonial_text_wrap"><?php echo $testimonial_content ?></div>
                    </div>
				<?php endif ?>
				<?php if ( $therapists ): foreach ( $therapists as $therapist ): ?>
                    <div class="col-md-4 col-sm-12 author-wrap">
                        <div class="author-image"
                             style="background-image:url(<?php echo wp_get_attachment_image_src( $therapist['testimonial_author'] )[0] ?>)"></div>
                        <div class="content-wrap">
                            <div class="text-wrap text-white"><h3 class="testimonial-title"><?php echo $therapist['title'] ?></h3>
                                <p class="author-designation"><?php echo $therapist['testimonial_desig'] ?></p>
                                <div class="mt-3 testimonial-content"><?php echo $therapist['testimonial_content'] ?></div>
                            </div>
                        </div>
                    </div>
				<?php endforeach;endif ?>
            </div>
        </div>
    </section>
<?php endif;
get_template_part( 'template-parts/home/section', 'gallery' ); ?>

    <!-- END Page Content -->

<?php get_footer(); ?>