<?php

include dirname( __FILE__ ) . '../../class-product.php';
include dirname( __FILE__ ) . '../../class-license-manager.php';

/**
 * Class Yoast_Product_Double
 */
class Yoast_Product_Double extends Yoast_Product {

	/**
	 * Construct the real Product class with our fake data
	 */
	public function __construct() {
		parent::__construct( get_site_url(), 'test-product', 'slug-test-product', '1.0.0' );
	}

}

/**
 * Class Yoast_License_Manager_Double
 */
class Yoast_License_Manager_Double extends Yoast_License_Manager {

	public $product;

	protected $__notices = array();
	protected $license_api_response = array();

	public function __construct() {
		$this->product = new Yoast_Product_Double();

		parent::__construct( $this->product );
	}

	/**
	 * Needs to be defined.
	 */
	public function specific_hooks() {
	}

	/**
	 * Needs to be defined.
	 */
	public function setup_auto_updater() {
	}

	/**
	 * Expose protected function.
	 */
	public function get_curl_version() {
		return parent::get_curl_version();
	}

	/**
	 * Expose protected function.
	 */
	public function get_custom_message( $result ) {
		return parent::get_custom_message( $result );
	}

	/**
	 * Expose protected function.
	 */
	public function get_locale() {
		return parent::get_locale();
	}

	/**
	 * Expose protected function.
	 */
	public function get_successful_activation_message( $result ) {
		return parent::get_successful_activation_message( $result );
	}

	/**
	 * Expose protected function.
	 */
	public function get_unsuccessful_activation_message( $result ) {
		return parent::get_unsuccessful_activation_message( $result );
	}

	/**
	 * Override functionality to catch notices
	 * @todo mock this instead
	 */
	protected function set_notice( $message, $success = true ) {
		$this->__notices[] = array( 'message' => $message, 'success' => $success );
	}

	/**
	 * Get caught notices
	 */
	public function __get_notices() {
		return $this->__notices;
	}

	/**
	 * Override functionality to return custom response
	 * @todo mock this instead
	 */
	public function call_license_api( $action ) {
		return $this->license_api_response[ $action ];
	}

	/**
	 * Set a certain response for a license_api call
	 *
	 * @param string $action   Action to respond to.
	 * @param mixed  $response Response to give back.
	 */
	public function set_license_api_response( $action, $response ) {
		$this->license_api_response[ $action ] = $response;
	}
}

class Test_Yoast_License_Manager extends Yst_License_Manager_UnitTestCase {

	/** @var Yoast_License_Manager_Double */
	private $class;

	public function setUp() {
		$this->class = new Yoast_License_Manager_Double();
	}

	/**
	 * Make sure the API url is correct in the product
	 *
	 * @covers Yoast_Product::get_api_url()
	 */
	public function test_get_api_url() {
		$this->assertEquals( $this->class->product->get_api_url(), get_site_url() );
	}

	/**
	 * Make sure the API url is correct in the product
	 *
	 * @covers Yoast_License_Manager::get_curl_version()
	 */
	public function test_get_curl_version_WITH_curl_installed_on_test_server() {
		$curl_result = $this->class->get_curl_version();

		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();

			$this->assertEquals( $curl_result, $curl_version['version'] );
		} else {
			$this->assertFalse( $curl_result );
		}
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_unlimited() {

		$object                = new StdClass();
		$object->license_limit = 0;
		$object->expiry_date   = false;

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= 'You have an unlimited license. ';

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_limited_license() {
		$object                = new StdClass();
		$object->site_count    = 2;
		$object->license_limit = 8;
		$object->expires       = false;

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= sprintf( 'You have used %d/%d activations. ', $object->site_count, $object->license_limit );

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_limited_license_upgrade() {
		$object                = new StdClass();
		$object->site_count    = 2;
		$object->license_limit = 3;
		$object->expires       = false;

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= sprintf( 'You have used %d/%d activations. ', $object->site_count, $object->license_limit );
		$message .= sprintf( '<a href="%s">Did you know you can upgrade your license?</a> ', $this->class->product->get_extension_url( 'license-nearing-limit-notice' ) );

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_limited_license_extend() {
		$days_left = 5;

		$object                = new StdClass();
		$object->license_limit = 0;
		$object->expires       = date( DATE_RSS, time() + ( $days_left * 86400 ) );

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= 'You have an unlimited license. ';
		$message .= sprintf( '<a href="%s">Your license is expiring in %d days, would you like to extend it?</a> ', $this->class->product->get_extension_url( 'license-expiring-notice' ), $days_left );

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_limited_license_extend_tomorrow() {
		$days_left = 1;

		$object                = new StdClass();
		$object->license_limit = 0;
		$object->expires       = date( DATE_RSS, time() + ( $days_left * 86400 ) );

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= 'You have an unlimited license. ';
		$message .= sprintf( '<a href="%s">Your license is expiring in %d day, would you like to extend it?</a> ', $this->class->product->get_extension_url( 'license-expiring-notice' ), $days_left );

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_successful_activation_message()
	 */
	public function test_get_successful_activation_message_limited_license_upgrade_extend() {
		$days_left = 5;

		$object                = new StdClass();
		$object->site_count    = 2;
		$object->license_limit = 3;
		$object->expires       = date( DATE_RSS, time() + ( $days_left * 86400 ) );

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= sprintf( 'You have used %d/%d activations. ', $object->site_count, $object->license_limit );
		$message .= sprintf( '<a href="%s">Did you know you can upgrade your license?</a> ', $this->class->product->get_extension_url( 'license-nearing-limit-notice' ) );
		$message .= sprintf( '<a href="%s">Your license is expiring in %d days, would you like to extend it?</a> ', $this->class->product->get_extension_url( 'license-expiring-notice' ), $days_left );

		$result = $this->class->get_successful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}


	/**
	 * @covers Yoast_License_Manager::get_unsuccessful_activation_message()
	 */
	public function test_get_unsuccessful_activation_message() {
		$object        = new StdClass();
		$object->error = '';

		$message = 'Failed to activate your license, your license key seems to be invalid.';

		$result = $this->class->get_unsuccessful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_unsuccessful_activation_message()
	 */
	public function test_get_unsuccessful_activation_message_no_activations_left() {
		$object        = new StdClass();
		$object->error = 'no_activations_left';

		$message = sprintf( 'You\'ve reached your activation limit. You must <a href="%s">upgrade your license</a> to use it on this site.', $this->class->product->get_extension_url( 'license-at-limit-notice' ) );

		$result = $this->class->get_unsuccessful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_unsuccessful_activation_message()
	 */
	public function test_get_unsuccessful_activation_message_expired() {
		$object        = new StdClass();
		$object->error = 'expired';

		$message = sprintf( 'Your license has expired. You must <a href="%s">extend your license</a> in order to use it again.', $this->class->product->get_extension_url( 'license-expired-notice' ) );

		$result = $this->class->get_unsuccessful_activation_message( $object );

		$this->assertEquals( $result, $message );
	}

	/**
	 * @covers Yoast_License_Manager::get_custom_message()
	 */
	public function test_get_custom_message() {
		$message = 'locale message';

		$locale = $this->class->get_locale();

		$object                                  = new StdClass();
		$object->{'html_description_' . $locale} = $message;

		$result = $this->class->get_custom_message( $object );

		$this->assertEquals( $result, '<br />' . $message );
	}

	/**
	 * @covers Yoast_License_Manager::activate_license()
	 */
	public function test_activate_license_failed_api_response() {
		$this->class->set_license_api_response( 'activate', false );

		$this->assertFalse( $this->class->activate_license() );
		$this->assertEquals( [], $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::activate_license()
	 */
	public function test_activate_license_success() {
		$object                = new StdClass();
		$object->license       = 'valid';
		$object->license_limit = 0;

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= 'You have an unlimited license. ';

		$this->class->set_license_api_response( 'activate', $object );
		$this->class->activate_license();

		$this->assertEquals( array(
			array(
				'message' => $message,
				'success' => true
			)
		), $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::activate_license()
	 */
	public function test_activate_license_success_custom_message() {
		$object                   = new StdClass();
		$object->license          = 'valid';
		$object->license_limit    = 0;
		$object->html_description = 'This is an HTML description.';

		$message = sprintf( 'Your %s license has been activated. ', $this->class->product->get_item_name() );
		$message .= 'You have an unlimited license. ';

		$this->class->set_license_api_response( 'activate', $object );
		$this->class->activate_license();

		$this->assertEquals( array(
			array(
				'message' => $message . '<br />' . $object->html_description,
				'success' => true
			)
		), $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::deactivate_license()
	 */
	public function test_deactivate_license_failed_response() {
		$this->class->set_license_status( 'activated' );
		$this->class->set_license_api_response( 'deactivate', false );

		$this->assertFalse( $this->class->deactivate_license() );
		$this->assertEquals( 'activated', $this->class->get_license_status() );
	}

	/**
	 * @covers Yoast_License_Manager::deactivate_license()
	 */
	public function test_deactivate_license_success() {
		$object          = new StdClass();
		$object->license = 'deactivated';

		$message = sprintf( 'Your %s license has been deactivated.', $this->class->product->get_item_name() );

		$this->class->set_license_status( 'activated' );
		$this->class->set_license_api_response( 'deactivate', $object );

		$this->assertTrue( $this->class->deactivate_license() );
		$this->assertEquals( array(
			array(
				'message' => $message,
				'success' => true
			)
		), $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::deactivate_license()
	 */
	public function test_deactivate_license_success_custom_message() {
		$object                   = new StdClass();
		$object->license          = 'deactivated';
		$object->html_description = 'This is an HTML description.';

		$message = sprintf( 'Your %s license has been deactivated.', $this->class->product->get_item_name() );

		$this->class->set_license_status( 'activated' );
		$this->class->set_license_api_response( 'deactivate', $object );

		$this->assertTrue( $this->class->deactivate_license() );
		$this->assertEquals( array(
			array(
				'message' => $message . '<br />' . $object->html_description,
				'success' => true
			)
		), $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::deactivate_license()
	 */
	public function test_deactivate_license_failed() {
		$object          = new StdClass();
		$object->license = 'activated'; // Expected to be deactivated.

		$message = sprintf( 'Failed to deactivate your %s license.', $this->class->product->get_item_name() );

		$this->class->set_license_status( 'activated' );
		$this->class->set_license_api_response( 'deactivate', $object );

		$this->assertFalse( $this->class->deactivate_license() );
		$this->assertEquals( array(
			array(
				'message' => $message,
				'success' => false
			)
		), $this->class->__get_notices() );
	}

	/**
	 * @covers Yoast_License_Manager::deactivate_license()
	 */
	public function test_deactivate_license_failed_custom_message() {
		$object                   = new StdClass();
		$object->license          = 'activated'; // Expected to be deactivated.
		$object->html_description = 'This is an HTML description.';

		$message = sprintf( 'Failed to deactivate your %s license.', $this->class->product->get_item_name() );

		$this->class->set_license_status( 'activated' );
		$this->class->set_license_api_response( 'deactivate', $object );

		$this->assertFalse( $this->class->deactivate_license() );
		$this->assertEquals( array(
			array(
				'message' => $message . '<br />' . $object->html_description,
				'success' => false
			)
		), $this->class->__get_notices() );
	}
}
