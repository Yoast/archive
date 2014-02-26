<?php

class Yoast_Plugin_License_Manager extends Yoast_License_Manager {

	/**
	* Setup auto updater for plugins
	*/
	public function setup_auto_updater() {
		if( $this->license_is_valid() ) {
			// setup auto updater
			require dirname( __FILE__ ) . '/class-update-manager.php';
			require dirname( __FILE__ ) . '/class-plugin-update-manager.php';
			new Yoast_Plugin_Update_Manager( $this->api_url, $this->item_name, $this->get_license_key(), $this->slug, $this->version, $this->author, $this->text_domain );
		}
	}
	
	/**
	* Setup hooks
	*/
	public function specific_hooks() {

		// deactivate the license remotely on plugin deactivation
		register_deactivation_hook( $this->slug, array($this, 'deactivate_license') );
	}

}