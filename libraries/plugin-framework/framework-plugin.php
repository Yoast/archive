<?php
/**
 * Plugin Name: Framework plugin
 * Plugin URI: https://yoast.com
 * Description: Framework test plugin
 * Version: 0.0.1
 * Author: Yoast BV & Peter van Wilderen
 * Author URI: https://yoast.com
 * License: GPL2
 */

if ( ! class_exists( 'Framework_Controller' ) ) {
	// Test if framework is already loaded
	require( plugin_dir_path( __FILE__ ) . 'lib/controllers/class-framework-controller.php' );
}

// Include this App Controller
require( plugin_dir_path( __FILE__ ) . 'controllers/class-app-controller.php' );