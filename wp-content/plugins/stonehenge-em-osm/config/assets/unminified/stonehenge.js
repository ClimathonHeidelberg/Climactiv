jQuery.noConflict();

function showErrors() {
	if( jQuery('.parsley-errors-list').length > 0) {
		jQuery('.listFieldError').html("<span class='stonehenge-error'>" + CORE.showError + "</span>").show();
		jQuery('.postbox').not('#qrcode').removeClass('closed');
	}
}


(function($) {
$(document).ready(function(){
// Settings Page
	var Sections = $('div.stonehenge-section').length;
	if( Sections > 2 ) {
		$('div.stonehenge-section-content').hide();
	}

	$('h3.handle').click( function() {
		$(this).closest('div').next('.stonehenge-section-content').slideToggle(250);
	});

// Repeatable divs.
	$(".button-add").attr('title', CORE.add);
	$(".button-remove").attr('title', CORE.remove);
	$(".button-edit").attr('title', CORE.edit);

	$(document.body).on('click', '.button-remove', function() {
    	$(this).closest('tr').remove();
	});

// Attachments
	$('.file-button').click( function(e) {
	    e.preventDefault();
		var mediaUploader,
			Button = $(this),
			Attachment;

		Button.click( function(e) {
			e.preventDefault();
			var Destination = $(this).prev('.filename');
			if ( mediaUploader ) {
				mediaUploader.open();
				return;
			}
			mediaUploader = wp.media.frames.file_frame = wp.media( {
				title: CORE.chooseFile,
				button:	{ text: CORE.chooseFile, },
				multiple: false,
			} );
			mediaUploader.on( 'select', function() {
				Attachment = mediaUploader.state().get('selection').first().toJSON();
				Destination.val( Attachment.url );
			} );
			mediaUploader.open();
		} );
	});

	$('.clear-file').click(function() {
		$(this).prevAll().val('');
	});

// Date Picker
	if( $('.datepicker').length > 0 ) {
		$('.hasDatepicker').datepicker();
		$(document).on('focus', '.datepicker', function() {
			$(this).datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: CORE.date_format_js,
				yearRange: CORE.year_range,
				minDate: 0,
			});
		});
	}

// Time Picker
	$(document).on('focus', '.time-input', function() {
	   	if( $(".time-input").length > 0 ){
			em_setup_timepicker('body');
		}
	});

// Color Picker
	$('.pickcolor').click(function(e) {
		colorPicker = jQuery(this).next('div');
		input = jQuery(this).prev('input');
		$.farbtastic($(colorPicker), function(a) { $(input).val(a).css('background', a); });
		colorPicker.show();
		e.preventDefault();
		$(document).mousedown( function() { $(colorPicker).hide(); });
	});

});
})
(jQuery);
