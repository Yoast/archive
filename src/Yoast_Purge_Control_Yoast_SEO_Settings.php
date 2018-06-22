<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

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

		// Make sure the Purge messages will never be shown again.
		WPSEO_Options::set( 'is-media-purge-relevant', false );

	}
}
