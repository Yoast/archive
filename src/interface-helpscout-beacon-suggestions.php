<?php

interface Yoast_HelpScout_Beacon_Suggestions {

	/**
	 * Returns a list of helpscout hashes to show the user for a certain page.
	 *
	 * @param string $page The current admin page we are on.
	 *
	 * @return array A list of suggestions for the beacon
	 */
	public function get_suggestions( $page );
}
