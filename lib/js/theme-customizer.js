(function ($) {
	"use strict";

	wp.customize('yst_logo_position', function (value) {
		value.bind(function (to) {
			$('body').removeClass('logo-position-left');
			$('body').removeClass('logo-position-middle');
			$('body').addClass('logo-position-' + to);

			if (to == 'middle')
				$('.header-widget-area').hide();
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
			var css = $('#vintage-css').attr('href');
			css = css.replace(/([A-Za-z]+)\.css/, to + '.css');
			$('#vintage-css').attr('href', css);
		});
	});

})(jQuery);