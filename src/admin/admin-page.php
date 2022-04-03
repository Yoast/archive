<?php
namespace Yoast\WP\Crawl_Cleanup\Admin;

use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * Class for the Yoast Crawl Cleanup plugin admin page.
 */
class Admin_Page extends Admin {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		new Admin_Options();

		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_styles' ] );
	}

	/**
	 * Enqueue the styles for the admin page.
	 *
	 * @param string $current_page The current page.
	 */
	public function config_page_styles( $current_page ): void {
		if ( $current_page !== 'settings_page_yoast-crawl-cleanup' ) {
			return;
		}

		wp_enqueue_style(
			'ycc-admin-css',
			YOAST_CRAWL_CLEANUP_PLUGIN_DIR_URL . 'css/dist/admin.css',
			null,
			YOAST_CRAWL_CLEANUP_PLUGIN_VERSION
		);
	}

	/**
	 * Enqueue the scripts for the admin page.
	 *
	 * @param string $current_page The current page.
	 */
	public function config_page_scripts( $current_page ): void {
		if ( $current_page !== 'settings_page_yoast-crawl-cleanup' ) {
			return;
		}

		wp_enqueue_script(
			'yoast-crawl-cleanup',
			YOAST_CRAWL_CLEANUP_PLUGIN_DIR_URL . 'js/admin.min.js',
			[ 'jquery' ],
			YOAST_CRAWL_CLEANUP_PLUGIN_VERSION,
			true
		);
	}

	/**
	 * Loads the configuration page view.
	 */
	public function config_page(): void {
		require YOAST_CRAWL_CLEANUP_PLUGIN_DIR_PATH . 'src/admin/views/admin-page.php';
	}
}
