<?php

/**
 * This class ensures the Yoast SEO Settings are being set as needed.
 */
class Yoast_Purge_Control_Yoast_SEO_Settings {
	/**
	 * Registers the WordPress hooks and filters.
	 */
	public function register_hooks() {
		register_activation_hook( YOAST_PURGE_FILE, array( $this, 'enforce_settings' ) );
	}

	/**
	 * Ensures the settings are set as we recommend them to be.
	 */
	public function enforce_settings() {
		// Disable the attachment pages and redirect them to the attachment itself.
		WPSEO_Options::set( 'disable-attachment', true );
	}
}
