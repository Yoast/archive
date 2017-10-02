<?php

/**
 * Framework config file
 *
 * Routes.php - define a route
 */

class Framework_Routes {

	/**
	 * @array
	 */
	public $routes;

	/**
	 * Construct the routes
	 */
	public function __construct() {
		$this->reset_routes();
	}

	/**
	 * Reset all routes
	 */
	public function reset_routes() {
		$this->routes = array();
	}

	/**
	 * Get the routes
	 *
	 * @return array
	 */
	public function get_routes() {
		return $this->routes;
	}

	/**
	 * Add a new route to the controllers
	 *
	 * @param bool   $admin
	 * @param string $link
	 * @param array  $action
	 */
	public function add_route( $admin = true, $link = 'url', $action = array() ) {
		$this->routes[] = array(
			'admin'  => $admin,
			'link'   => $link,
			'action' => $action,
		);
	}

}

global $framework_routes;
$framework_routes = new Framework_Routes;