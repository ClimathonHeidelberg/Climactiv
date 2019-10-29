
    <div class="wrap_inner inner one_wrap">
        <div class="one-card">

            <div class="loading-overlay fullscreen-loader new-staging">
                <div class="loading-overlay-content">
                    <h3 class="loading-overlay-message"><?php _e( 'Please wait, while we are copying your live site to the staging site.', 'onecom-wp' ); ?></h3>
                    <div class="loader"></div>
                </div>
            </div><!-- loader -->

            <?php if(!empty($clones)): ?>
                <div class="loading-overlay fullscreen-loader update-loader">
                    <div class="loading-overlay-content">
                        <h3 class="loading-overlay-message"><?php _e( 'Please wait, while we are updating staging site.', 'onecom-wp' ); ?></h3>
                        <div class="loader"></div>
                    </div>
                </div><!-- loader -->
                <div class="loading-overlay fullscreen-loader delete-loader">
                    <div class="loading-overlay-content">
                        <h3 class="loading-overlay-message"><?php _e( 'Please wait, while we are deleting staging site.', 'onecom-wp' ); ?></h3>
                        <div class="loader"></div>
                    </div>
                </div><!-- loader -->
            <?php endif; ?>

            <div class="one-card-inline-block one-card-align-left onecom-staging-logo">
	            <?php if(empty($clones) || $cloneExists): ?>
                    <img src="<?php echo ONECOM_WP_URL.'assets/images/staging-icon.svg' ?>" alt="One Staging - Ready" class="one-card-staging-create-icon" />
                <?php elseif(!empty($clones) && !$cloneExists): ?>
                    <img src="<?php echo ONECOM_WP_URL.'assets/images/staging-icon-blocked.svg' ?>" alt="One Staging - Broken" class="one-card-staging-create-icon" />
                <?php endif; ?>
            </div>
            <div class="one-card-inline-block one-card-align-left one-card-staging-content">

               <?php
                if(!empty($clones)):
                    require $this->path . 'views/ajax/staging_details.php'; ?>

                    <div id="staging-create" class="one-card-staging-create card-1 hide">

                <?php else: ?>
                    <div id="staging-create" class="one-card-staging-create card-1">
                <?php endif; ?>

                        <div class="one-card-staging-create-info">
                            <h3 class="no-top-margin"><?php _e( 'Create a staging version of your site to try out new plugins, themes and customizations', 'onecom-wp' ); ?></h3>
		                    <?php _e( 'The staging environment allows you to make changes to your site, without it affecting the live version of your website. When you create a staging site, it will be a snapshot of your live website.' , 'onecom-wp' );?><br><?php _e( 'You can use it as a playground to test new plugins and themes. Once you are satisfied with the result, you can copy all the changes to your live website with a single click.' , 'onecom-wp' ); ?><br><br>
                            <strong><?php _e( 'Please note', 'onecom-wp' ); ?>: </strong> <?php _e( 'Plugins that are URL sensitive may not work as expected on the staging site.' , 'onecom-wp' ); ?>
                            <br><br>
                            <a href="<?php echo onecom_generic_locale_link( 'staging_guide', get_locale() ); ?>" class="help_link" target="_blank"><?php _e( 'Need help?', 'onecom-wp' ); ?></a>
                            <br>
                            <div class="one-card-action">
                                <input type="button" value="<?php _e( 'Create staging site', 'onecom-wp' ); ?>" class="one-button btn button_1 one-button-create-staging no-left-margin no-right-margin" />
                            </div>
                        </div>
                    </div>
            </div>
    </div> <!-- wrap_inner -->

<style>
        /*#TB_window{max-height: 212px;    max-width: 510px;}*/
        .one-logo .textleft span.one-entry-flag {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            color: #396fc9;
            margin-left: 5px;
            vertical-align: 1px;
            /*background-color: #396fc9;*/
            border:1px solid #396fc9;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .one-staging-entry.staging-entry{
            display: inline-block;
            /*padding: 12px 20px;
            border: 1px solid #e6e6e6;
            min-width: 320px;
            background-color: #fff;
            box-shadow: 0 0 8px #EFEFF0;*/
        }
        .one-staging-entry .entry-name h4{
            margin:0 0 4px 0;
            font-size:16px;
        }
        .one-staging-entry .entry-controls ul,
        .one-staging-entry .entry-controls ul li{
            list-style-type: none;
            margin:0;
        }
        .one-staging-entry .entry-link{
            color:#666;
            margin-top: 10px;
        }
        .one-staging-entry .entry-controls{
            margin: 21px 0 1px;
            font-size:12px;
        }
        .one-staging-entry .entry-controls ul li{
            display: inline-block;
            vertical-align: top;
        }
        .one-staging-entry .entry-controls ul li{
            margin: 0 4px;
        }
        .one-staging-entry .entry-controls ul li:first-child{
            margin:0;
        }
        .one-staging-entry .entry-controls a{
            background-color:#fff;
            padding:3px 7px 2px 5px;
            border:1px solid #396fc9;
            color:#396fc9;
            cursor: pointer;
        }
        .one-staging-entry .entry-controls a.one-button-delete-staging{
            border-color:#ff6363;
            color: #ff6363;
        }
        .one-staging-entry .entry-controls a:hover{
            border-color:#396fc9;
            background-color:#396fc9;
            color:#fff;
        }
        .one-staging-entry .entry-controls a.one-button-delete-staging:hover{
            border-color:#ff6363;
            background-color:#ff6363;
            color:#fff;
        }
        .one-staging-entry .entry-controls .dashicons{
            font-size: 18px;
            vertical-align: -4px;
        }
        .loading-overlay.element-loader{
            min-height: 200px;
        }
    </style>