<?php

// Load the Yoast_Theme class
require_once( 'framework/class-theme.php' );

/**
 * Class Vintage
 */
class Yoast_Vintage extends Yoast_Theme {

	const NAME    = 'Vintage';
	const URL     = 'http://yoast.com/wordpress/themes/vintage/';
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

		// Set the default color scheme
		add_filter( 'yst_default_color_scheme', array( $this, 'set_default_color_scheme' ) );

		// Set the menu top offset
		add_filter( 'yoast_menu_top_offset', array( $this, 'set_menu_top_offset' ) );

		// Set custom header
		$this->set_custom_header();

		// Add support for 3-column footer widgets
		add_theme_support( 'genesis-footer-widgets', 3 );

		// Disable site layouts that are not used
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

		// Remove hook on secondary sidebar alt
		remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );
		unregister_sidebar( 'sidebar-alt' );

		// Remove unused sidebar
		if ( 'center' == get_theme_mod( 'yst_logo_position' ) ) {
			unregister_sidebar( 'header-right' );
		}

		// Register theme sidebars
		$this->register_sidebars();

		// Image sizes
		add_image_size( 'archive-thumb', 180, 120, true );
		add_image_size( 'sidebarfeatured-thumb', 230, 153, true );
		add_image_size( 'fullwidth-thumb', 290, 193, true );

		// Change image output
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
		add_action( 'genesis_entry_content', array( $this, 'do_post_image' ), 8 );

		// Activate blogroll widget
		add_filter( 'pre_option_link_manager_enabled', '__return_true' );

		// Load Google fonts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_google_fonts' ) );

		// Add the header image
		add_filter( 'genesis_do_nav', array( $this, 'add_header_image' ), 99 );

		// Display logo
		add_action( 'wp_head', array( $this, 'display_logo' ) );

		// Check if we should add a back to top link
		add_action( 'wp_head', array( $this, 'add_back_to_top' ), 14 );

		// Move Tag Line
		remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
		add_action( 'genesis_header', array( $this, 'show_tag_line' ), 14 );

		// Change news letter button label
		add_filter( 'yoast_theme_newsletter_submit_button_text', array( $this, 'change_newsletter_submit_button_text' ) );

		// Author gravatar size
		add_filter( 'genesis_author_box_gravatar_size', array( $this, 'author_box_gravatar_size' ) );

		// Add a read all posts and social links to the author box
		add_filter( 'genesis_author_box', array( $this, 'modify_genesis_author_box' ) );

		// Add search box to navigation
		add_filter( 'genesis_do_nav', array( $this, 'add_search_in_nav' ), 90 );

		// Add conditional comments
		add_action( 'wp_head', array( $this, 'conditional_comments' ) );

		// Alter the Genesis image
		add_filter( 'genesis_get_image', array( $this, 'filter_content_archive_image' ), 10, 2 );

		// Bind full width sidebars
		add_action( 'genesis_after_header', array( $this, 'bind_full_width_sidebars' ) );

		// Alter the body class
		add_filter( 'body_class', array( $this, 'alter_body_class' ) );

		// Reposition the breadcrumbs
		add_action( 'genesis_after_header', 'genesis_do_breadcrumbs' );
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

	}

	/**
	 * Register widget area's
	 */
	private function register_sidebars() {
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
	 * Set the custom header
	 */
	private function set_custom_header() {
		$args = array(
				'width'           => 940,
				'height'          => 280,
				'flex-height'     => true,
				'header_image'    => get_stylesheet_directory_uri() . '/assets/images/header-vintage.png',
				'uploads'         => true,
				'no_header_text'  => true,
				'header_callback' => '__return_true',
		);
		add_theme_support( 'genesis-custom-header', $args );
	}

	/**
	 * Enqueue Google font
	 */
	public function load_google_fonts() {
		wp_enqueue_style( 'google-font-quattrocento_sans', '//fonts.googleapis.com/css?family=Quattrocento+Sans:400,400italic,700,700italic);', array(), $this->get_version() );
	}

	/**
	 * Set the default color scheme
	 *
	 * @return string
	 */
	public function set_default_color_scheme() {
		return 'BeachGreen';
	}

	/**
	 * Set the menu top offset
	 *
	 * @return int
	 */
	public function set_menu_top_offset() {
		return 30;
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
	public function comment_callback( $comment, $args, $depth ) {
		global $post;
		$GLOBALS['comment'] = $comment; ?>

	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<article <?php echo genesis_attr( 'comment' ); ?>>

			<?php do_action( 'genesis_before_comment' ); ?>

			<div class="avatar">
				<?php
				$avatar_size = 1 == $depth ? 95 : 65;
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
	 * Add the header image
	 */
	public function add_header_image( $nav_output ) {
		$nav_output .= '<div id="header-image"></div>';

		return $nav_output;
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
			$css .= '@media(min-width: 640px){.site-header .title-area{background-image: url(' . $logo . ');}}';
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
				$css .= '@media(max-width: 640px){header.site-header, body.logo-position-center.logo-frame .site-header, body.sticky-menu.logo-position-center.logo-frame .site-header, body.sticky-menu.logo-position-left.logo-frame .site-header {background-color:#fff;background-image: url(' . $mobile_logo . ');background-repeat:no-repeat;background-position:50% 2px;}}';
			} else {
				$mobile_logo_height = $yst_mobile_logo_details['height'] - 41;
				if ( is_user_logged_in() ) {
					$mobile_logo_height -= 46;
				}
				$css .= '@media(max-width:640px){.site-container{padding-top:' . $mobile_logo_height . 'px;background-color::#fff;background-image:url(' . $mobile_logo . ');background-repeat:no-repeat;background-position:50% 2px;background-size: auto;}}';
			}
		}

		if ( get_header_image() ) {
			$css .= '#header-image{background-image: url(' . get_header_image() . ');height:' . get_custom_header()->height . 'px}';
		}

		if ( ! empty( $css ) ) {
			echo '<style id="tailor-made-inline-css">' . $css . '</style>';
			echo '<!--[if lte IE 8]>';
			echo '<style type="text/css">.site-header .title-area { background-image: url(' . $logo . '); } a#sidr-left, a#sidr-right { display:none;visibility:hidden; } </style>';
			echo '<![endif]-->';
		}
	}

	/**
	 * Add a genesis footer action for the back to top link
	 */
	public function add_back_to_top() {
		if ( is_single() ) {
			add_action( 'genesis_entry_footer', array( $this, 'display_back_to_top_link' ), 14 );
		}
		add_action( 'genesis_after_endwhile', array( $this, 'display_back_to_top_link' ), 14 );
	}

	/**
	 * Display the tag line
	 *
	 * @todo check if this can be moved to base theme
	 */
	public function show_tag_line() {
		$tagline = get_bloginfo( 'description' );

		if( empty( $tagline ) ) { 
			return;
		}

		echo '<p class="site-description" itemprop="description">' . html_entity_decode( $tagline ) . '</p>';
	}

	/**
	 * Change the submit button on the newsletter widget
	 *
	 * @return string
	 */
	public function change_newsletter_submit_button_text() {
		return '&raquo;';
	}

	/**
	 * Change the avatar size in the author box
	 *
	 * @return string
	 */
	public function author_box_gravatar_size() {
		return '95';
	}

	/**
	 * Add a read all posts and social links to the author box
	 *
	 * @param string $box The current author box
	 *
	 * @return string
	 */
	public function modify_genesis_author_box( $box ) {
		global $authordata;

		$out = '<p><a href="' . get_author_posts_url( $authordata->ID, $authordata->user_nicename ) . '">' . sprintf( __( 'View all posts by %s &raquo;', 'yoast-theme' ), get_the_author() ) . '</a></p>';

		$social = '';
		foreach ( array( 'facebook', 'twitter', 'linkedin', 'pinterest', 'googleplus' ) as $cm ) {
			$url = get_user_meta( $authordata->ID, $cm, true );
			if ( 'twitter' == $cm ) {
				$url = 'https://twitter.com/' . $url;
			}
			$rel = '';
			if ( 'googleplus' == $cm ) {
				$rel = 'rel="author"';
			}
			$social .= '<li class="' . $cm . '"><a ' . $rel . ' href="' . $url . '">&nbsp;</a></li>';
		}

		if ( ! empty( $social ) ) {
			$out = $out . '<ul class="author_social">' . $social . '</ul><div class="floatclearing"></div>';
		}

		$box = preg_replace( '|(</div></section>)|', $out . '</div></section>', $box );

		return $box;
	}

	/**
	 * Add a search box in the navigation based on the theme mod
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	public function add_search_in_nav( $output ) {
		if ( get_theme_mod( 'yst_theme_search_in_nav', true ) ) {
			$output = str_replace( '<div class="wrap">', '<div class="wrap"><div class="nav-search">' . genesis_search_form() . '</div>', $output );
		}

		return $output;
	}

	/**
	 * Enable style filtering for IE8/9
	 */
	public function conditional_comments() {
		echo '<!--[if lte IE 9]>';
		echo '<link href="' . get_stylesheet_directory_uri() . '/assets/css/old-ie.css" rel="stylesheet" type="text/css">';
		echo '<![endif]-->';
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
	 * Display the image thumb on front page
	 */
	public function do_post_image() {
		global $post;
		
		if ( ! has_post_thumbnail() ) {
			return;
		}

		// Set correct image alignment
		$align = 'right';

		//setup thumbnail image args to be used with genesis_get_image();
		$size         = ( is_single() ) ? null : 'archive-thumb'; // Change this to whatever add_image_size you want
		$default_attr = array(
				'class' => "align{$align}",
				'alt'   => $post->post_title,
				'title' => $post->post_title,
		);

		printf( '<a href="%s" title="%s" class="post-thumbnail yst-archive-image-link">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
	}

	/**
	 * Bind the full width sidebars
	 */
	public function bind_full_width_sidebars() {
		if ( 'full-width-content' == genesis_site_layout() ) {
			// Remove the Primary Sidebar from the Primary Sidebar area.
			remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
			remove_action( 'genesis_sidebar_alt', 'genesis_do_sidebar_alt' );

			// Place the Secondary Sidebar into the Primary Sidebar area.
			add_action( 'genesis_sidebar', array( $this, 'yoast_do_fullwidth_sidebars' ) );
		}
	}

	/**
	 * Display the Yoast fullwidth sidebars
	 */
	public function do_fullwidth_sidebars() {
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-1' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-2' );
		dynamic_sidebar( 'yoast-fullwidth-widgetarea-3' );
	}

	/**
	 * Add a body class that determines whether the logo is on the left or in the center.
	 *
	 * @param array $classes
	 *
	 * @return array $classes
	 */
	public function alter_body_class( $classes ) {
		$classes[] = 'logo-position-' . get_theme_mod( 'yst_logo_position', 'left' );

		if ( get_theme_mod( 'yst_logo_frame', 'on' ) )
			$classes[] = 'logo-frame';

		return $classes;
	}

}
