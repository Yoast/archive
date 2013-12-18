<?php

class Yoast_Theme_Customizer {

	function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
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
			'yst_footer',
			array(
				'default'   => sprintf( '[footer_copyright before="%s "] &#x000B7; [footer_childtheme_link before="" after=" %s"] Genesis &#x000B7; [footer_wordpress_link] &#x000B7;', __( 'Copyright', 'genesis' ), __( 'on', 'genesis' ) ),
				'transport' => 'refresh'
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

				$colours[ $matches[1] ] = trim( preg_replace( '/([A-Z])/', ' $1', $matches[1] ) );
			}
		}

		// This control goes into the default Color section
		$wp_customize->add_control(
			'yst_colour_scheme',
			array(
				'section'  => 'colors',
				'label'    => __('Color Scheme','yoast-theme'),
				'type'     => 'radio',
				'choices'  => $colours
			)
		);

		// This adds a new section for Logo uploads
		$wp_customize->add_section(
			'yst_logos',
			array(
				'title'     => 'Logo',
				'priority'  => 201
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'yst_logo',
				array(
					'label'    => 'Logo',
					'settings' => 'yst_logo',
					'section'  => 'yst_logos'
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'yst_logo_mobile',
				array(
					'label'    => 'Mobile Logo',
					'settings' => 'yst_mobile_logo',
					'section'  => 'yst_logos'
				)
			)
		);

		// This adds a new section for Footer settings
		$wp_customize->add_section(
			'yst_footer',
			array(
				'title'     => 'Footer',
				'priority'  => 202
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