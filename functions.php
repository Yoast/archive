<?php

function __yoast_main() {
	require_once( get_stylesheet_directory() . '/lib/class-tailor-made.php' );
	new Yoast_Tailor_Made();
}

add_action( 'genesis_setup', '__yoast_main', 15 );