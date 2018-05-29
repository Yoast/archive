<?php

/**
 * This class ensures the Yoast SEO Settings are being set as needed.
 */
class Yoast_Purge_Control_Yoast_SEO_Settings {
	/**
	 * Ensures the settings are set as we recommend them to be.
	 */
	public function enforce_settings() {
		// Disable the attachment pages and redirect them to the attachment itself.
		WPSEO_Options::set( 'disable-attachment', true );
	}
}
