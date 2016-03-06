<?php
/**
 * @package     YoastSEO_AMP_Glue\Admin
 * @author      Joost de Valk
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 */

if ( ! class_exists( 'YoastSEO_AMP_Backend' ) ) {
	/**
	 * This class improves upon the AMP output by the default WordPress AMP plugin using Yoast SEO metadata.
	 */
	class YoastSEO_AMP_Backend {

		/**
		 * @var array
		 */
		public $options;

		/**
		 * @var string
		 */
		public $option_name = 'wpseo_amp';

		/**
		 * YoastSEO_AMP_Backend constructor.
		 */
		public function __construct() {
			$this->options = get_option( $this->option_name );

			// Register settings
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Add subitem to menu
			add_filter( 'wpseo_submenu_pages', array( $this, 'add_submenu_page' ), 10, 1 );

			// Register AMP admin page as a Yoast SEO admin page
			add_filter( 'wpseo_admin_pages', array( $this, 'add_admin_pages' ) );
		}

		/**
		 * Register the premium settings
		 */
		public function register_settings() {
			register_setting( 'wpseo_amp_settings', $this->option_name, array( $this, 'sanitize_options' ) );
		}

		/**
		 * Sanitize options
		 *
		 * @param $options
		 *
		 * @return mixed
		 */
		public function sanitize_options( $options ) {
			$options['version'] = 1;

			return $options;
		}

		/**
		 * Add submenu item
		 *
		 * @param array $sub_menu_pages
		 *
		 * @return array
		 */
		public function add_submenu_page( $sub_menu_pages ) {

			$sub_menu_pages[] = array(
				'wpseo_dashboard',
				__( 'AMP', 'wordpress-seo' ),
				__( 'AMP', 'wordpress-seo' ),
				'manage_options',
				'wpseo_amp',
				array( $this, 'display' ),
				array( array( $this, 'enqueue_admin_page' ) ),
			);

			return $sub_menu_pages;
		}

		/**
		 * Displays the admin page
		 */
		public function display() {
			$this->ensure_options_exist();

			require 'views/admin-page.php';
		}

		/**
		 * Enqueue admin page JS
		 */
		public function enqueue_admin_page() {
			wp_enqueue_style( 'yoast_amp_css', plugin_dir_url( __FILE__ ) . 'assets/amp-admin-page.css', array( 'wp-color-picker' ), false );

			wp_enqueue_media(); // enqueue files needed for upload functionality
			wp_enqueue_script( 'wpseo-admin-media', plugins_url( 'js/wp-seo-admin-media-' . '310' . WPSEO_CSSJS_SUFFIX . '.js', WPSEO_FILE ), array(
				'jquery',
				'jquery-ui-core',
			), WPSEO_VERSION, true );
			wp_localize_script( 'wpseo-admin-media', 'wpseoMediaL10n', $this->localize_media_script() );

			wp_enqueue_script( 'yoast_amp_js', plugin_dir_url( __FILE__ ) . 'assets/amp-admin-page.js', array(
				'jquery',
				'wp-color-picker'
			), false, true );
		}

		/**
		 * Pass some variables to js for upload module.
		 *
		 * @return  array
		 */
		public function localize_media_script() {
			return array(
				'choose_image' => __( 'Use Logo', 'wordpress-seo' ),
			);
		}

		/**
		 * Add admin page to admin_pages so the correct assets are loaded by WPSEO
		 *
		 * @param $admin_pages
		 *
		 * @return array
		 */
		public function add_admin_pages( $admin_pages ) {
			$admin_pages[] = 'wpseo_amp';

			return $admin_pages;
		}

		/**
		 * @param string $var
		 * @param string $label
		 */
		private function color_picker( $var, $label ) {
			echo '<label class="checkbox" for="', $var, '">', $label, '</label>';
			echo '<input type="text" name="wpseo_amp[', $var, ']"';
			if ( isset( $this->options[ $var ] ) ) {
				echo ' value="' . $this->options[ $var ] . '"';
			}
			echo ' class="yst_colorpicker" id="', $var, '"/>';
			echo '<br/>';
		}

		/**
		 * Makes sure the options for each post type exist
		 */
		private function ensure_options_exist() {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $pt ) {
					if ( ! isset( $this->options[ 'post_types-' . $pt->name . '-amp' ] ) ) {
						if ( $pt->name === 'post' ) {
							$this->options[ 'post_types-' . $pt->name . '-amp' ] = 'on';
						} else {
							$this->options[ 'post_types-' . $pt->name . '-amp' ] = 'off';
						}
					}
				}
			}
			update_option( $this->option_name, $this->options );
		}
	}

}
