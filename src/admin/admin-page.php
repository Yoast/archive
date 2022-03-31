<?php
namespace Joost_Optimizations\Admin;

/**
 * Class for the Joost Optimizations plugin admin page.
 */
class Admin_Page extends Admin {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$options_admin = new Admin_Options();

		$this->options = $options_admin->get();

		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_styles' ] );
	}

	/**
	 * Enqueue the styles for the admin page.
	 *
	 * @param string $current_page The current page.
	 */
	public function config_page_styles( $current_page ) {
		if ( $current_page !== 'joost-optimizations' ) {
			return;
		}

		wp_enqueue_style(
			'joost-optimizations-admin-css',
			JOOST_OPTIMIZATIONS_PLUGIN_DIR_URL . 'css/dist/admin.css',
			null,
			JOOST_OPTIMIZATIONS_PLUGIN_VERSION
		);
	}

	/**
	 * Enqueue the scripts for the admin page.
	 *
	 * @param string $current_page The current page.
	 */
	public function config_page_scripts( $current_page ) {
		if ( $current_page !== 'joost-optimizations' ) {
			return;
		}

		wp_enqueue_script(
			'joost-optimizations',
			JOOST_OPTIMIZATIONS_PLUGIN_DIR_URL . 'js/admin.min.js',
			null,
			JOOST_OPTIMIZATIONS_PLUGIN_VERSION,
			true
		);
	}

	/**
	 * Creates the configuration page.
	 */
	public function config_page() {
		require JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH . 'src/admin/views/admin-page.php';
	}
}
