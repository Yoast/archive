<?php

/**
 * Class Yoast_Breadcrumb
 */
class Yoast_Breadcrumb {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'genesis_pre_get_option_breadcrumb_front_page', array( $this, 'override_breadcrumb_front_page' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_posts_page', array( $this, 'override_breadcrumb_posts_page' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_home', array( $this, 'override_breadcrumb_home' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_single', array( $this, 'override_breadcrumb_single' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_page', array( $this, 'override_breadcrumb_page' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_archive', array( $this, 'override_breadcrumb_archive' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_404', array( $this, 'override_breadcrumb_404' ) );
		add_filter( 'genesis_pre_get_option_breadcrumb_attachment', array( $this, 'override_breadcrumb_attachment' ) );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_front_page( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_front_page', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_posts_page( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_posts_page', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_home( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_home', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_single( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_single', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_page( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_page', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_archive( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_archive', $value, true );
	}

	/**
	 * Retrieve the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_404( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_404', $value, true );
	}

	/**
	 * Retrieves the content_archive setting from the theme settings
	 *
	 * @param null|string $value
	 *
	 * @return null|string
	 */
	public function override_breadcrumb_attachment( $value = null ) {
		return Yoast_Option_Helper::override_setting( 'yst_breadcrumb_attachment', $value, true );
	}

} 