<?php
/**
 * Style Switcher Widget *
 *
 * @package      Style Switcher Widget
 * @since        1.0.0
 * @author       Joost de Valk <joost@yoast.com>
 * @copyright    Copyright (c) 2014, Joost de Valk
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'YST_Header_Style_Switcher_Widget' ) ) {
	
	class YST_Header_Style_Switcher_Widget extends WP_Widget {

		/**
		 * Constructor
		 **/
		function __construct() {
			$widget_ops = array( 'classname' => 'widget_header_style_switcher', 'description' => 'Header Style switcher widget' );
			$this->WP_Widget( 'widget-header-style-switcher', __( 'Yoast &mdash; Header Style Switcher', 'yoast-theme' ), $widget_ops );
		}

		/**
		 * Outputs the HTML for this widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     An array of standard parameters for widgets in this theme
		 * @param array $instance An array of settings for this widget instance
		 *
		 * @return void Echoes it's output
		 **/
		function widget( $args, $instance ) {

			$title = 'Switch Header Style';

			echo $args['before_widget'];
			echo $args['before_title'] . $title . $args['after_title'];
			?>
			<select id="header_style_switcher">
				<option value="dark">Dark</option>
				<option value="light">Light</option>
			</select>
			<script>
			jQuery(document).ready(function ($) {
				// Preload the dark logo
				$('<img/>')[0].src = 'http://versatile.yoastdemo.com/wp-content/themes/versatile/assets/images/logo-dark.png';
				
				if ( $('body').hasClass('header-dark') ) {
					$('#header_style_switcher').val('dark');
				} else {
					$('#header_style_switcher').val('light');
				}
				$('#header_style_switcher').change( function() {
					var style = $(this).val();
					$('body').removeClass('header-dark');
					$('body').removeClass('header-light');
					$('body').addClass('header-'+style);
					
					if ( style == 'light' ) {
						$('.site-header .title-area').css('background-image','url(http://versatile.yoastdemo.com/wp-content/themes/versatile/assets/images/logo-dark.png)');
					} else {
						$('.site-header .title-area').css('background-image','url(http://versatile.yoastdemo.com/wp-content/themes/versatile/assets/images/logo.png)');
					}
				});
			});
			</script>
			<?php
			echo $args['after_widget'];

		}

	}

	/**
	 * Register the widget
	 */
	function yst_register_header_style_switcher_widget() {
		register_widget( 'YST_Header_Style_Switcher_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_header_style_switcher_widget' );
}
