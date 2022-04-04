<?php

namespace Yoast\WP\Crawl_Cleanup\Frontend;

use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * The frontend class that does the work.
 */
class Optimizations {

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

		new Clean_Permalink();
		new Clean_Feeds();

		add_action( 'wp_loaded', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register all our hooks.
	 */
	public function register_hooks(): void {
		// Remove stuff from the <head>.
		$this->clean_head();

		// Remove HTTP headers we don't want.
		add_action( 'send_headers', [ $this, 'clean_headers' ], 9999 );

		// Remove Gutenberg cruft.
		add_action( 'wp_enqueue_scripts', [ $this, 'unload_gutenberg' ], 10000 );

		// Advanced.
		add_action( 'wp_enqueue_scripts', [ $this, 'unload_styles_scripts' ], 10000 );
	}

	/**
	 * Clean the `<head>` section of a site.
	 */
	private function clean_head(): void {
		if ( $this->options->remove_shortlinks ) {
			// Remove shortlinks.
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
			remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
		}

		if ( $this->options->remove_rest_api_links ) {
			// Remove REST API links.
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		}

		if ( $this->options->remove_rsd_wlw_links ) {
			// Remove RSD and WLW Manifest links.
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( $this->options->remove_oembed_links ) {
			// Remove JSON+XML oEmbed links.
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		}

		if ( $this->options->remove_generator ) {
			remove_action( 'wp_head', 'wp_generator' );
		}

		if ( $this->options->remove_emoji_scripts ) {
			// Remove emoji scripts and additional stuff they cause.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			add_filter( 'wp_resource_hints', [ $this, 'resource_hints_plain_cleanup' ], 1 );
		}
	}

	/**
	 * Unload styles and scripts as specified in the advanced settings.
	 *
	 * @return void
	 */
	public function unload_styles_scripts(): void {
		if ( $this->options->remove_styles !== '' ) {
			$styles_to_remove = explode( ',', $this->options->remove_styles );
			foreach ( $styles_to_remove as $style ) {
				$style = preg_replace( '/(-inline)?(-css)$/', '', trim( $style ) );
				wp_deregister_style( $style );
				wp_dequeue_style( $style );
			}
		}

		if ( $this->options->remove_scripts !== '' ) {
			$scripts_to_remove = explode( ',', $this->options->remove_scripts );
			foreach ( $scripts_to_remove as $script ) {
				$script = trim( $script );
				wp_deregister_script( $script );
				wp_dequeue_script( $script );
			}
		}
	}

	/**
	 * Unloads Gutenberg styles.
	 */
	public function unload_gutenberg(): void {
		if ( $this->options->remove_gutenberg_global_styles ) {
			wp_dequeue_style( 'global-styles' );
		}
		if ( $this->options->remove_gutenberg_block_library ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'wp-block-library-theme' );
		}
		if ( $this->options->remove_gutenberg_duotone ) {
			remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		}
	}

	/**
	 * Removes X-Pingback and X-Powered-By headers as they're unneeded.
	 */
	public function clean_headers(): void {
		if ( headers_sent() ) {
			return;
		}

		if ( $this->options->remove_powered_by_header ) {
			header_remove( 'X-Pingback' );
		}
		if ( $this->options->remove_pingback_header ) {
			header_remove( 'X-Powered-By' );
		}
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
}
