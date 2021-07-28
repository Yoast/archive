<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * Override attachment sitemap integration.
 */
final class Yoast_Purge_Attachment_Sitemap {

	/**
	 * Yoast Purge options handler.
	 *
	 * @var Yoast_Purge_Options
	 */
	private $options;

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

		// Exclude attachments that were added after activation.
		add_filter( 'wpseo_typecount_where', array( $this, 'typecount_where_filter' ), 10, 2 );
		add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', array( $this, 'exclude_newly_added_attachments' ), 10, 2 );
	}

	/**
	 * Modifies the sitemap count for attachments added after activating the plugin.
	 *
	 * @param string $where     SQL part, defaults to empty string.
	 * @param string $post_type Post type name.
	 *
	 * @return string Adjusted WHERE query part.
	 */
	public function typecount_where_filter( $where, $post_type ) {
		global $wpdb;

		// Only hook into attachment sitemaps.
		if ( $post_type !== 'attachment' ) {
			return $where;
		}

		// Only count items added before activating.
		$where .= $wpdb->prepare(
			" AND {$wpdb->posts}.post_date <= %s",
			date( 'Y-m-d H:i:s', $this->options->get_activation_date() )
		);

		return $where;
	}

	/**
	 * Excludes attachments from the sitemap that were added after activating.
	 *
	 * @param array $excluded List of excluded post ids.
	 *
	 * @return array Modified list of excluded post ids.
	 */
	public function exclude_newly_added_attachments( $excluded ) {
		// Add newly added attachments to the list.
		$timestamp = $this->options->get_activation_date();

		$wp_query = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => '100000',
				'date_query'     => array(
					'after' => date( 'Y-m-d H:i:s', $timestamp ),
				),
				'fields'         => 'ids',
			)
		);

		if ( empty( $wp_query->posts ) ) {
			return $excluded;
		}

		// Make sure the variable is an array.
		$excluded = (array) $excluded;

		// Combine the two into a complete list.
		return array_unique( array_merge( $excluded, $wp_query->posts ) );
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
