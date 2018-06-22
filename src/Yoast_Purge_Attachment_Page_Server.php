<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * Serves a 410 page on attachment pages.
 */
final class Yoast_Purge_Attachment_Page_Server {

	/**
	 * Valid image mime types.
	 *
	 * @var array
	 */
	private $valid_image_types = array( 'image/jpeg', 'image/gif', 'image/png' );

	/**
	 * Registers to WordPress.
	 */
	public function register_hooks() {
		// We need to do this earlier than Yoast SEO redirects the attachment.
		add_action( 'template_redirect', array( $this, 'serve' ), -10 );
		add_action( 'wpseo_attachment_redirect_url', '__return_null' );
	}

	/**
	 * Renders a file with a specific mime type to the browser.
	 *
	 * @param string $filepath Path to the file to render.
	 * @param string $mime_type Mime type to render it with.
	 */
	public function render_file( $filepath, $mime_type ) {
		// Open the attachment in a binary mode.
		$file = fopen( $filepath, 'rb' );

		// Send the right headers.
		header( 'Content-Type: ' . $mime_type );
		header( 'Content-Length: ' . filesize( $filepath ) );

		// Doing this instead of file_get_contents -> echo prevents PHP memory exhaustion.
		fpassthru( $file );
	}

	/**
	 * Renders the current attached file to the browser.
	 */
	public function render_attached_file() {
		$attachment = get_post( get_queried_object_id() );
		$filepath   = get_attached_file( $attachment->ID );
		$mime_type  = $attachment->post_mime_type;

		if ( $this->is_image( $mime_type ) ) {
			$this->render_file( $filepath, $mime_type );
		}
		else {
			$this->set_404();
		}
	}

	/**
	 * Serve a 410 for attachment pages.
	 */
	public function serve() {
		if ( is_attachment() ) {
			status_header( 410 );
			$this->render_attached_file();
		}
	}

	/**
	 * Determines if a mime type is for an image.
	 *
	 * @param string $mime_type The mime type to check.
	 *
	 * @return bool Whether or not the given mime type is for an image.
	 */
	private function is_image( $mime_type ) {
		return in_array( $mime_type, $this->valid_image_types, true );
	}

	/**
	 * Sets the 404 status in WordPress to true.
	 */
	protected function set_404() {
		global $wp_query;
		$wp_query->is_404 = true;
	}
}
