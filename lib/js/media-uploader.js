ystThemeConfigL10n = { choose_image: "Use Image"};

// Taken and adapted from http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
jQuery(document).ready(function ($) {
	var yst_custom_uploader;
	var yst_target_id;
	$('.yst_image_upload_button').click(function (e) {
		yst_target_id = e.currentTarget.id.replace(/_button$/, '_input');
		e.preventDefault();
		if (yst_custom_uploader) {
			yst_custom_uploader.open();
			return;
		}
		yst_custom_uploader = wp.media.frames.file_frame = wp.media({
			title   : ystThemeConfigL10n.choose_image,
			button  : { text: ystThemeConfigL10n.choose_image },
			multiple: false
		});
		yst_custom_uploader.on('select', function () {
			attachment = yst_custom_uploader.state().get('selection').first().toJSON();
			$("#" + yst_target_id).val(attachment.url);
		});
		yst_custom_uploader.open();
	});
});
