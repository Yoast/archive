<?php

/**
 * Handels retrieving and saving options for the plugin.
 */
final class Yoast_Purge_Options {

	const KEY_ACTIVATION_DATE = 'yoast-index-purge-activation-date';
	const KEY_PURGE_ATTACHMENT_PAGES = 'yoast-index-purge-attachment-pages';

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
		return update_option( self::KEY_ACTIVATION_DATE, $value );
	}

	/**
	 * Returns whether or not attachment paged should be purged.
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
		$value = $value ? 'true' : 'false';

		return update_option( self::KEY_PURGE_ATTACHMENT_PAGES, $value );
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
