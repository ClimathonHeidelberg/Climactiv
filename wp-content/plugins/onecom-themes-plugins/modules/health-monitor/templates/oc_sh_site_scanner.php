<div class="wrap">
    <div class="loading-overlay fullscreen-loader">
        <div class="loading-overlay-content">
            <div class="loader"></div>
        </div>
    </div>
    <div class="onecom-notifier"></div>
    <h2 class="one-logo ocsh-logo">
        <div class="textleft">
            <span id="ocsh-site-security">
                <?php _e('Health Monitor', 'onecom-wp');?> <span class="components-spinner"></span>
            </span>
            <p class="ocsh-page-desc">
                <?php echo __('Health Monitor lets you monitor the essential security checkpoints and fix them if needed.', 'onecom-wp') ?>
            </p>
        </div>
        <div class="textright">
            <img src="<?php echo ONECOM_WP_URL . '/assets/images/one.com-logo.png' ?>" alt="One.com"
                srcset="<?php echo ONECOM_WP_URL . '/assets/images/one.com-logo@2x.png 2x' ?>" />
        </div>
    </h2>
    <div class="wrap_inner inner one_wrap">
        <ul class="hidden ocsh-scan-result">
            <li id='ocsh-updates' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('PHP version', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-plugin-updates' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Plugins', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-theme-updates' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Themes', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-wp-updates' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('WordPress version', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-wp-org-comm' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Connection to Wordpress.org', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-core-updates' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('WordPress core updates', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-ssl' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('SSL certificate', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-file-execution' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('File execution in uploads', 'onecom-wp') ?></h4>
                <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-file-permissions' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('WP file directory and files permissions', 'onecom-wp') ?>
                </h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-db' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Database security', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-file-edit' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('File editing from admin', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-usernames' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Insecure usernames', 'onecom-wp') ?></h4> <span></span>
                <div class="osch-desc hidden"></div>
            </li>
            <li id='ocsh-discouraged-plugins' class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Using discouraged plugins', 'onecom-wp') ?></h4>
                <span></span>
                <div class="osch-desc hidden"></div>
            </li>
        </ul>
        <ul id="ocsh-needs-attention" class="ocsh-scan-result">
            <?php if (oc_sh_check_debug_mode()['status'] == OC_OPEN): $debug_result = oc_sh_check_debug_mode();?>
            <li id="ocsh-err-reporting" class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Debug mode/Error reporting', 'onecom-wp') ?></h4> <span
                    class="ocsh-error"></span>
                <div class="osch-desc hidden">
                   <?php echo sprintf(__('Your site is configured to display code errors to visitors. Unless you are debugging or troubleshooting your site, we recommend turning this off. The reason for this is that the errors can be used to find vulnerabilities in your site. To fix this: %s link to guide %s', 'onecom-wp'), "<a href='https://help.one.com/hc/en-us/articles/115005593705-How-do-I-enable-error-messages-for-PHP-' target='_blank'>", "</a>","<a href='https://help.one.com/hc/en-us/articles/115005594045-How-do-I-enable-debugging-in-WordPress-' target='_blank'>", "</a>"); ?>
                   <!-- <a target="_blank" href="https://help.one.com/hc/en-us"><?php ?>Learn more</a> -->
                </div>
            </li>
            <?php endif;?>
        </ul>
        <div class="ocsh-separator hidden"></div>
        <ul id="ocsh-all-ok" class="ocsh-scan-result">
            <?php if (oc_sh_check_debug_mode()['status'] == OC_RESOLVED): ?>
            <li id="ocsh-err-reporting" class='ocsh-bullet'>
                <h4 class="ocsh-scan-title"><?php echo __('Debug mode/Error reporting', 'onecom-wp') ?></h4> <span class="ocsh-success"></span>
                <div class="osch-desc hidden"><?php echo oc_sh_check_debug_mode()['desc'] ?></div>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>
<?php oc_sh_check_auto_updates();?>