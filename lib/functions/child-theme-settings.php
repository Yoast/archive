<?php
/**
 * Child Theme Settings
 *
 * This file registers all of this child theme's specific Theme Settings, accessible from
 * Genesis > Child Theme Settings. Based in large parts on code by Bill Erickson.
 *
 * @package     BE_Genesis_Child
 * @since       1.0.0
 * @link        https://github.com/billerickson/BE-Genesis-Child
 * @author      Bill Erickson <bill@billerickson.net>
 * @copyright   Copyright (c) 2011, Bill Erickson
 * @copyright   Copyright (c) 2013, Yoast
 * @license     http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
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
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// Specify a unique page ID. 
		$page_id = 'child';

		// Set it as a child to genesis, and define the menu and page titles
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'genesis',
				'page_title'  => __( 'Genesis - Child Theme Settings by Yoast' ),
				'menu_title'  => __( 'Child Theme Settings', 'yoast-theme' )
			)
		);

		// Set up page options. These are optional, so only uncomment if you want to change the defaults
		$page_ops = array( //	'screen_icon'       => 'options-general',
			//	'save_button_text'  => __('Save Settings', 'yoast-theme'),
			//	'reset_button_text' => __('Reset Settings', 'yoast-theme'),
			//	'save_notice_text'  => __('Settings saved.', 'yoast-theme'),
			//	'reset_notice_text' => __('Settings reset.', 'yoast-theme')
		);

		// Give it a unique settings field. 
		// You'll access them from genesis_get_option( 'option_name', 'child-settings' );
		$settings_field = 'child-settings';

		// Set the default values
		$default_settings = array(
			'footer'          => 'Get this theme at <a href="http://yoast.com" name="Theme Creator Yoast">Yoast.com</a>',
			'yst-logo'        => '',
			'yst-mobile-logo' => '',
		);

		// Create the Admin Page
		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		// Initialize the Sanitization Filter
		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'yst_media_admin_scripts_style' ) );
	}

	/**
	 * Set up Sanitization Filters
	 *
	 * See /lib/classes/sanitization.php for all available filters.
	 *
	 * @since 1.0.0
	 */
	function sanitization_filters() {
		genesis_add_option_filter( 'safe_html', $this->settings_field,
			array(
				'footer',
				'yst-logo',
				'yst-mobile-logo',
			) );
	}

	/**
	 * Register metaboxes on Child Theme Settings page
	 * @since 1.0.0
	 */
	function metaboxes() {
		add_meta_box( 'footer_metabox', __( 'Footer', 'yoast-theme' ), array( $this, 'footer_metabox' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'yst_logos_metabox', __( 'Website Logo', 'yoast-theme' ), array( $this, 'yst_logos_metabox' ), $this->pagehook, 'main', 'high' );
	}

	/**
	 * Footer Metabox
	 * @since 1.0.0
	 */
	function footer_metabox() {
		wp_editor( $this->get_field_value( 'footer' ), $this->get_field_id( 'footer' ), array( 'textarea_rows' => 5 ) );
	}

	/**
	 * Helper function: Add scripts for media uploader
	 */
	function yst_media_admin_scripts_style() {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'child' ) {
			wp_enqueue_media();
			wp_register_script( 'yst-media-uploader', get_stylesheet_directory_uri() . '/lib/js/media-uploader.js', array( 'jquery' ) );
			wp_enqueue_script( 'yst-media-uploader' );
		}
	}

	/**
	 * Yoast Mobile Logo
	 *
	 * Add the option to upload a smaller version of a sites logo, to improve speed on mobile devices
	 *
	 * @since 1.0.0
	 */
	function yst_logos_metabox() {
		echo apply_filters( 'yst_logo_title', '<h4>Upload your logo</h4>' );
		echo '<p class="yst-logo-explanation">' . __( 'Your logo will be displayed using a fixed width of 360 pixels. The preferred height of the image is 144 pixels. For the best result, use an image with these dimensions.', 'yoast-theme' ) . ' </p>';
		?>
		<label for="upload_image">
			<input id="yst_logo_input" type="text" size="36" name="<?php echo $this->get_field_name( 'yst-logo' ); ?>" value="<?php echo $this->get_field_value( 'yst-logo' ); ?>" />
			<input id="yst_logo_button" class="yst_image_upload_button button" type="button" value="<?php _e( 'Upload Image', 'yoast-theme' ); ?>" />
		</label>
		<?php
		$yst_logo = $this->get_field_value( 'yst-logo' );
		if ( isset( $yst_logo ) && ! empty ( $yst_logo ) ) {
			echo '<p class="preview_logos">';
			//echo apply_filters( 'yst_logo_preview', '<h4>Preview Mobile logo</h4>' );
			echo '<div id="yst_logo_preview"><em>' . __( 'Your current logo:', 'yoast-theme' ) . '</em><br /><img src="' . esc_html( $this->get_field_value( 'yst-logo' ) ) . '" alt="' . __( 'Logo Preview', 'yoast-theme' ) . '" style="width:360px;" /></div>';
			echo '</p>';
		}
		echo apply_filters( 'yst_mobile_logo_title', '<h4>Upload your mobile logo</h4>' );
		echo '<p class="yst-mobile-logo-explanation">' . __( 'To prevent loading an unnecessary large logo on mobile devices, you can upload a small version of your logo here.', 'yoast-theme' ) . '<br />' . __( 'Your mobile logo will be displayed using a width of 230 pixels and a height of 36 pixels to fit the mobile version of your website. For the best result, use an image with these dimensions.', 'yoast-theme' ) . ' </p>';
		?>
		<label for="upload_mobile_image">
			<input id="yst_mobile_logo_input" type="text" size="36" name="<?php echo $this->get_field_name( 'yst-mobile-logo' ); ?>" value="<?php echo $this->get_field_value( 'yst-mobile-logo' ); ?>" />
			<input id="yst_mobile_logo_button" class="yst_image_upload_button button" type="button" value="<?php _e( 'Upload Image', 'yoast-theme' ); ?>" />
		</label>
		<?php
		$yst_mobile_logo = $this->get_field_value( 'yst-mobile-logo' );
		if ( isset( $yst_mobile_logo ) && ! empty ( $yst_mobile_logo ) ) {
			echo '<p class="preview_logos">';
			echo '<div id="yst_mobile_logo_preview"><em>' . __( 'Your current mobile logo:', 'yoast-theme' ) . '</em><br /><img src="' . esc_html( $this->get_field_value( 'yst-mobile-logo' ) ) . '" alt="' . __( 'Mobile Logo Preview', 'yoast-theme' ) . '" style="max-width:230px;max-height:36px;" /></div>';
			echo '</p>';
		}
	}
}

/**
 * Add the Theme Settings Page
 * @since 1.0.0
 *
 */
function yst_add_child_theme_settings() {
	global $_child_theme_settings;
	$_child_theme_settings = new Child_Theme_Settings;
}

add_action( 'genesis_admin_menu', 'yst_add_child_theme_settings' );