<?php

class Yoast_Theme_License_Manager extends Yoast_License_Manager {


	/**
	* @var string $theme_slug
	*/
	private $theme_slug;

	/**
	 * Constructor
	 *
	 * @param string $item_name The item name in the EDD shop
	 * @param string $item_url The absolute url on which users can purchase a license
	 * @param string $text_domain The text domain used for translating strings
	 * @param string $version The version number of the item 
	 */
	public function __construct( $item_name, $item_url, $theme_slug, $version, $text_domain = null ) {

		// store the license page url
		$license_page = 'themes.php?page=theme-license';

		parent::__construct( $item_name, $item_url, $version, $license_page, $text_domain );

		$this->theme_slug = $theme_slug;

		if( $this->license_is_valid() ) {
			// setup auto updater
			require dirname( __FILE__ ) . '/class-update-manager.php';
			require dirname( __FILE__ ) . '/class-theme-update-manager.php'; // @TODO: Autoload?
			new Yoast_Theme_Update_Manager( $this->api_url, $this->item_name, $this->get_license_key(), $this->theme_slug, $this->version, 'Yoast' );
		}
	}
	
	/**
	* Setup hooks
	*/
	public function specific_hooks() {
		// remotely deactivate license upon switching away from this theme
		add_action('switch_theme', array( $this, 'deactivate_license') );
		
		// Add the license menu
		add_action( 'admin_menu', array( $this, 'add_license_menu' ) );
	}

	/**
	 * Add license page and add it to Themes menu
	 */
	public function add_license_menu() {
		$theme_page = add_theme_page( sprintf( __( '%s License', $this->text_domain ), $this->item_name ), __( 'Theme License', $this->text_domain ), 'manage_options', 'theme-license', array( $this, 'show_license_page' ) );
	}
	
	/**
	* Shows license page
	*/
	public function show_license_page() {
		?>
		<div class="wrap">
			<?php settings_errors(); ?>

			<?php $this->show_license_form( false ); ?>
		</div>
		<?php
	}


}