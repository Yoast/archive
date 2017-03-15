<?php

class Yoast_Theme_License {


	private $theme_name = '';
	private $theme_version = '';

	private $option_name = '';
	private $license = null;

	public function __construct( $theme_name, $theme_version ) {
		$this->theme_name    = $theme_name;
		$this->theme_version = $theme_version;
		$this->option_name   = 'yoast_license_' . sanitize_title_with_dashes( $this->theme_name );
		$this->hooks();
	}

	private function hooks() {
		// Setup the admin notice
		add_action( 'admin_head', array( $this, 'check_display_admin_notice' ) );

		// Setup the updater
		add_action( 'admin_init', array( $this, 'setup_updater' ) );

		// Add the license menu
		add_action( 'admin_menu', array( $this, 'add_license_menu' ) );
	}

	/**
	 * Activate the Yoast theme license
	 *
	 * @param $license_key
	 *
	 * @return string
	 */
	private function activate_license( $license_key ) {

		// Try to activate the license
		$license_key = trim( $license_key );
		$api_params  = array(
				'edd_action' => 'activate_license',
				'license'    => $license_key,
				'item_name'  => urlencode( $this->theme_name )
		);

		// Do the request
		$response = wp_remote_get( add_query_arg( $api_params, 'https://yoast.com/' ), array( 'timeout' => 15, 'sslverify' => false ) );

		// Check the response for errors
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Get the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Save the new license
		$new_license = array( 'key' => $license_key, 'status' => $license_data->license );
		$this->save_license( $new_license );

		// Return
		return true;
	}

	/**
	 * Get the license option
	 *
	 * @return array
	 */
	private function get_license() {
		if ( null === $this->license ) {
			$this->license = wp_parse_args( get_option( $this->option_name, array() ), array( 'key' => '', 'status' => 'invalid' ) );
		}

		return $this->license;
	}

	/**
	 * Save the license to the database
	 *
	 * @param $license
	 */
	private function save_license( $license ) {
		$this->license = $license;
		update_option( $this->option_name, $this->license );
	}

	/**
	 * Get the theme license key
	 *
	 * @return string
	 */
	public function get_license_key() {
		$license = $this->get_license();

		return $license['key'];
	}

	/**
	 * Get the license status
	 *
	 * @return string
	 */
	public function get_license_status() {
		$license = $this->get_license();

		return $license['status'];
	}

	/**
	 * Returns if a license is valid and active
	 *
	 * @return boolean
	 */
	public function is_license_valid() {
		if ( 'valid' == $this->get_license_status() ) {
			return true;
		}

		return false;
	}

	/**
	 * Add hook to display the admin notice if the license is not valid
	 */
	public function check_display_admin_notice() {
		if ( false === $this->is_license_valid() ) {
			add_action( 'admin_notices', array( $this, 'display_license_admin_notice' ) );
		}
	}

	/**
	 * Display the license admin notice
	 */
	public function display_license_admin_notice() {
		echo '<div class="error"><p>';
		printf( __( '<b>Warning!</b> The %s theme license key is not valid, you\'re missing out on updates and support! <a href="%s">Enter your license key</a> or <a href="%s" target="_blank">get a license here</a>.', 'yoast-theme' ), $this->theme_name, admin_url() . 'themes.php?page=theme-license', 'https://yoast.com/wordpress/themes/' . sanitize_title_with_dashes( $this->theme_name ) );
		echo "</p></div>";
	}

	/**
	 * Setup the theme updater
	 */
	public function setup_updater() {

		// Load our custom theme updater
		if ( ! class_exists( 'EDD_SL_Theme_Updater' ) ) {
			require_once( 'class-edd-sl-theme-updater.php' );
		}

		// Setup the updater
		$edd_updater = new EDD_SL_Theme_Updater( array(
						'remote_api_url' => 'https://yoast.com', // Our store URL that is running EDD
						'version'        => $this->theme_version, // The current theme version we are running
						'license'        => $this->get_license_key(), // The license key (used get_option above to retrieve from DB)
						'item_name'      => $this->theme_name, // The name of this theme
						'author'         => 'Yoast',
						'theme_slug'     => sanitize_title_with_dashes( $this->theme_name )
				)
		);

	}

	/**
	 * Add license menu
	 */
	public function add_license_menu() {
		$theme_page = add_theme_page( 'Theme License', 'Theme License', 'manage_options', 'theme-license', array( $this, 'license_page' ) );
		add_action( 'load-' . $theme_page, array( $this, 'catch_license_post' ) );
	}

	/**
	 * Catch the license post
	 */
	public function catch_license_post() {
		if ( isset( $_POST['yoast_theme_license_key'] ) ) {

			// Only continue is the posted license key is different than the current license key
			if ( $this->get_license_key() != $_POST['yoast_theme_license_key'] ) {
				if ( $this->activate_license( $_POST['yoast_theme_license_key'] ) ) {
					remove_action( 'admin_init', array( $this, 'check_display_admin_notice' ) );
				}
			}

		}
	}

	/**
	 * Display the license page
	 */
	public function license_page() {
		?>
		<div class="wrap">
		<h2><?php _e( 'Theme License', 'yoast-theme' ); ?></h2>

		<form method="post" action="">

			<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'License Key', 'yoast-theme' ); ?>
					</th>
					<td>
						<input id="yoast_theme_license_key" name="yoast_theme_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $this->get_license_key() ); ?>" />
						<label class="description" for="yoast_theme_license_key"><?php _e( 'Enter your license key', 'yoast-theme' ); ?></label>
					</td>
				</tr>
				</tbody>
			</table>
			<?php submit_button(); ?>
		</form>
	<?php
	}

}