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
		parent::__construct();

		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	/**
	 * Register the needed option and its settings sections.
	 */
	public function admin_init() {
		register_setting(
			self::$option_group,
			parent::$option_name,
			[ $this, 'sanitize_options_on_save' ]
		);

		$this->register_basic_settings();
		$this->register_rss_settings();
	}

	/**
	 * Register the basic settings.
	 */
	private function register_basic_settings() {
		add_settings_section(
			'basic-settings',
			'',
			[ $this, 'basic_settings_intro' ],
			'joost-optimizations'
		);

		$settings = [
			'remove_shortlinks'     => [
				'label' => __( 'Remove shortlinks', 'joost-optimizations' ),
			],
			'remove_rest_api_links' => [
				'label' => __( 'Remove REST API links', 'joost-optimizations' ),
			],
			'remove_rsd_wlw_links'  => [
				'label' => __( 'Remove RSD / WLW links', 'joost-optimizations' ),
			],
			'remove_oembed_links'   => [
				'label' => __( 'Remove oEmbed links', 'joost-optimizations' )
			],
			'remove_emoji_scripts'  => [
				'label' => __( 'Remove emoji scripts', 'joost-optimizations' )
			],
		];
		foreach ( $settings as $key => $arr ) {
			$args = [
				'name'  => $key,
				'value' => $this->options[ $key ] ?? false,
				'desc'  => $arr['desc'] ?? '',
			];
			add_settings_field(
				$key,
				$arr['label'],
				[ $this, 'input_checkbox' ],
				'joost-optimizations',
				'basic-settings',
				$args
			);
		}
	}

	/**
	 * Register the separate advanced settings screen.
	 */
	private function register_rss_settings() {
		add_settings_section(
			'joost-optimizations-rss',
			'',
			[ $this, 'rss_settings_intro' ],
			'joost-optimizations-rss'
		);

		$rss_settings = [
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
		foreach ( $rss_settings as $key => $arr ) {
			$args = [
				'name'  => $key,
				'value' => $this->options[ $key ] ?? false,
				'desc'  => $arr['desc'] ?? '',
			];
			add_settings_field(
				$key,
				$arr['label'],
				[ $this, 'input_checkbox' ],
				'joost-optimizations-rss',
				'joost-optimizations-rss',
				$args
			);
		}
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
	public function sanitize_options_on_save( array $new_options ): array {
//		echo '<pre>', print_r( $_POST, 1 ), '</pre>';
		foreach ( self::$option_var_types as $key => $type ) {
			switch ( $type ) {
				case 'string':
					$new_options[ $key ] = $this->sanitize_string( $new_options[ $key ] );
					break;
				case 'bool':
					if ( isset( $new_options[ $key ] ) ) {
						$new_options[ $key ] = true;
					} else {
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
	public function basic_settings_intro() {
		echo '<p class="intro">';
		esc_html_e( 'Remove links added by WordPress to the header and <head>.', 'joost-optimizations' );
		echo '</p>';
	}

	/**
	 * Intro for the basic settings screen.
	 */
	public function rss_settings_intro() {
		echo '<p class="intro">';
		esc_html_e( 'Remove feed links added by WordPress that aren\'t needed for this site.', 'joost-optimizations' );
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
		echo '<input class="checkbox" type="checkbox" ' . checked( $option, true, false ) . ' name="joost_optimizations[' . esc_attr( $args['name'] ) . ']"/>';
		$this->input_desc( $args );
	}
}
