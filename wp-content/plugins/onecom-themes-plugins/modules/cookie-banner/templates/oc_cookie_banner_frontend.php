<?php 
    $oc_cb_data = get_site_option('oc_cb_configuration');
    $oc_cb_data = $oc_cb_data['config'];
    $oc_cb_show             = $oc_cb_data['show'];
    $oc_cb_text             = $oc_cb_data['banner_text'];
    $oc_cb_policy_link      = $oc_cb_data['policy_link'];
    $oc_cb_policy_link_text = $oc_cb_data['policy_link_text'];
    $oc_cb_policy_link_url  = $oc_cb_data['policy_link_url'];
    $oc_cb_button_text      = $oc_cb_data['button_text'];
    $oc_cb_banner_style     = $oc_cb_data['banner_style'];
 ?>

<?php if($oc_cb_show): ?>
    <!-- Cookie banner START -->
        <div id="oc_cb_wrapper" class="oc_cb_wrapper <?php echo "fill_".$oc_cb_banner_style; ?>"> 
            <div class="oc_cb_content">
                <?php if($oc_cb_text){ ?>
                    <div class="oc_cb_text">
                        <p>
                            <?php echo @force_balance_tags($oc_cb_text)."&nbsp;"; ?>
                            <?php if($oc_cb_policy_link && $oc_cb_policy_link_text && $oc_cb_policy_link_url){ ?>
                                <?php printf('<a href="%s" target="_blank">%s</a>', $oc_cb_policy_link_url, $oc_cb_policy_link_text); ?>
                            <?php } ?>
                        </p>
                    </div>
                <?php } ?>

                <?php if($oc_cb_button_text) { ?>
                    <div class="oc_cb_btn_wrap">
                        <button class="oc_cb_btn" id="oc_cb_btn"><?php echo $oc_cb_button_text; ?></button>
                    </div>
                <?php } ?>
            </div>
        </div>
    <!-- Cookie banner END -->
<?php endif; ?>