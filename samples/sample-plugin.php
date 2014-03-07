<?php

/*
Plugin Name: Sample Plugin
Version: 1.0
Plugin URI: https://yoast.com/
Description: A sample plugin to test the License Manager
Author: Yoast, DvanKooten
Author URI: http://yoast.com/
Text Domain: sample-plugin
*/

/**
 * Class Sample_Plugin
 *
 * @todo example needs to be rewritten
 */
class Sample_Plugin {

	private $api_url = 'http://localhost/wp/latest/';
	private $item_name = 'Sample Plugin';
	private $item_url = '';
	private $version = '1.0';
	private $text_domain = 'sample-plugin';
	private $file_slug;
	private $author = 'Yoast';


	/**
	* @var Yoast_Plugin_License_Manager the license manager instance
	*/ 
	private $license_manager;

	public function __construct() {

		$this->file_slug = plugin_basename( __FILE__ );

		// we only need license stuff inside the admin area
		if( is_admin() ) {

			// add menu item
			add_action( 'admin_menu', array( $this, 'add_license_menu') ); 

			// load license class
			$this->load_license_manager();			
		}

		
	}

	/**
	* Loads the License_Plugin_Manager class
	*
	* The class will take care of the rest: notices, license (de)activations, updates, etc..
	*/
	public function load_license_manager() {
		// load license manager
			$options_page = 'options.php?page=' . $this->text_domain . '-license';
			require_once dirname( __FILE__ ) . '/License-Manager/class-license-manager.php';
			require_once dirname( __FILE__ ) . '/License-Manager/class-plugin-license-manager.php';

			// instantiate license class, this is all we need
			$this->license_manager = new Yoast_Plugin_License_Manager( $this->api_url, $this->item_name, $this->file_slug, $this->version, $this->item_url, $options_page, $this->text_domain, $this->author );
	}

	/**
	 * Add license page and add it to Themes menu
	 */
	public function add_license_menu() {
		$theme_page = add_options_page( sprintf( __( '%s License', $this->text_domain ), $this->item_name ), sprintf( __( '%s License', $this->text_domain ), $this->item_name ), 'manage_options', $this->text_domain . '-license', array( $this, 'show_license_page' ) );
	}
	
	/**
	* Shows license page
	*/
	public function show_license_page() {
		?>
		<div class="wrap">
			<?php //settings_errors(); ?>

			<?php $this->license_manager->show_license_form( false ); ?>
		</div>
		<?php
	}
}

new Sample_Plugin();
