<?php

namespace Joost\Optimizations;

/**
 * Joost's Optimizations Plugin.
 *
 * @wordpress-plugin
 * Plugin Name: Joost's optimizations.
 * Version:     1.0
 * Plugin URI:  https://joost.blog/optimizations/
 * Description: Header and meta optimizations.
 * Author:      Joost de Valk
 * Author URI:  https://joost.blog/
 * Domain Path: /languages/
 * License:     GPL v3
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

define( 'JOOST_OPTIMIZATIONS_PLUGIN_FILE', __FILE__ );
define( 'JOOST_OPTIMIZATIONS_PLUGIN_VERSION', '2.0' );
define( 'JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'JOOST_OPTIMIZATIONS_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Class Yoast Joost Optimizations base class.
 */
class Joost_Optimizations {

	/**
	 * Initialize the plugin settings.
	 */
	public function __construct() {
		if (
			( defined( 'DOING_AJAX' ) && DOING_AJAX )
			|| ( defined( 'WP_CLI' ) && WP_CLI )
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}

		if ( file_exists( JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH . 'vendor/autoload.php' ) ) {
			require_once JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH . 'vendor/autoload.php';
		}

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the whole plugin.
	 */
	public function init() {
		load_plugin_textdomain( 'joost-optimizations', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		if ( is_admin() ) {
			new Admin\Admin();

			return;
		}

		new Frontend\Optimizations();
	}
}

add_action(
	'plugins_loaded',
	static function () {
		new Joost_Optimizations();
	}
);
