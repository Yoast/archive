<?php

/**
* TODO: Documentation, fill in methods, create base class, etc..
*/
class Yoast_Plugin_Update_Manager {
	
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

		// setup hooks
		$this->setup_hooks();
	}

	/**
	* Setup hooks
	*/
	public function setup_hooks() {
		// check for updates
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'set_updates_available_data' ) );
		
		// get correct plugin information (when viewing details)
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
	}

	/**
	* Check for updates and if so, add to "updates available" data
	*
	* @param object $data
	* @return object $data
	*/
	public function set_updates_available_data( $data ) {

		if ( empty( $data ) ) {
			return $data;
		} 
		// send of API request to check for updates
		$api_response = $this->call_remote_api();

		// did we get a response?
		if( $api_response === false ) {
			return $data;
		}

		// compare versions
		if ( version_compare( $this->version, $api_response->new_version, '<' ) ) {

			// remote version is newer, add to data
			$data->response[$this->name] = $api_response;

		}

		return $data;
	}

	/**
	 * Gets new plugin version details (view version x.x.x details)
	 *
	 * @uses api_request()
	 *
	 * @param object $data
	 * @param string $action
	 * @param object $args (optional)
	 *
	 * @return object $data
	 */
	public function plugins_api_filter( $data, $action = '', $args = null ) {

		// only do something if we're checking for our plugin
		if ( $action !== 'plugin_information' || ! isset( $args->slug ) || $args->slug !== $this->slug ) {
			return $data;
		} 

		$api_response = $this->call_remote_api();
		
		// did we get a response?
		if ( $api_response === false ) {
			return $data;	
		}

		// return api response
		return $api_response;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses         get_bloginfo()
	 * @uses         wp_remote_post()
	 * @uses         is_wp_error()
	 *
	 * @return false||object
	 */
	private function call_remote_api() {

		global $wp_version;

		// setup api parameters
		$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_key,
				'name'       => $this->item_name,
				'slug'       => $this->slug,
				'author'     => $this->author
		);

		// setup request parameters
		$request_params = array( 
			'timeout' => 15, 
			'sslverify' => false, 
			'body' => $api_params 
		);

		// call remote api
		$response = wp_remote_post( $this->api_url, $request_params );

		// wp / http error?
		if( is_wp_error( $response) ) {
			return false;
		}

		// decode response
		$response = json_decode( wp_remote_retrieve_body( $response ) );
		$response->sections = maybe_unserialize( $response->sections );
		return $response;
	}


}