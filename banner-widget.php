<?php
/**
 * Banner widget to easily create banners *
 *
 * @package      Yoast Banner widget
 * @since        1.0.0
 * @author       Joost de Valk <joost@yoast.com>
 * @copyright    Copyright (c) 2013, Joost de Valk
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Sanity check to prevent double inclusion of this class.
if ( ! class_exists( 'YST_Banner_Widget' ) ) {

	class YST_Banner_Widget extends WP_Widget {

		/**
		 * @var array The variables used in this banner widget, the keys are the captions, which is why they need .
		 */
		var $vars = array();

		/**
		 * @var array The defaults for the values of this banner
		 */
		var $defaults = array(
			'title'        => '',
			'show_title'   => '',
			'image_url'    => '',
			'image_width'  => 250,
			'image_height' => '',
			'size_type'    => 'px',
			'class'        => '',
			'alt'          => '',
			'url'          => '',
			'post_type'    => 'page',
			'post_id'      => 0,
			'nofollow'     => false
		);

		/**
		 * Constructor
		 **/
		public function __construct() {
			$this->vars = array(
				'title'        => __( 'Title', 'yoast-theme' ),
				'show_title'   => __( 'Show title', 'yoast-theme' ),
				'image_url'    => __( 'Image URL', 'yoast-theme' ),
				'image_width'  => __( 'Image width (integer)', 'yoast-theme' ),
				'image_height' => __( 'Image height (integer)', 'yoast-theme' ),
				'size_type'    => __( 'Size in pixels or percentages', 'yoast-theme' ),
				'class'        => __( 'CSS Class', 'yoast-theme' ),
				'alt'          => __( 'Alt', 'yoast-theme' ),
				'url'          => __( 'URL', 'yoast-theme' ),
				'post_type'    => __( 'Post Type', 'yoast-theme' ),
				'post_id'      => __( 'Page To Link To', 'yoast-theme' ),
				'nofollow'     => __( 'Nofollow this link', 'yoast-theme' ),
			);

			$widget_ops = array( 'classname' => 'widget_banner' );
			$this->WP_Widget( 'yst_banner_widget', __( 'Yoast &mdash; Banner', 'yoast-theme' ), $widget_ops );
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
			// Don't show the widget if it's advertising the current page.
			if ( isset( $instance['post_id'] ) && ! empty( $instance['post_id'] ) && is_singular() ) {
				global $post;
				if ( $instance['post_id'] == $post->ID ) {
					return;
				}
			}

			if ( ! isset( $instance['image_url'] ) ) {
				return;
			}

			if ( isset( $instance['post_id'] ) && ! empty( $instance['post_id'] ) ) {
				$instance['url'] = get_permalink( $instance['post_id'] );
			}

			if ( ! isset( $instance['url'] ) ) {
				return;
			}

			// If the link has to be nofollow, well, nofollow it.
			$link_attr = '';
			if ( isset( $instance['nofollow'] ) && $instance['nofollow'] ) {
				$link_attr = 'rel="nofollow"';
			}

			if ( isset( $instance['class'] ) ) {
				$args['before_widget'] = str_replace( 'class="', 'class="yst-banner-widget ' . $instance['class'] . ' ', $args['before_widget'] );
			}

			echo $args['before_widget'];
			if ( isset ( $instance['title'] ) && ! empty( $instance['title'] ) && ( isset( $instance['show_title'] ) && '1' == $instance['show_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'yst_banner_widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
			}
			$out = '<img ';
			if ( ( isset ( $instance['image_width'] ) && ! empty( $instance['image_width'] ) ) || ( isset ( $instance['image_height'] ) && ! empty( $instance['image_height'] ) ) ) {
				$out .= 'style="';
				if ( ! empty( $instance['image_width'] ) ) {
					$out .= 'width:' . $instance['image_width'] . ( $instance['size_type'] == 'perc' ? '%' : 'px' ) . '; ';
				}

				if ( ! empty( $instance['image_height'] ) ) {
					$out .= 'height:' . $instance['image_height'] . ( $instance['size_type'] == 'perc' ? '%' : 'px' ) . '; ';
				}
				$out .= '"';
			}

			if ( ! empty( $instance['alt'] ) ) {
				$out .= 'alt="' . $instance['alt'] . '" title="' . $instance['alt'] . '" ';
			}

			$out .= apply_filters( 'yst_banner_widget_img_class', 'class="hires"' );
			$out .= 'src="' . $instance['image_url'] . '"/>';

			if ( isset( $instance['url'] ) && ! empty( $instance['url'] ) ) {
				$out = '<a ' . $link_attr . ' href="' . $instance['url'] . '">' . $out . '</a>';
			}

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
		 * @return string|void echoes output to the screen
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			foreach ( $this->vars as $var => $label ) {
				echo '<p>';
				$label = '<label for="' . $this->get_field_name( $var ) . '">' . $label . '</label>';

				$input_attr = 'name="' . $this->get_field_name( $var ) . '" id="' . $this->get_field_id( $var ) . '"';

				if ( $var == 'post_id' ) {
					echo $label;
					echo '<select class="widefat" ' . $input_attr . '>';
					if ( isset( $instance['post_id'] ) ) {
						echo '<option value="-1">' . __( 'No change', 'yoast-theme' ) . '</option>';
					}
					echo '<option value="">' . __( 'Other URL', 'yoast-theme' ) . '</option>';
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
					if ( $var == 'post_type' ) {
						echo $label;
						echo '<select class="widefat" ' . $input_attr . '>';
						foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
							$name = $post_type->name;
							if ( isset( $post_type->label ) ) {
								$name = $post_type->label;
							}

							echo '<option ' . selected( $instance[$var], $post_type->name, false ) . ' value="' . $post_type->name . '">' . esc_html( $name ) . '</option>';
						}
						echo '</select>';
					} else {
						if ( $var == 'size_type' ) {
							echo $label;
							echo '<select class="widefat" ' . $input_attr . '>';
							echo '<option value="px" ' . selected( $instance[$var], "px", false ) . '>' . __( 'pixels', 'yoast-theme' ) . '</option>';
							echo '<option value="perc" ' . selected( $instance[$var], "perc", false ) . '>' . __( 'percentages', 'yoast-theme' ) . '</option>';
							echo '</select>';
						} else {
							if ( $var == 'nofollow' || $var == 'show_title' ) {
								echo '<input class="checkbox" ' . $input_attr . ' type="checkbox" ' . checked( $instance[$var], true, false ) . '/> ';
								echo $label;
							} else {
								echo $label;
								echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" />';
								if ( $var == 'url' ) {
									echo '<br /><em>';
									_e( 'Used only if "Other page" is selected in "Page To Link To".', 'yoast-theme' );
									echo '</em>';
								}
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
			// -1 means: "no change" was selected.
			if ( $new_instance['post_id'] == '-1' ) {
				$new_instance['post_id'] = $old_instance['post_id'];
			}

			if ( isset( $new_instance['show_title'] ) ) {
				$new_instance['show_title'] = true;
			}

			if ( isset( $new_instance['nofollow'] ) ) {
				$new_instance['nofollow'] = true;
			}

			if ( isset( $new_instance['url'] ) && '#' !== $new_instance['url'] ) {
				$new_instance['url'] = esc_url( $new_instance['url'] );
			}

			if ( isset ( $new_instance['image_height'] ) && 0 != $new_instance['image_height'] ) {
				$new_instance['image_height'] = absint( $new_instance['image_height'] );
			}

			if ( isset ( $new_instance['image_width'] ) && 0 != $new_instance['image_width'] ) {
				$new_instance['image_width'] = absint( $new_instance['image_width'] );
			}

			if ( isset ( $new_instance['class'] ) ) {
				$new_instance['class'] = sanitize_text_field( $new_instance['class'] );
			}

			if ( isset ( $new_instance['size_type'] ) && $new_instance['size_type'] != 'perc' ) {
				$new_instance['size_type'] = 'px';
			}

			return $new_instance;
		}
	}

	/**
	 * Register the banner widget.
	 */
	function yst_register_banner_widget() {
		register_widget( 'YST_Banner_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_banner_widget' );

}