<?php

class Yoast_Option_Helper {

	/**
	 * Function to override genesis settings with theme_mod settings
	 *
	 * @param string  $setting
	 * @param string  $value
	 * @param boolean $checkbox
	 *
	 * @return string
	 */
	public static function override_setting( $setting, $value, $checkbox = false ) {
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

	/**
	 * Get the license key option name
	 *
	 * @param $theme_name
	 *
	 * @return string
	 */
	public static function get_license_key_option_name( $theme_name ) {
		return 'yoast_theme_license_key_' . sanitize_title_with_dashes( $theme_name );
	}

} 