<?php

class Yoast_Theme_Customizer {

	function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'style' ), 20 );
	}

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
		</style>
	<?php
	}

	function customize_register( $wp_customize ) {
		//All our sections, settings, and controls will be added here
		$wp_customize->add_setting(
			'yst_colour_scheme',
			array(
				'default'   => 'WarmBlue',
				'transport' => 'refresh'
			)
		);

		$wp_customize->add_setting(
			'yst_logo',
			array(
				'default'   => get_stylesheet_directory_uri() . '/assets/images/logo.png',
				'transport' => 'refresh'
			)
		);

		$wp_customize->add_setting(
			'yst_mobile_logo',
			array(
				'default'   => get_stylesheet_directory_uri() . '/assets/images/logo-mobile.png',
				'transport' => 'refresh'
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

		// This adds a new section for Genesis Layouts
		$wp_customize->add_section(
			'yst_genesis_layout',
			array(
				'title'    => 'Layout',
				'priority' => 61
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
				'section' => 'colors',
				'label'   => __( 'Color Scheme', 'yoast-theme' ),
				'type'    => 'radio',
				'choices' => $colours
			)
		);

		// This adds a new section for Logo uploads
		$wp_customize->add_section(
			'yst_logos',
			array(
				'title'    => 'Logo',
				'priority' => 81
			)
		);

		$wp_customize->add_control(
			new Yoast_Logo_Image_Control(
				$wp_customize,
				'yst_logo',
				array(
					'label'   => __( 'Logo', 'yoast-theme' ),
					'setting' => 'yst_logo',
					'section' => 'yst_logos',
					'context' => 'yst_logo',
				)
			)
		);

		$wp_customize->add_control(
			new Yoast_Logo_Image_Control(
				$wp_customize,
				'yst_mobile_logo',
				array(
					'label'   => __( 'Mobile Logo', 'yoast-theme' ),
					'setting' => 'yst_mobile_logo',
					'section' => 'yst_logos',
					'context' => 'yst_mobile_logo',
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

			$this->add_tab( 'upload-new', __('Upload'), array( $this, 'tab_upload_new' ) );
			$this->add_tab( 'uploaded',   __('Uploaded'),   array( $this, 'tab_uploaded' ) );

			$this->setting = $this->manager->get_setting( $id );
			$this->context = $args['context'];

			if ( isset( $this->setting->default ) ) {
				$this->add_tab( 'default', __( 'Default' ), array( $this, 'tab_default_logo' ) );
			}
		}

		/**
		 * Render the control's content.
		 *
		 * @since 3.4.0
		 */
		public function render_content() {
			$src = $this->value();
			if ( isset( $this->get_url ) )
				$src = call_user_func( $this->get_url, $src );

			$image_details = get_theme_mod( $this->context . '_details' );

			// We might need more image details, so storing these in a separate theme mod for easy access.
			if ( ! $image_details || ! is_array( $image_details ) ) {
				global $wpdb;
				$img_att = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid = '%s' LIMIT 1", $src ) );
				if ( $img_att ) {
					$image_details = wp_get_attachment_metadata( $img_att );
					set_theme_mod( $this->context . '_details', $image_details );
				}
			}

			?>
			<div class="customize-image-picker">
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

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
		public function tab_default_logo() {
			$this->print_tab_image( $this->setting->default );
		}
	}
}
