<?php

namespace Joost_Optimizations\Options;

/**
 * Options Class for the Joost Optimizations plugin.
 *
 * @since 1.5
 */
class Options {

	/**
	 * The default options for the Joost Optimizations plugin.
	 *
	 * @var array
	 */
	public static array $option_defaults = [
//		'site_id'          => '',
		'remove_shortlinks'           => true,
		'remove_rest_api_links'       => true,
		'remove_rsd_wlw_links'        => true,
		'remove_oembed_links'         => true,
		'remove_emoji_scripts'        => true,
		'remove_feed_global'          => false,
		'remove_feed_global_comments' => true,
		'remove_feed_post_types'      => false,
		'remove_feed_taxonomies'      => false,
		'remove_feed_post_comments'   => true,
	];

	/**
	 * Holds the type of variable that each option is, so we can cast it to that.
	 *
	 * @var string[]
	 */
	public static array $option_var_types = [
//		'site_id'          => 'string',
		'remove_shortlinks'           => 'bool',
		'remove_rest_api_links'       => 'bool',
		'remove_rsd_wlw_links'        => 'bool',
		'remove_oembed_links'         => 'bool',
		'remove_emoji_scripts'        => 'bool',
		'remove_feed_global'          => 'bool',
		'remove_feed_global_comments' => 'bool',
		'remove_feed_post_types'      => 'bool',
		'remove_feed_taxonomies'      => 'bool',
		'remove_feed_post_comments'   => 'bool',
	];

	/**
	 * Name of the option we're using.
	 *
	 * @var string
	 */
	public static string $option_name = 'joost_optimizations';

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
	 * Class constructor.
	 */
	public function __construct() {
		$this->load_options();
		$this->sanitize_options();
	}

	/**
	 * Loads Joost Optimizations-options set in WordPress.
	 * If already set: trim some option, otherwise load defaults.
	 */
	private function load_options(): void {
		$options = get_option( self::$option_name );

		if ( ! is_array( $options ) ) {
			$this->options = self::$option_defaults;
			update_option( self::$option_name, $this->options );
		} else {
			$this->options = array_merge( self::$option_defaults, $options );
		}
	}

	/**
	 * Forces all options to be of the type we expect them to be of.
	 */
	private function sanitize_options(): void {
		foreach ( $this->options as $key => $value ) {
			switch ( self::$option_var_types[ $key ] ) {
				case 'string':
					$this->options[ $key ] = (string) $value;
					break;
				case 'bool':
					$this->options[ $key ] = (bool) $value;
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
		}

		return self::$instance;
	}

	/**
	 * Returns the Joost Optimizations options.
	 *
	 * @return array
	 */
	public function get(): array {
		return $this->options;
	}
}
