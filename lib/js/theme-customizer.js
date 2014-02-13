(function ($) {
	"use strict";

	wp.customize( 'yst_logo', function ( value ) {
		value.bind( function ( to ) {
			$('.title-area').css( 'background-image', 'url('+to+')' );
		});
	});

	wp.customize( 'yst_mobile_logo', function ( value ) {
		value.bind( function ( to ) {
			$('#versatile-inline-css').text('@media(max-width: 640px){header.site-header {background: url(' + to + ') no-repeat 50% 0;	}}');
		});
	});

	wp.customize( 'yst_colour_scheme', function ( value ) {
		value.bind( function ( to ) {
			var css = $('#child-theme-css').attr('href');
			css = css.replace( /([A-Za-z]+)\.css/, to+'.css' );
			$('#child-theme-css').attr('href',css);
		});
	});

	wp.customize( 'yst_tagline_positioner', function ( value ) {
		value.bind( function ( to ) {
			$('.tagline_top').removeClass('tagline_top_right');
			$('.tagline_top').removeClass('tagline_top_left');
			$('.tagline_top').addClass( 'tagline_' + to );
			console.log('Value: ' + value + ' & to: ' + to + '!');
		});
	});

	wp.customize( 'yst_header_color_picker', function ( value ) {
		value.bind( function ( to ) {
			console.log('Value: ' + value + ' & to: ' + to + '!');
			$( 'body' ).removeClass( 'header-light');
			$( 'body' ).removeClass( 'header-dark');
			$( 'body' ).addClass( 'header-' + to );
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
