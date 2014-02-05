<?php

function __yoast_main() {
	require_once( get_stylesheet_directory() . '/lib/class-vintage.php' );
	new Yoast_Vintage();
}
add_action( 'genesis_setup', '__yoast_main', 15 );

require_once 'old-functions.php';