<?php

namespace Yoast\WP\Crawl_Cleanup\Frontend;

use WP_Query;
use Yoast\WP\Crawl_Cleanup\Options\Options;

/**
 * The frontend class that cleans up search result spam.
 */
class Clean_Searches {

	/**
	 * Patterns to match against to find spam.
	 *
	 * @var array
	 */
	private array $patterns = [
		'/[：（）【】［］]+/u',
		'/(TALK|QQ)\:/iu',
	];

	/**
	 * Holds our options instance.
	 *
	 * @var Options
	 */
	private Options $options;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->options = Options::instance();

		add_action( 'wp_loaded', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register our Search related hooks.
	 */
	public function register_hooks(): void {
		add_filter( 'pre_get_posts', [ $this, 'validate_search' ] );
	}

	/**
	 * Check if we want to allow this search to happen.
	 *
	 * @param WP_Query $query The main query.
	 *
	 * @return WP_Query
	 */
	public function validate_search( WP_Query $query ): WP_Query {
		if ( ! $query->is_search() ) {
			return $query;
		}
		// First check against emoji and patterns we might not want.
		$this->check_unwanted_patterns( $query );

		// Then limit the number of words in the search query.
		$this->limit_words();

		// Then limit characters if still needed.
		$this->limit_characters();

		// No need to redirect as the clean permalink code will automatically do that.
		return $query;
	}

	/**
	 * Check query against unwanted search patterns.
	 *
	 * @param WP_Query $query The main WordPress query.
	 *
	 * @return void
	 */
	private function check_unwanted_patterns( WP_Query $query ): void {
		$s = rawurldecode( $query->query_vars['s'] );
		if ( $this->options->search_cleanup_emoji && $this->has_emoji( $s ) ) {
			$this->redirect_away( 'We don\'t allow searches with emoji.' );
		}

		if ( ! $this->options->search_cleanup_patterns ) {
			return;
		}
		foreach ( $this->patterns as $pattern ) {
			$outcome = preg_match( $pattern, $s, $matches );
			if ( $outcome && $matches !== [] ) {
				$this->redirect_away( 'Your search matched a common spam pattern.' );
			}
		}
	}

	/**
	 * Redirect to the homepage for invalid searches.
	 *
	 * @param string $reason The reason for redirecting away.
	 *
	 * @return void
	 */
	private function redirect_away( string $reason ): void {
		wp_safe_redirect( get_home_url(), 301, 'Yoast Crawl Cleanup: ' . $reason );
		exit;
	}

	/**
	 * Limits the number of words in the search query.
	 *
	 * @return void
	 */
	private function limit_words(): void {
		$s = get_search_query();
		if ( str_word_count( $s, 0 ) > $this->options->search_word_limit ) {
			$words    = str_word_count( $s, 2 );
			$position = array_keys( $words );
			$new_s    = rtrim( substr( $s, 0, $position[ $this->options->search_word_limit ] ), ' ' );
			set_query_var( 's', $new_s );
		}
	}

	/**
	 * Limits the number of characters in the search query.
	 *
	 * @return void
	 */
	private function limit_characters(): void {
		$s = get_search_query();
		if ( strlen( $s ) > $this->options->search_character_limit ) {
			$new_s = substr( $s, 0, $this->options->search_character_limit );
			set_query_var( 's', $new_s );
		}
	}

	/**
	 * Determines if a string contains an emoji or not.
	 *
	 * @param string $string The string to detect emoji in.
	 *
	 * @return bool
	 */
	private function has_emoji( string $string ): bool {
		$emojis_regex =
			'/[\x{0080}-\x{02AF}'
			.'\x{0300}-\x{03FF}'
			.'\x{0600}-\x{06FF}'
			.'\x{0C00}-\x{0C7F}'
			.'\x{1DC0}-\x{1DFF}'
			.'\x{1E00}-\x{1EFF}'
			.'\x{2000}-\x{209F}'
			.'\x{20D0}-\x{214F}'
			.'\x{2190}-\x{23FF}'
			.'\x{2460}-\x{25FF}'
			.'\x{2600}-\x{27EF}'
			.'\x{2900}-\x{29FF}'
			.'\x{2B00}-\x{2BFF}'
			.'\x{2C60}-\x{2C7F}'
			.'\x{2E00}-\x{2E7F}'
			.'\x{3000}-\x{303F}'
			.'\x{A490}-\x{A4CF}'
			.'\x{E000}-\x{F8FF}'
			.'\x{FE00}-\x{FE0F}'
			.'\x{FE30}-\x{FE4F}'
			.'\x{1F000}-\x{1F02F}'
			.'\x{1F0A0}-\x{1F0FF}'
			.'\x{1F100}-\x{1F64F}'
			.'\x{1F680}-\x{1F6FF}'
			.'\x{1F910}-\x{1F96B}'
			.'\x{1F980}-\x{1F9E0}]/u';
		preg_match($emojis_regex, $string, $matches);
		return !empty($matches);
	}
}
