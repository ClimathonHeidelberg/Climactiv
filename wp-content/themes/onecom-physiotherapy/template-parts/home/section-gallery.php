<?php
$id = get_the_ID();
$home_gallery_switch = get_post_meta($id, 'home_gallery_switch', true);
$gallery_section_title = get_post_meta($id, 'gallery_section_title', true);
$gallery_section_content = get_post_meta($id, 'gallery_section_content', true);
$gallery_btn_title = get_post_meta($id, 'gallery_btn_title', true);
$gallery_images = get_post_meta($id, 'gallery_images', true);
if ($home_gallery_switch != 'off' && (strlen($gallery_section_title) || strlen($gallery_section_content))):
    ?>
    <section class="section section-gallery solid text-center white-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2 class="textheading2 oversized"><?php echo $gallery_section_title; ?></h2>
                        <?php onecom_edit_icon('page_meta', '#setting_home_newsletter', $id); ?>
                    </div>
                    <div class="section-content textnormal my-5">
                        <?php echo wpautop($gallery_section_content); ?>
                    </div>                        
                </div>
                <div class="col-12">
                    <?php echo do_shortcode($gallery_images); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>