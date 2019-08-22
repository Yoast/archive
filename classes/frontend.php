<?php
/**
 * YoastSEO_AMP_Glue plugin file.
 *
 * @package     YoastSEO_AMP_Glue\Frontend
 * @author      Joost de Valk
 * @copyright   2016 Yoast BV
 * @license     GPL-2.0+
 */

if ( ! class_exists( 'YoastSEO_AMP_Frontend' ) ) {
	/**
	 * This class improves upon the AMP output by the default WordPress AMP plugin using Yoast SEO metadata.
	 */
	class YoastSEO_AMP_Frontend {

		/**
		 * WPSEO_Frontend singleton instance.
		 *
		 * @var WPSEO_Frontend
		 */
		private $front;

		/**
		 * YoastSEO_AMP_Glue options.
		 *
		 * @var array
		 */
		private $options;

		/**
		 * All WPSEO options.
		 *
		 * @var array
		 */
		private $wpseo_options;

		/**
		 * YoastSEO_AMP_Frontend constructor.
		 */
		public function __construct() {
			$this->set_options();

			add_action( 'amp_init', array( $this, 'post_types' ) );

			add_action( 'amp_post_template_css', array( $this, 'additional_css' ) );
			add_action( 'amp_post_template_head', array( $this, 'extra_head' ) );
			add_action( 'amp_post_template_footer', array( $this, 'extra_footer' ) );

			add_filter( 'amp_post_template_data', array( $this, 'fix_amp_post_data' ) );
			add_filter( 'amp_post_template_metadata', array( $this, 'fix_amp_post_metadata' ), 10, 2 );
			add_filter( 'amp_post_template_analytics', array( $this, 'analytics' ) );

			add_filter( 'amp_content_sanitizers', array( $this, 'add_sanitizer' ) );
		}

		/**
		 * Retrieve the plugin options and set the relevant properties.
		 *
		 * @return void
		 */
		private function set_options() {
			$this->wpseo_options = WPSEO_Options::get_all();
			$this->options       = YoastSEO_AMP_Options::get();
		}

		/**
		 * Adds the blacklist sanitizer to the array of available sanitizers.
		 *
		 * @param array $sanitizers The current list of sanitizers.
		 *
		 * @return array The new array of sanitizers.
		 */
		public function add_sanitizer( $sanitizers ) {
			require_once 'blacklist-sanitizer.php';

			$sanitizers['Yoast_AMP_Blacklist_Sanitizer'] = array();

			return $sanitizers;
		}

		/**
		 * Outputs the analytics tracking, if it has been set.
		 *
		 * @param array $analytics The available analytics options.
		 *
		 * @return array The analytics tracking code to output.
		 */
		public function analytics( $analytics ) {
			// If Monster Insights is outputting analytics, don't do anything.
			if ( ! empty( $analytics['monsterinsights-googleanalytics'] ) ) {
				// Clear analytics-extra options because Monster Insights is taking care of everything.
				$this->options['analytics-extra'] = '';

				return $analytics;
			}

			if ( ! empty( $this->options['analytics-extra'] ) ) {
				return $analytics;
			}

			if ( ! class_exists( 'Yoast_GA_Options' ) || Yoast_GA_Options::instance()->get_tracking_code() === null ) {
				return $analytics;
			}
			$tracking_code = Yoast_GA_Options::instance()->get_tracking_code();

			$analytics['yst-googleanalytics'] = array(
				'type'        => 'googleanalytics',
				'attributes'  => array(),
				'config_data' => array(
					'vars'     => array(
						'account' => $tracking_code,
					),
					'triggers' => array(
						'trackPageview' => array(
							'on'      => 'visible',
							'request' => 'pageview',
						),
					),
				),
			);

			return $analytics;
		}

		/**
		 * Enables AMP for all the post types we want it for.
		 *
		 * @return void
		 */
		public function post_types() {
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $post_type ) {

					$post_type_name = $post_type->name;

					if ( ! isset( $this->options[ 'post_types-' . $post_type_name . '-amp' ] ) ) {
						continue;
					}

					// If AMP page support is not present, don't allow enabling it here.
					if ( 'page' === $post_type_name && ! post_type_supports( 'page', AMP_QUERY_VAR ) ) {
						continue;
					}

					if ( $this->options[ 'post_types-' . $post_type_name . '-amp' ] === 'on' ) {
						add_post_type_support( $post_type_name, AMP_QUERY_VAR );
						continue;
					}

					if ( 'post' === $post_type_name ) {
						add_action( 'wp', array( $this, 'disable_amp_for_posts' ) );
						continue;
					}

					remove_post_type_support( $post_type_name, AMP_QUERY_VAR );
				}
			}
		}

		/**
		 * Disables AMP for posts specifically.
		 *
		 * {@internal Runs later because of AMP plugin internals.}
		 *
		 * @return void
		 */
		public function disable_amp_for_posts() {
			remove_post_type_support( 'post', AMP_QUERY_VAR );
		}

		/**
		 * Transforms the site's canonical URL and site icon URL and to be AMP compliant.
		 *
		 * Also ensures that the proper analytics script is loaded (if applicable).
		 *
		 * @param array $data The current post data.
		 *
		 * @return array The transformed post data.
		 */
		public function fix_amp_post_data( $data ) {
			if ( ! $this->front ) {
				$this->front = WPSEO_Frontend::get_instance();
			}

			$data['canonical_url'] = $this->front->canonical( false );

			if ( ! empty( $this->options['amp_site_icon'] ) ) {
				$data['site_icon_url'] = $this->options['amp_site_icon'];
			}

			// If we are loading extra analytics, we need to load the module too.
			if ( ! empty( $this->options['analytics-extra'] ) ) {
				$data['amp_component_scripts']['amp-analytics'] = 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js';
			}

			return $data;
		}

		/**
		 * Transforms the site's organization object, site description and post image to be AMP compliant.
		 *
		 * @param array   $metadata The meta data to transform.
		 * @param WP_Post $post     The post to transform the meta data for.
		 *
		 * @return array The transformed post meta data.
		 */
		public function fix_amp_post_metadata( $metadata, $post ) {
			if ( ! $this->front ) {
				$this->front = WPSEO_Frontend::get_instance();
			}

			$this->build_organization_object( $metadata );

			$desc = $this->front->metadesc( false );
			if ( $desc ) {
				$metadata['description'] = $desc;
			}

			$image = isset( $metadata['image'] ) ? $metadata['image'] : null;

			$metadata['image'] = $this->get_image( $post, $image );
			$metadata['@type'] = $this->get_post_schema_type( $post );

			return $metadata;
		}

		/**
		 * Adds additional CSS to the AMP output.
		 *
		 * @return void
		 */
		public function additional_css() {
			require 'views/additional-css.php';

			$selectors = $this->get_class_selectors();

			$css_builder = new YoastSEO_AMP_CSS_Builder();
			$css_builder->add_option( 'header-color', $selectors['header-color'], 'background' );
			$css_builder->add_option( 'headings-color', $selectors['headings-color'], 'color' );
			$css_builder->add_option( 'text-color', $selectors['text-color'], 'color' );

			$css_builder->add_option( 'blockquote-bg-color', $selectors['blockquote-bg-color'], 'background-color' );
			$css_builder->add_option( 'blockquote-border-color', $selectors['blockquote-border-color'], 'border-color' );
			$css_builder->add_option( 'blockquote-text-color', $selectors['blockquote-text-color'], 'color' );

			$css_builder->add_option( 'link-color', $selectors['link-color'], 'color' );
			$css_builder->add_option( 'link-color-hover', $selectors['link-color-hover'], 'color' );

			$css_builder->add_option( 'meta-color', $selectors['meta-color'], 'color' );

			echo $css_builder->build();

			if ( ! empty( $this->options['extra-css'] ) ) {
				$safe_text = strip_tags( $this->options['extra-css'] );
				$safe_text = wp_check_invalid_utf8( $safe_text );
				$safe_text = _wp_specialchars( $safe_text, ENT_NOQUOTES );
				echo $safe_text;
			}
		}

		/**
		 * Outputs extra code in the head, if set.
		 *
		 * @return void
		 */
		public function extra_head() {
			$options = WPSEO_Options::get_option( 'wpseo_social' );

			if ( $options['twitter'] === true ) {
				WPSEO_Twitter::get_instance();
			}

			if ( $options['opengraph'] === true ) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- WPSEO global var.
				$GLOBALS['wpseo_og'] = new WPSEO_OpenGraph();
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WPSEO hook.
			do_action( 'wpseo_opengraph' );

			echo strip_tags( $this->options['extra-head'], '<link><meta>' );
		}

		/**
		 * Outputs analytics code in the footer, if set.
		 *
		 * @return void
		 */
		public function extra_footer() {
			echo $this->options['analytics-extra'];
		}

		/**
		 * Builds the organization object if needed.
		 *
		 * @param array $metadata The data to base the organization object on.
		 *
		 * @return void
		 */
		private function build_organization_object( &$metadata ) {
			// While it's using the blog name, it's actually outputting the company name.
			if ( ! empty( $this->wpseo_options['company_name'] ) ) {
				$metadata['publisher']['name'] = $this->wpseo_options['company_name'];
			}

			// The logo needs to be 600px wide max, 60px high max.
			$logo = $this->get_image_object( $this->wpseo_options['company_logo'], array( 600, 60 ) );
			if ( is_array( $logo ) ) {
				$metadata['publisher']['logo'] = $logo;
			}
		}

		/**
		 * Builds an image object array from an image URL.
		 *
		 * @param string       $image_url Image URL to build URL for.
		 * @param string|array $size      Optional. Image size. Accepts any valid image size, or an array of width
		 *                                and height values in pixels (in that order). Default 'full'.
		 *
		 * @return array|false The image object array or false if the image URL is empty.
		 */
		private function get_image_object( $image_url, $size = 'full' ) {
			if ( empty( $image_url ) ) {
				return false;
			}

			$image_id  = attachment_url_to_postid( $image_url );
			$image_src = wp_get_attachment_image_src( $image_id, $size );

			if ( is_array( $image_src ) ) {
				return array(
					'@type'  => 'ImageObject',
					'url'    => $image_src[0],
					'width'  => $image_src[1],
					'height' => $image_src[2],
				);
			}

			return false;
		}

		/**
		 * Retrieves the Schema.org image for the passed post.
		 *
		 * If an OpenGraph image is available for the post, that one will be used. Otherwise, the default image is used.
		 * If neither exist, the passed image is used instead.
		 *
		 * @param WP_Post                            $post  The post to retrieve the image for.
		 * @param string|string[]|array|array[]|null $image The currently set post image(s). Can be either a URL string,
		 *                                                  an array of URL strings, an array as a single ImageObject,
		 *                                                  or an array of multiple ImageObject arrays. Null if none set.
		 *
		 * @return string|string[]|array|array[]|null The Schema.org-compliant image for the post.
		 */
		private function get_image( $post, $image ) {
			$og_image = $this->get_image_object( WPSEO_Meta::get_value( 'opengraph-image', $post->ID ) );
			if ( is_array( $og_image ) ) {
				return $og_image;
			}

			// Posts without an image fail validation in Google, leading to Search Console errors.
			if ( empty( $image ) && ! empty( $this->options['default_image'] ) ) {
				$default_image = $this->get_image_object( $this->options['default_image'] );
				if ( is_array( $default_image ) ) {
					return $default_image;
				}
			}

			return $image;
		}

		/**
		 * Gets the Schema.org type for the post, based on the post type.
		 *
		 * @param WP_Post $post The post to retrieve the data for.
		 *
		 * @return string The Schema.org type.
		 */
		private function get_post_schema_type( $post ) {
			$type = 'WebPage';
			if ( 'post' === $post->post_type ) {
				$type = 'Article';
			}

			/**
			 * Filter: 'yoastseo_amp_schema_type' - Allow changing the Schema.org type for the post.
			 *
			 * @api string $type The Schema.org type for the $post.
			 *
			 * @param WP_Post $post
			 */
			$type = apply_filters( 'yoastseo_amp_schema_type', $type, $post );

			return $type;
		}

		/**
		 * Gets the class names used by the AMP plugin.
		 *
		 * The AMP plugin changed the class names for a number of selectors between releases.
		 * This method makes sure the correct CSS class name is used depending on the used version of the AMP plugin.
		 *
		 * @return array The version dependent class names.
		 */
		private function get_class_selectors() {
			$selectors = array(
				'header-color'            => 'nav.amp-wp-title-bar',
				'headings-color'          => '.amp-wp-title, h2, h3, h4',
				'text-color'              => '.amp-wp-content',

				'blockquote-bg-color'     => '.amp-wp-content blockquote',
				'blockquote-border-color' => '.amp-wp-content blockquote',
				'blockquote-text-color'   => '.amp-wp-content blockquote',

				'link-color'              => 'a, a:active, a:visited',
				'link-color-hover'        => 'a:hover, a:focus',

				'meta-color'              => '.amp-wp-meta li, .amp-wp-meta li a',
			);

			// CSS classnames have been changed in version 0.4.0.
			if ( version_compare( AMP__VERSION, '0.4.0', '>=' ) ) {
				$selectors_v4 = array(
					'header-color'            => 'header.amp-wp-header, html',
					'text-color'              => 'div.amp-wp-article',
					'blockquote-bg-color'     => '.amp-wp-article-content blockquote',
					'blockquote-border-color' => '.amp-wp-article-content blockquote',
					'blockquote-text-color'   => '.amp-wp-article-content blockquote',
					'meta-color'              => '.amp-wp-meta, .amp-wp-meta a',
				);
				$selectors    = array_merge( $selectors, $selectors_v4 );
			}

			return $selectors;
		}
	}
}
