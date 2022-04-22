<?php

namespace Yoast\WP\Crawl_Cleanup\Options;

/**
 * Options Class for the Yoast Crawl Cleanup plugin.
 *
 * @property boolean clean_permalink
 * @property boolean clean_permalink_google_campaign
 * @property string  clean_permalink_extra_variables
 * @property boolean remove_shortlinks
 * @property boolean remove_rest_api_links
 * @property boolean remove_rsd_wlw_links
 * @property boolean remove_oembed_links
 * @property boolean remove_emoji_scripts
 * @property boolean remove_generator
 * @property boolean remove_powered_by_header
 * @property boolean remove_pingback_header
 * @property boolean remove_feed_global
 * @property boolean remove_feed_global_comments
 * @property boolean remove_feed_authors
 * @property boolean remove_feed_post_types
 * @property boolean remove_feed_categories
 * @property boolean remove_feed_tags
 * @property boolean remove_feed_custom_taxonomies
 * @property boolean remove_feed_post_comments
 * @property boolean remove_feed_search
 * @property boolean remove_gutenberg_global_styles
 * @property boolean remove_gutenberg_block_library
 * @property boolean remove_gutenberg_duotone
 * @property string  remove_styles
 * @property string  remove_scripts
 * @property boolean search_cleanup_characters
 * @property integer search_word_limit
 * @property integer search_character_limit
 */
class Options {

	/**
	 * The default options for the Yoast Crawl Cleanup plugin.
	 *
	 * @var array
	 */
	public static array $option_defaults = [
		'clean_permalink'                 => false,
		'clean_permalink_google_campaign' => true,
		'clean_permalink_extra_variables' => '',
		'remove_shortlinks'               => true,
		'remove_rest_api_links'           => true,
		'remove_rsd_wlw_links'            => true,
		'remove_oembed_links'             => true,
		'remove_emoji_scripts'            => true,
		'remove_generator'                => true,
		'remove_powered_by_header'        => true,
		'remove_pingback_header'          => true,
		'remove_feed_global'              => false,
		'remove_feed_global_comments'     => true,
		'remove_feed_authors'             => false,
		'remove_feed_post_types'          => false,
		'remove_feed_categories'          => false,
		'remove_feed_tags'                => false,
		'remove_feed_custom_taxonomies'   => false,
		'remove_feed_post_comments'       => true,
		'remove_feed_search'              => true,
		'remove_gutenberg_global_styles'  => false,
		'remove_gutenberg_block_library'  => false,
		'remove_gutenberg_duotone'        => true,
		'remove_styles'                   => '',
		'remove_scripts'                  => '',
		'search_cleanup_characters'       => true,
		'search_word_limit'               => 8,
		'search_character_limit'          => 50,
	];

	/**
	 * Holds the type of variable that each option is, so we can cast it to that.
	 *
	 * @var string[]
	 */
	public static array $option_var_types = [
		'clean_permalink'                 => 'bool',
		'clean_permalink_google_campaign' => 'bool',
		'clean_permalink_extra_variables' => 'string',
		'remove_shortlinks'               => 'bool',
		'remove_rest_api_links'           => 'bool',
		'remove_rsd_wlw_links'            => 'bool',
		'remove_oembed_links'             => 'bool',
		'remove_emoji_scripts'            => 'bool',
		'remove_generator'                => 'bool',
		'remove_powered_by_header'        => 'bool',
		'remove_pingback_header'          => 'bool',
		'remove_feed_global'              => 'bool',
		'remove_feed_global_comments'     => 'bool',
		'remove_feed_authors'             => 'bool',
		'remove_feed_post_types'          => 'bool',
		'remove_feed_categories'          => 'bool',
		'remove_feed_tags'                => 'bool',
		'remove_feed_custom_taxonomies'   => 'bool',
		'remove_feed_post_comments'       => 'bool',
		'remove_feed_search'              => 'bool',
		'remove_gutenberg_global_styles'  => 'bool',
		'remove_gutenberg_block_library'  => 'bool',
		'remove_gutenberg_duotone'        => 'bool',
		'remove_styles'                   => 'string',
		'remove_scripts'                  => 'string',
		'search_cleanup_characters'       => 'bool',
		'search_word_limit'               => 'int',
		'search_character_limit'          => 'int',
	];

	/**
	 * Name of the option we're using.
	 *
	 * @var string
	 */
	public string $option_name = 'yoast_crawl_cleanup';

	/**
	 * Saving active instance of this class in this static var.
	 *
	 * @var Options
	 */
	private static Options $instance;

	/**
	 * Holds the actual options.
	 *
	 * @var array
	 */
	public array $options = [];

	/**
	 * Loads Yoast Crawl Cleanup-options set in WordPress.
	 * If already set: trim some option, otherwise load defaults.
	 */
	private function load_options(): void {
		$options = get_option( $this->option_name );

		if ( ! is_array( $options ) ) {
			$this->options = self::$option_defaults;
			update_option( $this->option_name, $this->options );
		}
		else {
			$this->options = array_merge( self::$option_defaults, $options );
			$this->sanitize_options();
		}
	}

	/**
	 * Forces all options to be of the type we expect them to be of.
	 */
	private function sanitize_options(): void {
		foreach ( $this->options as $key => $value ) {
			if ( ! isset( self::$option_var_types[ $key ] ) ) {
				unset( $this->options[ $key ] );
				update_option( $this->option_name, $this->options );
			}
			switch ( self::$option_var_types[ $key ] ) {
				case 'string':
					$this->options[ $key ] = (string) $value;
					break;
				case 'bool':
					$this->options[ $key ] = (bool) $value;
					break;
				case 'int':
					$this->options[ $key ] = (int) $value;
			}
		}
	}

	/**
	 * Getting instance of this object. If instance doesn't exist it will be created.
	 *
	 * @return Options
	 */
	public static function instance(): Options {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Options();
			self::$instance->load_options();
		}

		return self::$instance;
	}

	/**
	 * Magic getter for our options.
	 *
	 * @param string $option The option to retrieve.
	 *
	 * @return bool|string
	 */
	public function __get( string $option ) {
		return ( $this->options[ $option ] ?? self::$option_defaults[ $option ] );
	}
}
