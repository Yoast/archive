<?php

require_once( get_stylesheet_directory() . '/lib/class-versatile.php' );
new Yoast_Versatile();

/*
function __yoast_main() {
	require_once( get_stylesheet_directory() . '/lib/class-versatile.php' );
	new Yoast_Versatile();
}
add_action( 'after_setup_theme', '__yoast_main', 1 );
*/
//add_action( 'genesis_setup', '__yoast_main', 15 );