<?php

if( is_admin() ) {

	// setup required variables
	$api_url 		= 'https://yoast.com';
	$item_name 		= 'Sample Theme';
	$item_url		= 'http://dannyvankooten.com/themes/sample-theme/';
	$version 		= '1.1';
	$text_domain 	= 'sample-theme';
	$theme_slug		= 'sample-theme';
	$author 		= 'Yoast';

	// load classes
	require_once dirname( __FILE__ ) . '/License-Manager/class-license-manager.php';
	require_once dirname( __FILE__ ) . '/License-Manager/class-theme-license-manager.php';

	// instantiate license class
	$license_manager = new Yoast_Theme_License_Manager( $api_url, $item_name, $theme_slug, $version, $item_url, $text_domain, $author );

}