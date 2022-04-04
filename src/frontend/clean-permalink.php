<?php

namespace Yoast\WP\Crawl_Cleanup\Frontend;

use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * The frontend class that cleans up the permalinks.
 */
class Clean_Permalink {

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
	 * Register all our hooks.
	 */
	public function register_hooks(): void {
		if ( $this->options->clean_permalink_google_campaign ) {
			add_action( 'template_redirect', [ $this, 'utm_redirect' ], 0 );
		}
		if ( $this->options->clean_permalink ) {
			add_action( 'template_redirect', [ $this, 'clean_permalink' ], 1 );
		}
	}

	/**
	 * Redirect utm variables away.
	 */
	public function utm_redirect(): void {
		// Prevents WP CLI from throwing an error.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! isset( $_SERVER['REQUEST_URI'] ) || strpos( $_SERVER['REQUEST_URI'], '?' ) === false ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( stripos( $_SERVER['REQUEST_URI'], 'utm_' ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$parsed = wp_parse_url( $_SERVER['REQUEST_URI'] );

			$query      = explode( '&', $parsed['query'] );
			$utms       = [];
			$other_args = [];

			foreach ( $query as $query_arg ) {
				if ( stripos( $query_arg, 'utm_' ) === 0 ) {
					$utms[] = $query_arg;
					continue;
				}
				$other_args[] = $query_arg;
			}

			$other_args_str = '';
			if ( count( $other_args ) > 0 ) {
				$other_args_str = '?' . implode( '&', $other_args );
			}

			$new_path = $parsed['path'] . $other_args_str . '#' . implode( '&', $utms );

			wp_safe_redirect( get_bloginfo( 'url' ) . $new_path, 301, 'Yoast Crawl Cleanup: redirect utm variables to #' );
			exit;
		}
	}

	/**
	 * Removes unneeded query variables from the URL.
	 */
	public function clean_permalink(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not processing anything yet...
		if ( is_robots() || get_query_var( 'sitemap' ) || empty( $_GET ) ) {
			return;
		}
		global $wp_query;

		$current_url = $this->recreate_current_url();
		$proper_url  = '';
		if ( is_singular() ) {
			global $post;
			$proper_url = get_permalink( $post->ID );

			$page = get_query_var( 'page' );
			if ( $page && $page !== 1 ) {
				$the_post   = get_post( $post->ID );
				$page_count = substr_count( $the_post->post_content, '<!--nextpage-->' );
				if ( $page > ( $page_count + 1 ) ) {
					$proper_url = user_trailingslashit( trailingslashit( $proper_url ) . ( $page_count + 1 ) );
				}
				else {
					$proper_url = user_trailingslashit( trailingslashit( $proper_url ) . $page );
				}
			}

			// Fix reply to comment links, whoever decided this should be a GET variable?
			// phpcs:ignore WordPress.Security -- We know this is scary.
			if ( isset( $_SERVER['REQUEST_URI'] ) && preg_match( '`(\?replytocom=[^&]+)`', sanitize_text_field( $_SERVER['REQUEST_URI'] ), $matches ) ) {
				$proper_url .= str_replace( '?replytocom=', '#comment-', $matches[0] );
			}
			unset( $matches );

			// Prevent cleaning out posts & page previews for people capable of viewing them.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We know this is scary.
			if ( isset( $_GET['preview'] ) && isset( $_GET['preview_nonce'] ) && current_user_can( 'edit_post' ) ) {
				$proper_url = '';
			}
		}
		elseif ( is_front_page() ) {
			if ( $this->is_home_posts_page() ) {
				$proper_url = get_bloginfo( 'url' ) . '/';
			}
			elseif ( $this->is_home_static_page() ) {
				$proper_url = get_permalink( $GLOBALS['post']->ID );
			}
		}
		elseif ( $this->is_posts_page() ) {
			$proper_url = get_permalink( get_option( 'page_for_posts' ) );
		}
		elseif ( is_category() || is_tag() || is_tax() ) {
			$term = $wp_query->get_queried_object();
			if ( is_feed() ) {
				$proper_url = get_term_feed_link( $term->term_id, $term->taxonomy );
			}
			else {
				$proper_url = get_term_link( $term, $term->taxonomy );
			}
		}
		elseif ( is_search() ) {
			$s          = rawurlencode( preg_replace( '`(%20|\+)`', ' ', get_search_query() ) );
			$proper_url = get_bloginfo( 'url' ) . '/?s=' . $s;
		}
		elseif ( is_404() ) {
			if ( is_multisite() && ! is_subdomain_install() && is_main_site() ) {
				if ( $current_url === get_bloginfo( 'url' ) . '/blog/' || $current_url === get_bloginfo( 'url' ) . '/blog' ) {
					if ( $this->is_home_static_page() ) {
						$proper_url = get_permalink( get_option( 'page_for_posts' ) );
					}
					else {
						$proper_url = get_bloginfo( 'url' ) . '/';
					}
				}
			}
		}
		if ( ! empty( $proper_url ) && $wp_query->query_vars['paged'] !== 0 && $wp_query->post_count !== 0 ) {
			if ( is_search() ) {
				$proper_url = get_bloginfo( 'url' ) . '/page/' . $wp_query->query_vars['paged'] . '/?s=' . rawurlencode( get_search_query() );
			}
			else {
				$proper_url = user_trailingslashit( trailingslashit( $proper_url ) . 'page/' . $wp_query->query_vars['paged'] );
			}
		}

		// Allow plugins to register their own variables not to clean.
		$whitelisted_extravars = apply_filters( 'Yoast\WP\Crawl_Cleanup\whitelist_permalink_vars', [] );

		if ( $this->options->clean_permalink_extra_variables !== '' ) {
			$whitelisted_extravars = array_merge( $whitelisted_extravars, explode( ',', $this->options->clean_permalink_extra_variables ) );
		}
		foreach ( $whitelisted_extravars as $get ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We know this is scary.
			if ( isset( $_GET[ trim( $get ) ] ) ) {
				$proper_url = '';
			}
		}
		if ( ! empty( $proper_url ) && $current_url !== $proper_url ) {
			header( 'Content-Type: redirect', true );
			header_remove( 'Content-Type' );
			header_remove( 'Last-Modified' );
			header_remove( 'X-Pingback' );

			wp_safe_redirect( $proper_url, 301, 'Yoast Crawl Cleanup: unregistered URL parameter removed' );
			exit;
		}
	}

	/**
	 * Recreate current URL.
	 *
	 * @return string
	 */
	private function recreate_current_url(): string {
		$current_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
			$current_url .= 's';
		}
		$current_url .= '://';

		if ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] !== '80' && $_SERVER['SERVER_PORT'] !== '443' ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		}
		else {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- We know this is scary.
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}

		return $current_url;
	}

	/**
	 * Determine whether the current page is the homepage and shows posts.
	 *
	 * @return bool
	 */
	private function is_home_posts_page(): bool {
		return ( is_home() && get_option( 'show_on_front' ) !== 'page' );
	}

	/**
	 * Determine whether the current page is a static homepage.
	 *
	 * @return bool
	 */
	private function is_home_static_page(): bool {
		return ( is_front_page() && get_option( 'show_on_front' ) === 'page' && is_page( get_option( 'page_on_front' ) ) );
	}

	/**
	 * Determine whether this is the posts page, regardless of whether it's the frontpage or not.
	 *
	 * @return bool
	 */
	private function is_posts_page(): bool {
		return ( is_home() && ! is_front_page() );
	}
}
