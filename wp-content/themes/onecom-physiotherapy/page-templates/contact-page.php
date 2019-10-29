<?php
/*
 * Template Name: Contact Us 
 */
get_header();
$id = get_the_ID();
get_template_part( 'template-parts/internal', 'banner' );
$booking_bg         = get_the_post_thumbnail_url( $id );
$contact_blocks     = get_post_meta( $id, 'contact_page_blocks', true );
$form_section_title = get_post_meta( $id, 'form_section_title', true );
$form_section_desc  = get_post_meta( $id, 'form_section_desc', true );
$map_section_code   = get_post_meta( $id, 'map_section_code', true );
$custom_title       = get_post_meta( $id, 'custom_page_title', true );
$map_switch         = get_post_meta( $id, 'map_sec_switch', true );
$map_class          = 'col-md-12';
if ( has_post_thumbnail() || $custom_title || $show_details_wrap ) {
	$map_class = 'col-md-5';
}
if ( isset( $contact_blocks ) ) {
	foreach ( $contact_blocks as $block ) {
		if ( $block['title'] || $block['block_content'] ) {
			$show_details_wrap = true;
		}
	}
}
?>
    <!-- START Page Content -->
<?php if ( get_post_meta( get_the_ID(), 'booking_sec_switch', true ) != 'off' ): ?>
    <section class="section background" id="form_section">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-12 form-bg">
                    <div class="row text-white">
                        <div class="col-md-7 col-sm-12">
                            <div class="form-container contact-form">
                                <div class="form-section-title">
                                    <h5><?php echo $form_section_title ?></h5>
                                </div>
                                <div class="form-section-content">
                                    <?php echo $form_section_desc?>
                                </div>
								<?php
								$booking_form_embed = get_post_meta( get_the_ID(), 'booking_form_embed', true );
								$custom_form_switch = get_post_meta( get_the_ID(), 'custom_form_switch', true );
								?>
								<?php if ( strlen( trim( $booking_form_embed ) ) && $custom_form_switch == 'on' ) { ?>

									<?php echo do_shortcode( $booking_form_embed ); ?>

								<?php } else { ?>

                                    <form id="booking_form" class="form text-left" role="form">
                                        <fieldset>
											<?php
											$label_2 = get_post_meta( get_the_ID(), 'form_label_2', true );
											$label_2 = ( isset( $label_2 ) && strlen( $label_2 ) ) ? $label_2 : __( "Name", "oct-physiotherapy" );
											?>
                                            <label><?php echo $label_2; ?> *</label>
                                            <input type="text" class="input booking_name" maxlength="120" required/>
                                            <input type="hidden" name="label_2" id="label_2"
                                                   value="<?php echo $label_2; ?>"/>
                                        </fieldset>
                                        <fieldset>
											<?php
											$label_5 = get_post_meta( get_the_ID(), 'form_label_5', true );
											$label_5 = ( isset( $label_5 ) && strlen( $label_5 ) ) ? $label_5 : __( "Phone", "oct-physiotherapy" );
											?>
                                            <label><?php echo $label_5; ?> *</label>
                                            <input type="text" class="input booking_phone" maxlength="120" required/>
                                            <input type="hidden" name="label_5" id="label_5"
                                                   value="<?php echo $label_5; ?>"/>
                                        </fieldset>
                                        <fieldset>
											<?php
											$label_1 = get_post_meta( get_the_ID(), 'form_label_1', true );
											$label_1 = ( isset( $label_1 ) && strlen( $label_1 ) ) ? $label_1 : __( "Email", "oct-physiotherapy" );
											?>
                                            <label><?php echo $label_1; ?> *</label>
                                            <input type="email" class="input booking_email" maxlength="120" required/>
                                            <input type="hidden" name="label_1" id="label_1"
                                                   value="<?php echo $label_1; ?>"/>
                                        </fieldset>
										<?php $service_options = get_post_meta( get_the_ID(), 'service_options', true ); ?>
										<?php if ( $service_options ): ?>
                                            <fieldset>
												<?php
												$label_6 = get_post_meta( get_the_ID(), 'form_label_6', true );
												$label_6 = ( isset( $label_6 ) && strlen( $label_6 ) ) ? $label_6 : __( "Choose service category", "oct-physiotherapy" );
												?>
                                                <label><?php echo $label_6; ?> *</label><br/>
												<?php foreach ( $service_options as $service_option ): ?>
                                                    <input type="radio" name="service" class="service_option"
                                                           value="<?php echo $service_option['s_value'] ? $service_option['s_value'] : $service_option['title'] ?>"/>
                                                    <label class="r-label"><?php echo $service_option['title'] ?></label>
                                                    <br/>
												<?php endforeach; ?>
                                                <input type="hidden" name="label_6" id="label_6"
                                                       value="<?php echo $label_6; ?>"/>
                                            </fieldset>
										<?php endif; ?>

                                        <fieldset>
											<?php
											$label_3 = get_post_meta( get_the_ID(), 'form_label_3', true );
											$label_3 = ( isset( $label_3 ) && strlen( $label_3 ) ) ? $label_3 : __( "Message", "oct-physiotherapy" );
											?>
                                            <label><?php echo $label_3; ?> *</label>
                                            <textarea rows="10" cols="50" class="input booking_msg" name="message"
                                                      required></textarea>
                                            <input type="hidden" name="label_3" id="label_3"
                                                   value="<?php echo $label_3; ?>"/>
                                        </fieldset>


										<?php
										/* Subject of the contact email */
										$subject = get_post_meta( get_the_ID(), 'cmail_subject', true );

										if ( ! isset( $subject ) && ! strlen( $subject ) ) {
											/* Set default if not subject saved from admin */
											$subject = 'Booking Query From ' . get_bloginfo( 'name' );
										}

										$contact_recipient = get_post_meta( get_the_ID(), 'recipient_email', true );
										if ( ! isset( $contact_recipient ) && ! strlen( $contact_recipient ) ) {
											/* Set default recipient to Admin Email */
											$contact_recipient = get_option( 'admin_email' );
										}
										?>
                                        <input type="hidden" name="contact_subject" id="contact_subject"
                                               value="<?php echo $subject; ?>"/>
                                        <input type="hidden" name="contact_recipient" id="contact_recipient"
                                               value="<?php echo $contact_recipient; ?>"/>

                                        <fieldset>
											<?php  echo oc_secure_fields(); ?>
											<?php $label_4 = get_post_meta( get_the_ID(), 'form_label_4', true ); ?>
                                            <input type="submit" class="submit button button-prime small float-right"
                                                   value="<?php echo( ( isset( $label_4 ) && strlen( $label_4 ) ) ? $label_4 : __( 'SEND MESSAGE', 'oct-physiotherapy' ) ); ?>"
                                                   name="booking-submit"/>
                                        </fieldset>
                                        <fieldset>
                                            <div class="form_message text-white"></div>
                                        </fieldset>
                                    </form>


								<?php } ?>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <div class="contact-us-page-content-wrap">
								<?php if ( have_posts() ) {
									while ( have_posts() ) {
										the_post();
										the_content();
									}
								} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            <?php
			$exp_section_bg = get_post_meta(get_the_ID(), 'booking_section_bg', true);
			if (!empty($exp_section_bg))
				{printf("#form_section{%s}", ot_check_bg_css_prop($exp_section_bg));}
			?>
        </style>
    </section>
<?php
endif;

?>
    <section class="page-content contact-details-section" role="main">
        <article id="page-<?php $id ?>" <?php post_class(); ?>>
            <div class="container">
                <div class="row no-gutters">
                    <!-- Content -->
                    <div class="contact-details-wrap <?php echo ( $map_switch ) ? 'col-md-7' : 'col-md-12'; ?> col-sm-12" <?php if ( $booking_bg ) {
						echo 'style="background-image:url(\'' . $booking_bg . '\')"';
					} ?>>
                        <div class="post-content">
							<?php

							if ( $custom_title ):
								?>
                                <h6 class="textheading2 text-white"><?php echo $custom_title ?></h6>                                &nbsp;
							<?php
							endif;
							if ( ! empty( $contact_blocks ) ): ?>
								<?php foreach ( $contact_blocks as $block ): ?>
                                    <h3 class="textheading3 text-white"><?php echo $block['title']; ?></h3>
                                    <div class="textnormal text-white"><?php echo wpautop( nl2br( $block['block_content'] ) ); ?></div>
                                    <br class="clear"/>
								<?php endforeach; ?>
							<?php endif; ?>
                        </div>
                    </div>
                    <!-- Featured Image -->
					<?php if ( $map_switch && $map_section_code ): ?>
                        <div class="<?php echo $map_class ?> d-none d-md-block">
                            <div class="map"><?php echo get_post_meta( get_the_ID(), 'map_section_code', true ); ?></div>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </article>
    </section>
<?php get_template_part( 'template-parts/home/section', 'gallery' ); ?>
    <!-- END Page Content -->
<?php get_footer(); ?>