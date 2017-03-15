(function ($) {
	"use strict";

	wp.customize('yst_logo_position', function (value) {
		value.bind(function (to) {
			$('body').removeClass('logo-position-left');
			$('body').removeClass('logo-position-center');
			$('body').addClass('logo-position-' + to);

			if (to == 'center')
				$('.header-widget-area').hide();
		});
	});

	wp.customize('yst_logo_frame', function (value) {
		value.bind(function (to) {
			if (to) {
				$('body').addClass('logo-frame');
			} else {
				$('body').removeClass('logo-frame');
			}
		});
	});

	wp.customize('yst_logo', function (value) {
		value.bind(function (to) {
			$('.title-area').css('background-image', 'url(' + to + ')');
		});
	});

	wp.customize('yst_mobile_logo', function (value) {
		value.bind(function (to) {
			$('#tailor-made-inline-css').text('@media(max-width: 640px){header.site-header {background: url(' + to + ') no-repeat 50% 0;	}}');
		});
	});

	wp.customize('yst_colour_scheme', function (value) {
		value.bind(function (to) {
			var css = $('#'+yoast_child_theme_name+'-css').attr('href');
			css = css.replace(/([A-Za-z]+)\.css/, to + '.css');
			$('#'+yoast_child_theme_name+'-css').attr('href', css);
		});
	});

	wp.customize('yst_footer', function (value) {
		value.bind(function (to) {
			var data = {
				action: 'yst_footer_update',
				footer: to,
				nonce: yoast_ajax_nonce
			};
			$.post(ajaxurl, data, function(response) {
				$('.site-footer .wrap p').html( response );
			});
		});
	});

})(jQuery);