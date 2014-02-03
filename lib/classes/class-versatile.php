<?php

// Load the Yoast_Theme class
require_once( 'class-theme.php' );

/**
 * Class Versatile
 */
class Yoast_Versatile extends Yoast_Theme {

	const NAME    = 'Versatile';
	const URL     = 'http://yoast.com/wordpress/themes/versatile/';
	const VERSION = '1.0.0';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Setup the theme
	 */
	public function setup_theme() {

		// Set the content width
		$this->set_content_width( 680 );

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 3 );

		// Disable site layouts that are not used
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

		// Remove hook on secondary sidebar alt
		remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

		// Remove unused sidebar
		unregister_sidebar( 'sidebar-alt' );

		// Register theme sidebars
		$this->register_sidebars();

		// Do mobile menu
		$this->do_mobile_menu();

		// Image sizes
		add_image_size( 'yst-archive-thumb', 170, 0, true );
		add_image_size( 'yst-single', 620, 315, true );

		// Activate blogroll widget
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );
	}

	/**
	 * Register widget area's
	 */
	private function register_sidebars() {
		genesis_register_sidebar( array(
				'id'          => 'yoast-after-header-1',
				'name'        => __( 'After Header 1', 'yoast-theme' ),
				'description' => __( 'After Header 1 widget area.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-after-header-2',
				'name'        => __( 'After Header 2', 'yoast-theme' ),
				'description' => __( 'After Header 2 widget area.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-after-header-3',
				'name'        => __( 'After Header 3', 'yoast-theme' ),
				'description' => __( 'After Header 3 widget area.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-fullwidth-widgetarea-1',
				'name'        => __( 'Full Width 1', 'yoast-theme' ),
				'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-fullwidth-widgetarea-2',
				'name'        => __( 'Full Width 2', 'yoast-theme' ),
				'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-fullwidth-widgetarea-3',
				'name'        => __( 'Full Width 3', 'yoast-theme' ),
				'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
				'id'          => 'yoast-after-post',
				'name'        => __( 'After Post', 'yoast-theme' ),
				'description' => __( 'Add a widget after the post on single pages.', 'yoast-theme' ),
		) );
	}

	/**
	 * Setup mobile menu
	 */
	public function do_mobile_menu() {
		add_action( 'genesis_header', array( $this, 'add_open_div_for_mobile_menu_borders' ), 11 );
		add_action( 'genesis_header', array( $this, 'add_close_div_for_mobile_menu_borders' ), 12 );
	}

	/**
	 * Opens div to fix borders in mobile menu
	 */
	function add_open_div_for_mobile_menu_borders() {
		echo '<div id="mobile-menu-helper">';
	}

	/**
	 *   Closes div to fix borders in mobile menu
	 */
	function add_close_div_for_mobile_menu_borders() {
		echo '<div class="clearfloat"></div></div>';
	}

}