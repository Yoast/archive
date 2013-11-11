<?php
/**
 * Sub Pages Widget *
 *
 * @package      Sub Pages Widget
 * @since        1.0.0
 * @author       Joost de Valk <joost@yoast.com>
 * @copyright    Copyright (c) 2013, Joost de Valk
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! class_exists( 'YST_Sub_Pages_Widget' ) ) {
	class YST_Sub_Pages_Widget extends WP_Widget {

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
				__( 'Title', 'yoast-theme' )          => 'title',
				__( 'Show info icon', 'yoast-theme' ) => 'show_info'
			);

			$this->defaults = array(
				'title'     => __( 'More information', 'yoast-theme' ),
				'show_info' => true
			);
			$widget_ops     = array( 'classname' => 'widget_sub_pages', 'description' => 'Sub-pages widget' );
			$this->WP_Widget( 'sub-pages-widget', __( 'Yoast &mdash; Subpages', 'yoast-theme' ), $widget_ops );
		}

		/**
		 * Get the breadcrumbs title for a post
		 *
		 * @param object $post
		 *
		 * @return string
		 */
		function get_wpseo_bc_title( $post ) {
			$bc_title = get_post_meta( $post->ID, '_yoast_wpseo_bctitle', true );
			if ( $bc_title && ! empty( $bc_title ) )
				return $bc_title;
			return $post->post_title;
		}

		/**
		 * Prints the jQuery script that makes toggling the info boxes possible.
		 */
		function info_link_script() {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$(".widget_sub_pages .info").click(function () {
						var par = $(this).parent().parent().attr('id');
						$(".widget_sub_pages li .description").hide();
						$("#" + par + " .description").toggle();
						return false;
					});
				});
			</script>
		<?php
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

			if ( ! is_singular() || is_singular( 'post' ) )
				return;

			$post = get_queried_object();

			$query_args = array(
				'post_type'   => $post->post_type,
				'exclude'     => $post->ID,
				'numberposts' => 10,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'meta_query'  => array(
					array(
						'key'     => '_yoast_wpseo_meta-robots-noindex',
						'value'   => '1',
						'compare' => '!='
					)
				)
			);

			if ( $post->post_parent != 0 ) {
				$query_args['post_parent'] = $post->post_parent;
			}
			else {
				$query_args['post_parent'] = $post->ID;
			}

			$children = get_posts( $query_args );

			// Enqueue the script to be loaded in the footer, this also prevents the script from being there twice
			if ( isset( $instance['show_info'] ) && $instance['show_info'] )
				add_action( 'wp_footer', array( $this, 'info_link_script' ), 90 );

			if ( count( $children ) > 0 ) {
				$title = apply_filters( 'widget_title', $instance['title'] );

				echo $args['before_widget'];
				if ( ! empty( $title ) )
					echo $args['before_title'] . $title . $args['after_title'];

				echo '<ul>';
				if ( $post->post_parent != 0 ) {
					$parent = get_post( $post->post_parent );
					echo '<li id="info_' . $parent->post_name . '"><a title="' . $parent->post_title . '" href="' . get_permalink( $parent->ID ) . '">' . $this->get_wpseo_bc_title( $parent ) . '</a>';
					if ( isset( $instance['show_info'] ) && $instance['show_info'] && ! empty( $parent->post_excerpt ) ) {
						echo '<div class="alignright"><span class="info" href="">' . __( 'Info', 'yoast-theme' ) . '</span></div>';
						echo '<p class="description">' . $parent->post_excerpt . ' <a href="' . get_permalink( $parent->ID ) . '">' . __( 'More &raquo;.', 'yoast-theme' ) . '</a></p>';
					}
					echo '</li>';
				}
				foreach ( $children as $child ) {
					echo '<li id="info_' . $child->post_name . '"><a title="' . $child->post_title . '" href="' . get_permalink( $child->ID ) . '">' . $this->get_wpseo_bc_title( $child ) . '</a>';
					if ( isset( $instance['show_info'] ) && $instance['show_info'] && ! empty( $child->post_excerpt ) ) {
						echo '<div class="alignright"><span class="info" href="">' . __( 'Info', 'yoast-theme' ) . '</span></div>';
						echo '<p class="description">' . $child->post_excerpt . ' <a href="' . get_permalink( $child->ID ) . '">' . __( 'More &raquo;', 'yoast-theme' ) . '</a></p>';
					}
					echo '</li>';
				}
				echo '</ul>';
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
			if ( isset( $new_instance['show_info'] ) )
				$new_instance['show_info'] = true;

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
				if ( $var == 'show_info' ) {
					echo '<input class="checkbox" ' . $input_attr . ' type="checkbox" ' . checked( $instance[$var], true, false ) . '/> ';
					echo $label;
				}
				else {
					echo $label;
					echo '<input class="widefat" ' . $input_attr . ' type="text" value="' . esc_html( $instance[$var] ) . '" />';
				}
				echo '</p>';
			}

		}
	}

	/**
	 * Register the widget
	 */
	function yst_register_sub_pages_widget() {
		register_widget( 'YST_Sub_Pages_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_sub_pages_widget' );
}
