<?php
/**
 * Bigbutton widget to easily create "buttons" in your widget areas
 *
 * @package      Yoast Bigbutton widget
 * @since        1.0.0
 * @author       Joost de Valk <joost@yoast.com>
 * @copyright    Copyright (c) 2013, Joost de Valk
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Sanity check to prevent double inclusion of this class.
if ( ! class_exists( 'YST_Bigbutton_Widget' ) ) {

	class YST_Bigbutton_Widget extends WP_Widget {

		/**
		 * @var array The variables used in this bigbutton widget, the keys are the captions, which is why they need .
		 */
		var $vars = array();

		/**
		 * @var array The defaults for the values of this banner
		 */
		var $defaults = array(
				'title'          => '',
				'text'           => '',
				'url'            => '',
				'intern_url'     => '',
				'class_selector' => 'cta-button',
				'class'          => '',
				'target'         => '_self',
				'nofollow'       => false,
				'show_on_self'   => false
		);

		/**
		 * Constructor
		 **/
		public function __construct() {
			$this->vars = array(
					'title'          => __( 'Title', 'yoast-theme' ),
					'text'           => __( 'Text', 'yoast-theme' ),
					'url'            => __( 'URL', 'yoast-theme' ),
					'intern_url'     => __( 'Internal URL', 'yoast-theme' ),
					'class_selector' => __( 'Class Selector', 'yoast-theme' ),
					'class'          => __( 'Extra CSS Class', 'yoast-theme' ),
					'target'         => __( 'Target', 'yoast-theme' ),
					'nofollow'       => __( 'Nofollow this link', 'yoast-theme' ),
					'show_on_self'   => __( 'Show button on target page', 'yoast-theme' ),
			);

			$widget_ops = array( 'classname' => 'widget_bigbutton' );
			$this->WP_Widget( 'yst_bigbutton_widget', __( 'Yoast &mdash; Big Button', 'yoast-theme' ), $widget_ops );
		}

		/**
		 * Outputs the HTML for this widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     An array of standard parameters for widgets in this theme
		 * @param array $instance An array of settings for this widget instance
		 *
		 * @return void Echoes its output
		 **/
		public function widget( $args, $instance ) {

			// Merge arguments
			$instance = wp_parse_args( $instance, $this->defaults );

			// Check if the widget is allowed to display on self
			if ( false === $instance['show_on_self'] ) {

				// Don't show the widget if it's linking to the current page.
				if ( isset( $instance['intern_url'] ) && ! empty( $instance['intern_url'] ) && is_singular() ) {
					global $post;
					if ( $instance['intern_url'] == $post->ID ) {
						return;
					}
				}

			}

			if ( ! isset( $instance['url'] ) ) {
				$instance['url'] = $_SERVER['HTTP_HOST'];
			}

			if ( ! isset( $instance['text'] ) ) {
				$instance['text'] = '';
			}

			if ( ! isset( $instance['target'] ) ) {
				$instance['target'] = '_self';
			}

			// If the link has to be nofollow, well, nofollow it.
			if ( isset( $instance['nofollow'] ) && $instance['nofollow'] ) {
				$link_attr = 'rel="nofollow"';
			}

			if ( isset( $instance['class_selector'] ) && ( $instance['class_selector'] != 'none' ) ) {
				$args['before_widget'] = str_replace( 'class="', 'class="' . $instance['class_selector'] . ' ', $args['before_widget'] );
			}

			if ( isset( $instance['class'] ) ) {
				$args['before_widget'] = str_replace( 'class="', 'class="' . $instance['class'] . ' ', $args['before_widget'] );
			}

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'yst_bigbutton_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
			}

			if ( isset ( $instance['intern_url'] ) && ! empty ( $instance['intern_url'] ) ) {
				$url = get_permalink( $instance['intern_url'] );
			} else {
				$url = $instance['url'];
			}

			$out = '<a ';
			if ( isset ( $url ) && ! empty( $url ) ) {
				$out .= 'href="' . $url . '" ';
			}

			if ( ! empty( $instance['text'] ) ) {
				$out .= 'alt="' . $instance['text'] . '" title="' . $instance['text'] . '" ';
			}

			if ( ! empty( $instance['target'] ) ) {
				$out .= 'target="' . $instance['target'] . '" ';
			}

			if ( isset ( $link_attr ) ) {
				$out .= $link_attr;
			}

			$out .= '>';
			$out .= '<span>';

			if ( ! empty( $instance['text'] ) ) {
				$out .= $instance['text'];
			}

			$out .= '</span>';
			$out .= '</a>';

			echo $out;

			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 *
		 * @return string|void
		 */
		public function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, $this->defaults );

			foreach ( $this->vars as $var => $label ) {
				echo '<p>';
				$label = '<label for="' . $this->get_field_id( $var ) . '">' . $label . '</label>';

				$input_attr = 'name="' . $this->get_field_name( $var ) . '" id="' . $this->get_field_id( $var ) . '"';

				if ( $var == 'intern_url' ) {
					echo $label;
					echo '<select class="widefat" ' . $input_attr . '>';
					echo '<option value="">' . __( 'Use external URL', 'yoast-theme' ) . '</option>';
					foreach ( get_posts(
												array(
														'post_type'   => ( isset( $instance['post_type'] ) ? $instance['post_type'] : 'page' ),
														'numberposts' => - 1,
														'orderby'     => 'title',
														'order'       => 'ASC'
												)
										) as $post ) {
						echo '<option ' . selected( $instance[$var], $post->ID, false ) . ' value="' . $post->ID . '">' . esc_html( $post->post_title ) . '</option>';
					}
					echo '</select>';
				} else {
					if ( 'nofollow' == $var || 'show_on_self' == $var ) {
						echo '<input class="checkbox" ' . $input_attr . ' type="checkbox" ' . checked( $instance[$var], true, false ) . '/> ';
						echo $label;
					} else {
						if ( $var == 'target' ) {
							echo $label;
							echo '<select class="widefat" ' . $input_attr . '>';
							echo '<option ' . selected( $instance[$var], '_self', false ) . ' value="_self">' . __( 'This page', 'yoast-theme' ) . '</option>';
							echo '<option ' . selected( $instance[$var], '_blank', false ) . ' value="_blank">' . __( 'New page', 'yoast-theme' ) . '</option>';
							echo '</select>';
						} else {
							if ( $var == 'class_selector' ) {
								echo $label;
								echo '<select class="widefat" ' . $input_attr . '>';
								echo '<option ' . selected( $instance[$var], 'cta-button', false ) . ' value="cta-button">' . __( 'Action Button', 'yoast-theme' ) . '</option>';
								echo '<option ' . selected( $instance[$var], 'cta-button-light', false ) . ' value="cta-button-light">' . __( 'Light Button', 'yoast-theme' ) . '</option>';
								echo '<option ' . selected( $instance[$var], 'cta-button-medium', false ) . ' value="cta-button-medium">' . __( 'Normal Button', 'yoast-theme' ) . '</option>';
								echo '<option ' . selected( $instance[$var], 'cta-button-dark', false ) . ' value="cta-button-dark">' . __( 'Dark Button', 'yoast-theme' ) . '</option>';
								echo '<option ' . selected( $instance[$var], 'none', false ) . ' value="none">' . __( 'Disable standard class', 'yoast-theme' ) . '</option>';
								echo '</select>';
							} else {
								echo $label;
								echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" />';
							}
						}
					}
				}

				echo '</p>';
			}
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			if ( isset( $new_instance['nofollow'] ) ) {
				$new_instance['nofollow'] = true;
			}

			if ( isset( $new_instance['show_on_self'] ) ) {
				$new_instance['show_on_self'] = true;
			}

			if ( isset( $new_instance['url'] ) ) {
				$new_instance['url'] = esc_url( $new_instance['url'] );
			}

			// If we have a Post ID, it's easy to prefill the alt based on the post title and the class based on the post_name
			if ( isset ( $new_instance['url'] ) && ( $new_instance['intern_url'] != '-1' || ! empty( $new_instance['intern_url'] ) ) ) {
				if ( isset( $new_instance['intern_url'] ) && ! empty( $new_instance['intern_url'] ) &&
						( ! isset( $new_instance['class'] ) || empty( $new_instance['class'] ) )
				) {
					$p = get_post( $new_instance['intern_url'] );
					if ( ( ! isset( $new_instance['class'] ) || empty( $new_instance['class'] ) ) && isset( $p->post_name ) && ! empty ( $p->post_name ) ) {
						$new_instance['class'] = $p->post_name . '-bigbutton';
					}
				}
			}

			return $new_instance;
		}
	}

	/**
	 * Register the bigbutton widget.
	 */
	function yst_register_bigbutton_widget() {
		register_widget( 'YST_Bigbutton_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_bigbutton_widget' );

}