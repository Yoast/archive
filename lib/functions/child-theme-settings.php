<?php
/**
 * Child Theme Settings
 *
 * This file registers all of this child theme's specific Theme Settings, accessible from
 * Genesis > Child Theme Settings.
 *
 * @package     BE_Genesis_Child
 * @since       1.0.0
 * @link        https://github.com/billerickson/BE-Genesis-Child
 * @author      Bill Erickson <bill@billerickson.net>
 * @copyright   Copyright (c) 2011, Bill Erickson
 * @license     http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link        https://github.com/billerickson/BE-Genesis-Child
 */

/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Child Theme Settings page.
 *
 * @since      1.0.0
 *
 * @package    BE_Genesis_Child
 * @subpackage Child_Theme_Settings
 */
class Child_Theme_Settings extends Genesis_Admin_Boxes {

	/**
	 * Create an admin menu item and settings page.
	 * @since 1.0.0
	 */
	function __construct() {

		// Specify a unique page ID. 
		$page_id = 'child';

		// Set it as a child to genesis, and define the menu and page titles
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'genesis',
				'page_title'  => 'Genesis - Child Theme Settings',
				'menu_title'  => 'Child Theme Settings',
			)
		);

		// Set up page options. These are optional, so only uncomment if you want to change the defaults
		$page_ops = array(//	'screen_icon'       => 'options-general',
			//	'save_button_text'  => 'Save Settings',
			//	'reset_button_text' => 'Reset Settings',
			//	'save_notice_text'  => 'Settings saved.',
			//	'reset_notice_text' => 'Settings reset.',
		);

		// Give it a unique settings field. 
		// You'll access them from genesis_get_option( 'option_name', 'child-settings' );
		$settings_field = 'child-settings';

		// Set the default values
		$default_settings = array(
			'home-intro'                   => '',
			'footer'                       => 'Copyright &copy; ' . date( 'Y' ) . ' All Rights Reserved',
			'subscribe-text'               => '',
			'genesis-text'                 => '',
			'plugin-archive-title'         => '',
			'plugin-archive-intro'         => '',
			'plugin-archive-lower-title'   => '',
			'plugin-archive-lower-content' => '',
			'speaking-intro'               => '',
		);

		// Create the Admin Page
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		// Initialize the Sanitization Filter
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) );

	}

	/**
	 * Set up Sanitization Filters
	 * @since 1.0.0
	 *
	 * See /lib/classes/sanitization.php for all available filters.
	 */
	function sanitization_filters() {

		genesis_add_option_filter( 'no_html', $this->settings_field,
			array(
				'plugin-archive-title',
				'plugin-archive-lower-title',
			) );

		genesis_add_option_filter( 'safe_html', $this->settings_field,
			array(
				'home-intro',
				'footer',
				'plugin-archive-intro',
				'plugin-archive-lower-content',
				'subscribe-text',
				'genesis-text',
				'speaking-intro'
			) );
	}

	/**
	 * Register metaboxes on Child Theme Settings page
	 * @since 1.0.0
	 */
	function metaboxes() {
		add_meta_box( 'home_intro_metabox', 'Home Intro', array( $this, 'home_intro_metabox' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'footer_metabox', 'Footer', array( $this, 'footer_metabox' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'after_post_information_metabox', 'After Post Information', array( $this, 'after_post_information_metabox' ), $this->pagehook, 'main', 'high' );
		//add_meta_box( 'plugin_archive_metabox', 'Plugin Archive', array( $this, 'plugin_archive_metabox' ), $this->pagehook, 'main', 'high' );

		//add_meta_box( 'cpt_intro_metabox', 'Custom Post Type Intro\'s', array( $this, 'cpt_intro_metabox' ), $this->pagehook, 'main', 'high' );
	}

	/**
	 * Home Intro
	 *
	 */
	function home_intro_metabox() {
		wp_editor( $this->get_field_value( 'home-intro' ), $this->get_field_id( 'home-intro' ), array( 'textarea_rows' => 5 ) );
	}

	/**
	 * Speaking Intro
	 *
	 */
	function cpt_intro_metabox() {
		foreach ( get_post_types( array( 'public' => true, '_builtin' => false, 'has_archive' => true ), 'objects' ) as $cpt ) {
			echo '<h4>' . $cpt->labels->name . ' (' . $cpt->name . ')</h4>';
			echo '<p><label for="' . $this->get_field_id( $cpt->name . '-title' ) . '">Title: </label><input type="text" name="' . $this->get_field_name( $cpt->name . '-title' ) . '" id="' . $this->get_field_id( $cpt->name . '-title' ) . '" value="' . esc_attr( $this->get_field_value( $cpt->name . '-title' ) ) . '" size="27" /></p>';
			wp_editor( $this->get_field_value( $cpt->name . '-intro' ), $this->get_field_id( $cpt->name . '-intro' ), array( 'textarea_rows' => 5 ) );
		}
	}

	/**
	 * Footer Metabox
	 * @since 1.0.0
	 */
	function footer_metabox() {

		wp_editor( $this->get_field_value( 'footer' ), $this->get_field_id( 'footer' ), array( 'textarea_rows' => 5 ) );
	}

	/**
	 * After Post Information Metabox
	 * @since 1.0.0
	 */
	function after_post_information_metabox() {

		echo '<p><strong>Subscribe text</strong></p>';
		wp_editor( $this->get_field_value( 'subscribe-text' ), $this->get_field_id( 'subscribe-text' ), array( 'textarea_rows' => 5 ) );

		echo '<p><strong>Genesis text</strong></p>';
		wp_editor( $this->get_field_value( 'genesis-text' ), $this->get_field_id( 'genesis-text' ), array( 'textarea_rows' => 5 ) );
	}

	/**
	 * Plugin Archive
	 *
	 */
	function plugin_archive_metabox() {

		echo '<p><strong>Introduction</strong></p>';
		echo '<p><label for="' . $this->get_field_id( 'plugin-archive-title' ) . '">Title: </label><input type="text" name="' . $this->get_field_name( 'plugin-archive-title' ) . '" id="' . $this->get_field_id( 'plugin-archive-title' ) . '" value="' . esc_attr( $this->get_field_value( 'plugin-archive-title' ) ) . '" size="27" /></p>';
		wp_editor( $this->get_field_value( 'plugin-archive-intro' ), $this->get_field_id( 'plugin-archive-intro' ) );

		echo '<p><strong>Lower Section</strong></p>';
		echo '<p><label for="' . $this->get_field_id( 'plugin-archive-lower-title' ) . '">Title: </label><input type="text" name="' . $this->get_field_name( 'plugin-archive-lower-title' ) . '" id="' . $this->get_field_id( 'plugin-archive-lower-title' ) . '" value="' . esc_attr( $this->get_field_value( 'plugin-archive-lower-title' ) ) . '" size="27" /></p>';
		wp_editor( $this->get_field_value( 'plugin-archive-lower-content' ), $this->get_field_id( 'plugin-archive-lower-content' ) );

	}

}

/**
 * Add the Theme Settings Page
 * @since 1.0.0
 */
function be_add_child_theme_settings() {
	global $_child_theme_settings;
	$_child_theme_settings = new Child_Theme_Settings;
}

add_action( 'genesis_admin_menu', 'be_add_child_theme_settings' );
