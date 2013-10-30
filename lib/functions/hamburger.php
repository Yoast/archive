<?
/**
 * Add JavaScripts for hamburgermenu
 */
function enqueue_hamburgermenu_scripts() {
	wp_enqueue_script( 'modernizr', get_stylesheet_directory_uri() . '/lib/js/modernizr.js' );
	wp_enqueue_script( 'hamburgermain', get_stylesheet_directory_uri() . '/lib/js/main.js' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_hamburgermenu_scripts' );

/**
 * Add styling for hamburgermenu
 */
function enqueue_styles_hambugermenu() {
	wp_enqueue_style( 'style-name', get_stylesheet_directory_uri() . '/assets/css/ROCM.css' );
}
add_action( 'wp_enqueue_scripts', 'enqueue_styles_hambugermenu' );

function add_to_genesis_header() {
	echo '<a class="nav-btn" id="nav-open-btn" href="#nav">Book Navigation</a>';
}
add_action( 'genesis_header', 'add_to_genesis_header' );

function add_to_genesis_menu() {
	echo '<a class="close-btn" id="nav-close-btn" href="#top">Return to Content</a>';
}
add_action( 'genesis_after_header', 'add_to_genesis_menu' );

function add_wrappers_open() {
	echo '<div id="outer-wrap"><div id="inner-wrap">';
}
add_action( 'genesis_before_header', 'add_wrappers_open' );
function add_wrappers_close() {
	echo '</div></div>';
}
add_action( 'genesis_after_footer', 'add_wrappers_close' );