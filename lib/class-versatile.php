<?php

// Load the Yoast_Theme class
require_once( 'framework/class-theme.php' );

/**
 * Class Versatile
 */
class Yoast_Versatile extends Yoast_Theme {

	const NAME    = 'Versatile';
	const URL     = 'http://yoast.com/wordpress/themes/versatile/';
	const VERSION = '1.0.0';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Setup the theme
	 */
	public function setup_theme() {

		// Set the theme properties
		$this->set_name( self::NAME );
		$this->set_url( self::URL );
		$this->set_version( self::VERSION );

		// Set the content width
		$this->set_content_width( 680 );

		// Set the default color scheme
		add_filter( 'yst_default_color_scheme', array( $this, 'set_default_color_scheme' ) );

		// Set the menu top offset
		add_filter( 'yoast_menu_top_offset', array( $this, 'set_menu_top_offset' ) );

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 3 );

		// Disable site layouts that are not used
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

		// Remove hook on secondary sidebar alt
		remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

		// Remove unused sidebar
		unregister_sidebar( 'sidebar-alt' );

		// Register theme sidebars
		$this->register_sidebars();

		// Do mobile menu
		$this->do_mobile_menu();

		// Image sizes
		add_image_size( 'yst-archive-thumb', 170, 0, true );
		add_image_size( 'yst-single', 620, 315, true );
		add_image_size( 'fullwidth-thumb', 290, 193, true );

		// Activate blogroll widget
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );

		// Load Google fonts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_fonts' ) );

		// Display the logo
		add_action( 'wp_head', array( $this, 'display_logo' ), 25 );

		// Check if we should add a back to top link
		add_action( 'wp_head', array( $this, 'conditional_add_back_to_top' ), 14 );

		// Display the after header widget area
		add_action( 'genesis_after_header', array( $this, 'after_header_widget_area' ), 12 );

		// Display the after header widget area
		add_action( 'genesis_after_content_sidebar_wrap', array( $this, 'full_width_sidebars' ) );

		//  Display the yoast-after-post widget area
		add_action( 'genesis_before_comments', array( $this, 'after_post_sidebar' ) );

		// Reposition the breadcrumbs
		add_action( 'genesis_after_header', 'genesis_do_breadcrumbs', 12 );
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

		// Change image output
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
		add_action( 'genesis_entry_content', array( $this, 'archive_image' ), 8 );

		// Display the image on a single page
		add_action( 'genesis_before_entry_content', array( $this, 'display_single_image' ) );

		// Display search box in header
		add_action( 'genesis_header_right', array( $this, 'header_search' ) );

		// Adds class to the body-element. Used in mobile menu.
		add_action( 'body_class', array( $this, 'add_body_class_for_tagline' ) );

		// Check if we should display the tag line
		add_action( 'genesis_before', array( $this, 'check_tag_line' ) );

		// Maybe move the primary navigation to top
		$this->maybe_move_nav_to_top();

		// Adds a class to the body-element to be used in checks for light or dark header.
		add_action( 'body_class', array( $this, 'add_body_class_for_header_style' ) );

		// Change the size of the attachement
		add_filter( 'prepend_attachment', array( $this, 'change_size_of_attachment' ) );

		// Open the header wrapper
		add_action( 'genesis_before_header', array( $this, 'open_header_wrapper' ) );

		// Close the header wrapper
		add_action( 'genesis_after_header', array( $this, 'close_div' ) );

		// Open nav wrapper
		add_action( 'genesis_after_header', array( $this, 'open_nav_wrapper' ), 11 );

		// Open de content wrapper
		add_action( 'genesis_after_header', array( $this, 'open_content_wrapper' ), 11 );

		// Close the content wrapper
		add_action( 'genesis_before_footer', array( $this, 'close_div' ) );

		// Close the sidebar wrapper
		add_action( 'genesis_after_content_sidebar_wrap', array( $this, 'close_div' ) );

		// Fake genesis custom header
		if ( is_admin() ) {
			add_action( 'current_screen', array( $this, 'fake_genesis_custom_header' ) );
		}
	}

	/**
	 * Register widget area's
	 */
	private function register_sidebars() {
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
	 * Maybe move the primary navigation to top
	 */
	private function maybe_move_nav_to_top() {
		if ( get_theme_mod( 'yst_nav_positioner' ) == 'top' ) {
			add_filter( 'body_class', array( $this, 'add_body_class_for_top_nav' ) );

			remove_action( 'genesis_after_header', 'genesis_do_nav' );
			add_action( 'genesis_before', 'genesis_do_nav' );
		}
	}

	/**
	 * Setup mobile menu
	 */
	public function do_mobile_menu() {
		add_action( 'genesis_header', array( $this, 'add_open_div_for_mobile_menu_borders' ), 11 );
		add_action( 'genesis_header', array( $this, 'add_close_div_for_mobile_menu_borders' ), 12 );
	}

	/**
	 * Set the default color scheme
	 *
	 * @return string
	 */
	public function set_default_color_scheme() {
		return 'SolidOrange';
	}

	/**
	 * Set the menu top offset
	 *
	 * @return int
	 */
	public function set_menu_top_offset() {
		$yst_nav_pos = get_theme_mod( 'yst_nav_positioner' );
		$menu_offset = 178;
		if ( $yst_nav_pos == 'top' ) {
			$menu_offset = 10;
		}
		return $menu_offset;
	}

	/**
	 * Opens div to fix borders in mobile menu
	 */
	public function add_open_div_for_mobile_menu_borders() {
		echo '<div id="mobile-menu-helper">';
	}

	/**
	 * Closes div to fix borders in mobile menu
	 */
	public function add_close_div_for_mobile_menu_borders() {
		echo '<div class="clearfloat"></div></div>';
	}

	/**
	 * Enqueue Google font
	 */
	public function load_google_fonts() {
		wp_enqueue_style( 'google-font-open_sans', '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic);', array(), $this->get_version() );
		wp_enqueue_style( 'google-font-ruda', '//fonts.googleapis.com/css?family=Ruda:400,700', array(), $this->get_version() );
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

		$mobile_logo = get_theme_mod( 'yst_mobile_logo' );
		if ( isset ( $mobile_logo ) && ! empty ( $mobile_logo ) ) {
			$css .= '@media(max-width: 640px){header.site-header {background-color:#fff; background-image: url(' . $mobile_logo . '); background-repeat: no-repeat;	}}';
		}

		if ( ! empty( $css ) ) {
			echo '<style id="versatile-inline-css">' . $css . '</style>';
		}
	}

	/**
	 * Add a genesis footer action if it's a 'single' page
	 */
	public function conditional_add_back_to_top() {
		if ( is_single() ) {
			add_action( 'genesis_entry_footer', array( $this, 'display_back_to_top_link' ), 14 );
		}
	}

	/**
	 * Add yst-after-header widget support for site. If widget not active, don't display
	 *
	 * @note This is sidebar / widget related
	 */
	public function after_header_widget_area() {
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
	 * Display full width widget areas in genesis_after_content_sidebar_wrap
	 *
	 * @note This is sidebar / widget related
	 */
	public function full_width_sidebars() {
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
	 * Display the yoast-after-post widget area
	 *
	 * @note This is sidebar / widget related
	 */
	public function after_post_sidebar() {
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
	 * Display the image thumb on front page
	 */
	public function archive_image() {
		global $post;

		if ( ! is_front_page() ) {
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
		$size         = 'yst-archive-thumb'; // Change this to whatever add_image_size you want
		$default_attr = array(
				'class' => "align{$align} attachment-{$size} {$size}",
				'alt'   => $post->post_title,
				'title' => $post->post_title,
		);

		printf( '<a href="%s" title="%s" class="yst-archive-image-link">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
	}

	/**
	 * Displays the single image
	 */
	public function display_single_image() {
		global $post;

		// Only on single
		if ( ! is_single() ) {
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
		$size         = 'yst-archive-thumb'; // Change this to whatever add_image_size you want
		$default_attr = array(
				'class' => "yst-single-image attachment-{$size} {$size}",
				'alt'   => $post->post_title,
				'title' => $post->post_title,
		);

		echo genesis_get_image( array( 'size' => 'yst-single', 'attr' => $default_attr ) );
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
	 * Display search box in header
	 */
	public function header_search() {
		echo genesis_search_form();
	}

	/**
	 * Adds class to the body-element. Used in mobile menu.
	 *
	 * @param array $classes Contains all the current body-classes
	 *
	 * @return array The updated array of body-classes
	 */
	public function add_body_class_for_tagline( $classes ) {
		$tagline_positioner = get_theme_mod( 'yst_tagline_positioner' );
		$tagline            = html_entity_decode( get_bloginfo( 'description' ) );
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

	/**
	 * Check if we should display the tag line
	 */
	public function check_tag_line() {
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
				add_action( 'genesis_header', array( $this, 'show_tag_line' ), 13 );
			}
		}
	}

	/**
	 * Display the tagline
	 */
	public function show_tag_line() {
		echo '<div class="tagline_top tagline_' . get_theme_mod( 'yst_tagline_positioner', 'top_right' ) . '">' . html_entity_decode( get_bloginfo( 'description' ) ) . '</div>';
	}

	/**
	 * Adds a class to the body-element to be used in checks for the location of the menu.
	 *
	 * @param array $classes contains all the current classes
	 *
	 * @return array Updated array of classes
	 */
	public function add_body_class_for_top_nav( $classes ) {
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
	public function add_body_class_for_header_style( $classes ) {
		$header_style = get_theme_mod( 'yst_header_color_picker', 'light' );

		if ( 'light' == $header_style ) {
			$classes[] = 'header-light';
		} else {
			$classes[] = 'header-dark';
		}

		return $classes;
	}

	/**
	 * Change the size of the attachement
	 *
	 * @return string
	 */
	public function change_size_of_attachment() {
		return '<p class="attachment">' . wp_get_attachment_link( 0, 'full', false ) . '</p>';
	}

	/**
	 * Open the header wrapper
	 */
	public function open_header_wrapper() {
		echo '<div id="header-wrapper">';
	}

	/**
	 * Open the nav wrapper
	 */
	public function open_nav_wrapper() {
		echo '<div id="center-wrapper">';
	}

	/**
	 * Open content wrapper
	 */
	public function open_content_wrapper() {
		echo '<div id="afterheader-content-wrapper">';
	}

}