<?php
/*
Plugin Name: Yoast WordPress SEO Research
Plugin URI: https://yoast.com/seo-research/
Description: This plugin exports a maximum of 10 random posts and 5 random terms from your blog, including their SEO data, to help in Yoast's SEO research.
Version: 1.0
Author: Team Yoast
Author URI: https://yoast.com
License: GPL2 or higher
*/

/**
 * Class Yoast_Research
 */
class Yoast_Research {
	var $output = array();

	/**
	 * Yoast_Research constructor.
	 */
	public function __construct() {
		$this->set_hooks();
	}

	/**
	 * Adds an activation error notice if the user doesn't run the right version of Yoast SEO.
	 *
	 * @return void
	 */
	public function activation_error_notice() {
		if ( get_transient( 'wordpress-seo-research-activation-failed' ) ) {
			$message = __( 'It seems that you are using an outdated version of Yoast SEO. Please update to the latest version before activating this plugin.', 'wordpress-seo-research' );
			printf( "<div class='error notice'><p>%s</p></div>", $message );

			deactivate_plugins( plugin_basename( __FILE__ ) );
			delete_transient( 'wordpress-seo-research-activation-failed' );
		}
	}

	/**
	 * Sets up the necessary hooks.
	 *
	 * @return void
	 */
	public function set_hooks() {
		add_action( 'wp', array( $this, 'retrieve_data' ), 100 );
		add_action( 'admin_menu', array( $this, 'menu' ) );

		register_activation_hook( __FILE__, array( $this, 'check_compatibility' ) );
		add_action( 'admin_notices', array( $this, 'activation_error_notice' ) );
	}

	/**
	 * Determines whether or not a version of Yoast SEO Free with a minimum requirement of 5.9.3 is present.
	 *
	 * @return bool True if the user is running a version of Yoast SEO Free that is >= 5.9.3.
	 */
	public function check_compatibility() {
		if ( ! class_exists( 'WPSEO_Post_Type' ) ) {
			set_transient( 'wordpress-seo-research-activation-failed', true, 5 );
		}

		return true;
	}

	/**
	 * Registers the menu page.
	 *
	 * @return void
	 */
	public function menu() {
		add_submenu_page(
			'wpseo_dashboard',
			__( 'SEO Research', 'wordpress-seo-research' ),
			__( 'SEO Research', 'wordpress-seo-research' ),
			'manage_options',
			'wpseo_research',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Output for the menu page.
	 *
	 * @return void
	 */
	public function admin_page() {
		echo '<div class="wrap">';
		printf( '<h2>%s</h2>', __( 'Yoast SEO Research', 'wordpress-seo-research' ) );
		printf( '<p>%s</p>', __( 'Thank you for participating in our research. You\'re helping us make Yoast SEO better!', 'wordpress-seo-research' ) );
		printf( '<p>%s</p>', __( 'Click on the button. This will create a file for you, which will contain a maximum of 10 random posts and 5 random categories from your site:', 'wordpress-seo-research' ) );
		echo '<a class="button-primary" href=" ' . home_url( '?output=yoast' ) . ' ">' . __( '1. Get research data', 'wordpress-seo-research' ) . '</a>';
		printf( '<p>%s</p>', __( 'Then go to the following URL and send the file to us:', 'wordpress-seo-research' ) );
		echo '<a class="button-primary" href="https://yoast.com/">' . __( '2. Submit data to Yoast', 'wordpress-seo-research' ) . '</a>';
		echo '</div>';
	}

	/**
	 * Retrieves the actual data for download.
	 *
	 * @return void
	 */
	public function retrieve_data() {
		if ( current_user_can( 'manage_options' ) && isset( $_GET['output'] ) && $_GET['output'] == 'yoast' ) {
			ob_start();
			$this->basic_data();
			$this->get_terms();
			$this->get_posts();
			ob_end_clean();
			header( 'Content-disposition: attachment; filename=wpseo-research.json' );
			header( 'Content-Type: application/json' );
			echo wp_json_encode( $this->output );
			die;
		}
	}

	/**
	 * Gathers basic data about a site to send along.
	 *
	 * @return void
	 */
	protected function basic_data() {
		$this->output['data'] = array(
			'locale'            => get_locale(),
			'wp_version'        => get_bloginfo( 'version' ),
			'yoast_seo_version' => WPSEO_VERSION,
			'home_url'          => home_url(),
		);
	}

	/**
	 * Gets 10 posts with a focus keyword and their post data.
	 *
	 * @return void
	 */
	protected function get_posts() {
		$args = array(
			'post_type'      => WPSEO_Post_Type::get_accessible_post_types(),
			'orderby'        => 'rand',
			'posts_per_page' => 10,
			'post_status' => array( 'publish' ),
			'has_password' => false,

			'meta_query' => array(
				array(
					'key'     => WPSEO_Meta::$meta_prefix . 'focuskw',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);

		remove_action( 'wp_head', 'wpseo_head' );
		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$this->output['posts'][] = $this->build_post_data();
			}
		}
	}

	/**
	 * Builds the post data needed: content, content score, focus keyword(s), generated meta description, SEO score,
	 * generated SEO title, title, and url.
	 *
	 * @return array The post data.
	 */
	protected function build_post_data() {
		WPSEO_Frontend::get_instance()->reset();

		return array(
			'content'          => get_the_content(),
			'content_score'    => WPSEO_Meta::get_value( 'content_score' ),
			'focus_keyword'    => WPSEO_Meta::get_value( 'focuskw' ),
			'meta_description' => WPSEO_Frontend::get_instance()->metadesc( false ),
			'multiple_focus'   => WPSEO_Meta::get_value( 'focuskeywords' ),
			'score'            => WPSEO_Meta::get_value( 'linkdex' ),
			'seo_title'        => WPSEO_Frontend::get_instance()->title( null ),
			'title'            => get_the_title(),
			'url'              => get_permalink(),
		);
	}

	/**
	 * Gets the following data for 5 terms: content, content score, focus keyword, generated meta description, SEO score, title,
	 * generated SEO title, and url.
	 *
	 * @return void
	 */
	protected function get_terms() {
		$terms = get_terms( array( 'taxonomy' => 'category', 'number' => 5 ) );
		foreach ( $terms as $term ) {
			$content = term_description( $term );
			if ( empty( $content ) ) {
				continue;
			}
			$this->output['terms'][] = array(
				'content'          => $content,
				'content_score'    => WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'content_score' ),
				'focus_keyword'    => WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'focuskw' ),
				'meta_description' => WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'desc' ),
				'score'            => WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'linkdex' ),
				'title'            => $this->get_term_seo_title( $term ),
				'url'              => get_term_link( $term ),
			);
		}
	}

	/**
	 * Works around the fact that we can't feed the term properly in another way to WPSEO_Frontend.
	 *
	 * @param \WP_Term $term The term object.
	 *
	 * @return string The term's title.
	 */
	protected function get_term_seo_title( $term ) {
		$title = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'title' );

		if ( is_string( $title ) && $title !== '' ) {
			return wpseo_replace_vars( $title, $term );
		}

		return WPSEO_Frontend::get_instance()->get_title_from_options( 'title-tax-' . $term->taxonomy, $term );
	}
}

new Yoast_Research();
