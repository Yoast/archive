<?php


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



