(function ($)
{

    var FLAG = {
        anyProcess: {
            'type' : '',
            'active' : false,
            'timer' : ''
        },
        jobFinished: false,
        getLogs: false
    };

    window.onbeforeunload = '';

    function nested_dirs(){
        if($('#dir_list').length){
            $('#dir_list').find('.wpstg-expand-dirs').bind('click', function(e){
                e.preventDefault();
                if(!$(this).hasClass('disabled')){
                    $(this).siblings(".wpstg-subdir").slideToggle();
                }
            });
        }
    }

    function onecom_get_staging() {

        var data = {
            action: 'onestg_get_staging',
            nonce: wpstg.nonce
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                //console.log(textStatus);

                $('#onme_errors').html("").html("Failed to get staging details, please reload the page and try again.");
            },
            success: function (data) {
                $(document).find('#staging_entry').slideUp().remove();
                $('#staging-create').slideUp('fast').before(data);
                $('.one-staging-msg').removeClass('hide');
                window.location.reload();
            },
            statusCode: {
                200: function () {},
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }

    function onecom_scanfiles() {

        var data = {
            action: 'onestg_scanning',
            nonce: wpstg.nonce
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                showGeneralError();
            },
            success: function (data) {

                if(data.error === true ){
                    showGeneralError(data.message);
                    return;
                }

                $('#dir_list').html('').html(data);

                // Bind Dir Tree Parent-Child slide animation
                nested_dirs();

                //Check free disk space
                checkDiskSpace();
            },
            statusCode: {
                200: function () {},
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }

    function checkDiskSpace() {

        var data = {
            action: 'onestg_check_disk_space',
            nonce: wpstg.nonce
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: 'JSON',
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                showGeneralError();
            },
            success: function (data) {

                if(data.error === true ){
                    showGeneralError(data.message);
                    return;
                }

                if (false === data)
                {
                    $("#onme_errors").text('Can not detect disk space.').show();
                    return;
                }

                // Get ready to start cloning.
                prepare_clone_data();

                // Not enough disk space
                $("#av_space").html('Available free disk space ' + data.freespace + ' <br> Estimated necessary disk space: ' + data.usedspace).show();
            },
            statusCode: {
                200: function () {},
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });
    }

    /**
     * Scroll the window log to bottom
     */
    function logscroll() {
        var $div = $("#console_log");
        if ("undefined" !== typeof ($div[0])) {
            $div.scrollTop($div[0].scrollHeight);
        }
    }

    /**
     * Append the log to the logging window
     */
    function getLogs(log)
    {
        if (log != null && "undefined" !== typeof (log)) {
            if (log.constructor === Array) {
                $.each(log, function (index, value) {
                    if (value === null) {
                        return;
                    }
                    if (value.type === 'ERROR') {
                        //$("#console_log").append('<span style="color:red;">[' + value.type + ']</span>-' + '[' + value.date + '] ' + value.message + '</br>');
                    } else {
                        //$("#console_log").append('[' + value.type + ']-' + '[' + value.date + '] ' + value.message + '</br>');
                    }
                })
            } else {
                //$("#console_log").append('[' + log.type + ']-' + '[' + log.date + '] ' + log.message + '</br>');
            }
        }
        logscroll();
    }

    /* Helper Functions */

    function getExcludedTables()
    {
        var excludedTables = [];

        $(".onestaging-db-table input:not(:checked)").each(function () {
            excludedTables.push(this.name);
        });

        return excludedTables;
    }

    function getIncludedDirectories()
    {
        var includedDirectories = [];

        $(".wpstg-dir input:checked").each(function () {
            var $this = $(this);
            if (!$this.parent(".wpstg-dir").parents(".wpstg-dir").children(".wpstg-expand-dirs").hasClass("disabled"))
            {
                includedDirectories.push($this.val());
            }
        });

        return includedDirectories;
    }


    function getExcludedDirectories()
    {
        var excludedDirectories = [];

        $(".wpstg-dir input:not(:checked)").each(function () {
            var $this = $(this);
            if (!$this.parent(".wpstg-dir").parents(".wpstg-dir").children(".wpstg-expand-dirs").hasClass("disabled"))
            {
                excludedDirectories.push($this.val());
            }
        });

        return excludedDirectories;
    }

    function getIncludedExtraDirectories()
    {
        var extraDirectories = [];

        if (!$("#wpstg_extraDirectories").val()) {
            return extraDirectories;
        }

        var extraDirectories = $("#wpstg_extraDirectories").val().split(/\r?\n/);
        console.log(extraDirectories);

        //excludedDirectories.push($this.val());

        return extraDirectories;
    }


    /*#############################
    * ## Delete Existing Staging ##
    * ###########################*/
    function delete_staging(staging_id){

        var totalTime = ( new Date().getTime() - FLAG.anyProcess.timer ).toFixed(2);
        totalTime = (totalTime/1000).toFixed(2);

        data = {
            action: "onestg_delete_clone",
            clone: staging_id,
            nonce: wpstg.nonce,
            excludedTables: getExcludedTables(),
            deleteDir: $("#deleteDirectory:checked").val(),
            totalTime: totalTime
        };

        onecom_staging_progress(wpstg.msgDelStg, 0, 0);

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                $("#onme_errors").text('Some error occurred').show();
                showGeneralError(data.message);
            },
            success: function (data) {

                if (data) {
                    // Error
                    if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                        showGeneralError(data.message);
                        return;
                    }

                    // Finished
                    if ("undefined" !== typeof data.delete && data.delete === 'finished') {
                        onecom_staging_progress(wpstg.msgDelStg, 100, 1);
                        $("#entry_"+staging_id).fadeIn(5000, function () {
                            $(this).remove();
                        });
                        FLAG.anyProcess.type = '';
                        FLAG.anyProcess.active = false;
                        window.onbeforeunload = '';
                        window.location.reload();

                        $( '.onecom-notifier' ).html( 'Staging website has been removed.' ).attr( 'type', 'success' ).addClass( 'show' );
                        setTimeout( function(){
                            $( '.onecom-notifier' ).removeClass( 'show' );
                            $( '.one-dialog-close' ).trigger( 'click' );
                            $( '.one-staging-details' ).hide();
                            $( '.one-staging-actions' ).hide();
                            $( '#staging-create' ).slideDown();
                            $( '.one-card-staging-create' ).fadeIn( 300 );
                        }, 7000 );
                        return;
                    }
                }
                // continue
                if (true !== data)
                {
                    delete_staging(staging_id);
                    return;
                }
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }


    /* ############################
    * ##  Deploy Staging to Live ##
    * ########################## */
    function prepare_staging_to_live(live_directory) {
        if(!live_directory) return;


        /* Existing staging ID in the below statement to update  */
        var liveID = live_directory;
        var excludedTables = getExcludedTables();
        var includedDirectories = getIncludedDirectories();
        var excludedDirectories = getExcludedDirectories();
        var extraDirectories = getIncludedExtraDirectories();
        console.log(includedDirectories);

        data = {
            action: "onestg_deploy",
            nonce: wpstg.nonce,
            liveDirectory: liveID,
            excludedTables: excludedTables,
            includedDirectories: includedDirectories,
            excludedDirectories: excludedDirectories,
            extraDirectories: extraDirectories
        };

        //console.log('Preparing for UPDATING clone...');


        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            error: function (xhr, textStatus, errorThrown) {

                console.log(xhr.status + ' ' + xhr.statusText);

                showGeneralError();
            },
            success: function (data) {
                // Error
                if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                    showGeneralError(data.message);
                    return;
                }

                // Start cloning
                start_clone();
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }



    /* ############################
    * ## Update Existing Staging ##
    * ########################## */
    function prepare_update_staging(staging_id) {
        if(!staging_id) return;


        /* Existing staging ID in the below statement to update  */
        var cloneID = staging_id;
        var excludedTables = getExcludedTables();
        var includedDirectories = getIncludedDirectories();
        var excludedDirectories = getExcludedDirectories();
        var extraDirectories = getIncludedExtraDirectories();

        data = {
            action: "onestg_update",
            nonce: wpstg.nonce,
            cloneID: cloneID,
            excludedTables: excludedTables,
            includedDirectories: includedDirectories,
            excludedDirectories: excludedDirectories,
            extraDirectories: extraDirectories
        };

        console.log('Preparing for UPDATING cloning...');

        $.ajax({
            url: ajaxurl,
            type: "POST",
            /*dataType: "JSON",*/
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                showGeneralError();
            },
            success: function (data) {
                // Error
                if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                    showGeneralError(data.message);
                    return;
                }
                
                // Start cloning
                start_clone();
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }


    /*#######################*/
    /* Start Cloning Process */
    /*#######################*/
    function prepare_clone_data() {
        onecom_staging_progress(wpstg.msgPrep, 0, 0);

        /* Set new ID for the staging in the below statement */
        var cloneID = wpstg.stgID;
        var stgPrefix = wpstg.stgPrefix;
        var excludedTables = getExcludedTables();
        var includedDirectories = getIncludedDirectories();
        var excludedDirectories = getExcludedDirectories();
        var extraDirectories = getIncludedExtraDirectories();

        data = {
            action: "onestg_cloning",
            nonce: wpstg.nonce,
            cloneID: cloneID,
            stgPrefix: stgPrefix,
            excludedTables: excludedTables,
            includedDirectories: includedDirectories,
            excludedDirectories: excludedDirectories,
            extraDirectories: extraDirectories
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText);
                showGeneralError();
            },
            success: function (data) {
                // Error
                if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                    showGeneralError(data.message);
                    return;
                }
                onecom_staging_progress(wpstg.msgPrep, 100, 0);

                // Start cloning
                start_clone();
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

        //return data;
    }


    function start_clone() {
        onecom_staging_progress(wpstg.msgNewStg, 0, 0);
        /*show spinner here*/

        // Clone Database
        setTimeout(function () {
            onecom_staging_progress(wpstg.msgCopyDB, 0, 0);
            cloneDatabase();
        }, wpstg.cpuLoad);
    }

    // Step 1: Clone Database
    function cloneDatabase()
    {
        if (true === FLAG.getLogs)
        {
            getLogs();
        }



        setTimeout(
            function () {

                var data = {
                    action: "onestg_clone_database",
                    nonce: wpstg.nonce
                };

                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    dataType: "JSON",
                    data: data,
                    error: function (xhr, textStatus, errorThrown) {
                        console.log(xhr.status + ' ' + xhr.statusText);
                        showGeneralError();
                    },
                    success: function (data) {

                        // Error
                        if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                            showGeneralError(data.message);
                            return;
                        }

                        onecom_staging_progress(wpstg.msgCopyDB, data.percentage, 0);
                        
                        // Add Log
                        if ("undefined" !== typeof (data.last_msg))
                        {
                            getLogs(data.last_msg);
                        }

                        // Continue clone DB
                        if (false === data.status)
                        {
                            setTimeout(function () {
                                cloneDatabase();
                            }, wpstg.cpuLoad);
                        }
                        // Next Step
                        else if (true === data.status)
                        {
                            setTimeout(function () {
                                onecom_staging_progress(wpstg.msgPrepDirs,0, 0);
                                prepareDirectories();
                            }, wpstg.cpuLoad);
                        }
                    },
                    statusCode: {
                        404: function () {
                            $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                        },
                        500: function () {
                            $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                        }
                    }
                });
            },
            500
        );
    }

    // Step 2: Prepare Directories
    function prepareDirectories()
    {
        if (true === FLAG.jobFinished)
        {
            return false;
        }

        if (true === FLAG.getLogs)
        {
            getLogs();
        }

        $('#clone_log').append('Creating Directory Tree...<hr>');

        setTimeout(
            function () {

                var data = {
                    action: "onestg_clone_prepare_directories",
                    nonce: wpstg.nonce
                };

                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: data,
                    error: function (xhr, textStatus, errorThrown) {
                        showGeneralError();
                    },
                    success: function (data) {
                        // Error
                        if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                            showGeneralError(data.message);
                            return;
                        }

                        onecom_staging_progress(wpstg.msgPrepDirs,data.percentage, 0);

                        // Add Log
                        if ("undefined" !== typeof (data.last_msg))
                        {
                            getLogs(data.last_msg);
                        }

                        if (false === data.status)
                        {
                            setTimeout(function () {
                                prepareDirectories();
                            }, wpstg.cpuLoad);
                        }
                        else if (true === data.status)
                        {
                            onecom_staging_progress(wpstg.msgCopyFiles,0,0);
                            cloneFiles();
                        }
                    },
                    statusCode: {
                        404: function () {
                            $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                        },
                        500: function () {
                            $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                        }
                    }
                });
            },
            500
        );
    }

    // Step 3: Clone Files
    function cloneFiles()
    {
        if (true === FLAG.jobFinished)
        {
            return false;
        }


        if (true === FLAG.getLogs)
        {
            getLogs();
        }

        var data = {
            action: "onestg_clone_files",
            nonce: wpstg.nonce
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                showGeneralError();
            },
            success: function (data) {
                // Error
                if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                    showGeneralError(data.message);
                    return;
                }

                if ("undefined" !== typeof (data.percentage)){
                    onecom_staging_progress(wpstg.msgCopyFiles,data.percentage,0);
                }

                // Add Log
                if ("undefined" !== typeof (data.last_msg))
                {
                    getLogs(data.last_msg);
                }

                if (false === data.status)
                {
                    setTimeout(function () {
                        cloneFiles();
                    }, wpstg.cpuLoad);
                }
                else if (true === data.status)
                {
                    setTimeout(function () {
                        replaceData();
                    }, wpstg.cpuLoad);
                }
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });

    }

    // Step 4: Replace Data
    function replaceData()
    {
        if (true === FLAG.jobFinished)
        {
            return false;
        }

        if (true === FLAG.getLogs)
        {
            getLogs();
        }

        var data ={
            action: "onestg_clone_replace_data",
            nonce: wpstg.nonce
        };

        onecom_staging_progress(wpstg.msgUpdateDB,0,0);

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                showGeneralError();
            },
            success: function (data) {
                // Error
                if ("undefined" !== typeof data.error && "undefined" !== typeof data.message) {
                    showGeneralError(data.message);
                    return;
                }
                onecom_staging_progress(wpstg.msgUpdateDB,data.percentage,0);

                // Add Log
                if ("undefined" !== typeof (data.last_msg))
                {
                    getLogs(data.last_msg);
                }

                if (false === data.status)
                {
                    setTimeout(function () {

                        replaceData();

                    }, wpstg.cpuLoad);
                }
                else if (true === data.status)
                {
                    finish();
                }
            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });


    }

    // Finish
    function finish(status)
    {
        if (true === FLAG.jobFinished)
        {
            showControls(status);
            return false;

        }

        onecom_staging_progress(wpstg.msgFinalize,0,0);

        var totalTime = ( new Date().getTime() - FLAG.anyProcess.timer ).toFixed(0);
        totalTime = (totalTime/1000).toFixed(2);

        var data = {
            action: "onestg_clone_finish",
            nonce: wpstg.nonce,
            totalTime: totalTime
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                showGeneralError();
                FLAG.anyProcess.timer = '';
            },
            success: function (data) {
                // Invalid data
                if ("object" !== typeof (data))
                {
                    console.log(
                        "Couldn't finish the cloning process properly. " +
                        "Your snapshot has been copied but failed to do clean up and " +
                        "saving its records to the database."
                    );
                    FLAG.anyProcess.timer = '';

                    return;
                }
                FLAG.anyProcess.type = '';
                FLAG.anyProcess.active = false;
                FLAG.anyProcess.timer = '';
                window.onbeforeunload = '';
                onecom_get_staging();

                $( '.onecom-notifier' ).html( wpstg.copy_msg ).attr( 'type', 'success' ).addClass( 'show' );
                setTimeout( function(){
                    $( '.onecom-notifier' ).removeClass( 'show' );
                    $( '.one-dialog-close' ).trigger( 'click' );
                }, 3000 );

                // Add Log
                if ("undefined" !== typeof (data.last_msg))
                {
                    getLogs(data.last_msg);
                }

                onecom_staging_progress(wpstg.msgFinished,100, 1);
                logscroll();


                // Finished
                FLAG.jobFinished = true;
                finish(data);

            },
            statusCode: {
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find ajax request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });
    }

    /* Display controls of newly created staging site */
    function showControls(data) {
        $('#clone_area_head').find('.spinner').removeClass('is-active');
        if('undefined' != data && 'undefined' != data.url){
            $('#onme_errors').html(
                "Staging site created : "+ data.url+'/wp-admin/'
            )
                .addClass('green').slideDown();
        }
        //Clone.Timer.toggle();
        $('.stopwatch._clone').addClass('done');
        return;
    }

    /*
    * Bind Button Event-handlers
    */

    // Create LIVE to STAGING
    $( '.one-button-create-staging' ).on('click', function() {
        $( '.loading-overlay.new-staging' ).addClass( 'show' );
        FLAG.anyProcess.type = 'staging_create';
        FLAG.anyProcess.active = true;
        FLAG.anyProcess.timer = new Date().getTime();
        window.onbeforeunload = onecomConfirmExit;
        onecom_scanfiles();
    } );

    $( '.one-button-update-staging' ).click( function() {
        var width = $( this ).attr( 'data-width' );
        var height = $( this ).attr( 'data-height' );
        var id = $( this ).attr( 'data-dialog-id' );
        var title = $( this ).attr( 'data-title' );
        tb_show( title, "#TB_inline?width="+width+"&height="+height+"&inlineId="+id+"&modal=true&class=thickbox", null );
    } );

    // Update LIVE to STAGING -- Confirmation Dialog
    $( '.one-button-update-staging-confirm' ).click( function() {
        $( '.loading-overlay.update-loader' ).addClass( 'show' );
        $( '.one-dialog-close' ).trigger('click');
        var staging_id = $('.one-staging-entry').data('staging-id');
        //console.log(staging_id);
        if(staging_id){
            FLAG.anyProcess.type = 'staging_rebuild';
            FLAG.anyProcess.active = true;
            FLAG.anyProcess.timer = new Date().getTime();
            window.onbeforeunload = onecomConfirmExit;
            //console.log(FLAG.anyProcess);
            prepare_update_staging(staging_id);
        }
        else{
            //$( '.loading-overlay' ).removeClass( 'show' );
            $( '.onecom-notifier' ).html( 'Error: Staging data not found.' ).attr( 'type', 'error' ).addClass( 'show' );
            setTimeout( function(){
                //$( '.loading-overlay' ).removeClass( 'show' );
                $( '.onecom-notifier' ).removeClass( 'show' );
                $( '.one-dialog-close' ).trigger( 'click' );
            }, 5000 );
        }
    } );

    // Cancel - Update Staging Button
    $( '.one-button-update-staging-cancel' ).click( function() {
        $(this).parents().find('.one-dialog-close').trigger('click');
    } );


    // Delete Staging button
    $( '.one-button-delete-staging' ).click( function() {
        var width = $( this ).attr( 'data-width' );
        var height = $( this ).attr( 'data-height' );
        var id = $( this ).attr( 'data-dialog-id' );
        var title = $( this ).attr( 'data-title' );
        tb_show( title, "#TB_inline?width="+width+"&height="+height+"&inlineId="+id+"&modal=true&class=thickbox", null );
    } );

    // Delete Staging button -- Confirmation
    $( '.one-button-delete-staging-confirm' ).click( function() {
        $( '.loading-overlay.delete-loader' ).first().addClass( 'show' );
        var staging_id = $('.one-staging-entry').data('staging-id');
        if(staging_id){
            // Call delete function
            $( '.one-dialog-close' ).trigger( 'click' );
            FLAG.anyProcess.type = 'staging_delete';
            FLAG.anyProcess.active = true;
            FLAG.anyProcess.timer = new Date().getTime();
            window.onbeforeunload = onecomConfirmExit;
            delete_staging(staging_id);
        }
        else{
            $( '.onecom-notifier' ).html( 'Error: Staging data not found.' ).attr( 'type', 'error' ).addClass( 'show' );
            setTimeout( function(){
                //$( '.loading-overlay' ).removeClass( 'show' );
                $( '.onecom-notifier' ).removeClass( 'show' );
                $( '.one-dialog-close' ).trigger( 'click' );
            }, 5000 );
        }
    } );

    /* *********************** */
    /* COPY STAGING TO LIVE  */
    /* *********************** */

    $( '.one-button-copy-to-live' ).click( function() {
        var width = $( this ).attr( 'data-width' );
        var height = $( this ).attr( 'data-height' );
        var id = $( this ).attr( 'data-dialog-id' );
        var title = $( this ).attr( 'data-title' );
        tb_show( title, "#TB_inline?width="+width+"&height="+height+"&inlineId="+id+"&modal=true&class=thickbox", null );
    } );

    $( '.one-button-copy-to-live-cancel, .one-button-delete-staging-cancel' ).click( function() {
        $( '.one-dialog-close' ).trigger( 'click' );
    } );



    $('#one-button-copy-to-live-confirm').on('click', function () {
        $( '.loading-overlay.deploy-loader' ).addClass( 'show' );
        $( '.one-dialog-close' ).trigger('click');
        var liveID = $('#deploy_to_live').data('live-id');
        console.log("Starting deployment Staging-to-Live : "+liveID);

        FLAG.anyProcess.type = 'staging_deploy';
        FLAG.anyProcess.active = true;
        FLAG.anyProcess.timer = new Date().getTime();
        window.onbeforeunload = onecomConfirmExit;
        prepare_staging_to_live(liveID);

    });

    /* ############# Warning on window leave ########### */
    function onecomConfirmExit()
    {
        if(FLAG.anyProcess.active === true){
            switch(FLAG.anyProcess.type){
                case 'stg':
                    return "Staging creation will get interrupted if you leave or reload this page.";
                    break;

                case 'live':
                    return "Copy staging to live will get interrupted if you leave or reload this page.";
                    break;

                case 'delete':
                    return "Staging will not get deleted properly if you leave or reload this page.";
                    break;

                case 'update':
                    return "Staging updation will get interrupted if you leave or reload this page.";
                    break;

                default:
                    return "Please do not leave the page. The current process will get interrupted.";
            }
        }
        return true;
    }

    function showGeneralError(msg){
        var cmsg = wpstg.error_msg;
        if('undefined' !== typeof(msg) && msg.length){
            cmsg = msg;
        }

        $( '.onecom-notifier' ).html( cmsg ).attr( 'type', 'error' ).addClass( 'show' );
        setTimeout( function(){
            $( '.onecom-notifier' ).removeClass( 'show' );
            $( '.one-dialog-close' ).trigger( 'click' );
        }, 5000 );

        //log the error
        onecom_log_staging_err(FLAG.anyProcess.type, cmsg);
    }

    function onecom_log_staging_err(stg_action, msg) {
        if(!stg_action.length)
            return;

        var _msg = msg;
        var _action = stg_action;

        var data ={
            action: "onestg_clone_log_error",
            nonce: wpstg.nonce,
            stg_action: _action,
            msg: _msg
        };

        $.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "JSON",
            data: data,
            error: function (xhr, textStatus, errorThrown) {
                console.log('Could not log the error');
                FLAG.anyProcess.type = '';
                FLAG.anyProcess.active = false;
                window.onbeforeunload = '';
            },
            success: function (data) {
                FLAG.anyProcess.type = '';
                FLAG.anyProcess.active = false;
                window.onbeforeunload = '';
            }
        });
    }

    function onecom_staging_progress(msg, count, hide){
        if( ! msg.length )
            return;

        if($('.loading-overlay.show').find('.onecom_progress_bar').length){
            //alert('already exists');
            $('.loading-overlay.show .onecom_progress_bar .job').html(msg);
            if(count == 0){
                $('.loading-overlay.show .onecom_progress_bar span').hide().width(count+'%').show();
            }
            else{
                $('.loading-overlay.show .onecom_progress_bar span').width(count+'%');
            }
        }
        else{
            //alert('doesnt exist and creating one');
            var progress =
                '<div class="onecom_progress_bar" style="display: none;">' +
                    '<div class="job">'+msg+'</div>' +
                    '<span style="width:'+count+'%"></span>' +
                '</div>';

            $('.loading-overlay.show').find('.loader').before(progress);
            $('.loading-overlay.show').find('.onecom_progress_bar').slideDown('fast');
        }

        if(hide.length && hide === 1){
            $('.loading-overlay.show').find('.onecom_progress_bar').slideUp('fast');
        }

    }

})(jQuery);