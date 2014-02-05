<?php

function __yoast_main() {
	require_once( get_stylesheet_directory() . '/lib/class-versatile.php' );
	new Yoast_Versatile();
}
add_action( 'genesis_setup', '__yoast_main', 15 );