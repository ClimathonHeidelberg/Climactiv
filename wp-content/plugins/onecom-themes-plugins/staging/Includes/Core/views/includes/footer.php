</div> <!-- wrap -->

<div id="onecom-staging-error-wrapper">
    <div id="onecom-staging-error-details"></div>
</div>

<?php add_thickbox(); ?>

<!-- LIVE TO STAGING MODALS -->
<div id="staging-update-confirmation" style="display:none">
    <div class="one-dialog-container">
        <span class="dashicons dashicons-no-alt one-dialog-close" title="close"></span>
        <div class="one-dialog-container-content">
            <h3><?php _e( 'Are you sure?','onecom-wp' ); ?></h3>
            <p>
				<?php _e( 'This will overwrite your staging website with a copy of the files and database from your live website. All changes made in your staging website will be lost.', 'onecom-wp' ); ?>
            </p>
            <p align="right">
                <button class="one-button btn button_1 one-button-update-staging-confirm"><?php _e( 'OK', 'onecom-wp' ); ?></button>
                <button class="one-button btn button_3 one-button-update-staging-cancel"><?php _e( 'Cancel', 'onecom-wp' ); ?></button>
            </p>
        </div>
    </div>
</div>
<div id="staging-deployment-confirmation" style="display:none">
    <div class="one-dialog-container">
        <span class="dashicons dashicons-no-alt one-dialog-close" title="close"></span>
        <div class="one-dialog-container-content">
            <h3><?php _e( 'Are you sure?','onecom-wp' ); ?></h3>
            <p>
				<?php _e( 'This takes a snapshot of your blog and copies it to a "staging area" where you can test changes without affecting your live site. There\'s only one staging area, so every time you click this button the old staging area is lost forever, replaced with a snapshot of your live blog.', 'onecom-wp' ); ?>
            </p>
            <p align="right">
                <button class="one-button btn button_1 one-button-copy-to-live-confirm"><?php _e( 'OK', 'onecom-wp' ); ?></button>
                <button class="one-button btn button_3 one-button-copy-to-live-cancel"><?php _e( 'Cancel', 'onecom-wp' ); ?></button>
            </p>
        </div>
    </div>
</div>
<div id="staging-delete" style="display:none">
    <div class="one-dialog-container">
        <span class="dashicons dashicons-no-alt one-dialog-close" title="close"></span>
        <div class="one-dialog-container-content">
            <h3><?php _e( 'Are you sure?','onecom-wp' ); ?></h3>
            <p>
				<?php _e( 'The staging site will be lost.', 'onecom-wp' ); ?>
            </p>

            <p align="right">
                <button class="one-button btn button_1 one-button-delete-staging-confirm"><?php _e( 'OK', 'onecom-wp' ); ?></button>
                <button class="one-button btn button_3 one-button-delete-staging-cancel"><?php _e( 'Cancel', 'onecom-wp' ); ?></button>
            </p>
        </div>
    </div>
</div>

<!-- STAGING TO LIVE MODAL -->
<div id="staging-copy-confirmation" style="display:none">
    <div class="one-dialog-container">
        <span class="dashicons dashicons-no-alt one-dialog-close" title="close"></span>
        <div class="one-dialog-container-content">
            <h3><?php _e( 'Are you sure?','onecom-wp' ); ?></h3>
            <p>
				<?php _e( 'This will overwrite your live website with a copy of the files and database from your staging website.', 'onecom-wp' ); ?>
            </p>
            <p align="right">
                <button id="one-button-copy-to-live-confirm" class="one-button btn button_1 one-button-copy-to-live-confirm"><?php _e( 'OK', 'onecom-wp' ); ?></button>
                <button class="one-button btn button_3 one-button-copy-to-live-cancel"><?php _e( 'Cancel', 'onecom-wp' ); ?></button>
            </p>
        </div>
    </div>
</div>

<!-- loader -->
<div class="loading-overlay fullscreen-loader">
    <div class="loader"></div>
</div>

<!-- Scroll to top -->
<span class="dashicons dashicons-arrow-up-alt onecom-move-up"></span>

<!-- DEBUGGER -->
<div id="console_log" disabled style="display: none;" ></div>
<a href="javascript:;" onclick="jQuery('#one_staging_area').toggle();" style="display: none">Dev Tools</a>
<?php /*echo "<pre>"; var_dump($clones); echo '</pre>'; */?>
<table id="one_staging_area" border="1" cellpadding="1" cellspacing="0" width="100%" style="display: none;">
    <caption id="onme_errors"></caption>
    <thead>
    <tr>
        <th>
            <h4><strong><u>Step</u> 1:</strong> Scan Directories & DB <button class="button button-primary" id="scan_dirs">Scan</button></h4>
            <div class="stopwatch _scan">Execution Time : <span class="dashicons dashicons-clock"></span> <span id="scan_stopwatch">00:00:00</span></div>
        </th>
        <th id="disk_area_head">
            <h4><strong><u>Step</u> 2:</strong> Check Disk Space <button class="button button-primary"  id="disk_space">Check</button></h4>
            <div class="stopwatch _disk"><span class="dashicons dashicons-clock"></span> <span id="disk_stopwatch">00:00:00</span></div>
        </th>
        <th id="clone_area_head">
            <h4><strong><u>Step</u> 3:</strong> Run Clone <button class="button button-primary" id="run_clone">Clone</button> <span class="spinner"></span></h4>
            <div class="stopwatch _clone"><span class="dashicons dashicons-clock"></span> <span id="clone_stopwatch">00:00:00</span></div>
        </th>

    </tr>
    </thead>
    <tbody>
    <tr>
        <td id="dir_list">&nbsp;</td>
        <td id="av_space" valign="top">&nbsp;</td>
        <td id="clone_log" valign="top">&nbsp;</td>
    </tr>
    </tbody>
</table>