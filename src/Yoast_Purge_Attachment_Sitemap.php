<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */


final class Yoast_Purge_Attachment_Sitemap {

	/** @var Yoast_Purge_Options */
	protected $options;

	/**
	 * Initializes.
	 *
	 * @param Yoast_Purge_Options $options The options class.
	 */
	public function __construct( Yoast_Purge_Options $options ) {
		$this->options = $options;
	}

	/**
	 * Registers hooks to WordPress.
	 */
	public function register_hooks() {
		add_filter( 'wpseo_sitemaps_providers', array( $this, 'add_provider' ) );
		add_filter( 'wpseo_build_sitemap_post_type', array( $this, 'change_type' ) );
	}

	/**
	 * Adds a provider to the list of Yoast SEO Sitemap providers.
	 *
	 * @param array $providers List of external sitemap providers.
	 *
	 * @return array List of external sitemap providers without our provider added.
	 */
	public function add_provider( $providers ) {
		require_once YOAST_PURGE_PLUGIN_DIR . '/src/Yoast_Purge_Attachment_Sitemap_Provider.php';

		$providers[] = new Yoast_Purge_Attachment_Sitemap_Provider( $this->options );

		return $providers;
	}

	/**
	 * Because Yoast SEO will mark the sitemap as invalid if the first provider
	 * doesn't return any links we need to change the type of the request so we
	 * make sure that our provider can handle the attachment sitemap.
	 *
	 * @param string $type The unmodified type for the sitemap.
	 * @return string The modified type for the sitemap.
	 */
	public function change_type( $type ) {
		if ( $type === 'attachment' ) {
			$type = 'purge_attachment';
		}

		return $type;
	}
}
