<?php

// Load the Yoast_Theme class
require_once( 'framework/class-theme.php' );

/**
 * Class Versatile
 */
class Yoast_Strategy extends Yoast_Theme {

	const NAME    = 'Strategy';
	const URL     = 'https://yoast.com/wordpress/themes/strategy/';
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
		$this->set_content_width( 645 );

		// Used for defaults in for instance the banner widget.
		define( 'YST_SIDEBAR_WIDTH', 300 );

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 3 );
		add_theme_support( 'genesis-connect-woocommerce' );

		// Disable site layouts that must not be used
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

		// Register theme sidebars
		$this->register_sidebars();

		// Unregister sidebars
		unregister_sidebar( 'sidebar-alt' );

		// Image Sizes
		add_image_size( 'yst-archive-thumb', 180, 0, true );			// Used on archive pages except frontpage
		add_image_size( 'yst-frontpage-thumb', 255, 0, true );			// Used on front page only
		add_image_size( 'fullwidth-thumb', 300, 200, true );			// Used on full width pages only

		// Change image output
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
		add_action( 'genesis_entry_content', array( $this, 'archive_image' ), 8 );

		// Display search box in header
		add_action( 'genesis_header_right', array( $this, 'header_search' ), 11 );

		// Activate blogroll widget
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );

		// change default image alignment
		add_filter( 'genesis_get_image', array( $this, 'filter_content_archive_image' ), 10, 2 );

		// Load Google Fonts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_fonts' ) );

		// Set the default color scheme
		add_filter( 'yst_default_color_scheme', array( $this, 'set_default_color_scheme' ) );

		// Display logo
		add_action( 'wp_head', array( $this, 'display_logo' ) );

		// Add back to top link (conditional)
		add_action( 'wp_head', array( $this, 'conditional_add_back_to_top_link' ), 14 );

		// Add conditional comments (ie)
		add_action( 'wp_head', array( $this, 'conditional_comments' ) );

		// Add support for yoast after header widget on frontpage
		add_action( 'genesis_after_header', array( $this, 'show_after_header' ) );

		// Conditionally add full width sidebars
		add_action( 'genesis_after_header', array( $this, 'add_full_width_sidebars' ) );

		// Reposition the breadcrumbs
		add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

		// Show menubar at the set position
		add_action( 'wp_head', array( $this, 'show_primary_nav' ) );

		// Adds class to the body-element. Used in sticky menu.
		add_action( 'body_class', array( $this, 'add_body_class_for_sticky_nav' ) );

		// Changes styling of galleries
		add_filter( 'gallery_style', array( $this, 'change_gallery_css' ) );
	}

	/**
	 * Register widget area's
	 */
	public function register_sidebars() {

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-fp-1',
			'name'        => __( 'After Header 1 for Front Page', 'yoast-theme' ),
			'description' => __( 'After Header 1 widget area. Will only display on the front page.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-fp-2',
			'name'        => __( 'After Header 2 for Front Page', 'yoast-theme' ),
			'description' => __( 'After Header 2 widget area. Will only display on the front page.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-fp-3',
			'name'        => __( 'After Header 3 for Front Page', 'yoast-theme' ),
			'description' => __( 'After Header 3 widget area. Will only display on the front page.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-1',
			'name'        => __( 'After Header 1', 'yoast-theme' ),
			'description' => __( 'After Header 1 widget area. Will display on all pages, except the front page.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-2',
			'name'        => __( 'After Header 2', 'yoast-theme' ),
			'description' => __( 'After Header 2 widget area. Will display on all pages, except the front page.', 'yoast-theme' ),
		) );

		genesis_register_sidebar( array(
			'id'          => 'yoast-after-header-3',
			'name'        => __( 'After Header 3', 'yoast-theme' ),
			'description' => __( 'After Header 3 widget area. Will display on all pages, except the front page.', 'yoast-theme' ),
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
	 * Display the image thumb on front page
	 */
	public function archive_image() {
		global $post;

		if ( ! has_post_thumbnail() ) {
			return;
		}

		if ( is_single() ) {
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
		$size         = 'yst-archive-thumb'; // Change this to whatever add_image_size you want
		if ( is_front_page() ) {
			$size = 'yst-frontpage-thumb';
		}
		$default_attr = array(
			'class' => "align{$align} attachment-{$size} {$size}",
			'alt'   => $post->post_title,
			'title' => $post->post_title,
		);

		printf( '<a href="%s" title="%s" class="yst-archive-image-link">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
	}

	/**
	 * Comment Callback Function
	 *
	 * @param stdClass $comment
	 * @param array    $args
	 * @param integer  $depth
	 *
	 * @todo try to get this into Yoast_Theme
	 * @todo create a template partial containing this HTML block
	 */
	public function comment_callback( $comment, $args, $depth ) {
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
							echo ' <span class="post_author_comment_wrap"><span class="post_author_comment_img">&nbsp;</span><span class="post_author_comment">' . __( 'Author', 'yoast-theme' ) . '</span></a>';
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
	 * Adds the tagline and the after-header widgetareas to the page.
	 * The order of the items is based on the settings in the customizer. If the primary navigation is at the lower setting (below the logo) the tagline can only be below the after-header-widgets (setting: middle).
	 *
	 * @since 1.0
	 */
	public function show_after_header() {
		$tagline_position = 'middle';

		if ( ( get_theme_mod( 'yst_primary_nav_position' ) == 'topright' || ! has_nav_menu( 'primary' ) ) && get_theme_mod( 'yst_tagline_positioner' ) == 'top' ) {
			$tagline_position = 'top';
		}
		if ( $tagline_position == 'top') {
			$this->show_tagline( $tagline_position );
		}
		if ( is_home() || is_front_page() ) {
			$this->show_after_header_widgetareas_front_page();
		} else {
			$this->show_after_header_widgetareas();
		}
		if ( $tagline_position != 'top') {
			$this->show_tagline( $tagline_position );
		}
	}

	/**
	 * Sets the default color scheme, used in Yoast_Theme class
	 * @return string Name of default color scheme
	 */
	public function set_default_color_scheme() {
		return 'GreenBright';
	}

	/**
	 * Enqueue Google fonts
	 */
	public function load_google_fonts() {
		wp_enqueue_style( 'google-font-quattrocento_sans', '//fonts.googleapis.com/css?family=Quattrocento+Sans:400,400italic,700,700italic);', array(), $this->get_version() );
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
	public function conditional_comments() {
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
		$logo = get_theme_mod( 'yst_logo', get_stylesheet_directory_uri() . '/assets/images/logo.png' );
		if ( isset( $logo ) && ! empty ( $logo ) ) {
			$css .= '@media(min-width: 640px){.site-header .title-area {background-image: url("' . $logo . '");}}';
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
				$css .= '@media(max-width: 640px){header.site-header {background:#fff url(' . $mobile_logo . ') no-repeat 50% 100%;	}}';
			} else {
				$mobile_logo_height = $yst_mobile_logo_details['height']; // - 41;
				//if ( is_user_logged_in() ) {
				//	$mobile_logo_height -= 46;
				//}
				$css .= '@media(max-width: 640px){.site-container {padding-top:' . $mobile_logo_height . 'px;background:#fff url(' . $mobile_logo . ') no-repeat 50% 0;background-size: auto;}}';
			}
		}

		if ( ! empty( $css ) ) {
			echo '<style id="strategy-inline-css">' . $css . '</style>';
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

	/**
	 * Show menu bar
	 *
	 * @since 1.0
	 */
	public function show_primary_nav() {
		if ( 'topright' == get_theme_mod( 'yst_primary_nav_position' ) ) {
			remove_action( 'genesis_after_header', 'genesis_do_nav' );
			add_action( 'genesis_header_right', 'genesis_do_nav' );
		}
		add_filter( 'genesis_structural_wrap-menu-primary', array( $this, 'add_search_to_nav' ), 10, 2 );

	}

	/**
	 * Print the WP search form in the nav wrapper
	 */
	public function add_search_to_nav( $output, $original_output ) {
		if ( 'close' == $original_output ) {
			$output = genesis_search_form() . $output;
		}
		return $output;
	}

	/**
	 * Display search box in header
	 */
	public function header_search() {
		echo genesis_search_form() . '<div class="clearfloat"></div>';
	}

	/**
	 * Shows the after header widgetareas for the front page
	 * These widgetareas differ from the ones shown on all other pages.
	 *
	 * @since 1.0
	 */
	private function show_after_header_widgetareas_front_page() {
		if ( is_front_page() && ( is_active_sidebar( 'yoast-after-header-fp-1' ) || is_active_sidebar( 'yoast-after-header-fp-2' ) || is_active_sidebar( 'yoast-after-header-fp-3' ) ) ) {
			echo '<div id="yoast-after-header-container"><div class="wrap">';

			$areas = array( 'yoast-after-header-fp-1', 'yoast-after-header-fp-2', 'yoast-after-header-fp-3' );

			foreach ( $areas as $area ) {
				genesis_widget_area( $area, array(
					'before' => '<div id="' . $area . '" class="yoast-after-header-fp-widget">',
					'after'  => '</div>',
					'show_inactive' => 1,
				) );
			}

			echo '<div class="clearfloat"></div></div></div>';
		}
	}

	/**
	 * Adds tagline to the page based on the settings in the customizer.
	 *
	 * @since 1.0
	 *
	 * @param string $location string used for customizing the class of the container.
	 */
	private function show_tagline( $location ) {
		$location = ( sanitize_text_field( $location ) == 'top' ? 'top' : 'middle' );
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
				$output = apply_filters( 'yst_tagline_afterheader', '<div id="yoast-tagline-after-header-container" class="tagline-'.$location.'"><p class="yoast-tagline">' . $tagline . '</p></div>', $tagline );
				echo $output;
			}
		}
	}

	/**
	 * Shows the after header widgetareas
	 * These widgetareas differ from the one shown on the front page.
	 *
	 * @since 1.0
	 */
	private function show_after_header_widgetareas() {
		if ( ! is_front_page() && ( is_active_sidebar( 'yoast-after-header-1' ) || is_active_sidebar( 'yoast-after-header-2' ) || is_active_sidebar( 'yoast-after-header-3' ) ) ) {
			echo '<div id="yoast-after-header-container"><div class="wrap">';

			$areas = array( 'yoast-after-header-1', 'yoast-after-header-2', 'yoast-after-header-3' );

			foreach ( $areas as $area ) {
				genesis_widget_area( $area, array(
					'before' => '<div id="' . $area . '" class="yoast-after-header-widget">',
					'after'  => '</div>',
					'show_inactive' => 1,
				) );
			}

			echo '<div class="clearfloat"></div></div></div>';
		}
	}

	/**
	 * Adds a class to the body-element to be used in checks for location of primary navigation (menu).
	 *
	 * @param array $classes contains all the current classes
	 *
	 * @return array Updated array of classes
	 */
	public function add_body_class_for_sticky_nav( $classes ) {
		if ( get_theme_mod( 'yst_primary_nav_position' ) == 'topright' ) {
			$classes[] = 'menu-right';
		}
		return $classes;
	}

	public function change_gallery_css( $css ) {
		return preg_replace( "/margin: auto;/", 'margin: 0;', $css );
	}
}