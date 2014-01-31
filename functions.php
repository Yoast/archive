<?php

// @TODO: Clean up this file and add some structure to it.

define( 'CHILD_THEME_NAME', 'Versatile' );
define( 'CHILD_THEME_URL', 'http://yoast.com/wordpress/themes/versatile/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

add_action( 'genesis_setup', 'child_theme_setup', 15 );

/**
 * Creates the child theme actions, filters and settings
 */
function child_theme_setup() {
	global $content_width;

	// Defines the content width of the content-sidebar design, as that's the default and this is needed for oEmbed.
	$content_width = 680;

	// Used for defaults in for instance the banner widget.
	define( 'YST_SIDEBAR_WIDTH', 261 );

	//* Start the engine
	include_once( get_template_directory() . '/lib/init.php' );

	require_once CHILD_DIR . '/lib/functions/theme-customizer.php';

	if ( is_admin() ) {
		// Editor Styles
		add_editor_style( 'assets/css/editor-style.css' );

		add_action( 'current_screen', 'fake_genesis_custom_header_thinking' );
	}

	/** Load widgets from /lib/widgets/ */
	foreach ( glob( CHILD_DIR . "/lib/widgets/*-widget.php" ) as $file ) {
		require_once $file;
	}

	// Add HTML5 markup structure
	add_theme_support( 'html5' );

	// Just allow for primary navigation
	add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'genesis' ) ) );

	// Add viewport meta tag for mobile browsers
	add_theme_support( 'genesis-responsive-viewport' );

	// Add support for 3-column footer widgets
	add_theme_support( 'genesis-footer-widgets', 3 );

	// Disable site layouts that must not be used
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );

	// Remove unused sidebars
	unregister_sidebar( 'sidebar-alt' );

	/** Register widget areas */
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
		'id'          => 'yoast-after-post',
		'name'        => __( 'After Post', 'yoast-theme' ),
		'description' => __( 'Add a widget after the post on single pages.', 'yoast-theme' ),
	) );

	function yst_show_fullwidth_sidebars() {
		if ( 'full-width-content' == genesis_site_layout() ) {
			// Remove the Primary Sidebar from the Primary Sidebar area.
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
			remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

			// Place the Secondary Sidebar into the Primary Sidebar area.
			add_action( 'genesis_sidebar', 'yoast_do_fullwidth_sidebars' );

			// Move the featured image to the right
			add_action( 'genesis_after_entry_content', 'yst_image_full_width', 15 );
		}
	}

	add_action( 'genesis_after_header', 'yst_show_fullwidth_sidebars' );

	function yoast_do_fullwidth_sidebars() {
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-1' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-2' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-3' );
	}

	/**
	 * Show the after post sidebar
	 */
	function yoast_do_after_post_sidebar() {
		if ( is_single() ) {
			dynamic_sidebar( 'yoast-after-post' );
		}
	}

	/**
	 * Show the post thumbnail in the full width archives
	 */
	function yst_image_full_width() {

		if( ! is_front_page() ) {
			return;
		}

		if ( ! get_theme_mod( 'yst_content_archive_thumbnail' ) ) {
			return;
		}

		$thumbnail = get_the_post_thumbnail( null, 'fullwidth-thumb' );
		if ( $thumbnail ) {
			echo '<div class="thumb full-width-thumb">';
			echo $thumbnail;
			echo '</div>';
		}
	}

	/**
	 * Opens div to fix borders in mobile menu
	 */
	function yst_add_open_div_for_mobile_menu_borders() {
		echo '<div id="mobile-menu-helper">';
	}

	add_action( 'genesis_header', 'yst_add_open_div_for_mobile_menu_borders', 11 );

	/**
	 * 	 Closes div to fix borders in mobile menu
	 */
	function yst_add_close_div_for_mobile_menu_borders() {
		echo '<div class="clearfloat"></div></div>';
	}

	add_action( 'genesis_header', 'yst_add_close_div_for_mobile_menu_borders', 12 );


	add_image_size( 'archive-thumb', 180, 120, true );
	add_image_size( 'sidebarfeatured-thumb', 230, 153, true );
	add_image_size( 'fullwidth-thumb', 290, 193, true );

	add_image_size( 'yst-archive-thumb', 170, 0, true );
	add_image_size( 'yst-single', 620, 315, true );

	// Activate blogroll widget
	add_filter( 'pre_option_link_manager_enabled', '__return_true' );

	// Change the main stylesheet URL
	add_filter( 'stylesheet_uri', 'yst_stylesheet_uri', 10, 2 );

	add_action( 'wp_enqueue_scripts', 'enqueue_form_styles', 25 );
	add_action( 'wp_enqueue_scripts', 'yst_add_google_fonts' );
	add_action( 'wp_enqueue_scripts', 'yst_include_sidr' );

	add_action( 'genesis_header', 'yst_mobile_nav', 11 );
	add_action( 'wp_head', 'yst_display_logo', 25 );
	add_action( 'wp_head', 'yst_conditional_add_backtotop', 14 );

	add_action( 'genesis_after_header', 'yst_after_header_genesis', 12 );
	add_action( 'genesis_after_content_sidebar_wrap', 'yst_fullwidth_sitebars_genesis' );
	add_action( 'genesis_before_comments', 'yst_after_post_sitebar_genesis' );

	// Reposition the breadcrumbs
	add_action( 'genesis_after_header', 'genesis_do_breadcrumbs', 12 );
	remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

	add_action( 'genesis_before_loop', 'yoast_term_archive_intro', 20 );

	// Change image output
	remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
	add_action( 'genesis_entry_content', 'yst_archive_image', 8 );

	add_action( 'wp_footer', 'yst_activate_sidr_and_sticky_menu' );

	// Add Read More Link to Excerpts
	add_filter( 'excerpt_more', 'yst_get_read_more_link' );
	add_filter( 'the_content_more_link', 'yst_get_read_more_link' );

	// Footer stuff
	add_filter( 'genesis_footer_creds_text', 'yst_footer_creds_text' );

	// Change the comment handling function
	add_filter( 'genesis_comment_list_args', 'yst_comment_list_args' );

	// Override Genesis settings with theme mod settings
	add_filter( 'genesis_pre_get_option_image_size', 'yst_override_content_thumbnail_setting' );
	add_filter( 'genesis_pre_get_option_content_archive', 'yst_override_content_archive_setting' );
	add_filter( 'genesis_pre_get_option_content_archive_thumbnail', 'yst_override_content_archive_thumbnail' );
	add_filter( 'genesis_pre_get_option_posts_nav', 'yst_override_posts_nav' );
	add_filter( 'genesis_pre_get_option_breadcrumb_front_page', 'yst_override_breadcrumb_front_page' );
	add_filter( 'genesis_pre_get_option_breadcrumb_posts_page', 'yst_override_breadcrumb_posts_page' );
	add_filter( 'genesis_pre_get_option_breadcrumb_home', 'yst_override_breadcrumb_home' );
	add_filter( 'genesis_pre_get_option_breadcrumb_single', 'yst_override_breadcrumb_single' );
	add_filter( 'genesis_pre_get_option_breadcrumb_page', 'yst_override_breadcrumb_page' );
	add_filter( 'genesis_pre_get_option_breadcrumb_archive', 'yst_override_breadcrumb_archive' );
	add_filter( 'genesis_pre_get_option_breadcrumb_404', 'yst_override_breadcrumb_404' );
	add_filter( 'genesis_pre_get_option_breadcrumb_attachment', 'yst_override_breadcrumb_attachment' );

	//add_filter( 'genesis_get_image', 'yst_filter_content_archive_image', 10, 2 );

	add_filter( 'user_contactmethods', 'yst_modify_contact_methods' );
	add_filter( 'genesis_post_meta', 'yst_post_meta_filter' );

	add_filter( 'genesis_search_text', 'yst_change_search_text' );
	add_filter( 'genesis_next_link_text', 'yst_add_spacing_next_prev' );
	add_filter( 'genesis_prev_link_text', 'yst_add_spacing_next_prev' );
	remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

	add_action( 'genesis_header_right', 'yst_header_search' );

	add_filter( 'comment_form_defaults', 'yst_change_comment_form_submit_button_text' );

	// Integration between Genesis and theme customizer
	add_filter( 'genesis_pre_get_option_site_layout', 'get_site_layout_from_theme_mod' );
	add_action( 'genesis_admin_before_metaboxes', 'remove_genesis_settings_boxes' );

	add_action( 'genesis_before_entry_content', 'yst_single_image' );
}

function yst_change_comment_form_submit_button_text( $defaults ) {
	$defaults['label_submit'] = 'Post Comment »';
	return $defaults;
}

/**
 * Display search box in header
 */
function yst_header_search() {
	echo genesis_search_form();
}

/**
 * Display the single image
 */
function yst_single_image() {
	global $post;

	// Only on single
	if( ! is_single() ) {
		return;
	}

	// Only when we have a thumbnail
	if ( ! has_post_thumbnail() ) {
		return;
	}

	// Set correct image alignment
	$align = 'left';
	if ( 'sidebar-content' == genesis_site_layout() ) {
		$align = 'right';
	}

	//setup thumbnail image args to be used with genesis_get_image();
	$size = 'yst-archive-thumb'; // Change this to whatever add_image_size you want
	$default_attr = array(
			'class' => "yst-single-image attachment-{$size} {$size}",
			'alt'   => $post->post_title,
			'title' => $post->post_title,
	);

	echo genesis_get_image( array( 'size' => 'yst-single', 'attr' => $default_attr ) );
}

/**
 * Display the image thumb on front page
 */
function yst_archive_image() {
	global $post;

	if( ! is_front_page() ) {
		return;
	}

	if ( ! has_post_thumbnail() ) {
		return;
	}

	// No inline image on full-width
	if ( 'full-width-content' == genesis_site_layout() ) {
		return;
	}

	// Set correct image alignment
	$align = 'left';
	if ( 'sidebar-content' == genesis_site_layout() ) {
		$align = 'right';
	}

	//setup thumbnail image args to be used with genesis_get_image();
	$size = 'yst-archive-thumb'; // Change this to whatever add_image_size you want
	$default_attr = array(
			'class' => "align{$align} attachment-{$size} {$size}",
			'alt'   => $post->post_title,
			'title' => $post->post_title,
	);

	printf( '<a href="%s" title="%s" class="yst-archive-image-link">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
}

/**
 * Fake Genesis into thinking we support a custom header
 */
function fake_genesis_custom_header_thinking() {
	global $pagenow;
	if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'genesis' == $_GET['page'] ) {
		add_theme_support( 'custom-header' );
	}
}

/**
 * @return string
 */
function get_site_layout_from_theme_mod() {
	return get_theme_mod( 'yst_default_layout' );
}

/**
 * Remove the Genesis layout settings and nav metaboxes
 */
function remove_genesis_settings_boxes() {
	global $wp_meta_boxes;
//	var_dump( $wp_meta_boxes );
	unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-layout'] );
	unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-nav'] );
	unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-posts'] );
	unset( $wp_meta_boxes['toplevel_page_genesis']['main']['default']['genesis-theme-settings-breadcrumb'] );
}

/**
 * Change stylesheet URL.
 *
 * @param string $stylesheet_uri
 * @param string $stylesheet_dir_uri
 *
 * @return string
 */
function yst_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {
	$colour_scheme = get_theme_mod( 'yst_colour_scheme', 'BrightBlue' );

	return $stylesheet_dir_uri . '/assets/css/' . $colour_scheme . '.css';
}

/**
 * Seperate callback because of low priority loading of stylesheet.
 *
 * @fixme: if-statement is always true because contactform 7 always loads their css. This breaks ours.
 */
function enqueue_form_styles() {
	if ( wp_style_is( 'gforms_browsers_css', $list = 'enqueued' ) || wp_style_is( 'contact-form-7', $list = 'enqueued' ) ) {
		wp_enqueue_style( 'yst-form-style', get_stylesheet_directory_uri() . '/assets/css/forms.css' );
	}
}

/**
 * Enqueue Google font
 */
function yst_add_google_fonts() {
	wp_enqueue_style( 'google-font-open_sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic);', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'google-font-ruda', '//fonts.googleapis.com/css?family=Ruda:400,700', array(), CHILD_THEME_VERSION );
}

/**
 * Add yst-after-header widget support for site. If widget not active, don't display
 */
function yst_after_header_genesis() {
	if ( is_front_page() && ( is_active_sidebar( 'yoast-after-header-1' ) || is_active_sidebar( 'yoast-after-header-2' ) || is_active_sidebar( 'yoast-after-header-3' ) ) ) {
		echo '<div id="yoast-after-header-container"><div class="wrap">';

		$areas = array( 'yoast-after-header-1', 'yoast-after-header-2', 'yoast-after-header-3' );

		foreach ( $areas as $area ) {
			genesis_widget_area( $area, array(
				'before' => '<div id="' . $area . '" class="yoast-after-header-widget">',
				'after'  => '</div>',
			) );
		}

		echo '<div class="clearfloat"></div></div></div>';
	}
}

/**
 * @todo: add documentation
 */
function yst_tagline() {
	// We explicitly allow for HTML in taglines.
	$tagline = html_entity_decode( get_bloginfo( 'description' ) );
	if ( isset ( $tagline ) && ! empty( $tagline ) ) {
		if (
				( is_home() && get_theme_mod( 'yst_tagline_home' ) ) ||
				( is_front_page() && ! is_home() && get_theme_mod( 'yst_tagline_front_page' ) ) ||
				( is_home() && ! is_front_page() && get_theme_mod( 'yst_tagline_posts_page' ) ) ||
				( is_singular() && get_theme_mod( 'yst_tagline_singular' ) ) ||
				( is_archive() && get_theme_mod( 'yst_tagline_archive' ) ) ||
				( is_404() && get_theme_mod( 'yst_tagline_404' ) ) ||
				( is_attachment() && get_theme_mod( 'yst_tagline_attachment' ) )
		) {
			add_action( 'genesis_header', 'yst_show_tagline', 13 );
		}
	}
}

add_action( 'genesis_before', 'yst_tagline' );

/**
 * @todo: add documentation
 */
function yst_show_tagline() {
	echo '<div class="tagline_top tagline_' . get_theme_mod( 'yst_tagline_positioner', 'top_right' ) .'">' . html_entity_decode( get_bloginfo( 'description' ) ) . '</div>';
}

/**
 * Adds class to the body-element. Used in mobile menu.
 *
 * @param array $classes Contains all the current body-classes
 *
 * @return array The updated array of body-classes
 */
function yst_add_body_class_for_tagline( $classes ) {
	$tagline_positioner = get_theme_mod( 'yst_tagline_positioner' );
	$tagline = html_entity_decode( get_bloginfo( 'description' ) );
	if ( isset( $tagline_positioner ) && ! empty ( $tagline_positioner ) && isset ( $tagline ) && ! empty( $tagline ) ) {
		if (
				( is_home() && get_theme_mod( 'yst_tagline_home' ) ) ||
				( is_front_page() && ! is_home() && get_theme_mod( 'yst_tagline_front_page' ) ) ||
				( is_home() && ! is_front_page() && get_theme_mod( 'yst_tagline_posts_page' ) ) ||
				( is_singular() && get_theme_mod( 'yst_tagline_singular' ) ) ||
				( is_archive() && get_theme_mod( 'yst_tagline_archive' ) ) ||
				( is_404() && get_theme_mod( 'yst_tagline_404' ) ) ||
				( is_attachment() && get_theme_mod( 'yst_tagline_attachment' ) )
		) {
			$classes[] = 'show_tagline';
		}
	}
	return $classes;
}

add_action( 'body_class', 'yst_add_body_class_for_tagline' );

/**
 * @todo add documentation
 */
function yst_fullwidth_sitebars_genesis() {
	if ( 'full-width-content' == genesis_site_layout() ) {
		echo '<div id="yoast-fullwidth-bottom-container"><div class="wrap">';

		$i = 1;
		while ( $i < 4 ) {
			genesis_widget_area( 'yoast-fullwidth-widgetarea-' . $i, array(
					'before' => '<div id="yoast-fullwidth-widgetarea-' . $i . '" class="yoast-fullwidth-widget">',
					'after'  => '</div>',
				) );
			$i ++;
		}

		echo '</div></div>';
	}
}

/**
 * @todo add documentation
 */
function yst_after_post_sitebar_genesis() {
	if ( is_active_sidebar( 'yoast-after-post' ) && is_single() ) {
		echo '<div id="yoast-after-post-container"><div class="wrap">';
		genesis_widget_area( 'yoast-after-post', array(
			'before' => '<div id="yoast-after-post-widgetarea" class="yoast-after-post-widget">',
			'after'  => '</div>',
		) );
		echo '</div></div>';
	}
}

/**
 *
 * @todo make sure this has a filter.
 *
 * @return string
 */
function yst_get_read_more_link() {
	return '&hellip; <div class="excerpt_readmore"><a href="' . get_permalink() . '">' . __( 'Read more', 'yoast-theme' ) . '</a></div>';
}

/**
 * Includes SIDR
 *
 * @link http://www.berriart.com/sidr/#documentation
 */
function yst_include_sidr() {
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
function yst_activate_sidr_and_sticky_menu() {
	$yst_nav_pos = get_theme_mod ('yst_nav_positioner');
	if ($yst_nav_pos == 'top') {
		$yst_Ypos = 10;
	} else {
		$yst_Ypos = 178;
	}
	?>
	<script>
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
 */
function yst_footer_creds_text( $footer_creds_text ) {
	$yst_footer = get_theme_mod( 'yst_footer' );
	if ( ! $yst_footer || empty( $yst_footer ) ) {
		return $footer_creds_text;
	}

	return $yst_footer;
}

/**
 * Displays a term archive intro
 */
function yoast_term_archive_intro() {
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return;
	}

	if ( get_query_var( 'paged' ) ) {
		return;
	}

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
 * Fix Search tekst
 */
function yst_change_search_text() {
	return __( 'Search', 'yoast-theme' ) . '&#x02026;';
}

/**
 * Add back to top link
 *
 * @fixme If there is a better solid way to do this or Genesis fixes this feature, use that
 */
function yst_add_backtotop() {
	echo '<p class="back-to-top"><a href="#">' . __( 'Back to top', 'yoast-theme' ) . ' &#9652;</a></p>';
}

/**
 * @todo add documentation
 */
function yst_conditional_add_backtotop() {
	if ( is_single() ) {
		add_action( 'genesis_entry_footer', 'yst_add_backtotop', 14 );
	}
//	if ( is_singular() ) {
//		add_action( 'genesis_after_endwhile', 'yst_add_backtotop', 14 );
//	}
}

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

/**
 * Change default image alignment
 *
 * @since 1.0.0
 *
 * @param string $img  Image HTML output
 * @param array  $args Arguments for the image
 *
 * @return string Image HTML output.
 */
/*
function yst_filter_content_archive_image( $img, $args ) {
	if ( 'sidebar-content' == genesis_site_layout() || 'full-width-content' == genesis_site_layout() ) {
		if ( 'archive' == $args['context'] ) {
			$img = str_replace( 'alignleft', 'alignright', $img );
		}
	}

	return $img;
}
*/

/**
 * Use the logo's set in the Child Theme Settings
 *
 * Adds CSS to wp_head to show either the regular logo or the mobile logo, if they are set. If they're not set, no logo-image will be used.
 *
 * @since 1.0.0
 */
function yst_display_logo() {
	// This holds the CSS that will be echoed
	$css = '';

	// Normal logo
	$logo = get_theme_mod( 'yst_logo' );
	if ( isset( $logo ) && ! empty ( $logo ) ) {
		$css .= '@media(min-width: 640px){.site-header .title-area {background-image: url(' . $logo . ');}}';
	}

	$mobile_logo = get_theme_mod( 'yst_mobile_logo' );
	if ( isset ( $mobile_logo ) && ! empty ( $mobile_logo ) ) {
		$css .= '@media(max-width: 640px){header.site-header {background-color:#fff; background-image: url(' . $mobile_logo . '); background-repeat: no-repeat;	}}';
	}

	if ( ! empty( $css ) ) {
		echo '<style id="versatile-inline-css">' . $css . '</style>';
	}
}

/**
 * Open wrapper around main content for alignment
 * @fixme: can be combined with yst_add_wrapper_after_content() in one function.
 */
function yst_add_wrapper_before_content() {
	echo '<div id="main-content-wrap">';
}

/**
 *  * Close wrapper around main content for alignment
 *  * @fixme: can be combined with yst_add_wrapper_before_content() in one function.
 */
function yst_add_wrapper_after_content() {
	echo '</div>';
}

/**
 * Comment List Arguments, modify to change the callback function
 *
 * @param array $args
 *
 * @return array
 */
function yst_comment_list_args( $args ) {
	$args['callback'] = 'yst_comment_callback';

	return $args;
}

/**
 * Comment Callback Function
 *
 * @param stdClass $comment
 * @param array    $args
 * @param integer  $depth
 */
function yst_comment_callback( $comment, $args, $depth ) {
	global $post;
	$GLOBALS['comment'] = $comment; ?>

<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
	<article <?php echo genesis_attr( 'comment' ); ?>>

		<?php do_action( 'genesis_before_comment' ); ?>

		<div class="avatar">
			<?php
			$avatar_size = 1 == $depth ? 88 : 62;
			echo get_avatar( $comment, $avatar_size );
			?>
		</div>
		<div class="comment-content" itemprop="commentText">
			<header class="comment-header">
				<p <?php echo genesis_attr( 'comment-author' ); ?>>
					<?php

					$author = get_comment_author();
					$url = get_comment_author_url();

					if ( ! empty( $url ) && 'http://' !== $url ) {
						$author = sprintf( '<a href="%s" rel="external nofollow" itemprop="url">%s</a>', esc_url( $url ), $author );
					}

					printf( __( 'By %s', 'yoast-theme' ), sprintf( '<span itemprop="name">%s</span> ', $author ) );
					_e( ' on ', 'yoast-theme' );

					$pattern = '<time itemprop="commentTime" datetime="%s"><a href="%s" itemprop="url">%s %s %s</a></time>';
					printf( $pattern, esc_attr( get_comment_time( 'c' ) ), esc_url( get_comment_link( $comment->comment_ID ) ), esc_html( get_comment_date() ), __( 'at', 'yoast-theme' ), esc_html( get_comment_time() ) );

					if ( $comment->user_id === $post->post_author ) {
						echo ' <span class="post_author_comment">' . __( 'Author', 'yoast-theme' ) . '</span>';
					}

					?>

				</p>
			</header>
			<?php if ( ! $comment->comment_approved ) : ?>
				<p class="alert"><?php echo apply_filters( 'genesis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'genesis' ) ); ?></p>
			<?php endif; ?>

			<?php comment_text(); ?>

			<p class="comment-actions">
				<?php
				comment_reply_link( array_merge( $args, array(
					'reply_text' => __( 'Reply &raquo;', 'yoast-theme' ),
					'depth'      => $depth,
					'before'     => '<span class="comment-reply">',
					'after'      => '</span>',
				) ) );
				edit_comment_link( __( 'Edit comment', 'yoast-theme' ), ' <span class="edit">', '</span>' );
				?>
			</p>
		</div>

		<?php do_action( 'genesis_after_comment' ); ?>

		<div class="floatclearing"></div>

	</article>
	<?php
	//* No ending </li> tag because of comment threading

}

/**
 * Customize the post meta function, only show categories and tags on single()
 *
 * @param string $post_meta Contains the current value of post meta data
 *
 * @return string Returns new post meta data
 */
function yst_post_meta_filter( $post_meta ) {
	if ( is_single() ) {
		$post_meta = '[post_categories before="Filed Under: "] [post_tags before="Tagged: "]';

		return $post_meta;
	}
}

/**
 * By default, Genesis lack a space after the raquo and laquo, this adds it.
 *
 * @param string $link
 *
 * @return string
 */
function yst_add_spacing_next_prev( $link ) {
	$link = str_replace( '&#x000BB;', ' &#x000BB;', $link );
	$link = str_replace( '&#x000AB;', '&#x000AB; ', $link );

	return $link;
}

/**
 * Override the image size for full-width designs, user settings are now completely ignored.
 *
 * @param null|string $size
 *
 * @return null|string
 */
function yst_override_content_thumbnail_setting( $size = null ) {
	if ( false !== strpos( genesis_site_layout(), 'full-width' ) ) {
		return 'fullwidth-thumb';
	}

	return $size;
}

/**
 * Function to override genesis settings with theme_mod settings
 *
 * @param string  $setting
 * @param string  $value
 * @param boolean $checkbox
 *
 * @return string
 */
function yst_override_genesis_setting( $setting, $value, $checkbox = false ) {
	$theme_setting = get_theme_mod( $setting );
	if ( isset( $theme_setting ) && ! empty( $theme_setting ) ) {
		return $theme_setting;
	} else {
		if ( $checkbox ) {
			return false;
		}
	}

	return $value;
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_content_archive_setting( $value = null ) {
	return yst_override_genesis_setting( 'yst_content_archive', $value );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_content_archive_thumbnail( $value = null ) {
	if ( false !== strpos( genesis_site_layout(), 'full-width' ) ) {
		return false;
	}

	return yst_override_genesis_setting( 'yst_content_archive_thumbnail', $value, true );
}

/**
 * Retrieve the posts_nav setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_posts_nav( $value = null ) {
	return yst_override_genesis_setting( 'yst_posts_nav', $value );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_front_page( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_front_page', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_posts_page( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_posts_page', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_home( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_home', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_single( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_single', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_page( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_page', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_archive( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_archive', $value, true );
}

/**
 * Retrieve the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_404( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_404', $value, true );
}

/**
 * Retrieves the content_archive setting from the theme settings
 *
 * @param null|string $value
 *
 * @return null|string
 */
function yst_override_breadcrumb_attachment( $value = null ) {
	return yst_override_genesis_setting( 'yst_breadcrumb_attachment', $value, true );
}

/***
 **		@TODO: 	The following functions are used to create the needed HTML-structure, but still need proper naming and may be combined into less functions.
 **/

function yst_open_div_before_header() {
	echo '<div id="header-wrapper">';
}

function yst_close_div_after_header() {
	echo '</div>';
}

add_action( 'genesis_before_header', 'yst_open_div_before_header' );
add_action( 'genesis_after_header', 'yst_close_div_after_header' );

function yst_open_div_before_nav() {
	echo '<div id="center-wrapper">';
}

function yst_close_div_after_content() {
	echo '</div>';
}

add_action( 'genesis_after_header', 'yst_open_div_before_nav', 11 );
add_action( 'genesis_before_footer', 'yst_close_div_after_content' );

function yst_open_div_after_afterheader() {
	echo '<div id="afterheader-content-wrapper">';
}

add_action( 'genesis_after_header', 'yst_open_div_after_afterheader', 11 );
add_action( 'genesis_after_content_sidebar_wrap', 'yst_close_div_after_content' );

/**
 * Moves menu to top of page
 */
function yst_move_nav_to_top() {
	if ( get_theme_mod( 'yst_nav_positioner' ) == 'top' ) {
		remove_action( 'genesis_after_header', 'genesis_do_nav' );
		add_filter( 'body_class', 'yst_add_body_class' );
		add_action( 'genesis_before', 'genesis_do_nav' );
	}
}

add_action( 'after_setup_theme', 'yst_move_nav_to_top' );

/**
 * Adds a class to the body-element to be used in checks for the location of the menu.
 *
 * @param array $classes contains all the current classes
 *
 * @return array Updated array of classes
 */
function yst_add_body_class_for_nav( $classes ) {
	$classes[] = 'menu-at-top';

	return $classes;
}

/**
 * Adds a class to the body-element to be used in checks for light or dark header.
 *
 * @param array $classes contains all the current classes
 *
 * @return array Updated array of classes
 */
function yst_add_body_class_for_header_style ( $classes ) {
	$header_style = get_theme_mod( 'yst_header_color_picker', 'light' );

	if ( 'light' == $header_style ) {
		$classes[] = 'header-light';
	} else {
		$classes[] = 'header-dark';
	}

	return $classes;
}

add_action('body_class', 'yst_add_body_class_for_header_style');
