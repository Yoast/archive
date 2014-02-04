<?php

class Yoast_Settings_Helper {

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Function to override genesis settings with theme_mod settings
	 *
	 * @param string  $setting
	 * @param string  $value
	 * @param boolean $checkbox
	 *
	 * @return string
	 */
	public function override_setting( $setting, $value, $checkbox = false ) {
		$theme_setting = get_theme_mod( $setting );
		if ( isset( $theme_setting ) && ! empty( $theme_setting ) ) {
			return $theme_setting;
		} else {
			if ( $checkbox ) {
				return false;
			}
		}

		return $value;
	}

} 