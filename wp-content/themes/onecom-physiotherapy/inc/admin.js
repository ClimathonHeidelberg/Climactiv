(function ($) {
    $(document).ready(function ($) {


        function onecom_colorpicker_init(parent){
            jQuery(parent).find('.onecom_widget_colorpicker').each(function(i, v){
                jQuery(v).wpColorPicker({
                    change: function(event, ui) {
                        var element = event.target;
                        var color = ui.color.toString();

                        // Enable the save widget button
                        jQuery(event.target).parents('form').find('input[name="savewidget"]').val(wpWidgets.l10n.save).prop('disabled', false);
                    }
                });
            });
        }

        if ($('.onecom_widget_colorpicker').length > 0) {
            onecom_colorpicker_init('.widgets-sortables');
        }

        if ($('.one-social-default-checkbox').length > 0) {
            $('.one-social-default-checkbox').each(function (event, checkbox) {
                var checked = ($(this).is(':checked')) ? true : false;
                if (checked) {
                    $(this).parents('table:first').find('.toggle-tr').hide();
                } else {
                    $(this).parents('table:first').find('.toggle-tr').show();
                }
            });

            $(document).on('click', '.one-social-default-checkbox', function (event) {
                var checked = ($(this).is(':checked')) ? true : false;
                console.log(checked);
                if (checked) {
                    $(this).parents('table:first').find('.toggle-tr').hide();
                } else {
                    $(this).parents('table:first').find('.toggle-tr').show();
                }
            });


            $(document).on('widget-updated widget-added', function (e, widget) {
                onecom_colorpicker_init(widget);
                var checkbox = $(widget).find('.one-social-default-checkbox');
                var checked = ($(checkbox).is(':checked')) ? true : false;
                if (checked) {
                    $(checkbox).parents('table:first').find('.toggle-tr').hide();
                } else {
                    $(checkbox).parents('table:first').find('.toggle-tr').show();
                }
            });
        }
    });
})(jQuery);