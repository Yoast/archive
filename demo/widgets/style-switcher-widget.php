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

if ( ! class_exists( 'YST_Style_Switcher_Widget' ) ) {
	
	class YST_Style_Switcher_Widget extends WP_Widget {

		/**
		 * @var array The variables used in this banner widget, the keys are the captions, which is why they need .
		 */
		var $vars = array();

		/**
		 * @var array
		 */
		var $defaults = array();

		/**
		 * Constructor
		 **/
		function __construct() {
			$this->vars = array(
				__( 'Title', 'yoast-theme' ) => 'title',
			);
			
			$this->defaults = array(
				'title'     => __( 'Style switcher', 'yoast-theme' ),
			);
			
			$widget_ops = array( 'classname' => 'widget_style_switcher', 'description' => 'Style switcher widget' );
			$this->WP_Widget( 'widget-style-switcher', __( 'Yoast &mdash; Style Switcher', 'yoast-theme' ), $widget_ops );
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

			$cur_color_scheme = get_theme_mod( 'yst_colour_scheme' );
			
			foreach ( glob( CHILD_DIR . "/assets/css/*.css" ) as $file ) {

				// Clean out the path
				$file = str_replace( CHILD_DIR . "/assets/css/", "", $file );

				preg_match( '/(.+).css/', $file, $matches );
				if ( isset ( $matches[1] ) ) {
					if ( in_array( $matches[1], array( 'forms', 'editor-style', 'old-ie' ) ) ) {
						continue;
					}

					$colours[$matches[1]] = trim( preg_replace( '/([A-Z])/', ' $1', $matches[1] ) );
				}
			}

			if ( count( $colours ) > 0 ) {
				$title = apply_filters( 'widget_title', $instance['title'] );

				echo $args['before_widget'];
				if ( ! empty( $title ) ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}

				echo '<select id="style_switcher">';
				foreach ( $colours as $val => $colour ) {
					$sel = '';
					if ( $val == $cur_color_scheme )
						$sel = 'selected';
					echo '<option '.$sel.' value="'.$val.'">'.$colour.'</option>';
				}
				echo '</select>';
				?>
				<script>
				jQuery(document).ready(function ($) {
					$('#style_switcher').change( function() {
						var url = [location.protocol, '//', location.host, location.pathname].join('');
						document.location = url + '?color_scheme=' + $('#style_switcher').val();
					});
				});
				</script>
				<?php
				echo $args['after_widget'];
			}

		}

		/**
		 * Deals with the settings when they are saved by the admin. Here is
		 * where any validation should be dealt with.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance An array of new settings as submitted by the admin
		 * @param array $old_instance An array of the previous settings
		 *
		 * @return array The validated and (if necessary) amended settings
		 **/
		function update( $new_instance, $old_instance ) {
			return $new_instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance An array of the current settings for this widget
		 *
		 * @return void Echoes it's output
		 **/
		function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, $this->defaults );

			foreach ( $this->vars as $label => $var ) {
				$input_attr = 'name="' . $this->get_field_name( $var ) . '" id="' . $this->get_field_id( $var ) . '"';
				$label      = '<label for="' . $this->get_field_name( $var ) . '">' . $label . '</label>';

				echo '<p>';
				echo $label;
				echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" />';
				echo '</p>';
			}

		}
	}

	/**
	 * Register the widget
	 */
	function yst_register_style_switcher_widget() {
		register_widget( 'YST_Style_Switcher_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_style_switcher_widget' );
}
