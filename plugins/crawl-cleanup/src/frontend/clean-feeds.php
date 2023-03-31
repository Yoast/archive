<?php

namespace Yoast\WP\Crawl_Cleanup\Frontend;

use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * The frontend class that cleans up the different feeds.
 */
class Clean_Feeds {

	/**
	 * Holds our options instance.
	 *
	 * @var Options
	 */
	private Options $options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Options::instance();

		add_action( 'wp_loaded', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register our RSS related hooks.
	 */
	public function register_hooks(): void {
		if ( $this->options->remove_feed_global ) {
			add_action( 'feed_links_show_posts_feed', '__return_false' );
		}
		if ( $this->options->remove_feed_global_comments ) {
			add_action( 'feed_links_show_comments_feed', '__return_false' );
		}

		// Replace core's feed_links_extra with our own which allows us control over which feeds to show.
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		add_action( 'wp_head', [ $this, 'feed_links_extra_replacement' ], 3 );

		// Redirect the ones we don't want to exist.
		add_action( 'wp', [ $this, 'redirect_unwanted_feeds' ], - 10000 );
	}

	/**
	 * Redirect feeds we don't want away.
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
		elseif ( get_query_var( 'attachment', false ) && $feed === 'feed' ) {
			$this->redirect_feed( $url, 'Attachment should not have feeds, so we disable them.' );
		}
		// Only if we're on the global feed, the query is _just_ `'feed' => 'feed'`, hence this check.
		elseif ( $GLOBALS['wp_query']->query === [ 'feed' => 'feed' ] && $this->options->remove_feed_global ) {
			$this->redirect_feed( get_home_url(), 'We disable the RSS feed for performance reasons.' );
		}
		elseif ( is_comment_feed() && ! ( is_singular() || is_attachment() ) && $this->options->remove_feed_global_comments ) {
			$this->redirect_feed( $url, 'We disable comment feeds for performance reasons.' );
		}
		elseif ( is_comment_feed() && is_singular() && $this->options->remove_feed_post_comments ) {
			$url = get_permalink( get_queried_object() );
			$this->redirect_feed( $url, 'We disable post comment feeds for performance reasons.' );
		}
		elseif ( is_author() && $this->options->remove_feed_authors ) {
			$author_id = (int) get_query_var( 'author' );
			$url       = get_author_posts_url( $author_id );
			$this->redirect_feed( $url, 'We disable author feeds for performance reasons.' );
		}
		elseif ( ( is_tax() && $this->options->remove_feed_custom_taxonomies ) || ( is_category() && $this->options->remove_feed_categories ) || ( is_tag() && $this->options->remove_feed_tags ) ) {
			$term = get_queried_object();
			$url  = get_term_link( $term, $term->taxonomy );
			if ( is_wp_error( $url ) ) {
				$url = get_home_url();
			}
			$this->redirect_feed( $url, 'We disable taxonomy feeds for performance reasons.' );
		}
		elseif ( ( is_post_type_archive() ) && $this->options->remove_feed_post_types ) {
			$url = get_post_type_archive_link( $this->get_queried_post_type() );
			$this->redirect_feed( $url, 'We disable post type feeds for performance reasons.' );
		}
		elseif ( is_search() && $this->options->remove_feed_search ) {
			$this->redirect_feed( esc_url( trailingslashit( get_home_url() ) . '?s=' . get_search_query() ), 'We disable search RSS feeds for performance reasons.' );
		}
		// Always redirect paginated feeds.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		elseif ( is_feed() && preg_match( '|/page/\d+/|', $_SERVER['REQUEST_URI'] ) ) {
			$this->redirect_feed( get_home_url(), 'We disable all paginated RSS feeds for performance reasons.' );
		}
	}

	/**
	 * Adapted from `feed_links_extra` in WP core, this is a version that allows us to control which feeds to show.
	 *
	 * @param array $args Optional arguments.
	 */
	public function feed_links_extra_replacement( $args ): void {
		$defaults = [
			/* translators: Separator between blog name and feed type in feed links. */
			'separator'     => _x( '-', 'feed link', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Post title. */
			'singletitle'   => __( '%1$s %2$s %3$s Comments Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Category name. */
			'cattitle'      => __( '%1$s %2$s %3$s Category Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Tag name. */
			'tagtitle'      => __( '%1$s %2$s %3$s Tag Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Term name, 4: Taxonomy singular name. */
			'taxtitle'      => __( '%1$s %2$s %3$s %4$s Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Author name. */
			'authortitle'   => __( '%1$s %2$s Posts by %3$s Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Search query. */
			'searchtitle'   => __( '%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed', 'yoast-crawl-cleanup' ),
			/* translators: 1: Blog name, 2: Separator (raquo), 3: Post type name. */
			'posttypetitle' => __( '%1$s %2$s %3$s Feed', 'yoast-crawl-cleanup' ),
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_singular() && $this->options->remove_feed_post_comments === false ) {
			$id   = 0;
			$post = get_post( $id );

			if ( comments_open() || pings_open() || $post->comment_count > 0 ) {
				$title = sprintf( $args['singletitle'], get_bloginfo( 'name' ), $args['separator'], the_title_attribute( [ 'echo' => false ] ) );
				$href  = get_post_comments_feed_link( $post->ID );
			}
		}
		elseif ( is_post_type_archive() && $this->options->remove_feed_post_types === false ) {
			$post_type_obj = get_post_type_object( $this->get_queried_post_type() );
			$title         = sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], $post_type_obj->labels->name );
			$href          = get_post_type_archive_feed_link( $post_type_obj->name );
		}
		elseif ( is_category() && $this->options->remove_feed_categories === false ) {
			$term = get_queried_object();

			if ( $term ) {
				$title = sprintf( $args['cattitle'], get_bloginfo( 'name' ), $args['separator'], $term->name );
				$href  = get_category_feed_link( $term->term_id );
			}
		}
		elseif ( is_tag() && $this->options->remove_feed_tags === false ) {
			$term = get_queried_object();

			if ( $term ) {
				$title = sprintf( $args['tagtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name );
				$href  = get_tag_feed_link( $term->term_id );
			}
		}
		elseif ( is_tax() && $this->options->remove_feed_custom_taxonomies === false ) {
			$term = get_queried_object();

			if ( $term ) {
				$tax   = get_taxonomy( $term->taxonomy );
				$title = sprintf( $args['taxtitle'], get_bloginfo( 'name' ), $args['separator'], $term->name, $tax->labels->singular_name );
				$href  = get_term_feed_link( $term->term_id, $term->taxonomy );
			}
		}
		elseif ( is_author() && $this->options->remove_feed_authors === false ) {
			$author_id = (int) get_query_var( 'author' );

			$title = sprintf( $args['authortitle'], get_bloginfo( 'name' ), $args['separator'], get_the_author_meta( 'display_name', $author_id ) );
			$href  = get_author_feed_link( $author_id );
		}
		elseif ( is_search() && $this->options->remove_feed_search === false ) {
			$title = sprintf( $args['searchtitle'], get_bloginfo( 'name' ), $args['separator'], get_search_query( false ) );
			$href  = get_search_feed_link();
		}

		if ( isset( $title ) && isset( $href ) ) {
			echo '<link rel="alternate" type="application/rss+xml" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
		}
	}

	/**
	 * Sends a cache control header.
	 *
	 * @param int $expiration The expiration time.
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

	/**
	 * Retrieves the queried post type.
	 *
	 * @return string The queried post type.
	 */
	private function get_queried_post_type(): string {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		return $post_type;
	}

	/**
	 * Redirect a feed result to somewhere else.
	 *
	 * @param string $url    The location we're redirecting to.
	 * @param string $reason The reason we're redirecting.
	 */
	private function redirect_feed( string $url, string $reason ): void {
		header_remove( 'Content-Type' );
		header_remove( 'Last-Modified' );

		$this->cache_control_header( 7 * DAY_IN_SECONDS );

		wp_safe_redirect( $url, 301, 'Yoast Crawl Cleanup: ' . $reason );
		exit;
	}
}
