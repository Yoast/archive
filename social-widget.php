<?php
/**
 * Social Widget *
 *
 * @package      Yoast Social widget
 * @since        1.0.0
 * @author       Joost de Valk <joost@yoast.com>
 * @copyright    Copyright (c) 2013, Joost de Valk
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Sanity check to prevent double inclusion of this class.
if ( ! class_exists( 'YST_Social_Widget' ) ) {
	class YST_Social_Widget extends WP_Widget {

		/**
		 * @var array The defaults for the values
		 */
		var $defaults = array(
			'yst_title'      => '',
			'yst_facebook'   => '',
			'yst_twitter'    => '',
			'yst_linkedin'   => '',
			'yst_googleplus' => '',
			'yst_youtube'    => '',
			'yst_pinterest'  => '',
			'yst_rss'        => ''
		);

		var $defaults_flw = array(
			'yst_title_flw'      => '',
			'yst_facebook_flw'   => '',
			'yst_twitter_flw'    => '',
			'yst_linkedin_flw'   => '',
			'yst_googleplus_flw' => '',
			'yst_youtube_flw'    => '',
			'yst_pinterest_flw'  => '',
			'yst_rss_flw'        => '',
		);

		/**
		 * Constructor
		 */
		function __construct() {
			$this->vars = array(
				'yst_title'      => __( 'Title', 'yoast-theme' ),
				'yst_facebook'   => __( 'Facebook', 'yoast-theme' ),
				'yst_twitter'    => __( 'Twitter', 'yoast-theme' ),
				'yst_linkedin'   => __( 'LinkedIn', 'yoast-theme' ),
				'yst_googleplus' => __( 'Google+', 'yoast-theme' ),
				'yst_youtube'    => __( 'YouTube', 'yoast-theme' ),
				'yst_pinterest'  => __( 'Pinterest', 'yoast-theme' ),
				'yst_rss'        => __( 'RSS', 'yoast-theme' ),
			);

			$this->flw = array(
				'yst_facebook_flw'   => __( 'Facebook Likes', 'yoast-theme' ),
				'yst_twitter_flw'    => __( 'Twitter Followers', 'yoast-theme' ),
				'yst_linkedin_flw'   => __( 'LinkedIn Connections', 'yoast-theme' ),
				'yst_googleplus_flw' => __( 'Google+ Pluses', 'yoast-theme' ),
				'yst_youtube_flw'    => __( 'YouTube Subscribers', 'yoast-theme' ),
				'yst_pinterest_flw'  => __( 'Pinterest Followers', 'yoast-theme' ),
				'yst_rss_flw'        => __( 'RSS Readers', 'yoast-theme' ),
			);

			$control_ops = array( 'width' => 300 );
			$widget_ops  = array( 'classname' => 'widget_socials', 'description' => 'Social icon widget' );
			$this->WP_Widget( 'yst_social_widget', __( 'Yoast &mdash; Social Widget', 'yoast-theme' ), $widget_ops, $control_ops );
		}

		/**
		 * Outputs the HTML for this widget.
		 *
		 * @param array  An array of standard parameters for widgets in this theme
		 * @param array  An array of settings for this widget instance
		 *
		 * @return void Echoes it's output
		 **/
		function widget( $args, $instance ) {
			echo $args['before_widget'];

			$instance = wp_parse_args( (array) $instance, $this->defaults );

			if ( isset ( $instance['yst_title'] ) && ! empty( $instance['yst_title'] ) ) {
				echo $args['before_title'] . $instance['yst_title'] . $args['after_title'];
			}

			echo '<div id="yst_social_widget">';
			foreach ( $this->vars as $var => $label ) {
				if ( isset ( $instance[$var] ) && ! empty ( $instance[$var] ) ) {
					switch ( $var ) {
						case 'yst_facebook':
							$class = "btn-fb";
							break;
						case 'yst_twitter':
							$class = "btn-tw";
							break;
						case 'yst_linkedin':
							$class = "btn-li";
							break;
						case 'yst_googleplus':
							$class = "btn-gp";
							break;
						case 'yst_youtube':
							$class = "btn-yt";
							break;
						case 'yst_pinterest':
							$class = "btn-pin";
							break;
						case 'yst_rss':
							$class = "btn-rss";
							break;
						default:
							$class = "btn-default";
					}

					$input_attr = 'class= "' . $class . '" name="' . $var . '" id="' . $this->get_field_id( $var ) . '"';

					if ( $var == 'yst_title' ) {
						continue;
					} else {
						if ( $var == 'yst_rss' ) {
							echo '<a href="' . site_url( "feed" ) . '" ' . $input_attr . ' alt="' . $label . '" target="_blank"><div class="ysw_flw_wrapper"><div class="ysw_flw">' . $this->kformat( (int) $instance[( $var . '_flw' )] ) . '</div></div></a>';
						} else {
							echo '<a href="' . $instance[$var] . '" ' . $input_attr . ' alt="' . $label . '" target="_blank"><div class="ysw_flw_wrapper"><div class="ysw_flw">' . $this->kformat( (int) $instance[( $var . '_flw' )] ) . '</div></div></a>';
						}
					}
				}
			}
			echo '</div>';
			echo $args['after_widget'];
		}

		/**
		 * Deals with the settings when they are saved by the admin. Here is
		 * where any validation should be dealt with.
		 *
		 * @param array  An array of new settings as submitted by the admin
		 * @param array  An array of the previous settings
		 *
		 * @return array The validated and (if necessary) amended settings
		 **/
		function update( $new_instance, $old_instance ) {
			$new_instance['yst_title']      = sanitize_text_field( $new_instance['yst_title'] );
			$new_instance['yst_facebook']   = esc_url( $new_instance['yst_facebook'] );
			$new_instance['yst_twitter']    = esc_url( $new_instance['yst_twitter'] );
			$new_instance['yst_linkedin']   = esc_url( $new_instance['yst_linkedin'] );
			$new_instance['yst_googleplus'] = esc_url( $new_instance['yst_googleplus'] );
			$new_instance['yst_youtube']    = esc_url( $new_instance['yst_youtube'] );
			$new_instance['yst_pinterest']  = esc_url( $new_instance['yst_pinterest'] );

			$new_instance['yst_facebook_flw']   = (int) $new_instance['yst_facebook_flw'];
			$new_instance['yst_twitter_flw']    = (int) $new_instance['yst_twitter_flw'];
			$new_instance['yst_linkedin_flw']   = (int) $new_instance['yst_linkedin_flw'];
			$new_instance['yst_googleplus_flw'] = (int) $new_instance['yst_googleplus_flw'];
			$new_instance['yst_youtube_flw']    = (int) $new_instance['yst_youtube_flw'];
			$new_instance['yst_pinterest_flw']  = (int) $new_instance['yst_pinterest_flw'];
			$new_instance['yst_rss_flw']        = (int) $new_instance['yst_rss_flw'];
			if ( isset( $new_instance['yst_rss'] ) ) {
				$new_instance['yst_rss'] = true;
			}

			return $new_instance;
		}

		/**
		 * Output numbers with postfixes.
		 *
		 * @param $number An integer
		 *
		 * @return string
		 */
		function kformat( $number ) {
			$prefixes = 'kMGTPEZY';
			if ( $number >= 1000 ) {
				$log1000 = (int) floor( log10( $number ) / 3 );

				return (int) floor( $number / pow( 1000, $log1000 ) ) . $prefixes[$log1000 - 1];
			}

			return $number;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @param array  An array of the current settings for this widget
		 *
		 * @return void Echoes it's output
		 **/
		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			foreach ( $this->vars as $var => $label ) {
				$flw_var = $var . "_flw";

				if ( $var != 'yst_title' ) {
					$flw_label = $this->flw[$var . '_flw'];
					$labelflw  = '<label for="' . $this->get_field_name( $flw_var ) . '">' . $flw_label . '</label>';
				}

				$label      = '<label for="' . $this->get_field_name( $var ) . '">' . $label . '</label>';
				$input_attr = 'name="' . $this->get_field_name( $var ) . '" id="' . $this->get_field_id( $var ) . '"';

				if ( $var == 'yst_title' ) {
					echo '<strong>' . $label . '</strong>';
					$input_attr .= 'placeholder="Title on the page. E.g. Follow Us!"';
					echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . $instance[$var] . '"/>';

				} else {
					if ( $var == 'yst_rss' ) {
						echo '<strong>' . $label . '</strong><br />';
						echo '<input class="checkbox" ' . $input_attr . ' type="checkbox" ' . checked( $instance[$var], true, false ) . '/>';
						echo ' Show ' . $label . '<br /><br />';
						echo $labelflw;
						echo '<input class="widefat" type="number" name="' . $this->get_field_name( $flw_var ) . '" value="' . $instance[$flw_var] . '" />';
					} else {
						echo '<strong>' . $label . '</strong><br />';
						echo $label . __( ' url', 'yoast-theme' );
						echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" />';
						echo $labelflw;
						echo '<input class="widefat" type="number" name="' . $this->get_field_name( $flw_var ) . '" value="' . $instance[$flw_var] . '" />';
					}
				}
				echo '</p>';
			}

		}
	}

	function yst_register_social_widget() {
		register_widget( 'YST_Social_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_social_widget' );
}