<?php


final class Yoast_Purge_Attachment_Sitemap_Provider extends WPSEO_Post_Type_Sitemap_Provider {

	/** @var Yoast_Purge_Options */
	protected $options;

	/**
	 * Initializes.
	 *
	 * @param Yoast_Purge_Options $options The options class.
	 */
	public function __construct( Yoast_Purge_Options $options ) {
		$this->options = $options;

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
				'post_type' => 'attachment',
				'post_status' => 'any',
				'posts_per_page' => '-1',
				'date_query'     => array(
					'after' => date( 'Y-m-d H:i:s', $timestamp ),
				),
				'fields' => 'ids'
			)
		);

		if ( empty( $wp_query->posts ) ) {
			return $excluded;
		}

		return array_unique( array_merge( $excluded, $wp_query->posts ) );
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
