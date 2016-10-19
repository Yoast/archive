<?php


namespace Yoast\YoastCom\OAuthClientMods;


/**
 * Class Login_Styler
 * @package Yoast\YoastCom\OAuthClientMods
 */
class Login_Styler {

	/**
	 * Registers hooks to style the login page.
	 */
	public function register_hooks() {
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_login_scripts' ) );
	}

	/**
	 * enqueues the style sheet to alter the login page.
	 */
	public function enqueue_login_scripts() {
		if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
			wp_enqueue_style( 'yoast-hide-login', plugins_url( '../assets/login-style.css', __FILE__ ) );
		}
	}
}
