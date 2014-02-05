<?php

// @TODO: Clean up this file and add some structure to it.

//define( 'CHILD_THEME_NAME', 'Vintage' );
//define( 'CHILD_THEME_URL', 'http://yoast.com/wordpress/themes/vintage/' );
//define( 'CHILD_THEME_VERSION', '0.0.1' );

add_action( 'genesis_setup', 'child_theme_setup', 15 );

/**
 * Creates the child theme actions, filters and settings
 */
function child_theme_setup() {

	// Used for defaults in for instance the banner widget.
	/**
	 * @todo this define should be removed
	 */
	define( 'YST_SIDEBAR_WIDTH', 261 );





}


// Below functions appear not to be used...

function yoast_do_after_post_sidebar() {
	if ( is_single() ) {
		dynamic_sidebar( 'yoast-after-post' );
	}
}

/**
 * Open wrapper around main content for alignment
 * @fixme: can be combined with yst_add_wrapper_after_content() in one function.
 */
function yst_add_wrapper_before_content() {
	echo '<div id="main-content-wrap">';
}

/**
 *  * Close wrapper around main content for alignment
 *  * @fixme: can be combined with yst_add_wrapper_before_content() in one function.
 */
function yst_add_wrapper_after_content() {
	echo '</div>';
}
