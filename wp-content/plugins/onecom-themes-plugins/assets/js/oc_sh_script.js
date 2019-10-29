jQuery(document).ready(function () {
    ocsh_checkPHPUpdates();
    jQuery(document).on('click', '.ocsh-scan-result li', function (e) {
        //exclude click events coming from description text        
        if(
            (jQuery(e.target).attr('class') != 'ocsh-scan-title') && (jQuery(e.target).attr('class') != 'ocsh-error') && (jQuery(e.target).parent().attr('class') == 'osch-desc' || jQuery(e.target).parent().attr('class') == 'ocsh-bullet')){
            return;
        }
        // jQuery(this).parent().find('.osch-desc').slideToggle('slow');
        jQuery(this).find('.osch-desc').toggleClass('hidden');
    });
});
function ocsh_checkPHPUpdates() {
    // show the Error reporting section first as its aleady loaded
    jQuery('#ocsh-err-reporting').slideDown('slow');
    var data = {
        action: 'ocsh_check_php_updates'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-updates', response);
        ocsh_checkPluginUpdates();
    });
}

function ocsh_checkPluginUpdates() {
    var data = {
        action: 'ocsh_check_plugin_updates'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-plugin-updates', response);
        ocsh_checkThemeUpdates();
    });
}

function ocsh_checkThemeUpdates() {
    var data = {
        action: 'ocsh_check_theme_updates'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-theme-updates', response);
        ocsh_checkWPUpdates();
    });
}

function ocsh_checkWPUpdates() {
    var data = {
        action: 'ocsh_check_wp_updates'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-wp-updates', response);
        ocsh_checkWPCon();
    });
}

function ocsh_checkWPCon() {
    var data = {
        action: 'ocsh_wp_connection'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-wp-org-comm', response);
        ocsh_checkCoreUpdates();
    });
}

function ocsh_checkCoreUpdates() {
    var data = {
        action: 'ocsh_check_core_updates'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-core-updates', response);
        ocsh_checkSSL()
    });
}

function ocsh_checkSSL() {
    var data = {
        action: 'ocsh_check_ssl'
    };

    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-ssl', response);
        ocsh_checkFileExecution()
    });
}

function ocsh_checkFileExecution() {
    var data = {
        action: 'ocsh_check_file_execution'
    };
    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-file-execution', response);
        ocsh_checkFilePermissions();
    });
}

function ocsh_checkFilePermissions() {
    var data = {
        action: 'ocsh_check_file_permissions'
    };

    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-file-permissions', response);
        ocsh_checkDB();
    });
}

function ocsh_checkDB() {
    var data = {
        action: 'ocsh_DB'
    };

    jQuery.post(ajaxurl, data, function (response) {
        ocsh_processReponse('#ocsh-db', response);
        ocsh_checkFileEdit()
    });
}

function ocsh_checkFileEdit() {
    jQuery.post(ajaxurl, {
        action: 'ocsh_check_file_edit'
    }, function (response) {
        ocsh_processReponse('#ocsh-file-edit', response);
        ocsh_checkUserNames();
    });
}

function ocsh_checkUserNames() {
    jQuery.post(ajaxurl, {
        action: 'ocsh_check_usernames'
    }, function (response) {
        ocsh_processReponse('#ocsh-usernames', response);
        ocsh_checkDiscouragedPlugins()
    });
}

function ocsh_checkDiscouragedPlugins() {
    jQuery.post(ajaxurl, {
        action: 'ocsh_check_dis_plugin'
    }, function (response) {
        ocsh_processReponse('#ocsh-discouraged-plugins', response);
        ocsh_calculateSiteSecurity();
    });
}

function ocsh_processReponse(element, response) {
    var desc = response.desc;
    var html = desc;

    jQuery(element).find('.osch-desc').html(html);

    var clone = jQuery(element).clone();

    // if a fix is detected
    if (response.status === oc_constants.OC_RESOLVED) {
        jQuery(clone).find('span').addClass('ocsh-success');
        jQuery(clone).appendTo('#ocsh-all-ok').slideDown('slow');

        // remove description for a fix issue
        jQuery(clone).find('h4.ocsh-scan-title').html(desc)
        jQuery(clone).find('.osch-desc').remove();
        jQuery(clone).addClass('resolved');
    } 
    // if an issue is detected
    else {
        jQuery(clone).find('span').addClass('ocsh-error');
        jQuery(clone).clone().appendTo('#ocsh-needs-attention').slideDown('slow');
    }

    // show separator if there are both kind of bullets
    if (jQuery('#ocsh-needs-attention li').length > 0 && jQuery('#ocsh-all-ok li').length > 0) {
        jQuery('.ocsh-separator').removeClass('hidden');
    }
}

function ocsh_calculateSiteSecurity() {
    var okCount = parseInt(jQuery('#ocsh-all-ok li').length);
    var errCount = parseInt(jQuery('#ocsh-needs-attention li').length);
    var healthPercent = ((okCount * 100) / (okCount + errCount)).toFixed(2);
    if (healthPercent == '100.00'){
        healthPercent = 100;
    }
    ocsh_save_result(healthPercent);
}

function ocsh_save_result(result) {

    var color = '#4ab865';
    if (result < 85 && result >=50){
        color = '#ffb900';
    } else if(result < 50){
        color = '#dc3232';
    }
    var score = '<i style="color:'+color+'">'+Number(result).toFixed(0)+'%</i> ';

    jQuery.post(ajaxurl, {
        action: 'ocsh_save_result',
        osch_Result: result
    }, function (response) {
        var existingText = jQuery('#ocsh-site-security').text();
        jQuery('#ocsh-site-security').html(existingText + ' - ' + score + '<a class="button" href="'+ oc_constants.ocsh_page_url +'">'+oc_constants.ocsh_scan_btn+'</a>');
    });
}