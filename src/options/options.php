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
		'site_id'          => '',    // There is no default site ID as we don't know it...
		'site_key'         => '',    // There is no default site key as we don't know it...
		'admin_site_key'   => '',    // There is no default admin site key as we don't know it...
		'outbound_pattern' => '',    // By defaulting to an empty string here, we disable this functionality until it's set.
		'ignore_admin'     => false, // While ignoring an admin by default would make sense, it leads to admins thinking the plugin doesn't work.
		'cookies_disable'  => false, // No need to disable cookies by default as it severely impacts the quality of tracking.
		'disable_stats'    => false, // The stats on the frontend are often found useful, but some people might want to disable them.
	];

	/**
	 * Holds the type of variable that each option is, so we can cast it to that.
	 *
	 * @var string[]
	 */
	public static array $option_var_types = [
		'site_id'          => 'string',
		'site_key'         => 'string',
		'admin_site_key'   => 'string',
		'outbound_pattern' => 'string',
		'ignore_admin'     => 'bool',
		'track_names'      => 'bool',
		'cookies_disable'  => 'bool',
		'disable_stats'    => 'bool',
	];

	/**
	 * Name of the option we're using.
	 *
	 * @var string
	 */
	public static string $option_name = 'joost-optimizations';

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
		}
		else {
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