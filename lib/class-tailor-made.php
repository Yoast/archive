<?php

// Load the Yoast_Theme class
require_once( 'framework/class-theme.php' );

/**
 * Class Versatile
 */
class Yoast_Tailor_Made extends Yoast_Theme {

	const NAME    = 'Tailor Made';
	const URL     = 'http://yoast.com/wordpress/themes/tailor-made/';
	const VERSION = '1.0.0';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( self::NAME, self::URL, self::VERSION );
	}

	/**
	 * Setup the theme
	 */
	public function setup_theme() {

		// Set the content width
		$this->set_content_width( 680 );

		// Used for defaults in for instance the banner widget.
		define( 'YST_SIDEBAR_WIDTH', 261 );

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 4 );

		// Disable site layouts that must not be used
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

		// Register theme sidebars
		$this->register_sidebars();

		// Image Sizes
		add_image_size( 'archive-thumb', 180, 120, true );
		add_image_size( 'sidebarfeatured-thumb', 230, 153, true );
		add_image_size( 'fullwidth-thumb', 290, 193, true );

		// Activate blogroll widget
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );

		// change default image alignment
		add_filter( 'genesis_get_image', array( $this, 'filter_content_archive_image' ), 10, 2 );

		// Load Google Fonts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_fonts' ) );

		// Set the default color scheme
		add_filter( 'yst_default_color_scheme', array( $this, 'set_default_color_scheme' ) );

		// Display logo
		add_action( 'wp_head', array( $this, 'display_logo') );

		// Add back to top link (conditional)
		add_action( 'wp_head', array( $this, 'conditional_add_back_to_top_link' ), 14 );

		// Add conditional comments (ie)
	    add_action( 'wp_head', array( $this, 'conditional_comments' ) );

	    // Add support for yoast after header widget
		add_action( 'genesis_after_header', array( $this, 'after_header_genesis' ) );

		// Conditionally add full width sidebars
		add_action( 'genesis_after_header', array( $this, 'add_full_width_sidebars' ) );

		// Reposition the breadcrumbs
		add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

		// Add top right widget area (for search widget)
		add_action( 'genesis_header', array( $this, 'add_top_right_area' ) );

		// Fake Genesis Custom Header
		if ( is_admin() ) {
			add_action( 'current_screen', array($this, 'fake_genesis_custom_header' ) );
		}
	}

	/**
	 * Register widget area's
	 */
	public function register_sidebars() {
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
			'id'          => 'yoast-after-post',
			'name'        => __( 'After Post', 'yoast-theme' ),
			'description' => __( 'Add a widget after the post on single pages.', 'yoast-theme' ),
		) );
	}

	/**
	 * Comment Callback Function
	 *
	 * @param stdClass $comment
	 * @param array    $args
	 * @param integer  $depth
	 *
	 * @todo create a template partial containing this HTML block
	 */
	public function comment_callback($comment, $args, $depth) {
		global $post;
		$GLOBALS['comment'] = $comment; ?>

		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<article <?php echo genesis_attr( 'comment' ); ?>>

				<?php do_action( 'genesis_before_comment' ); ?>

				<header class="comment-header">
					<p <?php echo genesis_attr( 'comment-author' ); ?>>
						<?php

						$author = get_comment_author();
						$url = get_comment_author_url();

						if ( ! empty( $url ) && 'http://' !== $url ) {
							$author = sprintf( '<a href="%s" rel="external nofollow" itemprop="url">%s</a>', esc_url( $url ), $author );
						}

						printf( 'By <span itemprop="name">%s</span> ', $author );

						$pattern = 'on <time itemprop="commentTime" datetime="%s"><a href="%s" itemprop="url">%s %s %s</a></time>';
						printf( $pattern, esc_attr( get_comment_time( 'c' ) ), esc_url( get_comment_link( $comment->comment_ID ) ), esc_html( get_comment_date() ), __( 'at', 'yoast-theme' ), esc_html( get_comment_time() ) );

		                if ( $comment->user_id === $post->post_author ) {
		                echo ' <span class="post_author_comment">' . __( 'Author', 'yoast-theme' ) . '</span>';
		                }

		                ?>

					</p>
				</header>
				<div class="avatar">
					<?php
					$avatar_size = 1 == $depth ? 126 : 80;
					echo get_avatar( $comment, $avatar_size );
					?>
				</div>
				<div class="comment-content" itemprop="commentText">
					<?php if ( ! $comment->comment_approved ) : ?>
						<p class="alert"><?php echo apply_filters( 'genesis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'genesis' ) ); ?></p>
					<?php endif; ?>

					<?php comment_text(); ?>

					<p class="comment-actions">
						<?php
						comment_reply_link( array_merge( $args, array(
							'depth'  => $depth,
							'before' => '<span class="comment-reply">',
							'after'  => '</span>',
						) ) );
						edit_comment_link( __( 'Edit comment', 'yoast-theme' ), ' <span class="edit">', '</span>' );
						?>
					</p>
				</div>
		        <div class="clearfloat"></div>

				<?php do_action( 'genesis_after_comment' ); ?>

			</article>
		<?php
		//* No ending </li> tag because of comment threading
	}

	/**
	 * Add yst-after-header widget support for site. If widget not active, don't display
	 */
	public function after_header_genesis() {
		if ( is_front_page() && ( is_active_sidebar( 'yoast-after-header-1' ) || is_active_sidebar( 'yoast-after-header-2' ) || is_active_sidebar( 'yoast-after-header-3' ) ) ) {
			echo '<div id="yoast-after-header-container"><div class="wrap">';

			$areas = array( 'yoast-after-header-1', 'yoast-after-header-2', 'yoast-after-header-3' );
			if ( 'sidebar-content' == genesis_site_layout() ) {
				$areas = array_reverse( $areas );
			}

			foreach ( $areas as $area ) {
				genesis_widget_area( $area, array(
					'before' => '<div id="' . $area . '" class="yoast-after-header-widget">',
					'after'  => '</div>',
				) );
			}

			echo '<div class="clearfloat"></div></div></div>';
		}

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
				$output = apply_filters( 'yst_tagline_afterheader', '<div id="yoast-tagline-after-header-container"><p class="yoast-tagline">' . $tagline . '</p></div>', $tagline );
				echo $output;
			}
		}
	}

	/**
	* Sets the default color scheme, used in Yoast_Theme class 
	* @return string Name of default color scheme
	*/
	public function set_default_color_scheme() {
		return 'WarmBlue';
	}

	/**
	 * Enqueue Google fonts
	 */
	public function load_google_fonts() {
		wp_enqueue_style( 'google-font-quattrocento_sans', '//fonts.googleapis.com/css?family=Quattrocento+Sans:400,400italic,700,700italic);', array(), $this->get_version() );
	}

	/**
	 * Add top-right widget area for search-widget
	 */
	public function add_top_right_area() {
		genesis_widget_area( 'yoast-top-right', array(
			'before' => '<div id="yoast-top-right" class="widget-area yoast-top-right-widget">',
			'after'  => '</div>',
		) );
	}

	/**
	 * Add hook to display a "back to top" link after posts or after The Loop
	 */
	public function conditional_add_back_to_top_link() {

		if ( is_single() ) {
			add_action( 'genesis_entry_footer', array( $this, 'display_back_to_top_link' ), 14 );
		}

		add_action( 'genesis_after_endwhile', array( $this, 'display_back_to_top_link' ), 14 );
	}

	/**
	 * Enable style filtering for IE8/9
	 */
	public  function conditional_comments() {
	    echo '<!--[if lte IE 9]>';
	    echo '<link href="' . get_stylesheet_directory_uri() . '/assets/css/old-ie.css" rel="stylesheet" type="text/css" />';
	    echo '<![endif]-->';
	}

	/**
	* Remove genesis sidebars and add full width widget areas
	*
	* @note This is sidebar / widget related
	*/
	public function add_full_width_sidebars() {
		if ( 'full-width-content' == genesis_site_layout() ) {
			// Remove the Primary Sidebar from the Primary Sidebar area.
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
			remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

			// Place the Secondary Sidebar into the Primary Sidebar area.
			add_action( 'genesis_sidebar', array( $this, 'show_full_width_sidebars' ) );

			// Move the featured image to the right
			add_action( 'genesis_entry_header', array( $this, 'show_full_width_image' ), 0 );
		}
	}

	/**
	* Show 'yoast-fullwidth-widgetarea' full width widget areas
	* 
	* @note This is sidebar / widget related
	*/
	public function show_full_width_sidebars() {
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-1' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-2' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-3' );
	}

	/**
	 * Show the post thumbnail in the full width archives
	 */
	public function show_full_width_image() {
		if ( ! is_single() || ! get_theme_mod( 'yst_content_archive_thumbnail' ) ) {
			return;
		}

		$thumbnail = get_the_post_thumbnail( null, 'fullwidth-thumb' );
		if ( $thumbnail ) {
			echo '<div class="alignright thumb">';
			echo $thumbnail;
			echo '</div>';
		}
	}

	/**
	 * Use the logo's set in the Child Theme Settings
	 *
	 * Adds CSS to wp_head to show either the regular logo or the mobile logo, if they are set. If they're not set, no logo-image will be used.
	 *
	 * @since 1.0.0
	 */
	public function display_logo() {
		// This holds the CSS that will be echoed
		$css = '';

		// Normal logo
		$logo = get_theme_mod( 'yst_logo' );
		if ( isset( $logo ) && ! empty ( $logo ) ) {
	        $css .= '@media(min-width: 640px){.site-header .title-area {background-image: url(' . $logo . ');}}';
		}

		// Mobile logo, positioning depends on whether the logo is wider than 230px and / or higher than 36px, if it is, alternate positioning is used.
		$mobile_logo = get_theme_mod( 'yst_mobile_logo' );
		if ( isset ( $mobile_logo ) ) {
			$yst_mobile_logo_details = get_theme_mod( 'yst_mobile_logo_details' );

			$use_alt_positioning = false;
			if ( isset( $yst_mobile_logo_details ) && is_array( $yst_mobile_logo_details ) && ( $yst_mobile_logo_details['width'] > 230 || $yst_mobile_logo_details['height'] > 36 ) ) {
				$use_alt_positioning = true;
			}

			if ( ! $use_alt_positioning ) {
				$css .= '@media(max-width: 640px){header.site-header {background:#fff url(' . $mobile_logo . ') no-repeat 50% 0;	}}';
			} else {
				$mobile_logo_height = $yst_mobile_logo_details['height'];// - 41;
				//if ( is_user_logged_in() ) {
				//	$mobile_logo_height -= 46;
				//}
				$css .= '@media(max-width: 640px){.site-container {padding-top:' . $mobile_logo_height . 'px;background:#fff url(' . $mobile_logo . ') no-repeat 50% 0;background-size: auto;}}';
			}
		}

		if ( ! empty( $css ) ) {
			echo '<style id="tailor-made-inline-css">' . $css . '</style>';
	        echo '<!--[if lte IE 8]>';
	        echo '<style type="text/css">.site-header .title-area { background-image: url(' . $logo . '); } a#sidr-left, a#sidr-right { display:none;visibility:hidden; } </style>';
	        echo '<![endif]-->';
		}
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
	public function filter_content_archive_image( $img, $args ) {
		if ( 'sidebar-content' == genesis_site_layout() || 'full-width-content' == genesis_site_layout() ) {
			if ( 'archive' == $args['context'] ) {
				$img = str_replace( 'alignleft', 'alignright', $img );
			}
		}
		return $img;
	}



}