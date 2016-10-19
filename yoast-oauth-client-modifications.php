<?php

/*
Plugin Name: Yoast OAuth client modifications
Description: Improves the my.yoast OAuth flow. This plugin hides the normal login fields and redirect the user to my.yoast when editing their profile.
Version: 1.0
Author: Team Yoast
Author URI: https://Yoast.com
*/

namespace Yoast\YoastCom\OAuthClientMods;

include_once 'autoloader.php';

/**
 * Class Yoast_OAuth_Client
 *
 * Hides the normal login fields and redirect the user to my.yoast when editing their profile.
 */
class Yoast_OAuth_Client_Modifications {

	private $profile_redirects;

	private $login_styler;

	public function __construct() {
		$this->init_profile_redirects();
		$this->init_login_styler();
	}

	/**
	 * Initializes the profile page redirects to my.yoast.com
	 */
	private function init_profile_redirects() {
		$this->profile_redirects = new Profile_Redirects();
		$this->profile_redirects->register_hooks();
	}

	/**
	 * initializes the login page style overwrites.
	 */
	private function init_login_styler() {
		$this->login_styler = new Login_Styler();
		$this->login_styler->register_hooks();
	}
}

new Yoast_OAuth_Client_Modifications();
