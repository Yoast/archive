<?php

/**
* TODO: Documentation, fill in methods, create base class, etc..
*/
class Yoast_Theme_Update_Manager {
	
	/**
	* @var string
	*/
	private $api_url;

	/**
	* @var string
	*/
	private $item_name;

	/**
	* @var string
	*/
	private $slug;

	/**
	* @var string
	*/
	private $license_key;

	/**
	* @var string
	*/
	private $version;

	/**
	* @var string
	*/
	private $author;

	/**
	* Constructor
	*
	* @param string $api_url
	* @param string $item_name
	* @param string $license_key
	* @param string $slug
	* @param string $version
	* @param string $author (optional)
	*/
	public function __construct( $api_url, $item_name, $license_key, $slug, $version, $author = '') {
		$this->api_url = $api_url;
		$this->item_name = $item_name;
		$this->license_key = $license_key;
		$this->slug = $slug;
		$this->version = $version;
		$this->author = $author;
		$this->response_key = $this->slug . '-update-response';

		// setup hooks
		$this->setup_hooks();
	}

	/**
	* Setup hooks
	*/
	public function setup_hooks() {
		add_filter( 'site_transient_update_themes', array( $this, 'theme_update_transient' ) );
		add_filter( 'delete_site_transient_update_themes', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-update-core.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'load_themes_screen' ) );
	}

	public function load_themes_screen() {
		add_thickbox();
		add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
	}

	public function show_admin_notice() {
		
	}

	public function theme_update_transient( $value ) {
		return $value;
	}

	public function delete_theme_update_transient() {
		delete_transient( $this->response_key );
	}

	public function check_for_update() {

	}


}