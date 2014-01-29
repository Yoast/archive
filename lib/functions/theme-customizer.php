<?php

/**
 * Class Yoast_Theme_Customizer
 *
 * Hooks the settings to the WordPress theme customizer
 */
class Yoast_Theme_Customizer {

	/**
	 * Class constructor
	 */
	function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'style' ), 20 );
		add_action( 'customize_preview_init', array( $this, 'enqueue' ) );

		// Add customizer ajax hook - Update image details
		add_action( 'customize_save_after', array( $this, 'update_image_details' ), 1 );
	}

	/**
	 * Enqueue scripts
	 */
	function enqueue() {
		wp_enqueue_script( 'yst-theme-customizer', get_stylesheet_directory_uri() . '/lib/js/theme-customizer.js?v=' . filemtime( get_stylesheet_directory() . '/lib/js/theme-customizer.js' ), array( 'jquery', 'customize-preview' ), '0.1', true );
	}

	/**
	 * Update image details
	 */
	public function update_image_details() {

		// Decode JSON
		$customized = json_decode( stripslashes( $_POST['customized'] ) );

		// Set context
		$context = 'yst_mobile_logo_details';

		// Set src
		if ( ! empty( $customized->yst_mobile_logo ) ) {
			$src = $customized->yst_mobile_logo;

			// We might need more image details, so storing these in a separate theme mod for easy access.
			global $wpdb;
			$img_att = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid = '%s' LIMIT 1", $src ) );
			if ( $img_att ) {
				$image_details = wp_get_attachment_metadata( $img_att );

				if ( isset ( $image_details ) && ! empty ( $image_details ) && is_array( $image_details ) ) {
					// Store theme mod
					set_theme_mod( $context, $image_details );
				}
			}
		} else {
			// Delete details
			remove_theme_mod( $context );
		}

	}

	/**
	 * Outputs customizer specific styles for our custom controls
	 */
	function style() {
		?>
		<style>
			.image-input-label {
				position: relative;
				height: 95px;
				display: block;
				vertical-align: text-top;
			}

			.image-input-label input {
				position: absolute;
				top: 10px;
			}

			.image-input-label img {
				position: relative;
				left: 20px;
				width: 100px;
				height: 90px;
			}

			.customize-control-description {
				display: block;
				margin: 5px 0;
			}
		</style>
	<?php
	}

	/**
	 * Customize the customizer
	 *
	 * @param object $wp_customize
	 */
	function customize_register( $wp_customize ) {
		/**
		 * Add settings
		 */
		$wp_customize->add_setting(
				'yst_nav_positioner',
				array(
						'default'   => 'content',
						'transport' => 'refresh'
				)
		);

		$wp_customize->add_setting(
				'yst_tagline_positioner',
				array(
						'default'   => 'top_right',
						'transport' => 'postMessage'
				)
		);

		$wp_customize->add_setting(
				'yst_header_color_picker',
				array(
						'default'   => 'dark',
						'transport' => 'postMessage'
				)
		);

		$wp_customize->add_setting(
				'yst_colour_scheme',
				array(
						'default'   => 'BrightBlue',
						'transport' => 'postMessage'
				)
		);

		$wp_customize->add_setting(
				'yst_logo',
				array(
						'default'   => get_stylesheet_directory_uri() . '/assets/images/logo.png',
						'transport' => 'postMessage'
				)
		);

		$wp_customize->add_setting(
				'yst_mobile_logo',
				array(
						'default'   => get_stylesheet_directory_uri() . '/assets/images/logo-mobile.png',
						'transport' => 'postMessage'
				)
		);

		$wp_customize->add_setting(
				'yst_default_layout',
				array(
						'default'   => 'content-sidebar',
						'transport' => 'refresh'
				)
		);

		$wp_customize->add_setting(
				'yst_footer',
				array(
						'default'   => sprintf( '[footer_copyright before="%s "] &#x000B7; [footer_childtheme_link before="" after=" %s"] Genesis &#x000B7; [footer_wordpress_link] &#x000B7;', __( 'Copyright', 'genesis' ), __( 'on', 'genesis' ) ),
						'transport' => 'refresh'
				)
		);

		$wp_customize->add_setting(
				'yst_content_archive',
				array(
						'default'   => 'excerpts',
						'transport' => 'refresh'
				)
		);

		$wp_customize->add_setting(
				'yst_content_archive_thumbnail',
				array(
						'default'   => 'on',
						'transport' => 'refresh'
				)
		);

		$wp_customize->add_setting(
				'yst_posts_nav',
				array(
						'default'   => 'numeric',
						'transport' => 'refresh'
				)
		);

		$breadcrumb_settings = array(
				'yst_breadcrumb_home'       => array( 'label' => __( 'Homepage', 'yoast-theme' ), 'default' => false ),
				'yst_breadcrumb_front_page' => array( 'label' => __( 'Front Page', 'yoast-theme' ), 'default' => false ),
				'yst_breadcrumb_posts_page' => array( 'label' => __( 'Posts Page', 'yoast-theme' ), 'default' => false ),
				'yst_breadcrumb_single'     => array( 'label' => __( 'Single Posts', 'yoast-theme' ), 'default' => true ),
				'yst_breadcrumb_page'       => array( 'label' => __( 'Single Pages', 'yoast-theme' ), 'default' => true ),
				'yst_breadcrumb_archive'    => array( 'label' => __( 'Archive Pages', 'yoast-theme' ), 'default' => true ),
				'yst_breadcrumb_404'        => array( 'label' => __( '404 Pages', 'yoast-theme' ), 'default' => true ),
				'yst_breadcrumb_attachment' => array( 'label' => __( 'Attachment Pages', 'yoast-theme' ), 'default' => true )
		);

		if ( 'page' == get_option( 'show_on_front' ) ) {
			unset( $breadcrumb_settings['yst_breadcrumb_home'] );
		} else {
			unset( $breadcrumb_settings['yst_breadcrumb_front_page'], $breadcrumb_settings['yst_breadcrumb_posts_page'] );
		}

		foreach ( $breadcrumb_settings as $breadcrumb_setting => $values ) {
			$wp_customize->add_setting(
					$breadcrumb_setting,
					array(
							'default'   => $values['default'],
							'transport' => 'refresh'
					)
			);
		}

		$tagline_settings = array(
				'yst_tagline_home'       => array( 'label' => __( 'Homepage', 'yoast-theme' ), 'default' => true ),
				'yst_tagline_front_page' => array( 'label' => __( 'Front Page', 'yoast-theme' ), 'default' => true ),
				'yst_tagline_posts_page' => array( 'label' => __( 'Posts Page', 'yoast-theme' ), 'default' => false ),
				'yst_tagline_singular'   => array( 'label' => __( 'Single Posts, Pages & Post Types', 'yoast-theme' ), 'default' => false ),
				'yst_tagline_archive'    => array( 'label' => __( 'Archive Pages', 'yoast-theme' ), 'default' => false ),
				'yst_tagline_404'        => array( 'label' => __( '404 Pages', 'yoast-theme' ), 'default' => false ),
				'yst_tagline_attachment' => array( 'label' => __( 'Attachment Pages', 'yoast-theme' ), 'default' => false )
		);

		if ( 'page' == get_option( 'show_on_front' ) ) {
			unset( $tagline_settings['yst_tagline_home'] );
		} else {
			unset( $tagline_settings['yst_tagline_front_page'], $tagline_settings['yst_tagline_posts_page'] );
		}

		foreach ( $tagline_settings as $tagline_setting => $values ) {
			$wp_customize->add_setting(
					$tagline_setting,
					array(
							'default'   => $values['default'],
							'transport' => 'refresh'
					)
			);
		}

		/**
		 * Start adding sections
		 */
		$wp_customize->add_section(
				'yst_color_schemes',
				array(
						'title'       => __( 'Color Scheme', 'genesis' ),
						'description' => __( 'Determine the color scheme for your site. You can change as often as you want!', 'yoast-theme' ),
						'priority'    => 61
				)
		);

		$wp_customize->add_section(
				'yst_genesis_layout',
				array(
						'title'       => __( 'Layout', 'genesis' ),
						'description' => __( 'This determines the default layout of your site. You can override this on individual pages.', 'yoast-theme' ),
						'priority'    => 61
				)
		);

		$wp_customize->add_section(
				'yst_genesis_content_archives',
				array(
						'title'       => __( 'Content Archives', 'genesis' ),
						'description' => __( 'Determine what your archive sections will look like:', 'yoast-theme' ),
						'priority'    => 62
				)
		);

		$wp_customize->add_section(
				'yst_genesis_breadcrumbs',
				array(
						'title'    => __( 'Breadcrumbs', 'genesis' ),
						'priority' => 63
				)
		);

		// This adds a new section for Logo uploads
		$wp_customize->add_section(
				'yst_logos',
				array(
						'title'       => 'Logo',
						'description' => __( 'Upload a logo and a mobile logo here:', 'yoast-theme' ),
						'priority'    => 81
				)
		);

		/**
		 * Start adding controls
		 */
		$i = 1;
		foreach ( $breadcrumb_settings as $breadcrumb_setting => $values ) {
			$wp_customize->add_control(
					new Yoast_Customize_Control(
							$wp_customize,
							$breadcrumb_setting,
							array(
									'section'     => 'yst_genesis_breadcrumbs',
									'label'       => $values['label'],
									'type'        => 'checkbox',
									'description' => ( ( 1 == $i ) ? '<strong>' . __( 'Show breadcrumbs on:' ) . '</strong>' : '' ),
									'priority'    => $i
							)
					)
			);
			$i ++;
		}

		$i = 100;
		foreach ( $tagline_settings as $tagline_setting => $values ) {
			$wp_customize->add_control(
					new Yoast_Customize_Control(
							$wp_customize,
							$tagline_setting,
							array(
									'section'     => 'title_tagline',
									'setting'     => $tagline_setting,
									'label'       => $values['label'],
									'description' => ( ( 100 == $i ) ? '<strong>' . __( 'Show tagline on:' ) . '</strong>' : '' ),
									'type'        => 'checkbox',
									'priority'    => $i
							)
					)
			);
			$i ++;
		}

		$wp_customize->add_control(
				'yst_content_archive',
				array(
						'section' => 'yst_genesis_content_archives',
						'label'   => __( 'Content Archive Settings', 'yoast-theme' ),
						'type'    => 'radio',
						'choices' => array(
								'full'     => __( 'Display post content', 'genesis' ),
								'excerpts' => __( 'Display post excerpts', 'genesis' ),
						)
				)
		);

		$wp_customize->add_control(
				'yst_content_archive',
				array(
						'section' => 'yst_genesis_content_archives',
						'label'   => __( 'Content Archive Settings', 'yoast-theme' ),
						'type'    => 'radio',
						'choices' => array(
								'full'     => __( 'Display post content', 'genesis' ),
								'excerpts' => __( 'Display post excerpts', 'genesis' ),
						)
				)
		);

		$wp_customize->add_control(
				'yst_content_archive_thumbnail',
				array(
						'section' => 'yst_genesis_content_archives',
						'label'   => __( 'Include the Featured Image?', 'genesis' ),
						'type'    => 'checkbox'
				)
		);

		$wp_customize->add_control(
				'yst_posts_nav',
				array(
						'section' => 'yst_genesis_content_archives',
						'label'   => __( 'Post Navigation Technique', 'yoast-theme' ),
						'type'    => 'radio',
						'choices' => array(
								'prev-next' => __( 'Previous / Next', 'genesis' ),
								'numeric'   => __( 'Numeric', 'genesis' ),
						)
				)
		);

		foreach ( genesis_get_layouts( 'site' ) as $id => $data ) {
			$layouts[$id] = '<img src="' . $data['img'] . '" alt="' . $data['label'] . '"/>';
		}

		$wp_customize->add_control(
				new Yoast_Radio_Image_Control(
						$wp_customize,
						'yst_default_layout',
						array(
								'label'    => __( 'Choose the default layout', 'yoast-theme' ),
								'settings' => 'yst_default_layout',
								'section'  => 'yst_genesis_layout',
								'choices'  => $layouts
						)
				)
		);
		foreach ( glob( CHILD_DIR . "/assets/css/*.css" ) as $file ) {

			// Clean out the path
			$file = str_replace( CHILD_DIR . "/assets/css/", "", $file );

			preg_match( '/(.+).css/', $file, $matches );
			if ( isset ( $matches[1] ) ) {
				if ( in_array( $matches[1], array( 'forms', 'editor-style' ) ) ) {
					continue;
				}

				$colours[$matches[1]] = trim( preg_replace( '/([A-Z])/', ' $1', $matches[1] ) );
			}
		}

		// This control goes into the default Color section
		$wp_customize->add_control(
				'yst_colour_scheme',
				array(
						'section' => 'yst_color_schemes',
						'label'   => __( 'Color Scheme', 'yoast-theme' ),
						'type'    => 'radio',
						'choices' => $colours,
				)
		);

		// This control goes into the default Site Title & Tagline section
		$wp_customize->add_control(
				'yst_nav_positioner',
				array(
						'section'  => 'nav',
						'label'    => __( 'Position of navigation', 'yoast-theme' ),
						'type'     => 'radio',
						'choices'  => array(
								'content' => __( 'Above content', 'yoast-theme' ),
								'top'     => __( 'At top of page', 'yoast-theme' ) ),
						'priority' => 200,
				)
		);

		// This control goes into the default Color section
		$wp_customize->add_control(
				'yst_header_color_picker',
				array(
						'section'  => 'yst_color_schemes',
						'label'    => __( 'Header Style', 'yoast-theme' ),
						'type'     => 'radio',
						'choices'  => array(
								'dark'  => __( 'Dark header', 'yoast-theme' ),
								'light' => __( 'Light header', 'yoast-theme' ) ),
						'priority' => 200,
				)
		);

		// This control goes into the default Site Title & Tagline section
		$wp_customize->add_control(
				'yst_tagline_positioner',
				array(
						'section'  => 'title_tagline',
						'label'    => __( 'Position of tagline', 'yoast-theme' ),
						'type'     => 'radio',
						'choices'  => array(
								'top_right' => __( 'Right', 'yoast-theme' ),
								'top_left'  => __( 'Left', 'yoast-theme' ) ),
						'priority' => 200,
				)
		);


		$wp_customize->add_control(
				new Yoast_Logo_Image_Control(
						$wp_customize,
						'yst_logo',
						array(
								'label'       => __( 'Logo', 'yoast-theme' ),
								'description' => __( 'Best size: 200px x 60px', 'yoast-theme' ),
								'setting'     => 'yst_logo',
								'section'     => 'yst_logos',
								'context'     => 'yst_logo',
						)
				)
		);

		$wp_customize->add_control(
				new Yoast_Logo_Image_Control(
						$wp_customize,
						'yst_mobile_logo',
						array(
								'label'       => __( 'Mobile Logo', 'yoast-theme' ),
								'description' => __( 'Best size: 230px x 36px', 'yoast-theme' ),
								'setting'     => 'yst_mobile_logo',
								'section'     => 'yst_logos',
								'context'     => 'yst_mobile_logo',
						)
				)
		);

		// This adds a new section for Footer settings
		$wp_customize->add_section(
				'yst_footer',
				array(
						'title'    => 'Footer',
						'priority' => 202
				)
		);

		$wp_customize->add_control(
				'yst_footer',
				array(
						'label'   => 'Footer text',
						'section' => 'yst_footer',
						'type'    => 'text',
				) );

	}

}

$yst_customize = new Yoast_Theme_Customizer();

if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Customize Radio buttons control with images
	 */
	class Yoast_Radio_Image_Control extends WP_Customize_Control {
		/**
		 * @var string
		 */
		public $type = 'yoast-radio-image-control';

		/**
		 * Render the control's content.
		 */
		public function render_content() {
			if ( empty( $this->choices ) ) {
				return;
			}

			$name = '_customize-radio-' . $this->id;

			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php
			foreach ( $this->choices as $value => $label ) :
				?>
				<label class="image-input-label">
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link();
					checked( $this->value(), $value ); ?>>
					<?php echo $label; ?>
					</input>
				</label>
			<?php
			endforeach;
		}
	}


	/**
	 * Customize Background Image Control Class
	 *
	 * @package    WordPress
	 * @subpackage Customize
	 * @since      3.4.0
	 */
	class Yoast_Logo_Image_Control extends WP_Customize_Image_Control {

		public $context;

		/**
		 * Constructor.
		 *
		 * @since 3.4.0
		 * @uses  WP_Customize_Image_Control::__construct()
		 *
		 * @param WP_Customize_Manager $manager
		 * @param string               $id
		 * @param array                $args
		 */
		public function __construct( $manager, $id, $args ) {
			parent::__construct( $manager, $args['setting'], array(
					'label'   => $args['label'],
					'section' => $args['section'],
					'context' => $args['context'],
					'get_url' => array( $this, 'get_image' ),
			) );

			$this->add_tab( 'upload-new', __( 'Upload' ), array( $this, 'tab_upload_new' ) );
			$this->add_tab( 'uploaded', __( 'Uploaded' ), array( $this, 'tab_uploaded' ) );

			$this->setting     = $this->manager->get_setting( $id );
			$this->context     = $args['context'];
			$this->description = $args['description'];

			if ( isset( $this->setting->default ) ) {
				$this->add_tab( 'default', __( 'Default' ), array( $this, 'tab_default_image' ) );
			}
		}

		/**
		 * Get the image source
		 *
		 * @return string source
		 */
		private function get_src() {
			$src = $this->value();
			if ( isset( $this->get_url ) ) {
				$src = call_user_func( $this->get_url, $src );
			}

			return $src;
		}

		/**
		 * Render the control's content.
		 *
		 * @since 3.4.0
		 */
		public function render_content() {
			$src = $this->get_src();
			?>
			<div class="customize-image-picker">
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<span class="customize-control-description"><?php echo nl2br( esc_html( $this->description ) ); ?></span>

				<div class="customize-control-content">
					<div class="dropdown preview-thumbnail" tabindex="0">
						<div class="dropdown-content">
							<?php if ( empty( $src ) ): ?>
								<img style="display:none;" />
							<?php else: ?>
								<img src="<?php echo esc_url( set_url_scheme( $src ) ); ?>" />
							<?php endif; ?>
							<div class="dropdown-status"></div>
						</div>
						<div class="dropdown-arrow"></div>
					</div>
				</div>

				<div class="library">
					<ul>
						<?php foreach ( $this->tabs as $id => $tab ): ?>
							<li data-customize-tab='<?php echo esc_attr( $id ); ?>' tabindex='0'>
								<?php echo esc_html( $tab['label'] ); ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php foreach ( $this->tabs as $id => $tab ): ?>
						<div class="library-content" data-customize-tab='<?php echo esc_attr( $id ); ?>'>
							<?php call_user_func( $tab['callback'] ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="actions">
					<a href="#" class="remove"><?php _e( 'Remove Image' ); ?></a>
				</div>
			</div>
		<?php
		}

		/**
		 * Get the current image for the setting this control controls
		 *
		 * @return string
		 */
		function get_image() {
			return get_theme_mod( $this->context );
		}

		/**
		 * @since 3.4.0
		 */
		public function tab_uploaded() {
			$images = get_posts( array(
					'post_type'  => 'attachment',
					'meta_key'   => '_wp_attachment_context',
					'meta_value' => $this->context,
					'orderby'    => 'none',
					'nopaging'   => true
			) );

			?>
			<div class="uploaded-target"></div><?php

			if ( empty( $images ) ) {
				return;
			}

			foreach ( (array) $images as $image ) {
				$this->print_tab_image( esc_url_raw( $image->guid ) );
			}
		}

		/**
		 * @since 3.4.0
		 * @uses  WP_Customize_Image_Control::print_tab_image()
		 */
		public function tab_default_image() {
			$this->print_tab_image( $this->setting->default );
		}
	}

	/**
	 * Customized Control Class that has a description option.
	 *
	 * @package    WordPress
	 * @subpackage Customize
	 * @since      3.4.0
	 */
	class Yoast_Customize_Control extends WP_Customize_Control {

		public $description = '';

		/**
		 * Render the control. Renders the control wrapper, then calls $this->render_content().
		 *
		 * @since 3.4.0
		 */
		protected function render() {
			$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
			$class = 'customize-control customize-control-' . $this->type;

			?>
		<li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php if ( ! empty( $this->description ) ) {
				echo wpautop( $this->description );
			} ?>
			<?php $this->render_content(); ?>
			</li><?php
		}
	}

}
