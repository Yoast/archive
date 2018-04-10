<?php
/**
 * YoastSEO_AMP_Glue plugin file.
 *
 * @package     YoastSEO_AMP_Glue\Options
 * @author      Jip Moors
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 */

if ( ! class_exists( 'YoastSEO_AMP_Options' ) ) {
	/**
	 * Class to manage the YoastSEO_AMP option.
	 */
	class YoastSEO_AMP_Options {

		/**
		 * Name of the option in the database.
		 *
		 * @var string
		 */
		private $option_name = 'wpseo_amp';

		/**
		 * Current options.
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Option defaults.
		 *
		 * @var array
		 */
		private $defaults = array(
			'version'                 => 1,
			'amp_site_icon'           => '',
			'default_image'           => '',
			'header-color'            => '',
			'headings-color'          => '',
			'text-color'              => '',
			'meta-color'              => '',
			'link-color'              => '',
			'link-color-hover'        => '',
			'underline'               => 'underline',
			'blockquote-text-color'   => '',
			'blockquote-bg-color'     => '',
			'blockquote-border-color' => '',
			'extra-css'               => '',
			'extra-head'              => '',
			'analytics-extra'         => '',
		);

		/**
		 * Class instance.
		 *
		 * @var self
		 */
		private static $instance;

		/**
		 * Constructor.
		 */
		private function __construct() {
			// Register settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}

		/**
		 * Register the premium settings.
		 */
		public function register_settings() {
			register_setting( 'wpseo_amp_settings', $this->option_name, array( $this, 'sanitize_options' ) );
		}

		/**
		 * Sanitize options.
		 *
		 * @param array $options Options as received in $_POST.
		 *
		 * @return mixed
		 */
		public function sanitize_options( $options ) {
			$options['version'] = 1;

			// Sanitize extra CSS field.
			$extra_css            = strip_tags( $options['extra-css'] );
			$extra_css            = wp_check_invalid_utf8( $extra_css );
			$extra_css            = _wp_specialchars( $extra_css, ENT_NOQUOTES );
			$options['extra-css'] = $extra_css;

			// Only allow meta and link tags in head.
			$options['extra-head'] = strip_tags( $options['extra-head'], '<link><meta>' );

			$colors = array(
				'header-color',
				'headings-color',
				'text-color',
				'meta-color',
				'link-color',
				'blockquote-text-color',
				'blockquote-bg-color',
				'blockquote-border-color',
			);

			foreach ( $colors as $color ) {
				$options[ $color ] = $this->sanitize_color( $options[ $color ], '' );
			}

			// Only allow 'on' or 'off'.
			foreach ( $options as $key => $value ) {
				if ( 'post_types-' === substr( $key, 0, 11 ) ) {
					$options[ $key ] = ( $value === 'on' ) ? 'on' : 'off';
				}
			}

			$options['analytics-extra'] = $this->sanitize_analytics_code( $options['analytics-extra'] );

			return $options;
		}

		/**
		 * Sanitize hexadecimal color.
		 *
		 * @param string $color   String to test for valid color.
		 * @param string $default Value the string will get when no color is found.
		 *
		 * @return string Color or $default.
		 */
		private function sanitize_color( $color, $default ) {
			if ( preg_match( '~^#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$~', $color, $matches ) ) {
				return $matches[0];
			}

			return $default;
		}

		/**
		 * Sanitize analytics code.
		 *
		 * @param string $source Raw input.
		 *
		 * @return string Sanitized code.
		 */
		private function sanitize_analytics_code( $source ) {
			$source = trim( $source );

			if ( empty( $source ) ) {
				return '';
			}

			// If no <amp-analytics> occurs in the code, the code is invalid.
			if ( strpos( $source, '<amp-analytics ' ) === false ) {
				return '';
			}

			if ( strpos( $source, '<script type="application/json">' ) === false ) {
				return strip_tags( $source, '<amp-analytics>' );
			}

			return $this->sanitize_analytics_json( $source );
		}

		/**
		 * Get the options.
		 *
		 * @return array
		 */
		public static function get() {

			$me = self::get_instance();
			$me->fetch_options();

			return $me->options;
		}

		/**
		 * Get the singleton instance of this class.
		 *
		 * @return YoastSEO_AMP_Options
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Collect options.
		 *
		 * @SuppressWarnings("PMD.UnusedPrivateMethod")
		 */
		private function fetch_options() {
			$saved_options = $this->options;
			if ( ! is_array( $this->options ) ) {
				$saved_options = get_option( 'wpseo_amp' );

				// Apply defaults.
				$this->options = wp_parse_args( $saved_options, $this->defaults );
			}

			// Make sure all post types are present.
			$this->update_post_type_settings();

			// Save changes to database.
			if ( $this->options !== $saved_options ) {
				update_option( $this->option_name, $this->options );
			}
		}

		/**
		 * Get post types.
		 */
		private function update_post_type_settings() {
			$post_type_names = array();

			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $post_type ) {
					if ( ! isset( $this->options[ 'post_types-' . $post_type->name . '-amp' ] ) ) {
						$this->options[ 'post_types-' . $post_type->name . '-amp' ] = 'off';
						if ( 'post' === $post_type->name ) {
							$this->options[ 'post_types-' . $post_type->name . '-amp' ] = 'on';
						}
					}

					$post_type_names[] = $post_type->name;
				}
			}
		}

		/**
		 * Sanitizes an analytics string when it has JSON in it.
		 *
		 * @param string $code The code to sanitize.
		 *
		 * @return string Sanitized string.
		 */
		private function sanitize_analytics_json( $code ) {
			// Strip all tags, to verify JSON input.
			$json = strip_tags( $code );

			// Non-parsable JSON is always bad.
			if ( is_null( json_decode( $json, true ) ) ) {
				return '';
			}

			$allowed_tags = strip_tags( $code, '<amp-analytics>' );

			// Strip JSON content so we can apply verified script tag.
			$tag = str_replace( $json, '', $allowed_tags );

			$parts     = explode( '><', $tag );
			$parts[0] .= '>';
			$parts[1]  = '<' . $parts[1];

			// Rebuild with script tag and JSON content.
			array_splice(
				$parts,
				1,
				null,
				array(
					'<script type="application/json">',
					trim( $json ),
					'</script>',
				)
			);

			return implode( "\n", $parts );
		}
	}
}
