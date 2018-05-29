<?php

/**
 * Serves a 410 page on attachment pages.
 */
final class Yoast_Purge_Attachment_Page_Server {

	/**
	 * Registers to WordPress.
	 */
	public function register_hooks() {
		add_action( 'template_redirect', array( $this, 'serve' ) );
	}

	/**
	 * Serve a 410 for attachment pages.
	 */
	public function serve() {
		if ( is_attachment() ) {
			$this->set_404();
			status_header( 410 );
		}
	}

	/**
	 * Sets the global wp query object so it thinks the current page is a 404.
	 */
	private function set_404() {
		global $wp_query;

		$wp_query->is_404 = true;
	}
}
