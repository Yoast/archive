<?php
/**
 * Yoast SEO: Search index purge plugin file.
 *
 * @package   WPSEO\Main
 * @copyright Copyright (C) 2018, Yoast BV - support@yoast.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

/**
 * This class will override the Media content tab content.
 */
class Yoast_Purge_Media_Settings_Tab_Content {
	/**
	 * Registers the WordPress hooks and filters.
	 */
	public function register_hooks() {
		add_filter( 'wpseo_option_tab-metas_media', array( $this, 'get_content' ) );
	}

	/**
	 * Retrieves the content that needs to be shown instead of the default Media configuration tab.
	 */
	public function get_content() {
		$content = '';

		$content .= sprintf(
			'<h1>%s</h1>',
			__( 'These settings are being overridden by the Search Index Purge plugin.', 'yoast-search-index-purge' )
		);

		$content .= sprintf(
			'<p>%s</p>',
			__( 'You are actively purging attachment URLs out of Google\'s search index.', 'yoast-search-index-purge' )
		);

		$content .= sprintf(
			'<p>%s</p>',
			sprintf(
				/* translators: %1$s expands to the link to the article, %2$s expands to the closing tag of the link */
				__( 'Read more about the %1$sSearch Index Purge plugin%2$s.', 'yoast-search-index-purge' ),
				'<a href="' . esc_attr( WPSEO_Shortlinker::get( 'https://yoa.st/2r8' ) ) . '" target="_blank" rel="noopener nofollow">',
				'</a>'
			)
		);

		return $content;
	}
}
