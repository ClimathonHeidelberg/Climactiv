<div id="onestaging-clonepage-wrapper">

    <!-- Page Header -->
    <?php require_once($this->path . "views/includes/header.php"); ?>

    <div class="wrap_inner inner one_wrap">
        <div class="one-card">
            <div class="one-card-inline-block one-card-align-left">
                <img src="<?php echo ONECOM_WP_URL.'assets/images/staging-icon-blocked.svg' ?>" alt="One Staging" class="one-card-staging-create-icon" />
            </div>
            <div class="one-card-inline-block one-card-align-left one-card-staging-content">
                <div id="staging-create" class="one-card-staging-create card-1">
                    <div class="one-card-staging-create-info" id="staging_entry">
                        <h3 class="no-top-margin"><?php _e( 'Staging feature not available.', 'onecom-wp' ); ?></h3>
                        <div class="one-staging-actions card-3">
                            <p><strong><?php _e( 'Staging feature is not yet available for the following type of WordPress websites:');?></strong></p>
                            <ol>
                                <li>WordPress Multisite</li>
                                <li>WordPress having files inside a subdirectory but running on root</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- wrap_inner -->
    </div> <!-- wrap -->
</div>