<?php

/**
 * Interface iYoast_Theme
 */
interface iYoast_Theme {
	public function setup_theme();
	public function comment_callback( $comment, $args, $depth );
}

/**
 * Class Yoast_Theme
 */
abstract class Yoast_Theme implements iYoast_Theme {

	private $name;
	private $url;
	private $version;

	private $breadcrumb;

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

		// Setup a Yoast Breadcrumb
		$this->breadcrumb = new Yoast_Breadcrumb();

		// Hook setup layout on send_headers
		add_action( 'send_headers', array( $this, 'setup_layout' ) );

		// Alter theme stylesheet uri
		add_filter( 'stylesheet_uri', array( $this, 'alter_stylesheet_uri' ), 10, 2 );

		// Load form CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_form_styles' ), 25 );

		// Enqueue SIDR
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_sidr' ) );

		// Activate SIDR
		add_action( 'wp_footer', array( $this, 'activate_sidr_and_sticky_menu' ) );

		// Add Read More Link to Excerpts
		add_filter( 'excerpt_more', array( $this, 'read_more_link' ) );
		add_filter( 'the_content_more_link', array( $this, 'read_more_link' ) );

		// Footer creds
		add_filter( 'genesis_footer_creds_text', array( $this, 'footer_creds_text' ) );

		// Override Genesis settings with theme mod settings
		add_filter( 'genesis_pre_get_option_image_size', array( $this, 'override_content_thumbnail_setting' ) );
		add_filter( 'genesis_pre_get_option_content_archive', array( $this, 'override_content_archive_setting' ) );
		add_filter( 'genesis_pre_get_option_content_archive_thumbnail', array( $this, 'override_content_archive_thumbnail' ) );
		add_filter( 'genesis_pre_get_option_posts_nav', array( $this, 'override_posts_nav' ) );

		// Modify contact methods
		add_filter( 'user_contactmethods', array( $this, 'modify_contact_methods' ) );

		// Customize the post meta function, only show categories and tags on single()
		add_filter( 'genesis_post_meta', array( $this, 'post_meta_filter' ) );

		// Change search input placeholder
		add_filter( 'genesis_search_text', array( $this, 'change_search_placeholder' ) );

		// By default, Genesis lack a space after the raquo and laquo, this adds it.
		add_filter( 'genesis_next_link_text', array( $this, 'add_spacing_next_prev' ) );
		add_filter( 'genesis_prev_link_text', array( $this, 'add_spacing_next_prev' ) );

		// Remove the default genesis site description
		remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

		// Change the default post comment submit label
		add_filter( 'comment_form_defaults', array( $this, 'change_comment_form_submit_button_text' ) );

		// Integration between Genesis and theme customizer
		add_filter( 'genesis_pre_get_option_site_layout', array( $this, 'get_site_layout_from_theme_mod' ) );
		add_action( 'genesis_admin_before_metaboxes', array( $this, 'remove_genesis_settings_boxes' ) );
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

		// Change the comment handling function
		add_filter( 'genesis_comment_list_args', array( $this, 'comment_list_args' ) );
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
	 * Comment List Arguments, modify to change the callback function
	 *
	 * @todo move this method to Yoast_Theme and add comment_callback to interface so each theme should have it's own comment implementation
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function comment_list_args( $args ) {
		$args['callback'] = array( $this, 'comment_callback' );

		return $args;
	}

	/**
	 * Echo a div closer
	 */
	public function close_div() {
		echo '</div>';
	}

	/**
	 * Fake Genesis into thinking we support a custom header
	 */
	public function fake_genesis_custom_header() {
		global $pagenow;
		if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'genesis' == $_GET['page'] ) {
			add_theme_support( 'custom-header' );
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
	 * Change the stylesheet uri so we can load the correct color scheme
	 *
	 * @param $stylesheet_uri
	 * @param $stylesheet_dir_uri
	 *
	 * @return string
	 */
	public function alter_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
		$color_scheme = get_theme_mod( 'yst_colour_scheme', 'BrightBlue' );

		return $stylesheet_dir_uri . '/assets/css/' . $color_scheme . '.css';
	}

	/**
	 * Seperate callback because of low priority loading of stylesheet.
	 *
	 * @todo: if-statement is always true because contactform 7 always loads their css. This breaks ours.
	 */
	public function enqueue_form_styles() {
		if ( wp_style_is( 'gforms_browsers_css', $list = 'enqueued' ) || wp_style_is( 'contact-form-7', $list = 'enqueued' ) ) {
			wp_enqueue_style( 'yst-form-style', get_stylesheet_directory_uri() . '/assets/css/forms.css' );
		}
	}

	/**
	 * Enqueue SIDR
	 *
	 * @link http://www.berriart.com/sidr/#documentation
	 */
	public function enqueue_sidr() {
		wp_enqueue_script( 'yst_sidr', get_stylesheet_directory_uri() . '/lib/js/jquery.sidr.js', array( 'jquery' ), false, true );
	}

	/**
	 * Activates the sidr functionality, allowing for a left hand menu block.
	 *
	 * sidr takes the HTML from the elements referenced in source and puts them in the left hand menu.
	 *
	 * @link http://www.berriart.com/sidr/#documentation
	 *
	 * @todo check whether yPos in sticky menu code is the right position to switch.
	 */
	public function activate_sidr_and_sticky_menu() {
		$yst_nav_pos = get_theme_mod( 'yst_nav_positioner' );
		if ( $yst_nav_pos == 'top' ) {
			$yst_Ypos = 10;
		} else {
			$yst_Ypos = 178;
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('#sidr-left').sidr({
					name       : 'sidr-menu-left',
					source     : function () {
						var menu = "<h1><?php _e( "Navigation", "yoast-theme" ); ?></h1>";
						if ($('.menu-primary').length > 0) {
							menu += "<ul>" + $('.menu-primary').html() + "</ul>";
						} else if ($('.nav-header').length > 0) {
							menu += "<ul>" + $('.nav-header ul').html() + "</ul>";
						}
						if ($('.widget_categories').length > 0) {
							menu += '<h1>' + $('.widget_categories .widgettitle').html() + '</h1><ul>';
							menu += $('.widget_categories ul').html();
							menu += '</ul>';
						}
						if ($('.widget_recent_entries').length > 0) {
							menu += '<h1>' + $('.widget_recent_entries .widgettitle').html() + '</h1><ul>';
							menu += $('.widget_recent_entries ul').html();
							menu += '</ul>';
						}
						return menu;
					},
					coverScreen: true
				});
				$('#sidr-right').sidr({
					name       : 'sidr-menu-right',
					source     : function () {
						return "<h1><?php echo sprintf( __("Search %s","yoast-theme"), get_bloginfo('name') ); ?></h1>" + '<?php echo get_search_form(); ?>';
					},
					coverScreen: true,
					side       : 'right'
				});
				$(window).scroll(function () {
					var yPos = ( $(window).scrollTop() );
					if (yPos > <?php echo $yst_Ypos; ?>) {
						$("body").addClass("sticky-menu");
					} else {
						$("body").removeClass("sticky-menu");
					}
				});
			});
		</script>
	<?php
	}

	/**
	 *
	 * @todo make sure this has a filter.
	 *
	 * @return string
	 */
	public function read_more_link() {
		return '&hellip; <div class="excerpt_readmore"><a href="' . get_permalink() . '">' . __( 'Read more', 'yoast-theme' ) . '</a></div>';
	}

	/**
	 * Replace the Genesis footer creds text with our own template.
	 *
	 * @param string $footer_creds_text
	 *
	 * @return string
	 */
	public function footer_creds_text( $footer_creds_text ) {
		$yst_footer = get_theme_mod( 'yst_footer' );
		if ( ! $yst_footer || empty( $yst_footer ) ) {
			return $footer_creds_text;
		}

		return $yst_footer;
	}

	/**
	 * Override the image size for full-width designs, user settings are now completely ignored.
	 *
	 * @param null|string $size
	 *
	 * @return null|string
	 */
	public function override_content_thumbnail_setting( $size = null ) {
		if ( false !== strpos( genesis_site_layout(), 'full-width' ) ) {
			return 'fullwidth-thumb';
		}

		return $size;
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_content_archive_setting( $value = null ) {
		$settings_helper = new Yoast_Settings_Helper();

		return $settings_helper->override_setting( 'yst_content_archive', $value );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_content_archive_thumbnail( $value = null ) {
		if ( false !== strpos( genesis_site_layout(), 'full-width' ) ) {
			return false;
		}

		$settings_helper = new Yoast_Settings_Helper();

		return $settings_helper->override_setting( 'yst_content_archive_thumbnail', $value, true );
	}

	/**
	 * Retrieve the posts_nav setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_posts_nav( $value = null ) {
		$settings_helper = new Yoast_Settings_Helper();

		return $settings_helper->override_setting( 'yst_posts_nav', $value );
	}

	/**
	 * Add Pinterest en Linkedin to user profile
	 *
	 * @param $profile_fields
	 *
	 * @return mixed
	 */
	public function modify_contact_methods( $profile_fields ) {

		// Add new fields
		$profile_fields['pinterest'] = __( 'Pinterest profile URL', 'yoast-theme' );
		$profile_fields['linkedin']  = __( 'LinkedIn profile URL', 'yoast-theme' );

		return $profile_fields;
	}

	/**
	 * Customize the post meta function, only show categories and tags on single()
	 *
	 * @param string $post_meta Contains the current value of post meta data
	 *
	 * @return string Returns new post meta data
	 */
	public function post_meta_filter( $post_meta ) {
		if ( is_single() ) {
			$post_meta = '[post_categories before="Filed Under: "] [post_tags before="Tagged: "]';

			return $post_meta;
		}
	}

	/**
	 * Set correct search input placeholder
	 *
	 * @return string
	 */
	public function change_search_placeholder() {
		return __( 'Search', 'yoast-theme' ) . '&#x02026;';
	}

	/**
	 * By default, Genesis lack a space after the raquo and laquo, this adds it.
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	public function add_spacing_next_prev( $link ) {
		$link = str_replace( '&#x000BB;', ' &#x000BB;', $link );
		$link = str_replace( '&#x000AB;', '&#x000AB; ', $link );

		return $link;
	}

	/**
	 * Change the default post comment submit label
	 *
	 * @param $defaults
	 *
	 * @return mixed
	 */
	public function change_comment_form_submit_button_text( $defaults ) {
		$defaults['label_submit'] = __( 'Post Comment', 'yoast-theme' ) . ' »';
		return $defaults;
	}

	/**
	 * Remove the Genesis layout settings and nav metaboxes
	 */
	public function remove_genesis_settings_boxes() {
		global $wp_meta_boxes;
		unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-layout'] );
		unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-nav'] );
		unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-posts'] );
		unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-breadcrumb'] );
	}

	/**
	 * Set the correct site layout
	 *
	 * @return string
	 */
	public function get_site_layout_from_theme_mod() {
		return get_theme_mod( 'yst_default_layout' );
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
	 * Set the theme name
	 *
	 * @param $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
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
	 * Set the theme URL
	 *
	 * @param $url
	 */
	public function set_url( $url ) {
		$this->url = $url;
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
	 * Set the theme version
	 *
	 * @param $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
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