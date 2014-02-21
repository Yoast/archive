<?php

/**
* TODO: Documentation, fill in methods, create base class, etc..
*/
class Yoast_Theme_Update_Manager extends Yoast_Update_Manager {
	
	/**
	* @var string
	*/
	private $response_key;

	/**
	* Constructor
	*
	* @param string $api_url
	* @param string $item_name
	* @param string $license_key
	* @param string $slug
	* @param string $theme_version
	* @param string $author (optional)
	*/
	public function __construct( $api_url, $item_name, $license_key, $slug, $version = '', $author = '') {
		
		parent::__construct( $api_url, $item_name, $license_key, $slug, $version, $author );

		$this->response_key = $this->slug . '-update-response';

		// setup hooks
		$this->setup_hooks();
	}

	/**
	* Setup hooks
	*/
	private function setup_hooks() {
		add_filter( 'site_transient_update_themes', array( $this, 'set_theme_update_transient' ) );
		add_filter( 'delete_site_transient_update_themes', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-update-core.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'delete_theme_update_transient' ) );
		add_action( 'load-themes.php', array( $this, 'load_themes_screen' ) );
	}

	/**
	* Add hooks and scripts to the Appearance > Themes screen
	*/
	public function load_themes_screen() {
		add_thickbox();
		add_action( 'admin_notices', array( $this, 'show_update_details' ) );
	}

	/**
	* Show update link. 
	* Opens Thickbox with Changelog.
	*/
	public function show_update_details() {
		
		$update_data = $this->get_update_data();

		// only show if an update is available
		if( $update_data === false ) {
			return;
		}

		$update_url = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->theme_slug ), 'upgrade-theme_' . $this->theme_slug );
		$update_onclick = ' onclick="if ( confirm(\'' . esc_js( __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update." ) ) . '\') ) {return true;}return false;"';
		?>
		<div id="update-nag">
			<?php
				printf( 
					__( '<strong>%1%s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4$s">Check out what\s new</a> or <a href="%5$s" %6$s>update now</a>.' ),
					$this->item_name,
					$update_data->new_version,
					'#TB_inline?width=640&amp;inlineId=' . $this->slug . '_changelog',
					$this->item_name,
					$update_url,
					$update_onclick
				);
			?>
		</div>
		<div id="<?php echo $this->slug; ?>_changelog" style="display: none;">
			<?php echo wpautop( $update_data->sections['changelog'] ); ?>
		</div>	
		<?php
	}

	/**
	* Set "updates available" transient
	*/
	public function set_theme_update_transient( $value ) {
		$update_data = $this->get_update_data();

		if( $update_data ) {
			$value->response[ $this->slug ] = $update_data;
		}

		return $value;
	}

	public function get_theme_update_transient() {
		return get_transient( $this->response_key );
	}

	/*
	* Deletes "updates available" transient
	*/
	public function delete_theme_update_transient() {
		delete_transient( $this->response_key );
	}

	/**
	* Get update data
	*
	* This gets the update data from a transient (12 hours), if set.
	* If not, it will make a remote request and get the update data.
	*
	* @return array $update_data Array containing the update data
	*/
	public function get_update_data() {
		
		$update_data = $this->get_theme_update_transient();

		// if transient was not set, make a remote request
		if( $update_data === false ) {

			$api_response = $this->call_remote_api();

			// did we get a valid response?
			if( $api_response !== false && is_object( $api_response ) ) {

				set_transient( $this->response_key, $api_response, strtotime( '+12 hours') );
				$update_data = $api_response;

			} else {

				// remote request failed, try again in 30 minutes
				$fake_data = new stdClass;
				$fake_data->new_version = $this->get_theme_version();
				set_transient( $this->response_key, $fake_data, strtotime( '+30 minutes' ) );
				return false;

			}
		}

		// check if a new version is available. if not, abandon.
		if ( version_compare( $this->get_theme_version(), $update_data->new_version, '>=' ) ) {
			return false;
		}	

		// an update is available
		return (array) $update_data;
	}

	/**
	* Get the current theme version
	*
	* @return string The version number
	*/
	private function get_theme_version() {

		// if version was not set, get it from the Theme stylesheet
		if( $this->version === '' ) {
			$theme = wp_get_theme( $this->slug );
			return $theme->get( 'Version' );
		}

		return $this->version;
	}


}