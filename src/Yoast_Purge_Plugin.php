<?php

/**
 * The main class to initialize everything.
 */
final class Yoast_Purge_Plugin {

	/**
	 * @var array
	 */
	private $integrations = array();

	/** @var Yoast_Purge_Require_Yoast_SEO_Version */
	private $requirement_checker;

	/**
	 * Initializes the plugin.
	 */
	public function __construct() {
		$this->requirement_checker = new Yoast_Purge_Require_Yoast_SEO_Version();
	}

	/**
	 * Adds the integrations that the plugin needs.
	 */
	public function add_integrations() {
		$this->integrations = array(
			new Yoast_Purge_Attachment_Page_Server(),
			new Yoast_Purge_Control_Yoast_SEO_Settings(),
			new Yoast_Purge_Media_Settings_Tab_Content(),
		);
	}

	/**
	 * Registers hooks to WordPress.
	 */
	public function register_hooks() {
		// Always show notifications, even if the rest cannot be activated.
		$this->requirement_checker->register_hooks();
		if ( ! $this->requirement_checker->has_required_version() ) {
			return;
		}

		foreach ( $this->integrations as $integration ) {
			$integration->register_hooks();
		}
	}

	/**
	 * Retrieves the registered integrations.
	 *
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}
