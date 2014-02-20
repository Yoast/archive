<?php

interface iYoast_License_Manager {

	public function specific_hooks();

}

abstract class Yoast_License_Manager implements iYoast_License_Manager {

	/**
	* @var string The item name in the EDD shop.
	*/
	protected $item_name;

	/**
	* @var string The absolute url on which users can purchase a license
	*/
	protected $item_url = 'https://yoast.com';

	/**
	* @var string The URL of the shop running the EDD API. 
	*/
	protected $api_url = 'https://yoast.com';

	/**
	* @var string Relative admin URL on which users can enter their license key.
	*/
	protected $license_page;

	/**
	* @var string The text domain used for translating strings
	*/
	protected $text_domain = 'yoast';

	/**
	* @var string The option prefix. Used to prefix license related options.
	*/
	protected $option_prefix;

	/**
	* @var string The version number of the item
	*/ 
	protected $version;

	/**
	* @var string 
	*/
	private $license_constant_name = '';

	/**
	* @var boolean True if license is defined with a constant
	*/
	private $license_constant_is_defined = false;

	/**
	 * Constructor
	 *
	 * @param string $item_name The item name in the EDD shop
	 * @param string $item_url The absolute url on which users can purchase a license
	 * @param string $license_page Relative admin URL on which users can enter their license key
	 * @param string $text_domain The text domain used for translating strings
	 * @param string $version The version number of the item 
	 */
	public function __construct( $item_name, $item_url, $version, $license_page, $text_domain ) {

		$this->item_name = $item_name;
		$this->item_url = $item_url;
		$this->version = $version;	
		$this->license_page = $license_page;
		$this->text_domain = $text_domain;
		$this->option_prefix = 'yoast_' . sanitize_title_with_dashes( $item_name, null, 'save' );

		// setup hooks
		$this->hooks();

		// maybe set license key from constant
		$this->maybe_set_license_key_from_constant();
	}

	/**
	* Setup hooks
	*/
	private function hooks() {
		// show admin notice if license is not active
		add_action( 'admin_notices', array( $this, 'maybe_display_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'catch_post_request') );

		// setup item type (plugin|theme) specific hooks
		$this->specific_hooks();
	}

	/**
	* Display admin notice if there is no active license
	*/
	public function maybe_display_admin_notice() {

		// do not show notice if license is valid
		if( $this->license_is_valid() ) {
			return;
		}
		?>
		<div class="error">
			<p><?php printf( __( '<b>Warning!</b> Your %s license is inactive which means you\'re missing out on updated and support! <a href="%s">Enter your license key</a> or <a href="%s" target="_blank">get a license here</a>', $this->text_domain ), $this->item_name, admin_url( $this->license_page ), $this->item_url ); ?></p>
		</div>
		<?php
	}

	/**
	 * Remotely activate License
	 * @return boolean True if the license is now activated, false if not
	 */
	public function activate_license() {

		$result = $this->call_license_api( 'activate' );

		if( $result ) {
			$this->set_license_status( $result->license );
		}

		return ( $this->license_is_valid() );
	}


	/**
	 * Remotely deactivate License
	 * @return boolean True if the license is now deactivated, false if not
	 */
	public function deactivate_license () {

		$result = $this->call_license_api( 'deactivate' );

		if( $result ) {
			$this->set_license_status( $result->license );
		}

		return ( $this->get_license_status() === 'deactivated' );		
	}

	/**
	* @param string $action activate|deactivate
	* @return mixed 
	*/
	protected function call_license_api( $action ) {

		// don't make a request if license key is empty
		if( $this->get_license_key() === '' ) {
			return false;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => $action . '_license',
			'license'    => $this->get_license_key(),
			'item_name'  => urlencode( trim( $this->item_name ) )
		);

		// create api request url
		$url = add_query_arg( $api_params, $this->api_url );

		// request parameters
		$request_params = array( 
			'timeout' => 20, 
			'sslverify' => false, 
			'headers' => array( 'Accept-Encoding' => '*' ) 
		);

		// fire request to shop
		$response = wp_remote_get( $url, $request_params );

		// make sure response came back okay
		if( is_wp_error( $response ) ) {
			return false;
		}

		// decode api response
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		return $license_data;
	}

	/**
	* Set the license status
	*
	* @param string $license_status
	*/
	public function set_license_status( $license_status ) {
		update_option( $this->option_prefix . '_license_status', $license_status);
	}

	/**
	* Get the license status
	*
	* @return string $license_status;
	*/
	public function get_license_status() {

		$license_status = get_option( $this->option_prefix . '_license_status', false );

		if( $license_status === false ) {
			// insert empty license status, enables autoloading of option
			$this->set_license_status('');
			return '';
		}

		return trim( $license_status );
	}

	/**
	* Set the license key
	*
	* @param string $license_key 
	*/
	public function set_license_key( $license_key ) {

		// only update option if license key is different
		if( $license_key === $this->get_license_key() ) {
			return;
		}

		update_option( $this->option_prefix . '_license_key', $license_key);
	}

	/**
	* Gets the license key from constant or option
	*
	* @return string $license_key
	*/
	public function get_license_key() {

		$license_key = get_option( $this->option_prefix . '_license_key', false );

		if( $license_key === false ) {
			// insert empty license key, enables autoloading of option
			$this->set_license_key('');
			return '';
		}

		return trim( $license_key );
	}

	/**
	* Checks whether the license status is active
	*
	* @return boolean True if license is active
	*/
	public function license_is_valid() {
		return ($this->get_license_status() === 'valid');
	}

	/**
	* Show a form where users can enter their license key
	*/
	public function show_license_form() {

		$key_name = $this->option_prefix . '_license_key';
		$nonce_name = $this->option_prefix . '_license_nonce';
		$action_name = $this->option_prefix . '_license_action';

		if( strlen($this->get_license_key() ) > 4) {
			$visible_license_key = str_repeat('*', strlen( $this->get_license_key() ) - 4) . substr( $this->get_license_key(), -4 );
		} else {
			$visible_license_key = $this->get_license_key();
		}

		// make license key readonly when license key is valid or license is defined with a constant
		$readonly = ( $this->license_is_valid() || $this->license_constant_is_defined );
		?>
		<h3>
			<?php printf( __( "%s License Settings", $this->text_domain ), $this->item_name ); ?>&nbsp; &nbsp; 
			<small style="font-weight: normal;">
			<?php if( $this->license_is_valid() ) { ?>
				<span style="color: white; background: green; padding:3px 6px;">ACTIVE</span> - &nbsp; you are receiving updates.
			<?php } else { ?>
				<span style="color:white; background: red; padding: 3px 6px;">INACTIVE</span> - &nbsp; you are <strong>not</strong> receiving plugin updates.
			<?php } ?>
			</small>
		</h3>

		<form method="post" action="" id="yoast-license-form">

			<?php wp_nonce_field( $nonce_name, $nonce_name ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top"><?php _e('Toggle license status', $this->text_domain ); ?></th>
						<td id="yoast-license-toggler">

							<?php if( $this->license_is_valid() ) { ?>
								<button name="<?php echo esc_attr( $action_name ); ?>" type="submit" class="button-secondary yoast-license-deactivate" value="deactivate"><?php echo esc_html_e( 'Deactivate License', $this->text_domain ); ?></button> &nbsp; 
								<small><?php _e( '(deactivate your license so you can activate it on another WordPress site)', $this->text_domain ); ?></small>
							<?php } else { ?>

								<?php if( $this->get_license_key() !== '') { ?>
									<button name="<?php echo esc_attr( $action_name ); ?>" type="submit" class="button-secondary yoast-license-activate" value="activate" /><?php echo esc_html_e('Activate License', $this->text_domain ); ?></button> &nbsp; 
									<small><?php _e( '(activate your license to enable updates and support)', $this->text_domain ); ?></small>
								<?php } else { ?>
									<small><?php _e( 'Please enter a license key in the field below first.', $this->text_domain ); ?></small>
								<?php } ?>
								
							<?php } ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" valign="top"><?php _e( 'License Key', $this->text_domain ); ?></th>
						<td>
							<input id="yoast-license-key-field" name="<?php echo esc_attr( $key_name ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $visible_license_key ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'Paste your %s license key here..', $this->text_domain ), $this->item_name ) ); ?>" <?php if( $readonly ) { echo 'readonly="readonly"'; } ?> />
						</td>
					</tr>
					
				</tbody>
			</table>

			<?php 
			// only show "Save Changes" button if license is not activated and not defined with a constant
			if( $readonly === false ) {
				submit_button();
			} 
			?>
		</form>
		<?php

		// enqueue script in the footer
		add_action( 'admin_footer', array( $this, 'output_script'), 99 );
	}

	/**
	* Check if the license form has been submitted
	*/
	public function catch_post_request() {

		$name = $this->option_prefix . '_license_key';

		// check if license key was posted and not empty
		if( ! isset( $_POST[$name] ) ) {
			return;
		}

		// run a quick security check
		$nonce_name = $this->option_prefix . '_license_nonce';

		if ( ! check_admin_referer( $nonce_name, $nonce_name ) ) {
			return; 
		}

		// @TODO: check for user cap?

		// get key from posted value
		$license_key = $_POST[$name];

		// check if license key doesn't accidentally contain asterisks
		if( strstr($license_key, '*') === false ) {

			// sanitize key
			$license_key = trim( sanitize_key( $_POST[$name] ) );

			// save license key
			$this->set_license_key( $license_key );
		}

		// does user have an activated valid license
		if( ! $this->license_is_valid() ) {

			// try to auto-activate license
			return $this->activate_license();	

		}	

		$action_name = $this->option_prefix . '_license_action';

		// was one of the action buttons clicked?
		if( isset( $_POST[ $action_name ] ) ) {
			
			$action = trim( $_POST[ $action_name ] );

			switch($action) {

				case 'activate':
					return $this->activate_license();
					break;

				case 'deactivate':
					return $this->deactivate_license();
					break;
			}

		}
		
	}

	/**
	* Output the script containing the YoastLicenseManager JS Object
	*
	* This takes care of disabling the 'activate' and 'deactivate' buttons
	*/
	public function output_script() {
		?>
		<script type="text/javascript">
		(function($) {
	
			var YoastLicenseManager = (function () {

				var self = this;
				var $actionButton,
					$licenseForm, 
					$keyInput,
					$submitButtons;

				function init() {
					$licenseForm = $("#yoast-license-form");
					$keyInput = $licenseForm.find("#yoast-license-key-field");
					$actionButton = $licenseForm.find('#yoast-license-toggler button');
					$submitButtons = $licenseForm.find('input[type="submit"], button[type="submit"]');

					$submitButtons.click( addDisableEvent );
					$actionButton.click( actOnLicense );
					$keyInput.click( setEmptyValue );
				}

				function setEmptyValue() {
					if( ! $(this).is('[readonly]') ) {
						$(this).val('');
					}
				}

				function actOnLicense() {	
					// fake input field with exact same name => value			
					$("<input />")
						.attr('type', 'hidden')
						.attr( 'name', $(this).attr('name') )
						.val( $(this).val() )
						.appendTo($licenseForm);

					// change button text to show we're working..
					var text = ( $actionButton.hasClass('yoast-license-activate') ) ? "Activating..." : "Deactivating...";
					$actionButton.text( text );
				}

				function addDisableEvent() {
					$licenseForm.submit( disableButtons );
				}

				function disableButtons() {
					// disable submit buttons to prevent multiple requests
					$submitButtons.prop( 'disabled', true );
				}

				return {
					init: init
				}
			
			})();

			YoastLicenseManager.init();

		})(jQuery);
		</script>
		<?php
	}

	/**
	* Set the constant used to define the license
	*
	* @param string $license_constant_name The license constant name
	*/
	public function set_license_constant_name( $license_constant_name ) {
		$this->license_constant_name = trim( $license_constant_name );
	}

	/**
	* Maybe set license key from a defined constant
	*/
	private function maybe_set_license_key_from_constant() {
		// generate license constant name
		if( $this->license_constant_name === '') {
			$this->set_license_constant_name('YOAST_' . strtoupper( str_replace( array(' ', '-' ), '', sanitize_key( $this->item_name ) ) ) . '_LICENSE');
		}

		// set license key from constant
		if( defined( $this->license_constant_name ) ) {
			$this->set_license_key( constant( $this->license_constant_name ) );
			$this->license_constant_is_defined = true;
		}
	}

}