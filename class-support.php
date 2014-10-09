<?php

/* Build the main support class */

if ( ! class_exists( 'Yoast_Support_Framework' ) ) {
	class Yoast_Support_Framework {

		/**
		 * @var    string    The customer's question or bug report
		 */
		private $question;

		/**
		 * @var    string    If we have an error on validation, it is stored here
		 */
		private $error;

		/**
		 * @var        array    Store the registered plugins here, other plugins can register itself
		 */
		public $registered_plugins;

		/**
		 * @var    object    Instance of this class
		 */
		public static $instance;

		/**
		 * @var    string    Company name for the Support class
		 */
		private static $company_name;

		/**
		 * @var    string    Company url for the Support class
		 */
		private static $company_url;

		/**
		 * @var    string    Company email address for sending the support questions
		 */
		private static $company_support_email;

		/**
		 * @var    string    Company URL to push data to, eg. the support question with the WP envoirement data
		 */
		private static $company_support_push_url;

		/**
		 * Create the Support framework
		 *
		 * @param string $company_name
		 * @param string $company_url
		 * @param string $company_support_email
		 * @param string $company_support_push_url
		 */
		public function __construct( $company_name, $company_url, $company_support_email, $company_support_push_url ) {
			self::$company_name             = $company_name;
			self::$company_url              = $company_url;
			self::$company_support_email    = $company_support_email;
			self::$company_support_push_url = $company_support_push_url;

			$this->registered_plugins = array();

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
					$message = sprintf( __( 'Your question is successfully submitted to %s.', 'yoast-support-framework' ), '<a href="' . self::$company_url . '" target="_blank">' . self::$company_name . '</a>' );
				} else {
					$type    = 'error';
					$message = $this->get_error(); // Get the translated error
				}

				add_settings_error( 'yoast_support-notices', 'yoast_support-error', $message, $type );
			}

			$user = $this->find_admin_user();
			settings_errors( 'yoast_support-notices' );
			add_action( 'admin_notices', 'yoast_support_admin_messages' );
			add_action( 'admin_init', array( $this, 'init_yoast_support' ) );
		}

		/**
		 * Get the singleton instance of this class
		 *
		 * @return object
		 */
		public static function get_instance() {
			if ( ! ( self::$instance instanceof self ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Initiate the Yoast Support framework
		 */
		public function init_yoast_support() {
			add_action( 'admin_menu', array( $this, 'hook_menu' ) );
			add_filter( 'support_framework_plugins', array( $this, 'support_plugins_filter' ), 1 );
		}

		/**
		 * Register a new plugin through this WP filter (Filter name: support_framework_plugins)
		 *
		 * @param $plugins
		 *
		 * @return mixed
		 */
		public function support_plugins_filter( $plugins ) {
			$plugins = array_merge( $plugins, $this->registered_plugins );

			$this->registered_plugins = $plugins;

			return $plugins;
		}

		/**
		 * Hook on a menu to add the support class as an item
		 *
		 * @param array $submenu_pages
		 *
		 * @return array
		 */
		public function hook_menu( $submenu_pages = array() ) {
			$submenu_pages[] = array(
				'wpseo_dashboard',
				'',
				'<span style="color:#f18500">' . __( 'Support', 'yoast-support-framework' ) . '</span>',
				$manage_options_cap,
				'wpseo_support',
				array( $this, 'load_page' ),
				null,
			);

			return $submenu_pages;
		}

		/**
		 * Validate the post data and start pushing on success
		 * Returns true on success, false on fai
		 *
		 * @param array $data
		 *
		 * @return bool
		 */
		public function validate( $data ) {
			if ( ! empty( $data['yoast_support']['question'] ) ) {
				$this->question = array(
					'question'  => $data['yoast_support']['question'],
					'site_info' => $this->get_support_info()
				);

				if ( isset( $data['yoast_support']['plugin'] ) ) {
					$this->question['plugin'] = $data['yoast_support']['question'];
				}

				if ( $this->push_data( self::$company_support_push_url, $this->question, sprintf( __( 'Question about a %s plugin', 'yoast-support-framework' ), self::$company_name ) ) ) {
					return true;
				} else {
					$this->error = sprintf( __( 'Couldn&#8217;t sent your question to %s.', 'yoast-support-framework' ), self::$company_name );

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
		 * @return string
		 */
		public function support_message() {
			return __( 'Write your question here and provide as much info as you know to get a detailed answer from our support team.', 'yoast-support-framework' );
		}

		/**
		 * Create an admin account and push the data
		 *
		 * @return bool
		 */
		private function create_admin_details() {
			$password = wp_generate_password( 12, true, true );
			$userdata = array(
				'user_login' => $this->generate_username(),
				'user_url'   => self::$company_url,
				'user_pass'  => $password,
				'user_email' => self::$company_support_email,
				'role'       => 'administrator'
			);

			$user_id               = wp_insert_user( $userdata );
			$pushdata              = $userdata;
			$pushdata['admin_url'] = admin_url();

			if ( $this->push_data( self::$company_support_push_url, $pushdata, sprintf( __( 'Admin details for %s admin', 'yoast-support-framework' ), self::$company_name ) ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Generate a new admin username for the support functionality
		 *
		 * @return string
		 */
		private function generate_username() {
			return strtolower( str_replace( ' ', '', self::$company_name ) ) . '_' . strtolower( wp_generate_password( 10, false, false ) );
		}

		/**
		 * Remove the created admin account ( $this->createAdminDetails() )
		 *
		 * @return bool
		 */
		private function remove_admin_details() {
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
				self::$company_support_email
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
		 * @return string
		 */
		public function get_error() {
			return $this->error;
		}

		/**
		 * Return a view file
		 *
		 * @param string $item
		 *
		 * @return mixed
		 */
		public function get_view( $item ) {
			if ( file_exists( plugin_dir_path( __FILE__ ) . "views/" . $item . ".php" ) ) {
				ob_start();

				if ( $item == 'form' ) {
					$yoast_plugins = $this->get_wp_yoast_plugins();
				}

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
		 * Get the Yoast plugins which are active on this WordPress site
		 *
		 * @return array
		 */
		public function get_wp_yoast_plugins() {
			$plugins       = $this->get_wp_plugins();
			$yoast_plugins = array(
				'WordPress SEO',
				'Local SEO',
				'Video SEO',
				'WooCommerce SEO',
				'Google Analytics by Yoast',
				'eCommerce Tracking',
			);

			foreach ( $plugins as $key => $plugin ) {
				if ( ! in_array( $plugin['name'], $yoast_plugins ) ) {
					unset( $plugins[$key] );
				} else {
					$plugin['id']  = strtolower( str_replace( ' ', '-', $plugin['name'] ) );
					$plugins[$key] = $plugin;
				}
			}

			return $plugins;
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
			$themes    = array();
			$wp_themes = wp_get_themes();

			if ( is_array( $wp_themes ) && count( $wp_themes ) >= 1 ) {
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
		 * Get the PHP modules with all its version numbers
		 *
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
		 * Push data to the company
		 *
		 * @param string $url
		 * @param array  $data
		 * @param string $mail_fail_title
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
				$this->error   = sprintf( __( 'Something went wrong: %s' ), $error_message );

				// Need to mail it because the https post fails
				$user = $this->question['wp_userinfo'];

				$headers[] = 'From: ' . $user['first'] . ' ' . $user['last'] . ' <' . $user['email'] . '>';
				$message   = $data;

				if ( wp_mail( self::$company_support_email, $mail_fail_title, $message, $headers ) ) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}

		}

	}
}