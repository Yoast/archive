<?php

// @TODO: Clean up this file and add some structure to it.

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Tailor Made' );
define( 'CHILD_THEME_URL', 'http://yoast.com/wordpress/themes/tailor-made/' );
define( 'CHILD_THEME_VERSION', '0.0.1' );

add_action( 'genesis_setup', 'child_theme_setup', 15 );

/**
 *
 */
function child_theme_setup() {
	// Used for defaults in for instance the banner widget.
	define( 'YST_SIDEBAR_WIDTH', 261 );

	//* Start the engine
	include_once( get_template_directory() . '/lib/init.php' );

	// Setup Theme Settings
	// @todo Does this always have to be loaded or just in admin?
	include_once( CHILD_DIR . '/lib/functions/child-theme-settings.php' );

	/** Load widgets from /lib/widgets/ */
	foreach ( glob( CHILD_DIR . "/lib/widgets/*-widget.php" ) as $file ) {
		require_once $file;
	}

	//include_once( get_theme_root_uri() . '/theme001/lib/functions/yst-colourscheme-settings.php' );
	require_once( 'lib/functions/yst-colourscheme-settings.php' );

	//* Add HTML5 markup structure
	add_theme_support( 'html5' );

	//* Add viewport meta tag for mobile browsers
	add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
	add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
	add_theme_support( 'genesis-footer-widgets', 4 );

	// Disable site layouts that must not be used
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );

	/** Register widget areas */
	genesis_register_sidebar( array(
		'id'          => 'yoast-top-right',
		'name'        => __( 'Search', 'yoast-theme' ),
		'description' => __( 'Search widget area. Intended for search widget. Changes drastically on mobile.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-after-header-1',
		'name'        => __( 'After Header 1', 'yoast-theme' ),
		'description' => __( 'After Header 1 widget area.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-after-header-2',
		'name'        => __( 'After Header 2', 'yoast-theme' ),
		'description' => __( 'After Header 2 widget area.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-after-header-3',
		'name'        => __( 'After Header 3', 'yoast-theme' ),
		'description' => __( 'After Header 3 widget area.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-fullwidth-widgetarea-1',
		'name'        => __( 'Full Width 1', 'yoast-theme' ),
		'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-fullwidth-widgetarea-2',
		'name'        => __( 'Full Width 2', 'yoast-theme' ),
		'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-fullwidth-widgetarea-3',
		'name'        => __( 'Full Width 3', 'yoast-theme' ),
		'description' => __( 'Shows only on pages with full-width layout.', 'yoast-theme' ),
	) );

	genesis_register_sidebar( array(
		'id'          => 'yoast-tagline-after-header',
		'name'        => __( 'Tagline After Header', 'yoast-theme' ),
		'description' => __( 'Tagline After Header widget area.', 'yoast-theme' ),
	) );

	function yst_show_fullwidth_sidebars() {
		if ( 'full-width-content' == genesis_site_layout() ) {
			// Remove the Primary Sidebar from the Primary Sidebar area.
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
			remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

			// Place the Secondary Sidebar into the Primary Sidebar area.
			add_action( 'genesis_sidebar', 'yoast_do_fullwidth_sidebars' );
		}
	}

	add_action( 'genesis_after_header', 'yst_show_fullwidth_sidebars' );

	function yoast_do_fullwidth_sidebars() {
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-1' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-2' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-3' );
	}

	// Activate blogroll widget
	add_filter( 'pre_option_link_manager_enabled', '__return_true' );

	add_action( 'wp_enqueue_scripts', 'yst_load_css_from_setting' );
	add_action( 'wp_enqueue_scripts', 'enqueue_styles_basic' );
	add_action( 'wp_enqueue_scripts', 'yst_add_google_fonts' );
	add_action( 'wp_enqueue_scripts', 'yst_include_sidr' );

	add_action( 'wp_head', 'yst_header_sidr_css' );

	add_action( 'genesis_header', 'yst_mobile_nav' );

	add_action( 'genesis_after_header', 'yst_after_header_genesis' );
	add_action( 'genesis_after_content_sidebar_wrap', 'yst_fullwidth_sitebars_genesis' );

	//* Reposition the breadcrumbs
	add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );
	remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

	add_action( 'genesis_header', 'yst_add_top_right_area' );

	add_action( 'genesis_before_loop', 'yoast_term_archive_intro', 20 );

	add_action( 'wp_footer', 'yst_activate_sidr_and_sticky_menu' );

	// Add Read More Link to Excerpts
	add_filter( 'excerpt_more', 'yst_get_read_more_link' );
	add_filter( 'the_content_more_link', 'yst_get_read_more_link' );

	add_filter( 'genesis_footer_creds_text', 'yst_footer_creds_text' );

	add_filter( 'genesis_comment_list_args', 'yst_comments_gravatar' );
}

/**
 *
 * @fixme this uses a hardcoded theme path....
 */
function yst_load_css_from_setting() {
	wp_enqueue_style( 'yst_custom_css', get_theme_root_uri() . "/theme001" . genesis_get_option( 'yst_colourscheme' ), array( 'google-font-quattrocento_sans', 'admin-bar', 'theme001' ) );
}

/**
 *
 */
function enqueue_styles_basic() {
	wp_enqueue_style( 'style-basic', get_stylesheet_directory_uri() . '/assets/css/style.css' );
}

/**
 * Enqueue Google font
 */
function yst_add_google_fonts() {
	wp_enqueue_style( 'google-font-quattrocento_sans', '//fonts.googleapis.com/css?family=Quattrocento+Sans:400,400italic,700,700italic);', array(), CHILD_THEME_VERSION );
}

/**
 * Add yst-after-header widget support for site. If widget not active, don't display
 */
function yst_after_header_genesis() {
	if ( is_front_page() ) {
		echo '<div id="yoast-after-header-container"><div class="wrap">';

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

	if ( is_active_sidebar( 'yoast-tagline-after-header' ) && is_front_page() ) {
		echo '<div id="yoast-tagline-after-header-container"><div class="wrap">';
		genesis_widget_area( 'yoast-tagline-after-header', array(
			'before' => '<div id="yoast-tagline-after-header" class="yoast-tagline-after-header-widget">',
			'after'  => '</div>',
		) );
		echo '</div></div>';

	}
}

function yst_fullwidth_sitebars_genesis() {
	if ( 'full-width-content' == genesis_site_layout() ) {
		echo '<div id="yoast-fullwidth-bottom-container"><div class="wrap">';
		genesis_widget_area( 'yoast-fullwidth-widgetarea-1', array(
			'before' => '<div id="yoast-fullwidth-widgetarea-1" class="yoast-fullwidth-widget">',
			'after'  => '</div>',
		) );
		genesis_widget_area( 'yoast-fullwidth-widgetarea-2', array(
			'before' => '<div id="yoast-fullwidth-widgetarea-2" class="yoast-fullwidth-widget">',
			'after'  => '</div>',
		) );
		genesis_widget_area( 'yoast-fullwidth-widgetarea-3', array(
			'before' => '<div id="yoast-fullwidth-widgetarea-3" class="yoast-fullwidth-widget">',
			'after'  => '</div>',
		) );
		echo '</div></div>';
	}
}

/**
 * Add top-right widget area for search-widget
 */
function yst_add_top_right_area() {
	if ( true ) {
		genesis_widget_area( 'yoast-top-right', array(
			'before' => '<div id="yoast-top-right" class="widget-area yoast-top-right-widget">',
			'after'  => '</div>',
		) );
	}
}

/**
 *
 * @todo make sure this has a filter.
 *
 * @return string
 */
function yst_get_read_more_link() {
	return '...&nbsp;<div class="exerptreadmore"><a href="' . get_permalink() . '">' . __( 'Read more', 'yoast-theme' ) . '</a></div>';
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
		@media (min-width: 641px) {
			a.open, #sidr-left, #sidr-right {
				width: 0 !important;
				display: none !important;
			}
		}

		@media (max-width: 641px) {
			.nav-primary {
				display: none;
			}

			a.open {
				display: inline-block;
				text-indent: -10000px;
				z-index: 13;
				width: 25px;
				height: 25px;
				line-height: 22px;
				padding: 10px;
				margin: 0;
			}

			a#sidr-left {
				float: left;
				background: url('<?php echo get_stylesheet_directory_uri(); ?>/images/hamburger.png') 10px 10px no-repeat;
			}

			a#sidr-right {
				float: right;
				background: url('<?php echo get_stylesheet_directory_uri(); ?>/images/hamburger.png') 10px 10px no-repeat;
			}

			.nav-primary {
				display: none !important;
			}

			body.sticky-menu .site-container a#sidr-left {
				position: fixed;
				top: 0;
				left: 5px;
			}

			body.sticky-menu .site-container a#sidr-right {
				position: fixed;
				top: 0;
				right: 5px;
			}

			body.sticky-menu .site-header .title-area .site-title a {
				position: fixed;
				top: 0;
				margin-left: 50px;
			}
		}
	</style>
<?php
}

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

/**
 * Activates the sidr functionality, allowing for a left hand menu block.
 *
 * sidr takes the HTML from the elements referenced in source and puts them in the left hand menu.
 *
 * @link http://www.berriart.com/sidr/#documentation
 *
 * @todo check whether yPos in sticky menu code is the right position to switch.
 */
function yst_activate_sidr_and_sticky_menu() {
	?>
	<script>
		jQuery(document).ready(function ($) {
			$('#sidr-left').sidr({
				name       : 'sidr-menu-left',
				source     : function () {
					return "<h1><?php _e("Navigation","yoast-theme"); ?></h1><ul>" + $('.menu-primary').html() + "</ul>";
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
				if (yPos > 70) {
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
 * Output mobile navigation links
 */
function yst_mobile_nav() {
	echo '<a class="open" id="sidr-left" href="#sidr-left">' . __( 'Open Navigation', 'yoast-theme' ) . '</a>';
	echo '<a class="open" id="sidr-right" href="#sidr-right">' . __( 'Open Search', 'yoast-theme' ) . '</a>';
}

/**
 * Replace the Genesis footer creds text with our own template.
 *
 * @param string $footer_creds_text
 *
 * @return string
 *
 * @fixme If this becomes something you can change in the settings, just retrieve the option here and return it.
 */
function yst_footer_creds_text( $footer_creds_text ) {
	$yst_footer = '<p class="custom_footer">';
	$yst_footer .= trim( genesis_get_option( 'footer', 'child-settings' ) ) . '</p><p class="hardcoded-footer">';
	$yst_footer .= sprintf( __( '&#x000B7; Copyright &copy; %s &#x000B7; %s uses %s by %s and is powered by <a href="http://www.wordpress.org">WordPress</a> &#x000B7;', 'yoast-theme' ), date( 'Y' ), '<a href="' . home_url() . '">' . get_bloginfo( 'name' ) . '</a>', '<a href="http://yoast.com/wordpress/themes/tailor-made/" rel="nofollow">Tailor Made</a>', 'Yoast' );
	$yst_footer .= '</p>';
	return $yst_footer;
}

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
	/**
	 * This action allows you to output extra content in a term archive intro section.
	 */
	do_action( 'yst_term_archive_intro' );
	echo wpautop( term_description() );
	echo '</div>';
	echo '</div>';
}

/**
 * Changes the gravatar size to 100
 *
 * @param array $args
 *
 * @return array
 *
 * @todo make sure this is filterable.
 */
function yst_comments_gravatar( $args ) {
	$args['avatar_size'] = 100;
	return $args;
}

/**
 * Fix Search tekst
 */
function yst_change_search_text() {
	return __( 'Search', 'yoast-theme' ) . '&#x02026;';
}

add_filter( 'genesis_search_text', 'yst_change_search_text' );

/**
 * Add back to top link
 *
 * @fixme If there is a better solid way to do this or Genesis fixes this feature, use that
 */
function yst_add_backtotop_to_post_footer() {
	echo '<p class="back-to-top"><a href="#">' . __( 'Back to top', 'yoast-theme' ) . ' &#9652;</a></p>';
}

add_action( 'genesis_entry_footer', 'yst_add_backtotop_to_post_footer', 14 );

/**
 * @param $profile_fields
 *
 * @return mixed
 */
function yst_modify_contact_methods( $profile_fields ) {

	// Add new fields
	$profile_fields['pinterest'] = __( 'Pinterest profile URL', 'yoast-theme' );
	$profile_fields['linkedin']  = __( 'LinkedIn profile URL', 'yoast-theme' );
	return $profile_fields;
}

add_filter( 'user_contactmethods', 'yst_modify_contact_methods' );