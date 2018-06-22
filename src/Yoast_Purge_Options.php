<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * Handels retrieving and saving options for the plugin.
 */
final class Yoast_Purge_Options {

	/**
	 * Name of the option in which the plugin activation date is saved.
	 *
	 * @var string
	 */
	const KEY_ACTIVATION_DATE = 'yoast-index-purge-activation-date';

	/**
	 * Name of the option which contains the toggle whether or not attachment pages should be purged.
	 *
	 * @var string
	 */
	const KEY_PURGE_ATTACHMENT_PAGES = 'yoast-index-purge-attachment-pages';
	const KEY_VERSION = 'yoast-index-purge-version';

	/**
	 * Retrieves the stored version.
	 *
	 * @return string The version saved in the database.
	 */
	public function get_version() {
		return get_option( self::KEY_VERSION, null );
	}

	/**
	 * Updates the version.
	 *
	 * @param string $version The version to set.
	 *
	 * @return void
	 */
	public function set_version( $version ) {
		update_option( self::KEY_VERSION, $version, true );
	}

	/**
	 * Returns the activation date of the plugin as a unix timestamp.
	 *
	 * @return int The activation date.
	 */
	public function get_activation_date() {
		return get_option( self::KEY_ACTIVATION_DATE, null );
	}

	/**
	 * Sets the activation date of the plugin.
	 *
	 * @param int $value The activation date as a unix timestamp.
	 *
	 * @return bool Whether or not the value was updated.
	 */
	public function set_activation_date( $value ) {
		return update_option( self::KEY_ACTIVATION_DATE, $value, true );
	}

	/**
	 * Returns whether or not attachment pages should be purged.
	 *
	 * @return bool Whether or not attachment pages should be purged.
	 */
	public function get_purge_attachment_pages() {
		$saved = get_option( self::KEY_PURGE_ATTACHMENT_PAGES, null );

		if ( $saved === null ) {
			return null;
		}

		return $saved === 'true';
	}

	/**
	 * Returns whether or not attachment paged should be purged.
	 *
	 * @return bool Whether or not attachment pages should be purged.
	 */
	public function is_attachment_page_purging_active() {
		return $this->get_purge_attachment_pages();
	}

	/**
	 * Sets whether attachment pages should be purged.
	 *
	 * @param bool $value Whether attachment pages should be purged.
	 *
	 * @return bool Whether or not the value was updated.
	 */
	public function set_purge_attachment_pages( $value ) {
		$value = ( $value === true ) ? 'true' : 'false';

		return update_option( self::KEY_PURGE_ATTACHMENT_PAGES, $value, true );
	}

	/**
	 * Sets the database options to the default options.
	 */
	public function set_default_options() {
		$activation_date = $this->get_activation_date();
		if ( $activation_date === null ) {
			$this->set_activation_date( time() );
		}

		$purge_attachment_pages = $this->get_purge_attachment_pages();
		if ( $purge_attachment_pages === null ) {
			$this->set_purge_attachment_pages( true );
		}
	}
}
