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
		'/[：（）【】❤⛓［］]+/u',
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
		// First check against patterns we don't want.
		if ( $this->options->search_cleanup_characters ) {
			$this->check_unwanted_patterns( $query );
		}

		// Then limit words.
		$this->limit_words();

		// Then limit characters if still needed.
		$this->limit_characters();

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
		foreach ( $this->patterns as $pattern ) {
			$outcome = preg_match( $pattern, $s, $matches );
			if ( $outcome && $matches !== [] ) {
				$this->redirect_away();
			}
		}
	}

	/**
	 * Redirect to the homepage for invalid searches.
	 *
	 * @return void
	 */
	private function redirect_away(): void {
		wp_safe_redirect( get_home_url(), 301, 'Yoast Crawl Cleanup: We don\'t allow searches like that.' );
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
}
