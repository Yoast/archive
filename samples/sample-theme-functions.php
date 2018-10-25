<?php

if ( is_admin() ) {

	// Instantiate license class
	$license_manager = new Yoast_Theme_License_Manager_v2( new Sample_Product() );

	// Setup the required hooks
	$license_manager->setup_hooks();

}
