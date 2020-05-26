<?php
/**
 * YoastSEO_AMP_Glue plugin.
 *
 * @package     YoastSEO_AMP_Glue
 * @author      Joost de Valk
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Glue for Yoast SEO & AMP
 * Plugin URI:  https://wordpress.org/plugins/glue-for-yoast-seo-amp/
 * Description: Makes sure the default WordPress AMP plugin uses the proper Yoast SEO metadata
 * Version:     0.7
 * Author:      Joost de Valk
 * Author URI:  https://yoast.com
 * Text Domain: yoastseo-amp
 * Domain Path: /languages/
 * Depends:     Yoast SEO, AMP for WordPress
 */

if ( ! class_exists( 'YoastSEO_AMP', false ) ) {
	/**
	 * This class improves upon the AMP output by the default WordPress AMP plugin using Yoast SEO metadata.
	 */
	class YoastSEO_AMP {

		const VERSION = '0.7';

		/**
		 * YoastSEO_AMP constructor.
		 */
		public function __construct() {
			add_action( 'admin_notices', [ $this, 'sunset_notification' ] );
		}

		/**
		 * Outputs a WordPress admin notice.
		 *
		 * @return void
		 */
		public function sunset_notification() {
			global $pagenow;

			if ( in_array( $pagenow, [ 'plugins.php', 'plugin-install.php', 'update-core.php' ], true ) ) {
				printf(
					'<div class="notice notice-info"><p>%1$s</p></div>',
					sprintf(
						/* translators: 1: Expands to "Yoast SEO AMP". */
						esc_html__( 'The %1$s plugin is no longer needed. Through good collaboration with Google the functionality of this plugin is now part of both Yoast SEO and the official AMP plugin. If you still have this plugin running we’d suggest updating both the Yoast SEO and AMP plugins and removing the glue plugin.', 'yoastseo-amp' ),
						'Yoast SEO AMP'
					)
				);
			}
		}
	}

	new YoastSEO_AMP();
}
