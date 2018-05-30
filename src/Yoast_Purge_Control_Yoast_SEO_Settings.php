<?php

/**
 * This class ensures the Yoast SEO Settings are being set as needed.
 */
class Yoast_Purge_Control_Yoast_SEO_Settings {
	/**
	 * Adds WordPress hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'wpseo_option_tab-metas_media', array( $this, 'add_hidden_settings' ) );
	}

	/**
	 * Adds the setting to the media tab to make sure it is not overwritten with empty -> false.
	 *
	 * @param string|null $input The current content of the filter value.
	 *
	 * @return string|null The unmodified content of the filter value.
	 */
	public function add_hidden_settings( $input ) {
		echo '<input type="hidden" name="wpseo_titles[disable-attachment]" value="on">';

		return $input;
	}
}
