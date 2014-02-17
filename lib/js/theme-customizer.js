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
			var css = $('#child-theme-css').attr('href');
			css = css.replace( /([A-Za-z]+)\.css/, to+'.css' );
			$('#child-theme-css').attr('href',css);
		});
	});

	wp.customize( 'yst_primary_nav_position', function ( value ) {
		value.bind( function ( to ) {
			$( 'body' ).removeClass( 'primnav-topright');
			$( 'body' ).removeClass( 'primnav-centerleft');
			$( 'body' ).addClass( 'primnav-' + to );
		});
	});

})(jQuery);
