<?php
$home_features_switch = get_post_meta(get_the_ID(), 'home_features_switch', true);
$features_section_title = get_post_meta(get_the_ID(), 'features_section_title', true);
$features_section_content = get_post_meta(get_the_ID(), 'features_section_content', true);
$features_blocks = get_post_meta(get_the_ID(), 'features_list_item', true);
$features_section_btn_title = get_post_meta(get_the_ID(), 'features_section_btn_title', true);
$ftr_link = get_post_meta(get_the_ID(), 'features_section_btn_link', true);
$features_section_bg = get_section_background_image('features_section_bg');
?>

<?php if ($home_features_switch != 'off' && (strlen($features_section_title) || strlen($features_section_content) || !empty($features_blocks) )): ?>
    <section class="section background text-white benefit-section" style="background-image: url(<?php echo $features_section_bg; ?>);">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title">
                        <h2><?php echo $features_section_title; ?></h2>
						<?php onecom_edit_icon('page_meta', '#setting_home_features', get_the_ID()); ?>
                    </div>
                    <div class="section-content">
						<?php echo $features_section_content; ?>
                    </div>
					<?php
					if (!empty($features_blocks)):
						$total_count = count($features_blocks);
						?>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 d-md-flex d-sm-block align-items-center justify-content-center">
	                <?php if (strlen($features_section_btn_title)): ?>
                        <div class="section-button my-5 mb-md-0">
			                <?php
			                if ('#' == $ftr_link || '' == $ftr_link) {
				                $ftr_link = get_permalink(get_page_by_path('contact'));
			                }
			                ?>
                            <a href="<?php echo $ftr_link; ?>" class="subscribe-btn button-prime"><?php echo $features_section_btn_title; ?></a>
                        </div>
	                <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>