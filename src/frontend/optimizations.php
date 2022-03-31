<?php
namespace Joost\Optimizations\Frontend;

use Joost\Optimizations\Options\Options;

/**
 * The frontend class that does the work.
 */
class Optimizations {

	/**
	 * The options for the plugin.
	 *
	 * @var array
	 */
	private array $options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register all our hooks
	 */
	public function register_hooks(): void {
		$this->options = Options::instance()->get();

		if ( $this->options['remove_shortlinks'] ) {
			// Remove shortlinks.
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
			remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
		}

		if ( $this->options['remove_rest_api_links'] ) {
			// Remove REST API links.
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		}

		if ( $this->options['remove_rsd_wlw_links'] ) {
			// Remove RSD and WLW Manifest links.
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( $this->options['remove_oembed_links'] ) {
			// Remove JSON+XML oEmbed links.
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		}

		if ( $this->options['remove_emoji_scripts'] ) {
			// Remove emoji scripts and additional stuff they cause.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			add_filter( 'wp_resource_hints', [ $this, 'resource_hints_plain_cleanup' ], 1 );
		}

		// RSS.
		if ( $this->options['remove_feed_global_comments'] ) {
			add_action( 'feed_links_show_comments_feed', '__return_false' );    // Remove the overall comments feed.
		}
		if ( $this->options['remove_feed_post_comments'] || $this->options['remove_feed_post_types'] || $this->options['remove_feed_taxonomies'] ) {
			remove_action( 'wp_head', 'feed_links_extra', 3 );                    // Remove a lot of the other RSS links, for comment feeds, tag feeds etc.
		}
		if ( ! $this->options['remove_feed_post_types'] || ! $this->options['remove_feed_taxonomies'] ) {
			// Bring back the RSS feeds we *do* want.
			add_action( 'wp_head', [ $this, 'feed_links' ] );
		}

		// Redirect the ones we don't want to exist.
		add_action( 'wp', [ $this, 'redirect_unwanted_feeds' ], -10000 );
		// Remove HTTP headers we don't want.
		add_action( 'send_headers', [ $this, 'clean_headers' ], 9999 );
	}

	/**
	 * Removes X-Pingback and X-Powered-By headers as they're unneeded.
	 *
	 * @return void
	 */
	public function clean_headers(): void {
		if ( headers_sent() ) {
			return;
		}

		header_remove( 'X-Pingback' );
		header_remove( 'X-Powered-By' );
	}

	/**
	 * Remove the core s.w.org hint as it's only used for emoji stuff we don't use.
	 *
	 * @param array $hints The hints we're adding to.
	 *
	 * @return array
	 */
	public function resource_hints_plain_cleanup( $hints ): array {
		foreach ( $hints as $key => $hint ) {
			if ( strpos( $hint, '//s.w.org' ) !== false ) {
				unset( $hints[ $key ] );
			}
		}

		return $hints;
	}

	/**
	 * Redirect feeds we don't want away.
	 *
	 * @return void
	 */
	public function redirect_unwanted_feeds(): void {
		if ( ! is_feed() ) {
			return;
		}

		$feed = get_query_var( 'feed' );

		$url = get_home_url();
		if ( $feed === 'atom' || $feed === 'rdf' ) {
			$this->redirect_feed( $url, 'We disable ATOM and RDF feeds for performance reasons.' );
		}
		elseif ( is_comment_feed() && is_singular() && $this->options['remove_feed_post_comments'] ) {
			$url = get_permalink( get_queried_object() );
			$this->redirect_feed( $url, 'We disable comment feeds for performance reasons.' );
		}
		elseif ( is_comment_feed() && $this->options['remove_feed_global_comments'] ) {
			$this->redirect_feed( $url, 'We disable comment feeds for performance reasons.' );
		}
		elseif ( ( is_tax() || is_category() || is_tag() ) && $this->options['remove_feed_taxonomies'] ) {
			$this->redirect_feed( $url, 'We disable taxonomy feeds for performance reasons.' );
		}
		elseif ( ( is_post_type_archive() ) && $this->options['remove_feed_post_types'] ) {
			$this->redirect_feed( $url, 'We disable post type feeds for performance reasons.' );
		}
		elseif ( is_search() ) {
			// We're not even going to serve a result for this. Feeds for search results are not a service yoast.com should provide.
			$this->redirect_feed( esc_url( trailingslashit( get_home_url() ) . '?s=' . get_search_query() ), 'We disable search RSS feeds for performance reasons.' );
		}
		elseif ( $this->options['remove_feed_global'] ) {
			$this->redirect_feed( esc_url( trailingslashit( get_home_url() ) . '?s=' . get_search_query() ), 'We disable the RSS feed for performance reasons.' );
		}
	}

	/**
	 * Redirect a feed result to somewhere else.
	 *
	 * @param string $url    The location we're redirecting to.
	 * @param string $reason The reason we're redirecting.
	 *
	 * @return void
	 */
	private function redirect_feed( $url, $reason ): void {
		header_remove( 'Content-Type' );
		header_remove( 'Last-Modified' );

		$this->cache_control_header( 7 * DAY_IN_SECONDS );

		wp_safe_redirect( $url, 301, 'Strattic: ' . $reason );
		exit;
	}

	/**
	 * Adapted from `feed_links_extra` in WP core, this is a version that outputs a _lot_ less links.
	 *
	 * @return void
	 */
	public function feed_links(): void {
		$args = [
			/* translators: Separator between blog name and feed type in feed links. */
			'separator'     => '-',
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Term name, 4: Taxonomy singular name. */
			'taxtitle'      => __( '%1$s %2$s %3$s %4$s Feed', 'joost-optimizations' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Post type name. */
			'posttypetitle' => __( '%1$s %2$s %3$s Feed', 'joost-optimizations' ),
		];

		if ( is_post_type_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$post_type_obj = get_post_type_object( $post_type );
			$title         = sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], $post_type_obj->labels->name );
			$href          = get_post_type_archive_feed_link( $post_type_obj->name );
		}
		elseif ( is_tag() || is_tax() ) {
			$term = get_queried_object();

			if ( $term ) {
				$tax   = get_taxonomy( $term->taxonomy );
				$title = sprintf( $args['taxtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name, $tax->labels->singular_name );
				$href  = get_term_feed_link( $term->term_id, $term->taxonomy );
			}
		}

		if ( isset( $title ) && isset( $href ) ) {
			echo '<link rel="alternate" type="' . esc_attr( feed_content_type() ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
		}
	}

	/**
	 * Sends a cache control header.
	 *
	 * @param int $expiration The expiration time.
	 *
	 * @return void
	 */
	public function cache_control_header( int $expiration ): void {
		header_remove( 'Expires' );

		// The cacheability of the current request. 'public' allows caching, 'private' would not allow caching by proxies like CloudFlare.
		$cacheability = 'public';
		$format       = '%1$s, max-age=%2$d, s-maxage=%2$d, stale-while-revalidate=120, stale-if-error=14400';

		if ( is_user_logged_in() ) {
			$expiration   = 0;
			$cacheability = 'private';
			$format       = '%1$s, max-age=%2$d';
		}

		header( sprintf( 'Cache-Control: ' . $format, $cacheability, $expiration ), true );
	}
}
