<?php

namespace Joost\Optimizations\Admin;

use Joost\Optimizations\Options\Options;

/**
 * Backend Class for the Joost Optimizations plugin options.
 */
class Admin_Options extends Options {

	/**
	 * The option group name.
	 *
	 * @var string
	 */
	public static string $option_group = 'joost_optimizations_options';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	/**
	 * Register the needed option and its settings sections.
	 */
	public function admin_init(): void {
		register_setting(
			self::$option_group,
			parent::$option_name,
			[ $this, 'sanitize_options_on_save' ]
		);

		$this->register_basic_settings();
		$this->register_rss_settings();
		$this->register_gutenberg_settings();
		$this->register_advanced_settings();
	}

	/**
	 * Register the basic settings.
	 */
	private function register_gutenberg_settings(): void {
		$settings = [
			'remove_gutenberg_global_styles' => [
				'label' => __( 'Remove Gutenberg global styles', 'joost-optimizations' ),
			],
			'remove_gutenberg_block_library' => [
				'label' => __( 'Remove Gutenberg block library styles', 'joost-optimizations' ),
			],
			'remove_gutenberg_duotone'       => [
				'label' => __( 'Remove Gutenberg duotone output', 'joost-optimizations' ),
			],
		];

		$this->settings_section( 'gutenberg-settings', 'gutenberg_settings_intro', 'joost-optimizations-gutenberg', $settings );
	}

	/**
	 * Register the basic settings.
	 */
	private function register_basic_settings(): void {
		$settings = [
			'remove_shortlinks'        => [
				'label' => __( 'Remove shortlinks', 'joost-optimizations' ),
			],
			'remove_rest_api_links'    => [
				'label' => __( 'Remove REST API links', 'joost-optimizations' ),
			],
			'remove_rsd_wlw_links'     => [
				'label' => __( 'Remove RSD / WLW links', 'joost-optimizations' ),
			],
			'remove_oembed_links'      => [
				'label' => __( 'Remove oEmbed links', 'joost-optimizations' ),
			],
			'remove_generator'         => [
				'label' => __( 'Remove generator tag', 'joost-optimizations' ),
			],
			'remove_pingback_header'   => [
				'label' => __( 'Remove pingback HTTP header', 'joost-optimizations' ),
			],
			'remove_powered_by_header' => [
				'label' => __( 'Remove powered by HTTP header', 'joost-optimizations' ),
			],
			'remove_emoji_scripts'     => [
				'label' => __( 'Remove emoji scripts', 'joost-optimizations' ),
			],
		];

		$this->settings_section( 'basic-settings', 'basic_settings_intro', 'joost-optimizations', $settings );
	}

	/**
	 * Register the separate advanced settings screen.
	 */
	private function register_rss_settings(): void {
		$settings = [
			'remove_feed_global'          => [
				'label' => __( 'Remove the global feed', 'joost-optimizations' ),
			],
			'remove_feed_global_comments' => [
				'label' => __( 'Remove the global comments feed', 'joost-optimizations' ),
			],
			'remove_feed_post_types'      => [
				'label' => __( 'Remove post type feeds', 'joost-optimizations' ),
			],
			'remove_feed_taxonomies'      => [
				'label' => __( 'Remove taxonomy feeds', 'joost-optimizations' ),
			],
			'remove_feed_post_comments'   => [
				'label' => __( 'Remove post comment feeds', 'joost-optimizations' ),
			],
		];

		$this->settings_section( 'rss-settings', 'rss_settings_intro', 'joost-optimizations-rss', $settings );
	}

	/**
	 * Register the separate advanced settings screen.
	 */
	private function register_advanced_settings(): void {
		$settings = [
			'remove_scripts' => [
				'label' => __( 'Remove these scripts', 'joost-optimizations' ),
				'desc'  => __( 'Comma separate script identifiers', 'joost-optimizations' ),
				'input' => 'input_text',
			],
			'remove_styles'  => [
				'label' => __( 'Remove these styles', 'joost-optimizations' ),
				'desc'  => __( 'Comma separate style identifiers', 'joost-optimizations' ),
				'input' => 'input_text',
			],
		];

		$this->settings_section( 'advanced-settings', 'advanced_settings_intro', 'joost-optimizations-advanced', $settings );
	}

	/**
	 * Sanitizes and trims a string.
	 *
	 * @param string $text_string String to sanitize.
	 *
	 * @return string
	 */
	private function sanitize_string( string $text_string ): string {
		return (string) trim( sanitize_text_field( $text_string ) );
	}

	/**
	 * Sanitize options.
	 *
	 * @param array $new_options Options to sanitize.
	 *
	 * @return array
	 */
	public function sanitize_options_on_save( array $new_options ): array {
		foreach ( self::$option_var_types as $key => $type ) {
			switch ( $type ) {
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
		}

		return $new_options;
	}

	/**
	 * Intro for the basic settings screen.
	 */
	public function basic_settings_intro(): void {
		echo '<p class="intro">';
		esc_html_e( 'Remove links added by WordPress to the header and <head>.', 'joost-optimizations' );
		echo '</p>';
	}

	/**
	 * Intro for the RSS settings screen.
	 */
	public function rss_settings_intro(): void {
		echo '<p class="intro">';
		esc_html_e( 'Remove feed links added by WordPress that aren\'t needed for this site.', 'joost-optimizations' );
		echo '</p>';
	}

	/**
	 * Intro for the Gutenberg section.
	 */
	public function gutenberg_settings_intro(): void {
		echo '<p class="intro">';
		esc_html_e( 'Remove unwanted / unneeded Gutenberg output.', 'joost-optimizations' );
		echo '</p>';
	}

	/**
	 * Intro for the Advanced section.
	 */
	public function advanced_settings_intro(): void {
		echo '<p class="intro">';
		esc_html_e( 'Remove unwanted / unneeded scripts and styles.', 'joost-optimizations' );
		echo '</p>';
	}

	/**
	 * Output an optional input description.
	 *
	 * @param array $args Arguments to get data from.
	 */
	private function input_desc( $args ): void {
		if ( isset( $args['desc'] ) ) {
			echo '<p class="description">' . esc_html( $args['desc'] ) . '</p>';
		}
	}

	/**
	 * Create a text input.
	 *
	 * @param array $args Arguments to get data from.
	 */
	public function input_text( $args ): void {
		echo '<input type="text" class="text" name="joost_optimizations[' . esc_attr( $args['name'] ) . ']" value="' . esc_attr( $args['value'] ) . '"/>';
		$this->input_desc( $args );
	}

	/**
	 * Create a checkbox input.
	 *
	 * @param array $args Arguments to get data from.
	 */
	public function input_checkbox( $args ): void {
		$option = isset( $this->options[ $args['name'] ] ) ? $this->options[ $args['name'] ] : false;
		echo '<input class="checkbox" type="checkbox" ' . checked( $option, true, false ) . ' name="joost_optimizations[' . esc_attr( $args['name'] ) . ']"/>';
		$this->input_desc( $args );
	}

	/**
	 * Creates a setting section.
	 *
	 * @param string $section        The section identifier.
	 * @param string $intro_callback The callback function for the intro.
	 * @param string $page           The page (in our case, the tab).
	 * @param array  $settings       The settings to display.
	 *
	 * @return void
	 */
	private function settings_section( string $section, string $intro_callback, string $page, array $settings ): void {
		add_settings_section(
			$section,
			'',
			[ $this, $intro_callback ],
			$page
		);

		foreach ( $settings as $key => $arr ) {
			$field_type = ( $arr['input'] ?? 'input_checkbox' );
			$args       = [
				'name'  => $key,
				'value' => ( $this->options[ $key ] ?? false ),
				'desc'  => ( $arr['desc'] ?? '' ),
			];
			add_settings_field(
				$key,
				$arr['label'],
				[ $this, $field_type ],
				$page,
				$section,
				$args
			);
		}
	}
}
