<?php
/**
 * YoastSEO_AMP_Glue plugin file.
 *
 * @package     YoastSEO_AMP_Glue\CSS_Builder
 * @author      Jip Moors
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 */

if ( ! class_exists( 'YoastSEO_AMP_CSS_Builder', false ) ) {
	/**
	 * Class to build CSS.
	 */
	class YoastSEO_AMP_CSS_Builder {

		/**
		 * Option to CSS lookup map.
		 *
		 * @var array
		 */
		private $items = [];

		/**
		 * Adds the passed option to the CSS map.
		 *
		 * @param string $option_key Option key.
		 * @param string $selector   CSS Selector.
		 * @param string $property   CSS Property that will hold the value of the option.
		 *
		 * @return void
		 */
		public function add_option( $option_key, $selector, $property ) {
			$this->items[ $option_key ] = [
				'selector' => $selector,
				'property' => $property,
			];
		}

		/**
		 * Builds the CSS, based on the passed options, to be used on the frontend.
		 *
		 * @return string The CSS to be used on the frontend.
		 */
		public function build() {
			$options = YoastSEO_AMP_Options::get();

			$output = "\n";
			$css    = [];

			$options = array_filter( $options );
			$apply   = array_intersect_key( $this->items, $options );

			if ( is_array( $apply ) ) {
				foreach ( $apply as $key => $placement ) {

					if ( ! isset( $css[ $placement['selector'] ] ) ) {
						$css[ $placement['selector'] ] = [];
					}

					$css[ $placement['selector'] ][ $placement['property'] ] = $options[ $key ];
				}
			}

			if ( ! empty( $css ) ) {
				foreach ( $css as $selector => $properties ) {

					$inner = '';
					foreach ( $properties as $property => $value ) {
						$inner .= sprintf( "%s: %s;\n", $property, $value );
					}

					$output .= sprintf( "%s {\n%s}\n", $selector, $inner );
				}
			}

			return $output;
		}
	}
}
