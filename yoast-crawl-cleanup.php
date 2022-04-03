<?php

namespace Yoast\WP\Crawl_Cleanup;

/**
 * Yoast Crawl Cleanup Plugin.
 *
 * @wordpress-plugin
 * Plugin Name: Yoast Crawl Cleanup
 * Version:     1.0
 * Plugin URI:  https://yoast.com/wordpress/plugins/crawl-cleanup/
 * Description: Remove unnecessary WordPress output to clean up your site for crawling.
 * Author:      Joost de Valk & Team Yoast
 * Author URI:  https://yoast.com/
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

define( 'YOAST_CRAWL_CLEANUP_PLUGIN_FILE', __FILE__ );
define( 'YOAST_CRAWL_CLEANUP_PLUGIN_VERSION', '2.0' );
define( 'YOAST_CRAWL_CLEANUP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'YOAST_CRAWL_CLEANUP_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Yoast Crawl Cleanup base class.
 */
class Yoast_Crawl_Cleanup {

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

		if ( file_exists( YOAST_CRAWL_CLEANUP_PLUGIN_DIR_PATH . 'vendor/autoload.php' ) ) {
			require_once YOAST_CRAWL_CLEANUP_PLUGIN_DIR_PATH . 'vendor/autoload.php';
		}

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the whole plugin.
	 */
	public function init(): void {
		load_plugin_textdomain( 'yoast-crawl-cleanup', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

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
		new Yoast_Crawl_Cleanup();
	}
);
