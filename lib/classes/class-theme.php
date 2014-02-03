<?php

/**
 * Interface iYoast_Theme
 */
interface iYoast_Theme {
	public function setup_theme();
}

/**
 * Class Yoast_Theme
 */
abstract class Yoast_Theme implements iYoast_Theme {

	private $name;
	private $url;
	private $version;

	/**
	 * Constructor
	 */
	public function __construct() {

		// Setup autoloader
		spl_autoload_register( array( $this, 'autoload' ) );

		// Load widgets
		$this->load_widgets();

		// Setup theme basic settings
		$this->setup_theme_basic();

		// Setup the current loaded theme
		$this->setup_theme();

		// Load customizer
		$this->load_theme_customizer();

		// Load editor style
		$this->load_editor_style();

		// Hook setup layout on send_headers
		add_action( 'send_headers', array( $this, 'setup_layout' ) );
	}

	/**
	 * Autoloader
	 *
	 * @param String $class
	 */
	public function autoload( $class ) {
		if ( 0 === strpos( $class, 'Yoast_' ) ) {

			// Format file name
			$file_name = 'class-' . strtolower( str_ireplace( '_', '-', str_ireplace( 'Yoast_', '', $class ) ) ) . '.php';

			// Full file path
			$full_path = get_stylesheet_directory() . '/lib/classes/' . $file_name;

			// Load file
			if ( file_exists( $full_path ) ) {
				require_once( $full_path );
			}

		}
	}

	/**
	 * Setup the basic theme settings
	 */
	private function setup_theme_basic() {

		// Add HTML5 markup structure
		add_theme_support( 'html5' );

		// Create menu
		add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'genesis' ) ) );

		// Add viewport meta tag for mobile browsers
		add_theme_support( 'genesis-responsive-viewport' );

	}

	/**
	 * Load the widgets
	 *
	 * @todo rewrite the way widgets load
	 */
	private function load_widgets() {
		foreach ( glob( get_stylesheet_directory() . "/lib/widgets/*-widget.php" ) as $file ) {
			require_once( $file );
		}
	}

	/**
	 * Load the theme customizer
	 */
	private function load_theme_customizer() {
		if ( is_admin() ) {
			require_once( get_stylesheet_directory() . '/lib/functions/theme-customizer.php' );
		}
	}

	/**
	 * Load editor style
	 */
	private function load_editor_style() {
		if ( is_admin() ) {
			add_editor_style( 'assets/css/editor-style.css' );
		}
	}

	/**
	 * Setup the layout
	 */
	public function setup_layout() {
		// Format layout class name
		$class_name = 'Yoast_' . str_ireplace( ' ', '_', ucwords( str_ireplace( '-', ' ', genesis_site_layout() ) ) );

		// Create an instance of chosen layout
		if ( class_exists( $class_name ) ) {
			new $class_name();
		}
	}

	/**
	 * Set the content width
	 * Hopefully one day in a more clean way (https://core.trac.wordpress.org/ticket/21256).
	 *
	 * @param $new_width
	 */
	public function set_content_width( $new_width ) {
		global $content_width;
		$content_width = $new_width;
	}

	/**
	 * Get the theme name
	 *
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the theme URL
	 *
	 * @return mixed
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Get the theme version
	 *
	 * @return mixed
	 */
	public function get_version() {
		return $this->version;
	}

}