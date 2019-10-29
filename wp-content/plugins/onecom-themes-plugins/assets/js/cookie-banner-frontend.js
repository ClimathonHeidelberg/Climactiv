(function ($){

    /* handle cookie accept event */
    $(document).on('click', '#oc_cb_btn', function(e){
        e.preventDefault();

        // prepare the data
        var data = {
            action: 'oc_cb_cookie_consent'
        };

        $.ajax({
            url: oc_constants.ajaxurl,
            type: "POST",
            data: data,
            dataType: "JSON",
            error: function (xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                $('#oc_cb_wrapper').hide().remove();
            },
            success: function (data) {
                $('#oc_cb_wrapper').hide().remove();
            },
            statusCode: {
                200: function () {},
                404: function () {
                    $('#onme_errors').html("").html("Something went wrong; can't find request URL!");
                },
                500: function () {
                    $('#onme_errors').html("").html("Something went wrong; internal server error while processing the request!");
                }
            }
        });
        return false;
    });

})(jQuery);