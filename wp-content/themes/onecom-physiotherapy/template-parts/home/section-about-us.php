<?php
$id = get_the_ID();
$home_surfing_switch = get_post_meta($id, 'home_surfing_switch', true);
$experience_section_title = get_post_meta($id, 'experience_section_title', true);
$experience_section_sub_title = get_post_meta($id, 'experience_section_sub_title', true);
$experience_section_content = get_post_meta($id, 'experience_section_content', true);
$btn_link_1 = get_post_meta($id, 'experience_section_btn_link', true);
$btn_title_1 = get_post_meta($id, 'experience_section_btn_title', true);
$btn_link_2 = get_post_meta($id, 'experience_section_btn_link_2', true);
$btn_title_2 = get_post_meta($id, 'experience_section_btn_title_2', true);
$experience_section_bg = get_post_meta($id, 'experience_section_bg', true);
if ($experience_section_bg){
	$experience_section_bg_url = wp_get_attachment_image_src($experience_section_bg['background-image'], 'medium_large_featured');
}else{
	$experience_section_bg_url ='';
}
?>

<?php if ($home_surfing_switch != 'off' && (strlen($experience_section_title) || strlen($experience_section_content))): ?>
    <section class="section background about-us-home">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-5 about-us-home-bg">
                </div>
                <div class="col-sm-12 col-md-7 home-about-us-wrap">
                    <div class="home-about-us-text-wrap">
                        <div class="section-title">
                            <h2 class="textheading2 oversized"><?php echo $experience_section_title; ?></h2>
                            <?php onecom_edit_icon('page_meta', '#setting_home_experience', $id); ?>
                        </div>
                        <?php if ($experience_section_sub_title): ?>
                            <div class="section-sub-title">
                                <h3 class="textheading3 mt-4 mb-5"><?php echo $experience_section_sub_title ?></h3>
                            </div>
                        <?php endif; ?>
                        <div class="section-content textnormal">
                            <?php echo nl2br($experience_section_content); ?>
                        </div>

                        <div class="section-button button-wrap mt-5">
                            <?php
                            if ('' == $btn_link_1 || '#' == $btn_link_1) {
	                            $btn_link_1 = get_permalink(get_page_by_path('about-us'));
                            }
                            ?>
                            <?php if (strlen($btn_title_1)): ?>
                                <a href="<?php echo $btn_link_1; ?>" class="button button-alt"><?php echo $btn_title_1; ?></a>
                            <?php endif;
                            if ('#' == $btn_link_2 || '' == $btn_link_2) {
	                            $btn_link_2 = get_permalink(get_page_by_path('contact'));
                            }
                            ?>
                            <?php if (strlen($btn_title_2)): ?>
                                <a href="<?php echo $btn_link_2; ?>" class="button button-prime ml-md-3"><?php echo $btn_title_2; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
    <?php
    $exp_section_bg = get_post_meta($id, 'experience_section_bg', true);

    if (!empty($exp_section_bg)) {
        $exp_section_bg['background-image'] = wp_get_attachment_image_src($exp_section_bg['background-image'])[0];

        printf(".about-us-home-bg{%s}", ot_check_bg_css_prop($exp_section_bg));
    }
    ?>
        </style>
    </section>
<?php endif; ?>