<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * Sitemap attachment provider.
 */
final class Yoast_Purge_Attachment_Sitemap_Provider extends WPSEO_Post_Type_Sitemap_Provider {

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
	 * Determines whether this provider can handle the given type.
	 *
	 * @param string $type The type that could be handled.
	 *
	 * @return bool Whether this provider can handle the given type.
	 */
	public function handles_type( $type ) {
		return $type === 'purge_attachment';
	}

	/**
	 * Determines whether the given post type is valid for this provider.
	 *
	 * @param string $post_type The type to check.
	 *
	 * @return bool Whether this provider can handle the post type.
	 */
	public function is_valid_post_type( $post_type ) {
		return $post_type === 'attachment';
	}

	/**
	 * Returns the sitemap data for a given post.
	 *
	 * We only overwrite the last modified date because we want to hardcode it
	 * to the activation date of this plugin.
	 *
	 * @param stdClass $post The post data for which to build the sitemap item.
	 *
	 * @return array The data for the sitemap link.
	 */
	public function get_url( $post ) {
		$link = parent::get_url( $post );

		$link['mod'] = date( 'Y-m-d H:i:s', $this->options->get_activation_date() );

		return $link;
	}

	/**
	 * Returns the sitemap links for attachments.
	 *
	 * We could do an extra check on type here, but that should already be
	 * handled by `handles_type`.
	 *
	 * @param string $type         The type we are building for.
	 * @param int    $max_entries  The maximum amount of entries per page.
	 * @param int    $current_page The current page we are on.
	 *
	 * @return array List of links that should be rendered.
	 */
	public function get_sitemap_links( $type, $max_entries, $current_page ) {
		return parent::get_sitemap_links( 'attachment', $max_entries, $current_page );
	}

	/**
	 * Retrieves the index links for the sitemap to put in the index.
	 *
	 * @param int $max_entries Entries per sitemap.
	 *
	 * @return array List of sitemap index links.
	 */
	public function get_index_links( $max_entries ) {
		$index_links = parent::get_index_links( $max_entries );

		$index_links = array_map( array( $this, 'set_modification_date' ), $index_links );

		return $index_links;
	}

	/**
	 * Overwrites the modification date with the plugin activation date.
	 *
	 * @param array $entry Sitemap link index.
	 *
	 * @return array Modified sitemap link index.
	 */
	public function set_modification_date( $entry ) {
		$entry['lastmod'] = date( 'Y-m-d H:i:s', $this->options->get_activation_date() );

		return $entry;
	}
}
