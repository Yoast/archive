<?php

class Yoast_Theme_License_Manager extends Yoast_License_Manager {

	/**
	 * Constructor
	 *
	 * @param string $item_name The item name in the EDD shop
	 * @param string $item_url The absolute url on which users can purchase a license
	 * @param string $text_domain The text domain used for translating strings
	 * @param string $version The version number of the item 
	 */
	public function __construct( $item_name, $item_url, $version, $text_domain ) {

		// store the license page url
		$license_page = 'themes.php?page=theme-license';

		parent::__construct( $item_name, $item_url, $version, $license_page, $text_domain );
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
			<?php $this->show_license_form(); ?>
		</div>
		<?php
	}


}