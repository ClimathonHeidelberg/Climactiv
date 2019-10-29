(function ($){

    /* enable banner button */
    jQuery(document).on('change', '#cb_enable', function(){
        if(!jQuery(this).prop("checked")){
            jQuery('.fieldset.cb_fields').removeClass('show');
        }
        else{
            jQuery('.fieldset.cb_fields').removeClass('show').addClass('show');
        }
    });

    /* enable policy link */
    jQuery(document).on('change', '#toggle_policy', function(){
        if(!jQuery(this).prop("checked")){
            jQuery('.fieldset.policy_fields').removeClass('show');
        }
        else{
            jQuery('.fieldset.policy_fields').removeClass('show').addClass('show');
        }
    });

    /* policy text remaining characters */
    jQuery(document).on('input', '#oc_cb_config_form input, #oc_cb_config_form textarea', function(){
        setTimeout(function(){
            oc_cb_validate_form();
         },200);
    });

    /* update preview based on focused field */
    jQuery(document).on('click', '#oc_cb_config_form input, #oc_cb_config_form textarea', function(e){
        var status,fill,elm_class;

        // get bg fill color
        fill = jQuery('input[name="banner_style"]:checked').val();

        // check if banner disabled.
        status = jQuery('input[name="show"]:checked').val();
        if(!status){
            jQuery('#banner_preview').removeClass();
            return true;
        }
        

        // get the clicked element
        element = jQuery(this).attr('name');

        switch(element){
            case 'banner_style':
                fill = jQuery(this).val();
                elm_class = 'fill_'+fill;
                break;

            case 'banner_text':
                elm_class = 'text_'+fill;
                break;

            case 'policy_link':
            case 'policy_link_text':
            case 'policy_link_url':
                elm_class = 'link_'+fill;
                break;

            case 'button_text':
                elm_class = 'button_'+fill;
                break;

            default:
                elm_class = 'fill_'+fill;

        }
        jQuery('#banner_preview').removeClass();
        jQuery('#banner_preview').addClass(elm_class);

    });

    jQuery(document).on('submit', '#oc_cb_config_form', function(e){
        e.preventDefault();

        // validate fields
        if(!oc_cb_validate_form()){
            return false;
        }

        // hide any previously shown errors from UI
        jQuery('#oc_cb_errors').removeClass('show');
        jQuery(".oc_cb_spinner").removeClass('success').addClass('is-active');

        // collect the form fields data
        var form_data = jQuery('#oc_cb_config_form').serialize();
        var data = {
            action: 'oc_cb_settings',
            oc_cb_sec: oc_constants.oc_cb_token,
            settings: form_data
        };

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            dataType: "JSON",
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                jQuery('#oc_cb_errors').html("").html("Failed to save settings. Please reload the page and try again.").addClass('show');
                jQuery(".oc_cb_spinner").removeClass('is-active').removeClass('success').addClass('error');
            },
            success: function (data) {
                if(data.error){
                    jQuery(".oc_cb_spinner").removeClass('is-active').removeClass('success').addClass('error');
                    return false;
                }
                jQuery(".oc_cb_spinner").removeClass('is-active').addClass('success');
            },
            statusCode: {
                200: function () {},
                404: function () {
                    jQuery('#oc_cb_errors').html("").html("Failed to save settings. Please reload the page and try again.").addClass('show');
                },
                500: function () {
                    jQuery('#oc_cb_errors').html("").html("Something went wrong; internal server error while processing the request!").addClass('show');
                }
            }
        });
        return false;
    });

    /* save settings */
    jQuery(document).on('click', '#oc_cb_btn', function(e){
        jQuery('#oc_cb_config_form').submit();
    });

})(jQuery);

function oc_cb_validate_form(){

    var oc_cb_error = false;
    var oc_cb_submit = "#oc_cb_btn";

    // hide any previously shown errors from UI
    jQuery('#oc_cb_errors').removeClass('show');
    jQuery(".oc_cb_spinner").removeClass('success').removeClass('is-active');

    /* check textarea */
    var cb_text = "#banner_text";
    var cb_text_maxlength = jQuery(cb_text).attr('maxlength');
    var cb_rem = "#occb_rem";

    jQuery(cb_rem).html((jQuery(cb_text).val().length)+" / "+cb_text_maxlength);

    // return false if no text entered
    if(jQuery(cb_text).val().length == 0){
        jQuery(cb_text).addClass('occberror');
        oc_cb_error = true;
    }
    else if(jQuery(cb_text).val().length >= 490){
        jQuery(cb_rem).css("display", "inline-block");
        jQuery(cb_text).removeClass('maxlimit');
        // jQuery(cb_text).addClass('occberror');
    }
    else{
        jQuery(cb_rem).css("display", "none");
        jQuery(cb_text).removeClass('occberror');
        jQuery(cb_text).removeClass('maxlimit');
    }


    /* check policy link text */
    var cb_link_text = "#policy_link_text";
    if(jQuery(cb_link_text+':visible').length && jQuery(cb_link_text).val().length == 0){
        jQuery(cb_link_text).addClass('occberror');
        oc_cb_error = true;
    }
    else{
        jQuery(cb_link_text).removeClass('occberror');
    }

    /* check policy link */
    var cb_policy_link = "#policy_link_url";
    var cb_link_ptrn = new RegExp("(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9-]+[a-zA-Z0-9-]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})");

    if( jQuery(cb_policy_link+':visible').length && ((jQuery(cb_policy_link).val().length == 0) || (cb_link_ptrn.test(jQuery(cb_policy_link).val()) == false))){
        jQuery(cb_policy_link).addClass('occberror');
        oc_cb_error = true;
    }
    else{
        jQuery(cb_policy_link).removeClass('occberror');
    }
    


    /* check button text */
    var oc_cb_btn_text = "#button_text";
    if(jQuery(oc_cb_btn_text).val().length){
        jQuery(oc_cb_btn_text).removeClass('occberror');
    }
    else{
        jQuery(oc_cb_btn_text).addClass('occberror');
        oc_cb_error = true;
    }

    if(oc_cb_error && jQuery('#cb_enable:checked').length){
        jQuery(oc_cb_submit).attr("disabled", "disabled");
        return false;
    }
    jQuery(oc_cb_submit).removeAttr("disabled");
    return true;

}