<?php

/* Build the Yoast BV main support class */

class Yoast_Support_Framework {

	private $question;
	private $error;
	private $text_domain;

	public function __construct() {
		if ( isset( $_GET['admin'] ) ) {
			if ( $_GET['admin'] == 'sent' ) {
				if ( $this->create_admin_details() ) {
					add_settings_error( 'yoast_support-notices', 'yoast_support-error', __( 'The user is created successfully!', 'yoast-support-framework' ), 'updated' );
				} else {
					add_settings_error( 'yoast_support-notices', 'yoast_support-error', __( 'There was an error while creating the new user', 'yoast-support-framework' ), 'error' );
				}
			} elseif ( $_GET['admin'] == 'remove' ) {
				if ( $this->remove_admin_details() ) {
					add_settings_error( 'yoast_support-notices', 'yoast_support-error', __( 'The user is removed successfully!', 'yoast-support-framework' ), 'updated' );
				} else {
					add_settings_error( 'yoast_support-notices', 'yoast_support-error', __( 'The user couldn&#8217;t be removed', 'yoast-support-framework' ), 'error' );
				}
			}
		}

		if ( isset( $_POST['getsupport'] ) && false != wp_verify_nonce( $_REQUEST['_wpnonce'], 'yoast-support-request' ) ) {
			$data = $_POST;
			if ( $this->validate( $data ) ) {
				$type    = 'updated';
				$message = sprintf( __( 'Your question is successfully submitted to %s.', 'yoast-support-framework' ), '<a href="https://yoast.com" target="_blank">Yoast</a>' );
			} else {
				$type    = 'error';
				$message = $this->get_error(); // Get the translated error
			}

			add_settings_error( 'yoast_support-notices', 'yoast_support-error', __( $message, 'yoast-support-framework' ), $type );

		}

		$user = $this->find_admin_user();
		settings_errors( 'yoast_support-notices' );
		add_action( 'admin_notices', 'yoast_support_admin_messages' );
	}

	/**
	 * Validate the post data and start pushing on success
	 * Returns true on success, false on fai
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function validate( $data ) {
		if ( ! empty( $data['yoast_support']['question'] ) ) {
			$this->question = array(
				'question'  => $data['yoast_support']['question'],
				'site_info' => $this->get_support_info()
			);

			if ( $this->push_data( 'https://yoast.com/support-request', $this->question, 'Question about a Yoast plugin' ) ) {
				return true;
			} else {
				$this->error = __( 'Couldn&#8217;t sent your question to Yoast.', 'yoast-support-framework' );

				return false;
			}
		} else {
			$this->error = __( 'Please fill in a question in the form below.', 'yoast-support-framework' );

			return false;
		}
	}

	/**
	 * Return the i18n support message that is default in the support message field
	 *
	 * @return mixed
	 */
	public function support_message() {
		return __( 'Write your question here and provide as much info as you know to get a detailed answer from our support team.', 'yoast-support-framework' );
	}

	/**
	 * Create an admin account and push the data
	 *
	 * @return bool
	 */
	public function create_admin_details() {
		$website  = "https://yoast.com";
		$password = wp_generate_password( 12, true, true );
		$userdata = array(
			'user_login' => $this->generate_username(),
			'user_url'   => $website,
			'user_pass'  => $password,
			'user_email' => 'pluginsupport@yoast.com',
			'role'       => 'administrator'
		);

		$user_id               = wp_insert_user( $userdata );
		$pushdata              = $userdata;
		$pushdata['admin_url'] = admin_url();

		if ( $this->push_data( 'https://yoast.com/support-request', $pushdata, 'Admin details for Yoast admin' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generate a new Yoast admin username
	 *
	 * @return string
	 */
	private function generate_username() {
		return 'yoast_' . strtolower( wp_generate_password( 10, false, false ) );
	}

	/**
	 * Remove the created admin account ( $this->createAdminDetails() )
	 *
	 * @return bool
	 */
	public function remove_admin_details() {
		$user = $this->find_admin_user();

		if ( isset( $user->ID ) ) {
			wp_delete_user( $user->ID );

			return true;
		} else {
			return false;
		}

	}

	/**
	 * Find our admin user
	 *
	 * @return mixed
	 */
	public function find_admin_user() {
		return get_user_by(
			'email',
			'pluginsupport@yoast.com'
		);
	}

	/**
	 * Return all support info in one array
	 *
	 * @return array
	 */
	private function get_support_info() {
		return array(
			'wp_version'  => get_bloginfo( 'version' ),
			'wp_plugins'  => $this->get_wp_plugins(),
			'wp_themes'   => $this->get_wp_themes(),
			'wp_userinfo' => $this->get_user_info(),
			'url'         => get_bloginfo( 'url' ),
			'server_info' => $this->get_server_info(),
			'mysql'       => $this->get_mysql_info()
		);
	}

	/**
	 * Central function to return the error message to the user
	 *
	 * @return mixed
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Return a view file
	 *
	 * @param $item
	 *
	 * @return mixed
	 */
	public function get_view( $item ) {
		if ( file_exists( plugin_dir_path( __FILE__ ) . "views/" . $item . ".php" ) ) {
			ob_start();
			$yoast_support = $this;
			require( plugin_dir_path( __FILE__ ) . "views/" . $item . ".php" );
			unset( $yoast_support );
			$view = ob_get_clean();
		} else {
			$view = __( 'View not found!', 'yoast-support-framework' );
		}

		return $view;
	}

	/**
	 * Get text domain for translations
	 *
	 * @return mixed
	 */
	public function get_text_domain() {
		return 'yoast-support-framework';
	}

	#######################
	# Collect all data    #
	# - Private functions #
	#######################

	/**
	 * Return all WP Plugins (Name, plugin url and version)
	 *
	 * @return array
	 */
	private function get_wp_plugins() {
		$plugins    = array();
		$wp_plugins = get_plugins();

		if ( count( $wp_plugins ) >= 1 ) {
			foreach ( $wp_plugins as $name => $pluginInfo ) {
				if ( is_plugin_active( $name ) == 1 && ! empty( is_plugin_active( $name ) ) ) {
					$plugins[] = array(
						'name'       => $pluginInfo['Name'],
						'plugin_uri' => $pluginInfo['PluginURI'],
						'version'    => $pluginInfo['Version']
					);
				}
			}
		}

		return $plugins;
	}

	/**
	 * Return an array with all logged in user info
	 *
	 * @return array
	 */
	private function get_user_info() {
		global $current_user;
		get_currentuserinfo();

		return array(
			'username' => $current_user->user_login,
			'email'    => $current_user->user_email,
			'first'    => $current_user->user_firstname,
			'last'     => $current_user->user_lastname,
			'display'  => $current_user->display_name,
		);
	}

	/**
	 * Return the WP Themes
	 *
	 * @return array
	 */
	private function get_wp_themes() {
		$themes = array();
		if ( function_exists( 'wp_get_themes' ) ) {
			$wp_themes = wp_get_themes();
		} else {
			$wp_themes = get_themes();
		}

		if ( count( $wp_themes ) >= 1 ) {
			foreach ( $wp_themes as $themeInfo ) {
				$themes[] = array(
					'name'    => $themeInfo['Name'],
					'version' => $themeInfo['Version']
				);
			}
		}

		return $themes;
	}

	/**
	 * Return the server info
	 *
	 * @return array
	 */
	private function get_server_info() {
		return array(
			'engine'      => $_SERVER['SERVER_SOFTWARE'],
			'user'        => $_SERVER['USER'],
			'gateway'     => $_SERVER['GATEWAY_INTERFACE'],
			'server_port' => $_SERVER['SERVER_PORT'],
			'server_name' => $_SERVER['SERVER_NAME'],
			'encoding'    => $_SERVER['HTTP_ACCEPT_ENCODING'],
			'php_version' => phpversion(),
			'php_modules' => $this->get_php_modules()
		);
	}

	/**
	 * Get the phpmodules with all its version numbers
	 * @return array
	 */
	private function get_php_modules() {
		$modules = array();

		foreach ( get_loaded_extensions() as $ext ) {
			$modules[$ext] = phpversion( $ext );
		}

		return $modules;
	}

	/**
	 * Get all MySQL info of this database connection
	 *
	 * @return array
	 */
	private function get_mysql_info() {
		return array(
			'server'   => mysql_get_server_info(),
			'client'   => mysql_get_client_info(),
			'host'     => mysql_get_host_info(),
			'protocol' => mysql_get_proto_info(),
			'charset'  => mysql_client_encoding()
		);
	}

	/**
	 * Push data to Yoast
	 *
	 * @param $url
	 * @param $data
	 * @param $mail_fail_title
	 *
	 * @return bool
	 */
	private function push_data( $url, $data, $mail_fail_title ) {
		$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array( 'data' => json_encode( $data ) ),
				'cookies'     => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$this->error   = sprintf( __('Something went wrong: %s'), $error_message);

			// Need to mail it because the https post fails
			$user = $this->question['wp_userinfo'];

			$headers[] = 'From: ' . $user['first'] . ' ' . $user['last'] . ' <' . $user['email'] . '>';
			$message   = $data;

			if ( wp_mail( 'pluginsupport@yoast.com', $mail_fail_title, $message, $headers ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}

	}

}