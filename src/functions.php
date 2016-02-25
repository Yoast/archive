<?php
/**
 * @package Yoast\WP\HelpScout
 */

/**
 * Retrieve the instance of the beacon
 *
 * @param string $page The current admin page.
 *
 * @return WPSEO_HelpScout_Beacon
 */
function yoast_get_helpscout_beacon( $page ) {
	static $beacon;

	if ( ! isset( $beacon ) ) {
		$beacon = new WPSEO_HelpScout_Beacon( $page, array() );
	}

	return $beacon;
}
