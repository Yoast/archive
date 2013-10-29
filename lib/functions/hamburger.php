<?
/**
 * Add JavaScripts for hamburgermenu
 */
function enqueue_hamburgermenu_scripts() {
	wp_enqueue_script( get_stylesheet_directory_uri() . '/lib/js/modernizr.js' );
	wp_enqueue_script( get_stylesheet_directory_uri() . '/lib/js/main.js' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_hamburgermenu_scripts' );

