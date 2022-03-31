<?php
namespace Joost_Optimizations\Admin;

use Joost_Optimizations\Options\Options;

/**
 * Backend Class.
 */
class Admin {
	/**
	 * This holds the plugins options.
	 *
	 * @var array
	 */
	public array $options = [];

	/**
	 * Menu slug for WordPress admin.
	 *
	 * @var string
	 */
	public string $hook = 'joost-optimizations';

	/**
	 * Construct of class Clicky_admin.
	 *
	 * @link   https://codex.wordpress.org/Function_Reference/add_action
	 * @link   https://codex.wordpress.org/Function_Reference/add_filter
	 */
	public function __construct() {
		$this->options = Options::instance()->get();

		add_filter( 'plugin_action_links', [ $this, 'add_action_link' ], 10, 2 );

		add_action( 'admin_menu', [ $this, 'admin_init' ] );
	}

	/**
	 * Initialize needed actions.
	 */
	public function admin_init() {
		$public_post_types = get_post_types( [ 'public' => true ] );

		foreach ( $public_post_types as $post_type ) {
			add_meta_box(
				'clicky',
				__( 'Joost Optimizations Goal Tracking', 'joost-optimizations' ),
				[ $this, 'meta_box_content' ],
				$post_type,
				'side'
			);
		}

		$this->register_menu_pages();
	}

	/**
	 * Creates the dashboard and options pages.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/add_options_page
	 * @link https://codex.wordpress.org/Function_Reference/add_dashboard_page
	 */
	private function register_menu_pages() {
		add_options_page(
			__( 'Joost Optimizations', 'joost-optimizations' ),
			__( 'Joost Optimizations', 'joost-optimizations' ),
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
	private function plugin_options_url() {
		return admin_url( 'options-general.php?page=' . $this->hook );
	}

	/**
	 * Add meta box for entering specific goals.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/get_post_meta
	 */
	public function meta_box_content() {
		global $post;

		if ( ! isset( $this->options['site_id'] ) || empty( $this->options['site_id'] ) ) {
			return;
		}

		$clicky_goal = get_post_meta( $post->ID, '_clicky_goal', true );

		require JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH . 'admin/views/meta-box.php';
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
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = JOOST_OPTIMIZATIONS_PLUGIN_FILE;
		}
		if ( $file === $this_plugin ) {
			$settings_link = '<a href="' . esc_url( $this->plugin_options_url() ) . '">' . esc_html__( 'Settings', 'joost-optimizations' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		return $links;
	}
}