<?php
/**
 * Plugin Name: Yoast EDD VAT Updater
 * Plugin URI: https://yoast.com/wordpress/plugins/edd-vat-updater/
 * Description: Updates your Easy Digital Downloads VAT rates using data from the <a href="http://jsonvat.com">jsonvat.com</a> database.
 * Version: 1.0
 * Author: Team Yoast
 * Author URI: https://yoast.com/
 * License: GPL2
 */

/*
	Copyright 2015 Yoast BV (email: support@yoast.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Checks whether there's a scheduled task and if not, schedules one
 */
function yst_edd_vat_rate_check_activate() {
	$timestamp = wp_next_scheduled( 'yst_edd_vat_rate_check' );

	if ( ! $timestamp ) {
		// If there's no scheduled task, schedule one for tonight at 1 minute past twelve
		wp_schedule_event( strtotime( 'tomorrow 00:01' ), 'daily', 'yst_edd_vat_rate_check' );
	}
}
register_activation_hook( __FILE__, 'yst_edd_vat_rate_check_activate' );

/**
 * When we need to check the VAT rates, instantiate the task
 */
function yst_edd_vat_rate_check() {
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		require_once 'lib/yoast-vat-updater.php';

		new Yoast_VAT_Updater();
	}
}
add_action( 'yst_edd_vat_rate_check', 'yst_edd_vat_rate_check' );