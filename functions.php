<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

/** Load widgets from /lib/widgets/ */
foreach ( glob( CHILD_DIR . "/lib/widgets/*-widget.php" ) as $file ) {
	require_once $file;
}

//include_once( get_theme_root_uri() . '/theme001/lib/functions/yst-colourscheme-settings.php' );
require_once( 'lib/functions/yst-colourscheme-settings.php' );

// Load the right CSS
function yst_load_css_from_setting() {
	wp_enqueue_style( 'yst_custom_css', get_theme_root_uri() . "/theme001" . genesis_get_option( 'yst_colourscheme' ), array( 'google-font-quattrocento_sans', 'admin-bar', 'theme001' ) );
}

add_action( 'wp_enqueue_scripts', 'yst_load_css_from_setting' );

function enqueue_styles_basic() {
	wp_enqueue_style( 'style-basic', get_stylesheet_directory_uri() . '/assets/css/style.css' );
}

add_action( 'wp_enqueue_scripts', 'enqueue_styles_basic' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Theme001' );
define( 'CHILD_THEME_URL', 'http://yoast.com/' );
define( 'CHILD_THEME_VERSION', '0.0.1' );

//* Enqueue Google font
function yst_add_google_fonts() {
	wp_enqueue_style( 'google-font-quattrocento_sans', '//fonts.googleapis.com/css?family=Quattrocento+Sans:400,400italic,700,700italic);', array(), CHILD_THEME_VERSION );
}

add_action( 'wp_enqueue_scripts', 'yst_add_google_fonts' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 4 );

// Used for defaults in for instance the banner widget.
define( 'YST_SIDEBAR_WIDTH', 261 );

// Activate blogroll widget
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// Disable site layouts that must not be used
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'sidebar-content-sidebar' );

// Setup Theme Settings
include_once( CHILD_DIR . '/lib/functions/child-theme-settings.php' );

/** Register widget areas */
genesis_register_sidebar( array(
	'id'          => 'yoast-top-right',
	'name'        => __( 'Search', 'yoast' ),
	'description' => __( 'Search widget area. Intended for search widget. Changes drastically on mobile.', 'yoast' ),
) );

genesis_register_sidebar( array(
	'id'          => 'yoast-after-header-1',
	'name'        => __( 'After Header 1', 'yoast' ),
	'description' => __( 'After Header 1 widget area.', 'yoast' ),
) );

genesis_register_sidebar( array(
	'id'          => 'yoast-after-header-2',
	'name'        => __( 'After Header 2', 'yoast' ),
	'description' => __( 'After Header 2 widget area.', 'yoast' ),
) );

genesis_register_sidebar( array(
	'id'          => 'yoast-after-header-3',
	'name'        => __( 'After Header 3', 'yoast' ),
	'description' => __( 'After Header 3 widget area.', 'yoast' ),
) );

genesis_register_sidebar( array(
	'id'          => 'yoast-tagline-after-header',
	'name'        => __( 'Tagline After Header', 'yoast' ),
	'description' => __( 'Tagline After Header widget area.', 'yoast' ),
) );


/**
 * Add yoast-after-header widget support for site. If widget not active, don't display
 *
 */
function yoast_after_header_genesis() {

	echo '<div id="yoast-after-header-container"><div class="wrap">';

	// Change 'true' to specific page to show/hide widget area on that page
	if ( true ) {
		genesis_widget_area( 'yoast-after-header-1', array(
			'before' => '<div id="yoast-after-header-1" class="yoast-after-header-widget">',
			'after'  => '</div>',
		) );
		genesis_widget_area( 'yoast-after-header-2', array(
			'before' => '<div id="yoast-after-header-2" class="yoast-after-header-widget">',
			'after'  => '</div>',
		) );
		genesis_widget_area( 'yoast-after-header-3', array(
			'before' => '<div id="yoast-after-header-3" class="yoast-after-header-widget">',
			'after'  => '</div>',
		) );
		echo '<div class="clearfloat"></div></div></div>';
	}

	if ( is_active_sidebar( 'yoast-tagline-after-header' ) ) {
		echo '<div id="yoast-tagline-after-header-container"><div class="wrap">';
		genesis_widget_area( 'yoast-tagline-after-header', array(
			'before' => '<div id="yoast-tagline-after-header" class="yoast-tagline-after-header-widget">',
			'after'  => '</div>',
		) );
		echo '</div></div>';

	}
}

add_action( 'genesis_after_header', 'yoast_after_header_genesis' );

/**
 * Add top-right widget area for search-widget
 */
function yoast_add_top_right_area() {
	if ( true ) {
		genesis_widget_area( 'yoast-top-right', array(
			'before' => '<div id="yoast-top-right" class="widget-area yoast-top-right-widget">',
			'after'  => '</div>',
		) );
	}
}

add_action( 'genesis_header', 'yoast_add_top_right_area' );

// Add Read More Link to Excerpts
add_filter( 'excerpt_more', 'get_read_more_link' );
add_filter( 'the_content_more_link', 'get_read_more_link' );
function get_read_more_link() {
	return '...&nbsp;<div class="exerptreadmore"><a href="' . get_permalink() . '">Read more</a></div>';
}

/**
 * Test adding widget on Theme-change
 */
//add_action( 'after_switch_theme', 'yst_add_widget_after_activating_theme', 10, 2 );
//
//function yst_add_widget_after_activating_theme( $oldname, $oldtheme = false ) {
//	$sidebar_id                    = 'yoast-after-header-3';
//	$sidebars_widgets              = get_option( 'sidebars_widgets' );
//	$id                            = count( $sidebars_widgets ) + 1;
//	$sidebars_widgets[$sidebar_id] = array( "text-" . $id );
//
//	$ops      = get_option( 'widget_text' );
//	$ops[$id] = array(
//		'title' => 'Automatic Widget',
//		'text'  => 'Works!',
//	);
//	update_option( 'widget_text', $ops );
//	update_option( 'sidebars_widgets', $sidebars_widgets );
//}

/**
 * This function is temporary, all the CSS here should move to the main stylesheet later on
 *
 * @todo move this into main CSS file.
 */
function yst_header_sidr_css() {
	?>
	<style>
		@media (min-width: 640px) {
			a.open, #sidr-menu {
				width: 0 !important;
				display: none !important;
			}
		}
		@media (max-width: 639px) {
			a.open {
				display: block;
				text-indent: -10000px;
				width: 35px;
				height: 24px;
				line-height: 22px;
				padding: 15px;
				margin: 0;
				background: #333 url('<?php echo get_stylesheet_directory_uri(); ?>/images/hamburger.png') 5px 5px no-repeat;
			}
			#yst-nav {
				display: none;
			}
		}
	</style>
	<?php
}
add_action( 'wp_head', 'yst_header_sidr_css' );

/**
 * Includes SIDR and its stylesheet
 *
 * @todo the CSS enqueued should be merged into the main stylesheet.
 * @link http://www.berriart.com/sidr/#documentation
 */
function yst_include_sidr() {
	wp_enqueue_script( 'yst_sidr', get_stylesheet_directory_uri() . '/lib/js/jquery.sidr.js', array( 'jquery' ) );
	wp_enqueue_style( 'yst-sidr-css', get_stylesheet_directory_uri() . '/lib/css/jquery.sidr.dark.css' );
}

add_action( 'wp_enqueue_scripts', 'yst_include_sidr' );

/**
 * Activates the sidr functionality, allowing for a left hand menu block.
 *
 * sidr takes the HTML from the elements referenced in source and puts them in the left hand menu.
 *
 * @link http://www.berriart.com/sidr/#documentation
 *
 * @todo combine with sticky menu code.
 */
function yst_activate_sidr() {
?>
	<script>
		jQuery(document).ready(function($) {
			$('#sidr-open').sidr({
				name: 'sidr-menu',
				source: '.search-form,#yst-nav',
				coverScreen: true
			});
		});
	</script>
<?php
}
add_action( 'wp_footer', 'yst_activate_sidr' );

/**
 * This adds an ID to the nav element.
 *
 * @param $output
 *
 * @return mixed
 *
 * @todo fix this, should be possible to do this using a genesis attribute filter instead of a string replace.
 *
 */
function yst_override_nav_menu( $output ) {
	return str_replace( '<nav ', '<a class="open" id="sidr-open" href="#sidr-menu">&nbsp;Open navigation</a><nav id="yst-nav" ', $output );
}
add_filter( 'genesis_markup_nav-primary_output', 'yst_override_nav_menu' );

/**
 * Function Do Genesis Footer
 */
function yst_do_genesis_footer() {
	//* Build the text strings.
	$backtotop_text = '';
	$creds_text     = sprintf( '&#x000B7; Copyright &copy; %s &#x000B7; %s uses %s by %s and is powered by <a href="http://www.wordpress.org">WordPress</a> &#x000B7;', date( 'Y' ), '<a href="' . home_url() . '">' . get_bloginfo( 'name' ) . '</a>', '<a href="http://yoast.com" rel="nofollow">Theme001</a>', 'Yoast' );

	//* Filter the text strings
	$backtotop_text = apply_filters( 'genesis_footer_backtotop_text', $backtotop_text );
	$creds_text     = apply_filters( 'genesis_footer_creds_text', $creds_text );

	$backtotop = $backtotop_text ? sprintf( '<div class="gototop">%s</div>', $backtotop_text ) : '';
	$creds     = $creds_text ? sprintf( '<div class="creds">%s</div>', $creds_text ) : '';

	$output = $backtotop . $creds;

	echo apply_filters( 'genesis_footer_output', $output, $backtotop_text, $creds_text );
}

remove_action( 'genesis_footer', 'genesis_do_footer' );
add_action( 'genesis_footer', 'yst_do_genesis_footer' );

/**
 * Add sticky menu for scrolling
 */
function yst_sticky_menu() {
	echo '<script>jQuery(function( $ ){
		if ( $(window).width() > 768 ) {
			$(window).scroll(function() {
				var yPos = ( $(window).scrollTop() );
				if(yPos > 70) {
					$("body").addClass("sticky");
				} else {
					$("body").removeClass("sticky");
				}
			});
		}
	});</script>';
}
add_action( 'wp_footer', 'yst_sticky_menu' );

//* Reposition the breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );

/**
 * Displays a term archive intro
 */
function yoast_term_archive_intro() {
	if ( ! is_category() && ! is_tag() && ! is_tax() )
		return;

	if ( get_query_var( 'paged' ) )
		return;

	echo '<div class="term-intro">';
	echo '<h1>' . single_term_title( '', false ) . '</h1>';
	echo '<div class="entry-content">';
	do_action( 'yst_term_archive_intro' );
	echo wpautop( term_description() );
	echo '</div>';
	echo '</div>';
}

add_action( 'genesis_before_loop', 'yoast_term_archive_intro', 20 );


//* Modify the size of the Gravatar in comments
add_filter( 'genesis_comment_list_args', 'sp_comments_gravatar' );
function sp_comments_gravatar( $args ) {
    $args['avatar_size'] = 100;
    return $args;
}