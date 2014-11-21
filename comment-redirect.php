<?php
/*
Plugin Name: Comment Redirect by Yoast
Version: 1.1.3
Plugin URI: https://yoast.com/wordpress/plugins/comment-redirect/
Description: Redirect commenters who just made their first comment to a page of your choice. On this page, you could ask them to subscribe, like you on Facebook and lots more!
Author: Team Yoast
Author URI: https://yoast.com/
Text Domain: comment-redirect
License: GPL v3

Comment Redirect plugin: Redirect first time commenters
Copyright (C) 2008-2014, Joost de Valk - support@yoast.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Yoast Comment Redirect plugin. Redirects first time commenters to a special page.
 * @package Yoast_Comment_Redirect
 * @author Joost de Valk
 * @copyright Copyright (C) 2008-2014, Yoast BV
 */

/**
 * Class containing all the plugins functionality.
 * @package Yoast_Comment_Redirect
 */
class Yoast_Comment_Redirect {
	
	/**
	 * Hook into the proper hooks for setting up both the admin page as well as the settings array
	 *
	 * @since 1.0
	 */
	function __construct() {
		// Hook into init for registration of the option and the language files
		add_action(	'admin_init', array( $this, 'init' ) );

		// Hook for adding the config page
		add_action( 'admin_menu', array( $this, 'add_config_page' ) );

		// Filter the redirect URL
		add_filter( 'comment_post_redirect', array( $this, 'comment_redirect' ), 10, 2 );
	}
	
	/**
	 * PHP4 style constructor for PHP4 compatibility
	 *
	 * @since 1.0
	 */
	function CommentRedirect_Admin() {
		$this->__construct();
	}

	/**
	 * Register the config page for all users that have the manage_options capability
	 *
	 * @since 0.1
	 */
	function add_config_page() {
		add_plugins_page( __( 'Comment Redirect Configuration', 'comment-redirect' ), __( 'Comment Redirect', 'comment-redirect' ), 'manage_options', 'comment-redirect', array( $this, 'config_page' ) );
	}

	/**
	 * Register the textdomain and the options array along with the validation function
	 *
	 * @since 1.0
	 */
	function init() {
		// Allow for localization
		load_plugin_textdomain( 'comment-redirect', false, basename( dirname( __FILE__ ) ) . '/languages' );
		
		// Register our option array
		register_setting( 'CommentRedirect_options', 'CommentRedirect', array( $this, 'options_validate' ) );
	}
	
	/**
	 * Validate the input, make sure page is an integer.
	 *
	 * @since 1.0
	 * @param array $input with unvalidated options.
	 * @return array $newinput with validated options.
	 */
	function options_validate( $input ) {
		$newinput['page'] = (int) $input['page'];
		return $newinput;
	}
	
	/**
	 * Display the config page, the settings form uses the Settings API.
	 *
	 * @since 0.1
	 */
	function config_page() {
		if ( !current_user_can( 'manage_options' ) ) 
			die( __( 'You cannot edit the Comment Redirect options.', 'comment-redirect' ) );

		$options = get_option( 'CommentRedirect' );
		
		global $wp_version;

		// Since WP 3.2 outputs these errors by default, only display them when we're on versions older than 3.2 that do support the settings errors.
		if ( version_compare( $wp_version, '3.2', '<' ) )
			settings_errors();
		
		// Show the content of the options array when debug is enabled
		if ( WP_DEBUG )
			echo '<pre>Options:<br/><br/>' . print_r( $options , 1 ) . '</pre>';
		?>
		<div class="wrap">
			<h2><?php _e( 'Comment Redirect Configuration', 'comment-redirect' ); ?></h2>
			<form action="options.php" method="post">
				<?php settings_fields('CommentRedirect_options'); ?>
				<p><?php _e( 'Select the page below that a first time commenter should be redirected to:', 'comment-redirect' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">
							<label for="page_id"><?php _e( 'Redirect to', 'comment-redirect' ); ?>:</label>
						</th>
						<td><?php 
						
						// A dropdown of all pages in the current WP install.
						wp_dropdown_pages( array(
							'name' 		=> 'CommentRedirect[page]',
							'depth'		=> 0,
							'selected' 	=> isset( $options['page'] ) ? $options['page'] : 0,
						) ); 
						
						?></td>
					</tr>						
				</table>
				<br/>
				<p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'comment-redirect' ); ?>" /></p>
			</form>
		</div>
<?php	}

	/**
	 * Check whether the current commenter is a first time commenter, if so, redirect them to the specified settings.
	 * 
	 * @since 0.1
	 *
	 * @param string $url the original redirect URL
	 * @param object $comment the comment object
	 * @return string $url the URL to be redirected to, altered if this was a first time comment.
	 */
	function comment_redirect( $url, $comment ) {
		$cc = get_comments( array( 'author_email' => $comment->comment_author_email, 'count' => true ) );

		if ( 1 == $cc ) {
			$options = get_option( 'CommentRedirect' );
			// Only change $url when the page option is actually set and not zero
			if ( isset( $options['page'] ) && 0 != $options['page'] ) {
				$url = get_permalink( $options['page'] );
				
				// Allow other plugins to hook when the user is being redirected, for analytics calls or even to change the target URL.
				$url = apply_filters( 'yoast_comment_redirect', $url, $comment );
			}
		} 

		return $url;
	}
	
} // End of Yoast_Comment_Redirect class

// Instantiate our new class
$yoast_comment_redirect = new Yoast_Comment_Redirect();
