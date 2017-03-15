<?php
// Remove default loop
remove_action( 'genesis_loop', 'genesis_do_loop' );

// Add our custom loop
add_action( 'genesis_loop', 'yst_404' );

/**
 * This function outputs a custom 404 "Not Found" error message
 */
function yst_404() {

	echo '<article class="entry">';
	echo '<h1 class="entry-title">' . __( 'Sorry, this page could not be found.', 'yoast-theme' ) . '</h1>';
	echo '<div class="entry-content">';
	echo '<p>' . __( 'Sorry! The page you are looking for doesn\'t exist or no longer exists. Some options for how to find it:', 'yoast-theme' ) . '</p>';
	echo '<ol>';
	echo '<li>' . __( 'If you typed in a URL... make sure the spelling, cApitALiZaTiOn, and punctuation are correct. Then try reloading the page.', 'yoast-theme' ) . '</li>';
	echo '<li>' . sprintf( __( 'Start over again at the %1$shomepage%2$s.', 'yoast-theme' ), '<a href="' . get_site_url() . '">', '</a>' ) . '</li>';
	echo '<li>' . __( 'Search for it: ', 'yoast-theme' );
	echo get_search_form();
	echo '</li>';
	echo '</ol>';
	echo '</div>';
	echo '</article>';

}

genesis();
