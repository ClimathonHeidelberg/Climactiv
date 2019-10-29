<?php
$id = get_the_ID();
$home_testimonial_switch = get_post_meta($id, 'home_testimonial_switch', true);
$testimonial_bg = get_section_background_image('testimonial_bg');
$testimonial_section_title = get_post_meta($id, 'testimonial_section_title', true);
$testimonial_section_content = get_post_meta($id, 'testimonial_section_content', true);
$testimonials = get_post_meta($id, 'testimonial_list_item', true);
$testimonial_button_text = get_post_meta($id, 'testimonial_button_text', true);
$testimonial_button_link = get_post_meta($id, 'testimonial_button_link', true);
if ($home_testimonial_switch != 'off' && (strlen($testimonial_section_title) || !empty($testimonials))):
    ?>
    <section class="section background text-white text-center"
             style="background-image: url(<?php echo $testimonial_bg; ?>)">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="pricing-plan-content-wrap">
                        <div class="section-title">
                            <?php if (strlen($testimonial_section_title)): ?>
                                <h2 class="textheading2 oversized"><?php echo $testimonial_section_title; ?></h2>
                            <?php endif; ?>
                            <?php onecom_edit_icon('page_meta', '#setting_home_testimonial', $id); ?>
                        </div>
                        <div class="section-content textnormal">
                            &nbsp;&nbsp;
                            <?= $testimonial_section_content; ?>
                        </div>
                    </div>  
                </div>
            </div>
            <?php if (!empty($testimonials)): ?>
                <div class="testimonials-row row">
                    <?php foreach ($testimonials as $testimonial): if ($testimonial['title'] || $testimonial['testimonial_subtitle'] || $testimonial['testimonial_amount'] || $testimonial['testimonial_content']): ?>
                            <div class="pricing-col">
                                <div class="price-header">
                                    <h2 class="textheading2"><?php echo $testimonial['title']; ?></h2>
                                </div>
                                <div class="price-amount center-align mt-5">
                                    <p class="textnormal "><?php echo $testimonial['testimonial_amount']; ?></p>
                                </div>
                                <div class="textnormal"><?php echo nl2br($testimonial['testimonial_content']); ?></div>
                                <?php if ($testimonial_button_text):?>
                                <div class="price-cta"><a class="button-prime" href="<?php echo esc_url($testimonial_button_link) ?>"><?php echo $testimonial_button_text ?></a></div>
                                <?php endif;?>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
    <?php endif; ?>

        </div>
    </section>
<?php endif; ?>