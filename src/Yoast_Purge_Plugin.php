<?php

/**
 * The main class to initialize everything.
 */
final class Yoast_Purge_Plugin {

	/**
	 * @var array
	 */
	protected $integrations;

	/**
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->integrations = array();
	}

	/**
	 * Adds the integrations that the plugin needs.
	 */
	public function add_integrations() {
		$this->integrations = array(
			new Yoast_Purge_Attachment_Page_Server(),
		);
	}

	/**
	 * Registers hooks to WordPress.
	 */
	public function register_hooks() {
		foreach ( $this->integrations as $integration ) {
			$integration->register_hooks();
		}
	}

	/**
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}
