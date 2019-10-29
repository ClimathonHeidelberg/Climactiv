<div class="wrap onecom-staging-wrap">
    <div class="onecom-notifier"></div>
    <h2 class="one-logo">
        <div class="textleft">
            <span>
                <?php _e( 'One Staging', 'onecom-wp' ); ?>
                <?php if (isset($is_staging) && (bool) $is_staging === true): ?>
	                <span class="one-entry-flag one-entry-live" title="<?php _e( 'This is your Staging site.', 'onecom-wp' ); ?>">STAGING</span>
                <?php endif; ?>
			</span>
        </div>
        <div class="textright">
            <img src="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo.png' ?>" alt="One.com" srcset="<?php echo ONECOM_WP_URL.'/assets/images/one.com-logo@2x.png 2x' ?>" />
        </div>
    </h2>