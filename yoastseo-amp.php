<?php
/**
 * @package     YoastSEO_AMP_Glue
 * @author      Joost de Valk
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Glue for Yoast SEO & AMP
 * Plugin URI:  https://yoast.com/yoast-seo-amp-glue/
 * Description: Makes sure the default WordPress AMP plugin uses the proper Yoast SEO metadata
 * Version:     0.1
 * Author:      Joost de Valk
 * Author URI:  https://yoast.com
 */

if ( ! class_exists( 'YoastSEO_AMP' ) ) {
	/**
	 * This class improves upon the AMP output by the default WordPress AMP plugin using Yoast SEO metadata.
	 */
	class YoastSEO_AMP {

		/**
		 * YoastSEO_AMP constructor.
		 */
		public function __construct() {
			if ( is_admin() ) {
				require 'classes/class-backend.php';
				new YoastSEO_AMP_Backend();
			}
			else {
				require 'classes/class-frontend.php';
				new YoastSEO_AMP_Frontend();
			}
		}

	}
}

/**
 * Initialize the Yoast SEO AMP Glue plugin
 */
function yoast_seo_amp_glue_init() {
	if ( class_exists( 'WPSEO_Frontend' ) ) {
		new YoastSEO_AMP();
	}
}

add_action( 'init', 'yoast_seo_amp_glue_init', 9 );


