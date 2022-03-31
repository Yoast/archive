<?php
namespace Joost_Optimizations\Admin;

use Joost_Optimizations\Options\Options;

/**
 * Backend Class for the Joost Optimizations plugin options.
 */
class Admin_Options extends Options {

	/**
	 * The option group name.
	 *
	 * @var string
	 */
	public static $option_group = 'joost_optimizations_options';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		parent::__construct();
	}

	/**
	 * Register the needed option and its settings sections.
	 */
	public function admin_init() {
		register_setting( self::$option_group, parent::$option_name, [ $this, 'sanitize_options_on_save' ] );

		$this->register_basic_settings();
		$this->register_advanced_settings();
		$this->register_outbound_settings();
	}

	/**
	 * Register the basic settings.
	 */
	private function register_basic_settings() {
		add_settings_section(
			'basic-settings',
			__( 'Basic settings', 'joost-optimizations' ),
			[ $this, 'basic_settings_intro' ],
			'joost-optimizations'
		);

		$clicky_settings = [
			'site_id'        => __( 'Site ID', 'joost-optimizations' ),
			'site_key'       => __( 'Site Key', 'joost-optimizations' ),
			'admin_site_key' => __( 'Admin Site Key', 'joost-optimizations' ),
		];
		foreach ( $clicky_settings as $key => $label ) {
			$args = [
				'name'  => 'clicky[' . $key . ']',
				'value' => $this->options[ $key ],
			];
			add_settings_field(
				$key,
				$label,
				[ $this, 'input_text' ],
				'joost-optimizations',
				'basic-settings',
				$args
			);
		}

		add_settings_section(
			'clicky-like',
			__( 'Like this plugin?', 'joost-optimizations' ),
			[ $this, 'like_text' ],
			'joost-optimizations'
		);

		add_settings_section(
			'clicky-support',
			__( 'Need support?', 'joost-optimizations' ),
			[ $this, 'support_text' ],
			'joost-optimizations'
		);
	}

	/**
	 * Register the separate advanced settings screen.
	 */
	private function register_advanced_settings() {
		add_settings_section( 'joost-optimizations-advanced', __( 'Advanced Settings', 'joost-optimizations' ), null, 'joost-optimizations-advanced' );

		$advanced_settings = [
			'disable_stats'   => [
				'label' => __( 'Disable Admin Bar stats', 'joost-optimizations' ),
				'desc'  => __( 'If you don\'t want to display the stats in your admin menu, check this box.', 'joost-optimizations' ),
			],
			'ignore_admin'    => [
				'label' => __( 'Ignore Admin users', 'joost-optimizations' ),
				'desc'  => __( 'If you are using a caching plugin, such as W3 Total Cache or WP-Supercache, please ensure that you have it configured to NOT use the cache for logged in users. Otherwise, admin users <em>will still</em> be tracked.', 'joost-optimizations' ),
			],
			'cookies_disable' => [
				'label' => __( 'Disable cookies', 'joost-optimizations' ),
				'desc'  => __( 'If you don\'t want Joost Optimizations to use cookies on your site, check this button. By doing so, uniqueness will instead be determined based on their IP address.', 'joost-optimizations' ),
			],
			'track_names'     => [
				'label' => __( 'Track names of commenters', 'joost-optimizations' ),
			],
		];
		foreach ( $advanced_settings as $key => $arr ) {
			$args = [
				'name'  => $key,
				'value' => isset( $this->options[ $key ] ) ? $this->options[ $key ] : false,
				'desc'  => isset( $arr['desc'] ) ? $arr['desc'] : '',
			];
			add_settings_field(
				$key,
				$arr['label'],
				[ $this, 'input_checkbox' ],
				'joost-optimizations-advanced',
				'joost-optimizations-advanced',
				$args
			);
		}
	}

	/**
	 * Register the outbound links settings section.
	 */
	private function register_outbound_settings() {
		add_settings_section(
			'clicky-outbound',
			__( 'Outbound Links', 'joost-optimizations' ),
			[ $this, 'outbound_explanation' ],
			'joost-optimizations-advanced'
		);

		$args = [
			'name'  => 'clicky[outbound_pattern]',
			'value' => $this->options['outbound_pattern'],
			'desc'  => __( 'For instance: <code>/out/,/go/</code>', 'joost-optimizations' ),
		];
		add_settings_field(
			'outbound_pattern',
			__( 'Outbound Link Pattern', 'joost-optimizations' ),
			[ $this, 'input_text' ],
			'joost-optimizations-advanced',
			'clicky-outbound',
			$args
		);
	}

	/**
	 * Create a "plugin like" box.
	 */
	public function like_text() {
		require JOOST_OPTIMIZATIONS_PLUGIN_DIR_PATH . 'admin/views/like-box.php';
	}

	/**
	 * Sanitizes and trims a string.
	 *
	 * @param string $text_string String to sanitize.
	 *
	 * @return string
	 */
	private function sanitize_string( $text_string ) {
		return (string) trim( sanitize_text_field( $text_string ) );
	}

	/**
	 * Sanitize options.
	 *
	 * @param array $new_options Options to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_options_on_save( $new_options ) {
		foreach ( $new_options as $key => $value ) {
			switch ( self::$option_var_types[ $key ] ) {
				case 'string':
					$new_options[ $key ] = $this->sanitize_string( $new_options[ $key ] );
					break;
				case 'bool':
					if ( isset( $new_options[ $key ] ) ) {
						$new_options[ $key ] = true;
					}
					else {
						$new_options[ $key ] = false;
					}
					break;
			}

			switch ( $key ) {
				case 'site_id':
					$new_options[ $key ] = (int) $new_options[ $key ];
					if ( $new_options[ $key ] === 0 ) {
						$new_options[ $key ] = '';
					}
					break;

				case 'site_key':
				case 'admin_site_key':
					$new_options[ $key ] = preg_replace( '~[^a-zA-Z0-9]+~', '', $new_options[ $key ] );
					break;
			}
		}

		return $new_options;
	}

	/**
	 * Intro for the basic settings screen.
	 */
	public function basic_settings_intro() {
		echo '<p>';
		printf(
		/* translators: 1: link open tag to the Joost Optimizations user homepage; 2: link close tag. */
			esc_html__( 'Go to your %1$suser homepage on Clicky%2$s and click &quot;Preferences&quot; under the name of the domain, you will find the Site ID, Site Key, Admin Site Key and Database Server under Site information.', 'joost-optimizations' ),
			'<a href="http://clicky.com/145844">',
			'</a>'
		);
		echo '</p>';
	}

	/**
	 * Intro for the the outbound links section.
	 */
	public function outbound_explanation() {
		echo '<p>';
		printf(
		/* translators: 1: link open tag to the Joost Optimizations knowledge base article; 2: link close tag. */
			esc_html__( 'If your site uses redirects for outbound links, instead of links that point directly to their external source (this is popular with affiliate links, for example), then you\'ll need to use this variable to tell our tracking code additional patterns to look for when automatically tracking outbound links. %1$sRead more here%2$s.', 'joost-optimizations' ),
			'<a href="https://secure.getclicky.com/helpy?type=customization#outbound_pattern">',
			'</a>'
		);
		echo '</p>';
	}

	/**
	 * Text for the support box.
	 */
	public function support_text() {
		echo '<p>';
		printf(
		/* translators: 1: link open tag to Joost Optimizations forum website; 2: link close tag. */
			esc_html__( 'If you\'re in need of support with Joost Optimizations and / or this plugin, please visit the %1$sJoost Optimizations forums%2$s.', 'joost-optimizations' ),
			"<a href='https://clicky.com/forums/'>",
			'</a>'
		);
		echo '</p>';
	}

	/**
	 * Output an optional input description.
	 *
	 * @param array $args Arguments to get data from.
	 */
	private function input_desc( $args ) {
		if ( isset( $args['desc'] ) ) {
			echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
		}
	}

	/**
	 * Create a text input.
	 *
	 * @param array $args Arguments to get data from.
	 */
	public function input_text( $args ) {
		echo '<input type="text" class="text" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( $args['value'] ) . '"/>';
		$this->input_desc( $args );
	}

	/**
	 * Create a checkbox input.
	 *
	 * @param array $args Arguments to get data from.
	 */
	public function input_checkbox( $args ) {
		$option = isset( $this->options[ $args['name'] ] ) ? $this->options[ $args['name'] ] : false;
		echo '<input class="checkbox" type="checkbox" ' . checked( $option, true, false ) . ' name="clicky[' . esc_attr( $args['name'] ) . ']"/>';
		$this->input_desc( $args );
	}
}