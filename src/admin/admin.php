<?php
namespace Yoast\WP\Crawl_Cleanup\Admin;

use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * Backend Class.
 */
class Admin {

	/**
	 * Menu slug for WordPress admin.
	 *
	 * @var string
	 */
	public string $hook = 'yoast-crawl-cleanup';

	/**
	 * Class constructor.
	 *
	 * @link   https://codex.wordpress.org/Function_Reference/add_action
	 * @link   https://codex.wordpress.org/Function_Reference/add_filter
	 */
	public function __construct() {
		add_filter( 'plugin_action_links', [ $this, 'add_action_link' ], 10, 2 );

		add_action( 'admin_menu', [ $this, 'admin_init' ] );
	}

	/**
	 * Initialize needed actions.
	 */
	public function admin_init(): void {
		$this->register_menu_pages();
	}

	/**
	 * Creates the dashboard and options pages.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/add_options_page
	 * @link https://codex.wordpress.org/Function_Reference/add_dashboard_page
	 */
	private function register_menu_pages(): void {
		add_options_page(
			__( 'Yoast Crawl Cleanup', 'yoast-crawl-cleanup' ),
			__( 'Yoast Crawl Cleanup', 'yoast-crawl-cleanup' ),
			'manage_options',
			$this->hook,
			[ new Admin_Page(), 'config_page' ]
		);
	}

	/**
	 * Returns the plugins settings page URL.
	 *
	 * @return string Admin URL to the current plugins settings URL.
	 */
	private function plugin_options_url(): string {
		return admin_url( 'options-general.php?page=' . $this->hook );
	}

	/**
	 * Add a link to the settings page to the plugins list.
	 *
	 * @param array  $links Links to add.
	 * @param string $file  Plugin file name.
	 *
	 * @return array
	 */
	public function add_action_link( $links, $file ) {
		if ( preg_match( '/yoast-crawl-cleanup\.php$/', $file ) ) {
			$settings_link = '<a href="' . esc_url( $this->plugin_options_url() ) . '">' . esc_html__( 'Settings', 'yoast-crawl-cleanup' ) . '</a>';
			array_unshift( $links, $settings_link );
			$links = array_unique( $links );
		}

		return $links;
	}
}
