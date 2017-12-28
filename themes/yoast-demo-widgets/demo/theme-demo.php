<?php

/**
 * Filter the color scheme for the current URL
 *
 * @param string $color_scheme The color scheme name
 *
 * @return string
 */
function yst_filter_color_scheme( $color_scheme ) {
	// $_GET goes first
	if ( isset( $_GET['color_scheme'] ) && '' != $_GET['color_scheme'] ) {
		$domain = rtrim( preg_replace( '|https?://|', '', get_site_url() ), '/' );
		setcookie( 'yst_demo_color_scheme', $_GET['color_scheme'], time() + 60 * 60 * 24 * 30, '/', $domain );

		return esc_attr( $_GET['color_scheme'] );
	}

	// Post meta goes second
	if ( is_singular() ) {
		$meta = get_post_meta( get_the_ID(), 'yst_color_scheme', true );
		if ( is_string( $meta ) && '' != $meta ) {
			return $meta;
		}
	}

	// Lastly, try the cookie
	if ( is_string( $_COOKIE['yst_demo_color_scheme'] ) && '' != $_COOKIE['yst_demo_color_scheme'] ) {
		return $_COOKIE['yst_demo_color_scheme'];
	}

	return $color_scheme;
}

add_filter( 'theme_mod_yst_colour_scheme', 'yst_filter_color_scheme' );

/**
 * Load the widgets
 */
function yst_load_demo_widgets() {
	foreach ( glob( dirname( __FILE__ ) . "/widgets/*-widget.php" ) as $file ) {
		require_once( $file );
	}
}

add_action( 'after_setup_theme', 'yst_load_demo_widgets' );