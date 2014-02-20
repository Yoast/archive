<?php

class Yoast_Plugin_License_Manager extends Yoast_License_Manager {
	
	/**
	* @var string Path to the main plugin file in the wp-content/plugins directory
	*/
	private $file;

	/**
	* Constructor
	*
	* @param string $item_name The item name in the EDD shop.
	* @param string $api_url The URL of the shop running the EDD API. 
	*/
	public function __construct( $item_name, $item_url, $version, $license_page, $text_domain, $file ) {

		parent::__construct( $item_name, $item_url, $version, $license_page, $text_domain );

		$this->file = $file;
	}

	/**
	* Setup hooks
	*/
	public function specific_hooks() {
		// deactivate the license remotely on plugin deactivation
		register_deactivation_hook( $this->file, array($this, 'deactivate_license') );
	}

}