<?php

class Yoast_Plugin_License_Manager extends Yoast_License_Manager {
	
	/**
	* @var string Path to the main plugin file in the wp-content/plugins directory
	*/
	private $file_slug;

	/**
	* Constructor
	*
	* @param string $item_name The item name in the EDD shop.
	* @param string $api_url The URL of the shop running the EDD API. 
	*/
	public function __construct( $item_name, $item_url, $version, $license_page, $text_domain, $file_slug ) {

		parent::__construct( $item_name, $item_url, $version, $license_page, $text_domain );

		$this->file_slug = $file_slug;
		
		if( $this->license_is_valid() ) {
			// setup auto updater
			require dirname( __FILE__ ) . '/class-update-manager.php';
			require dirname( __FILE__ ) . '/class-plugin-update-manager.php'; // @TODO: Autoload?
			new Yoast_Plugin_Update_Manager( $this->api_url, $item_name, $this->get_license_key(), $this->file_slug, $this->version, 'Yoast' );
		}
	}

	/**
	* Setup hooks
	*/
	public function specific_hooks() {
		// deactivate the license remotely on plugin deactivation
		register_deactivation_hook( $this->file_slug, array($this, 'deactivate_license') );
	}

}