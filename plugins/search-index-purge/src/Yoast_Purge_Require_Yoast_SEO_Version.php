<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * The class to check for environment requirements.
 */
final class Yoast_Purge_Require_Yoast_SEO_Version {
	/**
	 * Registers hooks to WordPress.
	 */
	public function register_hooks() {
		$hook = 'admin_notices';
		if ( $this->use_multisite_notifications() ) {
			$hook = 'network_' . $hook;
		}

		add_action( $hook, array( $this, 'show_admin_notices' ) );
	}

	/**
	 * Checks if the required version is present and active.
	 *
	 * @return bool True if Yoast SEO has an acceptable version installed and activated.
	 */
	public function has_required_version() {
		return $this->is_yoast_seo_up_to_date();
	}

	/**
	 * Shows any queued admin notices.
	 *
	 * @return void
	 */
	public function show_admin_notices() {
		if ( $this->is_iframe_request() ) {
			return;
		}

		if ( ! $this->has_required_version() ) {
			$this->display_admin_error( sprintf(
				/* translators: %1$s expands to Yoast SEO. */
				esc_html__(
					'Please upgrade the %1$s plugin to the latest version to allow the Yoast SEO: Search Index Purge plugin to work.',
					'yoast-search-index-purge'
				),
				'Yoast SEO'
			) );
		}
	}

	/**
	 * Checks if the current request is an iFrame request.
	 *
	 * @return bool True if this request is an iFrame request.
	 */
	private function is_iframe_request() {
		return defined( 'IFRAME_REQUEST' ) && IFRAME_REQUEST !== false;
	}

	/**
	 * Displays an admin error.
	 *
	 * @param string $message Notice to display.
	 *
	 * @return void
	 */
	private function display_admin_error( $message ) {
		echo '<div class="error"><p>' . $message . '</p></div>';
	}

	/**
	 * Checks if Yoast SEO is active.
	 *
	 * @return bool True if Yoast SEO is active.
	 */
	private function is_yoast_seo_active() {
		return defined( 'WPSEO_VERSION' );
	}

	/**
	 * Checks if Yoast SEO is at a minimum required version.
	 *
	 * @return bool True if Yoast SEO is at a minimal required version.
	 */
	private function is_yoast_seo_up_to_date() {
		return $this->is_yoast_seo_active() && version_compare( WPSEO_VERSION, '7.5.3', '>=' );
	}

	/**
	 * Checks whether we should use multisite notifications or not.
	 *
	 * @return bool True if we want to use multisite notifications.
	 */
	private function use_multisite_notifications() {
		return is_multisite() && is_network_admin();
	}
}
