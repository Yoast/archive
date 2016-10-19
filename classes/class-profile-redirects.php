<?php

namespace Yoast\YoastCom\OAuthClientMods;


/**
 * Class Profile_Redirects
 * @package Yoast\YoastCom\OAuthClientMods
 */
class Profile_Redirects {

	/**
	 * Registers hooks to redirect profile (edit) pages to my.yoast.com
	 */
	public function register_hooks() {
		add_filter( 'current_screen', array( $this, 'redirect_profile_pages' ) );
	}

	/**
	 * Redirects the profile (edit) pages to my.yoast.com
	 */
	public function redirect_profile_pages() {

		$current_screen = get_current_screen();

		switch ( $current_screen->base ) {
			case 'profile':
				
				$redirect_url = apply_filters( 'yoast:domain', 'my.yoast.com' ) . '/wp-admin/profile.php';
				wp_redirect( $redirect_url );
				break;
			case 'user-edit':
				// get user Id from my.yoast
				// if it exists, redirect. if not show the normal edit page??
				break;
		}
	}
}
