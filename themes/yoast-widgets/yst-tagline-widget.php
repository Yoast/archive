<?php
/**
 * Tagline Widget *
 *
 * @package      Yoast Tagline widget
 * @since        1.0.0
 * @author       Taco Verdonschot <taco@yoast.com>
 * @copyright    Copyright (c) 2013, Yoast BV
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Sanity check to prevent double inclusion of this class.
if ( ! class_exists( 'YST_Tagline_Widget' ) ) {
	class YST_Tagline_Widget extends WP_Widget {

		/**
		 * Constructor
		 */
		function __construct() {
			$widget_ops = array( 'classname' => 'widget_tagline_yst', 'description' => 'Yoast Tagline Widget' );
			$this->WP_Widget( 'yst_tagline_widget', __( 'Yoast &mdash; Tagline Widget', 'yoast-theme' ), $widget_ops );
		}

		/**
		 * Outputs the HTML for this widget.
		 *
		 * @param array  An array of standard parameters for widgets in this theme
		 * @param array  An array of settings for this widget instance
		 *
		 * @return void Echoes its' output
		 **/
		function widget( $args, $instance ) {
			$tagline = html_entity_decode( get_bloginfo( 'description' ) );
			if ( ! empty( $tagline ) ) {
				echo $args['before_widget'];
				echo apply_filters( 'yst_tagline_widget_tagline', '<p class="yst_tagline">' . $tagline . '</p>' );
				echo $args['after_widget'];
			}
		}

		/**
		 * Deals with the settings when they are saved by the admin. Here is
		 * where any validation should be dealt with.
		 *
		 * @param array  An array of new settings as submitted by the admin
		 * @param array  An array of the previous settings
		 *
		 * @return array Empty array, since there are no settings.
		 **/
		function update( $new_instance, $old_instance ) {
			return array();
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @param array  An array of the current settings for this widget, unused in this widget.
		 *
		 * @return string To let WordPress know there's nothing to save.
		 **/
		function form( $instance ) {
			$tagline = get_bloginfo( 'description' );
			echo '<p class="tagline_description"><strong>';
			_e( 'Current tagline', 'yoast-theme' );
			echo '</strong></p>';
			echo '<p class="tagline_content" style="padding:10px;">';
			echo( ! empty( $tagline ) ? $tagline : 'You have no tagline.' );
			echo '</p><p class="yst_tagline_explanation"></p>';
			echo __( 'You can set your tagline in <a href="' . admin_url( 'options-general.php' ) . '">Settings -> General</a>.', 'yoast-theme' );
			echo '</p>';

			return 'noform';
		}
	}

	function yst_register_tagline_widget() {
		register_widget( 'YST_Tagline_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_tagline_widget' );
}