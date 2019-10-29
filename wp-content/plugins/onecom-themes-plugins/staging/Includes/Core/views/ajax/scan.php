<label id="onestaging-clone-label" for="onestaging-new-clone" style="display: none">
    <?php echo __('Staging Site Name:', 'onestaging')?>
    <input type="text" id="onestaging-new-clone-id" value="<?php echo $options->current; ?>"<?php if (null !== $options->current) echo " disabled='disabled'"?>>
</label>

<span class="onestaging-error-msg" id="onestaging-clone-id-error" style="display:none;">
        <?php echo __(
            "<br>Probably not enough free disk space to create a staging site. ".
            "<br> You can continue but its likely that the copying process will fail.",
            "onestaging"
        )?>
</span>

<table border="1" width="100%">
    <thead>
    <tr>
        <th>
	        <?php echo __("DB Tables", "onestaging")?>
        </th>
        <th>
	        <?php echo __("Files", "onestaging")?>
        </th>
    </tr>
    </thead>

    <tbody>
        <tr>
            <td width="45%" valign="top">
                <div class="onestaging-tab-section" id="onestaging-scanning-db">
		            <?php do_action("onestaging_scanning_db")?>
                    <h4 style="margin:0">
			            <?php

			            echo __(
				            "Please Deselect the tables you dont want to copy."
			            )

                        /*
                         * echo __(
				            "Please Deselect the tables you dont want to copy. <br>Recommended: Select tables with prefix '{$scan->prefix}', only.",
				            "onestaging"
			            )
                         * */
                        ?>
                    </h4>
                    <div style="margin-top:10px;margin-bottom:10px; display: none;">
                        <a href="#" class="onestaging-button-unselect button"> None </a>
                        <a href="#" class="onestaging-button-select button"> <?php _e(OneStaging\OneStaging::getTablePrefix(), 'onestaging'); ?> </a>
                    </div>
		            <?php
		            //print_r( $options->excludedTables);
		            foreach ($options->tables as $table):
			            $attributes = in_array($table->name, $options->excludedTables) ? '' : "checked";
			            $attributes .= in_array($table->name, $options->clonedTables) ? " disabled" : '';
			            ?>
                        <div class="onestaging-db-table">
                            <label>
                                <input class="onestaging-db-table-checkboxes" type="checkbox" name="<?php echo $table->name?>" <?php echo $attributes?>>
					            <?php echo $table->name?>
                            </label>
                            <span class="onestaging-size-info">
				<?php echo $scan->formatSize($table->size)?>
			</span>
                        </div>
		            <?php endforeach ?>
                    <div style="margin-top:10px;display: none;">
                        <a href="#" class="onestaging-button-unselect button"> None </a>
                        <a href="#" class="onestaging-button-select button"> <?php _e(OneStaging\OneStaging::getTablePrefix(), 'onestaging'); ?> </a>
                    </div>
                </div>
            </td>

            <td width="45%" valign="top">
                <div class="onestaging-tab-section" id="onestaging-scanning-files">
                    <h4 style="margin:0">
			            <?php echo __("Uncheck the folders you do not want to copy. Click on them for expanding!", "onestaging")?>
                    </h4>

		            <?php echo $scan->directoryListing()?>

                    <div style="display: none;">
                        <h4 style="margin:10px 0 10px 0; display: none;">
				            <?php echo __("Extra directories to copy", "onestaging")?>
                        </h4>

                        <textarea id="onestaging_extraDirectories" name="onestaging_extraDirectories" style="width:100%;height:250px;display: none;"></textarea>
                        <p>
            <span>
                <?php
                echo __(
	                "Enter one folder path per line.<br>".
	                "Folders must start with absolute path: " . $options->root,
	                "onestaging"
                )
                ?>
            </span>
                        </p>

                        <p>
            <span>
                <?php
                if (isset($options->clone)){
	                echo __("All files will be copied to: ", "onestaging") . $options->root . $options->clone;
                }
                ?>
            </span>
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>

</table>