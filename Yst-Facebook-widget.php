<?php
/**
 * Yoast Facebook Widget *
 *
 * @package      Yoast Facebook Widget
 * @since        1.0.0
 * @author       Taco Verdonschot
 * @copyright    Copyright (c) 2013, Yoast BV
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class YST_Facebook_Widget extends WP_Widget {

	/**
	 * Constructor of class YST_Facebook_Widget
	 *
	 * @since 1.0.0
	 */
	function YST_Facebook_Widget() {
		$this->labels = array(
			'title'            => __( 'Widget title', 'yoast-theme' ),
			'data_href'        => __( 'Facebook URL', 'yoast-theme' ),
			'data_width'       => __( 'Width', 'yoast-theme' ),
			'data_height'      => __( 'Height', 'yoast-theme' ),
			'data_colorscheme' => __( 'Colorscheme', 'yoast-theme' ),
			'data_show_faces'  => __( 'Display profile pictures', 'yoast-theme' ),
			'data_header'      => __( 'Show header', 'yoast-theme' ),
			'data_stream'      => __( 'Show stream', 'yoast-theme' ),
			'data_show_border' => __( 'Show border', 'yoast-theme' ),
			'data_force_wall'  => __( 'Show check-ins only', 'yoast-theme' )
		);

		$this->defaults = array(
			'title'            => __( 'Find us on Facebook', 'yoast-theme' ),
			'data_href'        => 'https://www.facebook.com/yoast',
			'data_width'       => 250,
			'data_height'      => 300,
			'data_colorscheme' => 'light',
			'data_show_faces'  => true,
			'data_header'      => false,
			'data_stream'      => false,
			'data_show_border' => false,
			'data_force_wall'  => false
		);

		$widget_ops = array( 'classname' => 'yst_fb_widget', 'description' => __( 'Yoast Facebook Widget: Easily add a Facebook Like-box to your widgets!', 'yoast-theme' ) );
		$this->WP_Widget( 'widget-yst-fb', __( 'Yoast &mdash; Facebook', 'yoast-theme' ), $widget_ops );

		add_action( 'get_header', array( $this, 'output_fb_root_div' ), 1 );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @see   WP_Widget::form()
	 * @since 1.0.0
	 *
	 * @param array $instance An array of the current settings for this widget
	 *
	 * @return void Echoes its output
	 **/
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		// Loop through all the variables to display them
		foreach ( $this->labels as $var => $label ) {
			$input_attr = 'name="' . $this->get_field_name( $var ) . '" id="' . $this->get_field_id( $var ) . '"';
			$label      = '<label for="' . $this->get_field_id( $var ) . '">' . $label . '</label>';

			echo '<p>';
			if ( $var == 'title' ) {
				echo $label;
				echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . strip_tags( $instance[$var] ) . '" />';
			}
			else if ( $var == 'data_href' ) {
				echo $label;
				echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" placeholder="' . esc_attr( $this->defaults['data_href'] ) . '" />';
			}
			else if ( $var == 'data_colorscheme' ) {
				echo $label;
				echo ' <select ' . $input_attr . '>';

				// Add option 'light'
				$output = '<option value="light" ';
				if ( $instance[$var] == 'light' ) {
					$output .= 'selected="yes"';
				}
				$output .= '>' . __( 'Light', 'yoast-theme' ) . '</option>';
				// Add option 'dark'
				$output .= '<option value="dark"';
				if ( $instance[$var] == 'dark' ) {
					$output .= 'selected="yes"';
				}
				$output .= '>' . __( 'Dark', 'yoast-theme' ) . '</option>';
				echo $output;
				echo '</select>';
			}
			else if ( $var == 'data_width' || $var == 'data_height' ) {
				echo $label;
				echo ' <input class="number-validation" ' . $input_attr . ' type="number" step="1" value="' . esc_html( $instance[$var] ) . '" /> ';
			}
			else {
				echo '<input class="checkbox" ' . $input_attr . ' type="checkbox" ' . checked( $instance[$var], true, false ) . '/> ';
				echo $label;
				if ( $var == 'data_force_wall' ) {
					echo '<br />(only applies to "Place"-pages)';
				}
			}
			echo '</p>';
		}

	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @see   WP_Widget::update()
	 * @since 1.0.0
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings
	 *
	 * @return array The validated and (if necessary) amended settings
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		foreach ( $this->labels as $var => $label ) {
			if ( ( $old_instance[$var] || $old_instance[$var] == false ) && ! isset ( $new_instance[$var] ) ) {
				$instance[$var] = false;
			}
			if ( isset( $new_instance[$var] ) ) {
				switch ( $var ) {
					case 'title':
						$instance[$var] = sanitize_text_field( $new_instance[$var] );
						break;
					case 'data_href':
						$instance[$var] = esc_url( $new_instance[$var] );
						break;
					case 'data_width':
					case 'data_height':
						$instance[$var] = absint( $new_instance[$var] );
						break;
					case 'data_colorscheme':
						if ( $new_instance[$var] == 'dark' ) {
							$instance[$var] = 'dark';
						}
						else {
							$instance[$var] = 'light';
						}
						break;
					default:
						$instance[$var] = true;
				}
			}
		}

		return $instance;
	}

	/**
	 * Helper function to add necessary div to body
	 *
	 * @since 1.0.0
	 */
	function output_fb_root_div() {
		?><div id="fb-root"></div><?php
	}

	/**
	 * Helper function to add necessary JS to footer
	 *
	 * @since 1.0.0
	 */
	function output_fb_script() {
		?><script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script><?php
	}

	/**
	 * Outputs the JavaScript and HTML for the widget
	 *
	 * @see   WP_Widget::widget()
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 */
	function widget( $args, $instance ) {

		foreach ( $this->labels as $var => $label ) {
			if ( ! isset ( $instance[$var] ) || ( $instance[$var] != false && empty ( $instance[$var] ) ) ) {
				echo '<p class="widget widget-error">' . __( 'Please fill in the details of the Yoast Facebook Widget', 'yoast-theme' ) . '</p>';
				return;
			}
		}

		add_action( 'wp_footer', array( $this, 'output_fb_script' ) );

		echo $args['before_widget'];

		if ( isset ( $instance['title'] ) ) {
			$title = $args['before_title'] . strip_tags( $instance['title'] ) . $args['after_title'];
			$title = apply_filters( 'yst_fb_title', $title );
			echo $title;
		}

		if ( $instance['data_colorscheme'] === 'dark' ) {
			$class = 'class="fb-like-box fb-dark-bg"';
		}
		else {
			$class = 'class="fb-like-box fb-light-bg"';
		}

		?>
		<div <?php echo $class; ?>
				data-href="<?php echo esc_url( $instance['data_href'] ); ?>"
				data-width="<?php echo absint( $instance['data_width'] ); ?>"
				data-height="<?php echo absint( $instance['data_height'] ); ?>"
				data-colorscheme="<?php echo esc_attr( $instance['data_colorscheme'] ); ?>"
				data-show-faces="<?php echo( $instance['data_show_faces'] == true ? 'true' : 'false' ); ?>"
				data-header="<?php echo( $instance['data_header'] == true ? 'true' : 'false' ); ?>"
				data-stream="<?php echo( $instance['data_stream'] == true ? 'true' : 'false' ); ?>"
				data-show-border="<?php echo( $instance['data_show_border'] == true ? 'true' : 'false' ); ?>"
				data-force-wall="<?php echo( $instance['data_force_wall'] == true ? 'true' : 'false' ); ?>">
		</div>
		<?php
		echo $args['after_widget'];
	}
}

/**
 * Register the widget
 */
function yst_register_yst_facebook_widget() {
	register_widget( 'YST_Facebook_Widget' );
}

add_action( 'widgets_init', 'yst_register_yst_facebook_widget' );