<?php

/**
 * This class will override the Media content tab content.
 */
class Yoast_Purge_Media_Settings_Tab_Content {
	/**
	 * Registers the WordPress hooks and filters.
	 */
	public function register_hooks() {
		add_filter( 'wpseo_option_tab-metas_media', array( $this, 'get_content' ) );
	}

	/**
	 * Retrieves the content that needs to be shown instead of the default Media configuration tab.
	 */
	public function get_content() {
		return 'Show some content.';
	}
}
