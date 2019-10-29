        <!--Staging entry box-->
        <div id="staging_entry">
            <div class="one-staging-details card-2">
                <div class="one-staging-site-info box">

                    <?php
                    if(!empty($clones)):
                        foreach ($clones as $key=>$clone): ?>
                            <div class="one-staging-entry staging-entry" id="entry_<?php echo $key; ?>" data-staging-id="<?php echo $key; ?>">
                                <div class="entry-name">

                                    <?php if($cloneExists): ?>
                                        <h3><?php _e( 'Your staging site', 'onecom-wp' ); ?></h3>
                                    <?php else: ?>
                                        <h3><?php _e( 'Staging site broken', 'onecom-wp' ); ?></h3>
                                    <?php endif; ?>

                                    <?php if($cloneExists): ?>
                                       <div class="entry-link">
                                            <p>
                                            <span><?php _e( 'Staging Frontend:', 'onecom-wp' ); ?> <a href="<?php echo $clone['url']; ?>" target="_blank"><?php echo $clone['url']; ?></a></span>
                                            <br><span><?php _e( 'Staging Backend:', 'onecom-wp' ); ?> <a href="<?php echo trailingslashit($clone['url']); ?>wp-login.php" target="_blank"><?php echo trailingslashit($clone['url']); ?>wp-login.php</a></span>
                                            </p>
                                       </div>
                                    <?php else: ?>
                                        <div>
                                            <p><?php _e('We have detected that your staging site is broken due to missing database table(s) and/or directory(s).', 'onecom-wp' ); ?><br>
                                               <?php _e('Click on "Rebuild Staging" to regenerate your staging site.', 'onecom-wp' ); ?></p>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php break;
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <div class="one-staging-actions card-3">
	            <?php if(isset($cloneExists) && $cloneExists): ?>
                    <p><?php _e( 'The staging website is a copy of your live website, where you can test new plugins and themes without affecting your live website.', 'onecom-wp' ); ?> <br>
                        <?php _e( 'Only one staging version can be created for each website. When you rebuild a staging website, any existing staging site will be replaced with a new snapshot of your live website.', 'onecom-wp' ); ?><br>
                        <?php _e( 'The login details for the staging backend are the same as for the live website.', 'onecom-wp' ); ?><br><br>

                        <?php _e( 'Caution: Rebuilding will overwrite all files and the database of your existing staging website.', 'onecom-wp' ); ?>
                    </p>
	            <?php endif; ?>
                <a href="<?php echo onecom_generic_locale_link( $request = 'staging_guide', get_locale() ); ?>" target="_blank" class="help_link"><?php _e( 'Need help?', 'onecom-wp' ); ?></a>
                <br><br>
                <button class="one-button btn button_1 one-button-update-staging" data-staging-id="" data-dialog-id="staging-update-confirmation" data-title="<?php _e( 'Are you sure?', 'onecom-wp' ); ?>" data-width="450" data-height="195"><?php _e( 'Rebuild Staging', 'onecom-wp' ); ?></button>
                <a href="javascript:;" class="staging-trash one-button-delete-staging"  title="<?php _e("Delete Staging", "onecom-wp"); ?>" data-title="<?php _e( 'Are you sure?', 'onecom-wp' ); ?>" data-dialog-id="staging-delete" data-width="450" data-height="155"><span class="dashicons dashicons-trash"></span> <?php _e("Delete Staging", "onecom-wp"); ?></a>
            </div>
        </div>