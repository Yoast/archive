(function ($) {
	"use strict";

	wp.customize( 'yst_logo', function ( value ) {
		value.bind( function ( to ) {
			$('.title-area').css( 'background-image', 'url('+to+')' );
		});
	});

	wp.customize( 'yst_mobile_logo', function ( value ) {
		value.bind( function ( to ) {
			$('#strategy-inline-css').text('@media(max-width: 640px){header.site-header {background: url(' + to + ') no-repeat 50% 0;	}}');
		});
	});

	wp.customize( 'yst_colour_scheme', function ( value ) {
		value.bind( function ( to ) {
			var $stylesheet = $("#" + yoast_child_theme_name  + "-css");
			var css = $stylesheet.attr('href');

			css = css.replace( /([A-Za-z]+)\.css/, to + '.css' );
			$stylesheet.attr('href', css);
		});
	});

//	wp.customize( 'yst_colour_scheme', function ( value ) {
//		value.bind( function ( to ) {
//			var css = $('#child-theme-css').attr('href');
//			css = css.replace( /([A-Za-z]+)\.css/, to+'.css' );
//			$('#child-theme-css').attr('href',css);
//			console.log("Color change.");
//		});
//	});

})(jQuery);
