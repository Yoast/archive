<?php

class Framework_Controller {

	/**
	 * @class
	 */
	public $Model;

	/**
	 * @class
	 */
	public $Routes;

	/**
	 * TEST - This must be removed..!
	 * @var
	 */
	public $Hello;

	/**
	 * @array
	 */
	public static $components;

	/**
	 * @class
	 */
	public $AppModel;

	/**
	 * Construct the framework
	 */
	public function __construct() {
		$this->load_config_files();
		$this->load_model();
	}

	/**
	 * Load the basic config files
	 */
	private function load_config_files() {
		global $framework_routes;

		if ( ! class_exists( 'Framework_Routes' ) && ! is_object( $framework_routes ) ) {
			require( plugin_dir_path( __FILE__ ) . '../config/class-framework-routes.php' );
		}
		require( plugin_dir_path( __FILE__ ) . '../../config/routes.php' );


		$this->Routes = $framework_routes->get_routes();
		$this->set_routes( $this->Routes );
	}

	/**
	 * Load the main model
	 */
	private function load_model() {
		if ( ! class_exists( 'Framework_Model' ) && ! is_object( $this->Model ) ) {
			require( plugin_dir_path( __FILE__ ) . '../models/class-framework-model.php' );
			require( plugin_dir_path( __FILE__ ) . '../../models/class-app-model.php' );

			$this->Model    = new Framework_Model;
			$this->AppModel = new App_Model;
		}
	}

	/**
	 * Init function for all controllers, set components etc here
	 */
	public static function init() {

		//print_r( self::$components );

	}

	/**
	 * Hook the urls to the correct controllers and load the specific model
	 */
	public function set_routes() {
		if ( isset( $_GET['page'] ) ) {
			add_action( 'admin_menu', array( $this, 'add_wordpress_page' ) );
		}
	}

	/**
	 * Add WordPress page
	 */
	public function add_wordpress_page() {
		foreach ( $this->Routes as $route ) {
			if ( $route['link'] == $_GET['page'] ) {

				add_submenu_page(
					'_fake_item_plugin_framework',
					'Test',
					'',
					'manage_options',
					$route['link'],
					array( $this, 'add_wordpress_page' )
				);

				$name            = 'Framework_' . ucfirst( $route['action']['controller'] ) . '_Controller';
				$pref_controller = 'Controller_' . ucfirst( $route['action']['controller'] );
				$model           = ucfirst( $route['action']['controller'] );
				$pref_model      = ucfirst( $route['action']['controller'] ) . '_Model';
				$action          = $route['action']['action'];

				if ( ! class_exists( $name ) ) {
					require( plugin_dir_path( __FILE__ ) . '../../models/class-' . $route['action']['controller'] . '-model.php' );
					require( plugin_dir_path( __FILE__ ) . '../../controllers/class-' . $route['action']['controller'] . '-controller.php' );

					//	Init the Model
					$this->$model = new $pref_model;

					// Init the controller and call the action
					$this->$pref_controller = new $name;
					$funcname               = $route['action']['action'];
					$this->$pref_controller->$funcname();
				}
			}
		}

	}

}

new Framework_Controller();