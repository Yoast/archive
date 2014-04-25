<?php

if( ! class_exists( "Yoast_Update_Manager", false ) ) {

	class Yoast_Update_Manager {

		/**
		 * @var Yoast_Product
		 */
		protected $product;

		/**
		 * @var Yoast_License_Manager
		 */
		protected $license_manager;

		/**
		 * @var string
		 */
		protected $error_message = '';

		/**
		 * @var object
		 */
		protected $update_response = null;

		/**
		 * Constructor
		 *
		 * @param string $api_url     The url to the EDD shop
		 * @param string $item_name   The item name in the EDD shop
		 * @param string $license_key The (valid) license key
		 * @param string $slug        The slug. This is either the plugin main file path or the theme slug.
		 * @param string $version     The current plugin or theme version
		 * @param string $author      (optional) The item author.
		 */
		public function __construct( Yoast_Product $product, $license_manager ) {
			$this->product = $product;
			$this->license_manager = $license_manager;

			// generate transient name
			$this->response_transient_key = $this->product->get_slug() . 'update-response';

			// maybe delete transient
			$this->maybe_delete_update_response_transient();
		}

		/**
		 * Deletes the update response transient
		 * If we're on the update-core.php?force-check=1 page
		 */
		private function maybe_delete_update_response_transient() {
			global $pagenow;

			if( $pagenow === 'update-core.php' && isset( $_GET['force-check'] ) ) {
				delete_transient( $this->response_transient_key );
			}
		}

		/**
		 * If the update check returned a WP_Error, show it to the user
		 */
		public function show_update_error() {

			if ( $this->error_message === '' ) {
				return;
			}

			?>
			<div class="error">
				<p><?php printf( __( '%s failed to check for updates because of the following error: <em>%s</em>', $this->product->get_text_domain() ), $this->product->get_item_name(), $this->error_message ); ?></p>
			</div>
			<?php
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

			// create transient name to store failed tries
			$failed_transient_name = $this->product->get_slug() . '-update-checked';

			// only check if the failed transient is not set (or if it's expired)
			if( get_transient( $failed_transient_name ) !== false ) {
				return false;
			}

			// setup api parameters
			$api_params = array(
				'edd_action' => 'get_version',
				'license'    => $this->license_manager->get_license_key(),
				'item_name'       => $this->product->get_item_name(),
				'wp_version'       => get_bloginfo('version'),
				'item_version'     => $this->product->get_version()
			);

			// setup request parameters
			$request_params = array(
				'method' => 'POST',
				'body'      => $api_params
			);

			require_once dirname( __FILE__ ) . '/class-api-request.php';
			$request = new Yoast_API_Request( $this->product->get_api_url(), $request_params );

			if( $request->is_valid() !== true ) {

				// show error message
				$this->error_message = $request->get_error_message();
				add_action( 'admin_notices', array( $this, 'show_update_error' ) );

				// set a transient to prevent failed update checks on every page load
				set_transient( $failed_transient_name, 'failed', 10800 );

				return false;
			}

			// decode response
			$response = $request->get_response();

			// check if response returned that a given site was inactive
			if( isset( $response->license_check ) && $response->license_check === 'site_inactive' ) {

				// deactivate local license
				$this->license_manager->set_license_status( 'inactive' );

				// show notice to let the user know we deactivated his/her license
				$this->error_message = __( "This site has not been activated properly on yoast.com and thus cannot check for future updates. Please activate your site with a valid license key.", $this->product->get_text_domain() );
				add_action( 'admin_notices', array( $this, 'show_update_error' ) );
			}

			$response->sections = maybe_unserialize( $response->sections );

			// store response
			set_transient( $this->response_transient_key, $response, 10800 );

			return $response;
		}

		/**
		 * Gets the remote product data (from the EDD API)
		 *
		 * - If it was previously fetched in the current requests, this gets it from the instance property
		 * - Next, it tries the 3-hour transient
		 * - Next, it calls the remote API and stores the result
		 *
		 * @return object
		 */
		protected function get_remote_data() {

			// always use property if it's set
			if( null !== $this->update_response ) {
				return $this->update_response;
			}

			// get cached remote data
			$data = $this->get_cached_remote_data();

			// if cache is empty or expired, call remote api
			if( $data === false ) {
				$data = $this->call_remote_api();
			}

			$this->update_response = $data;
			return $data;
		}

		/**
		 * Gets the remote product data from a 3-hour transient
		 *
		 * @return bool|mixed
		 */
		private function get_cached_remote_data() {

			$data = get_transient( $this->product->get_slug() . 'update-response' );

			if( $data ) {
				return $data;
			}

			return false;
		}

	}
	
}