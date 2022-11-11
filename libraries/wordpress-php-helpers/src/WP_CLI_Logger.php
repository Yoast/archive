<?php

namespace Yoast\WordPress;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * PSR-3 logger for WP CLI.
 */
final class WP_CLI_Logger extends AbstractLogger {

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return void
	 */
	public function log( $level, $message, array $context = array() ) {
		switch ( $level ) {
			case LogLevel::WARNING:
				\WP_CLI::warning( $message );
				break;
			case LogLevel::ERROR:
			case LogLevel::ALERT:
			case LogLevel::EMERGENCY:
			case LogLevel::CRITICAL:
				\WP_CLI::error( $message );
				break;
			case LogLevel::INFO:
			case LogLevel::DEBUG:
				\WP_CLI::debug( $message );
				break;
			case LogLevel::NOTICE:
			default:
				\WP_CLI::log( $message );
				break;
		}
	}
}
