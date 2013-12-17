<?php
/**
 * Yoast User Profile Widget *
 *
 * @package      Yoast User Profile Widget
 * @since        1.0.0
 * @author       Taco Verdonschot
 * @copyright    Copyright (c) 2013, Yoast BV
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 * Based on 'Genesis User Profile widget' by author StudioPress.
 */

if ( ! class_exists( 'YST_User_Profile_Widget' ) ) {
	class YST_User_Profile_Widget extends WP_Widget {

		/**
		 * Holds widget settings defaults, populated in constructor.
		 *
		 * @var array
		 */
		protected $defaults;
		protected $social;

		/**
		 * Constructor. Set the default widget options and create widget.
		 */
		function YST_User_Profile_Widget() {

			$this->defaults = array(
				'title'           => '',
				'alignment'       => 'left',
				'user'            => '',
				'size'            => '45',
				'author_info'     => '',
				'bio_text'        => '',
				'page'            => '',
				'page_link_text'  => __( 'Read More', 'yoast-theme' ) . '&#x02026;',
				'posts_link'      => '',
				'show_twitter'    => '',
				'show_facebook'   => '',
				'show_googleplus' => '',
				'show_pinterest'  => '',
				'show_linkedin'   => '',
			);

			$widget_ops = array(
				'classname'   => 'yst-user-profile',
				'description' => __( 'Show a user profile to your visitors and/or guide them to that users\' social media.', 'yoast-theme' ),
			);

			$control_ops = array(
				'id_base' => 'yst-user-profile',
				'width'   => 200,
				'height'  => 250,
			);

			//$this->WP_Widget( 'widget-yst-up', __( 'Yoast &mdash; User Profile', 'yoast-theme' ), $widget_ops );
			parent::__construct( 'yst-user-profile', __( 'Yoast - Extended User Profile', 'yoast-theme' ), $widget_ops, $control_ops );
		}

		/**
		 * Echo the widget content.
		 *
		 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget
		 */
		function widget( $args, $instance ) {

			//* Merge with defaults
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
			}

			$text = '';

			if ( ! empty( $instance['alignment'] ) )
				$text .= '<span class="align' . esc_attr( $instance['alignment'] ) . '">';

			$text .= get_avatar( $instance['user'], $instance['size'] );

			if ( ! empty( $instance['alignment'] ) )
				$text .= '</span>';

			if ( 'text' === $instance['author_info'] )
				$text .= $instance['bio_text']; //* We run KSES on update
			else
				$text .= get_the_author_meta( 'description', $instance['user'] );

			$text .= $instance['page'] ? sprintf( ' <a class="pagelink" href="%s">%s</a>', get_page_link( $instance['page'] ), $instance['page_link_text'] ) : '';

			//* Echo $text
			echo wpautop( $text );

			//* If posts link option checked, add posts link to output
			if ( $instance['posts_link'] )
				printf( '<div class="posts_link posts-link"><a href="%s">%s</a></div>', get_author_posts_url( $instance['user'] ), __( 'View My Blog Posts', 'yoast-theme' ) );

			if ( '1' === $instance['show_twitter'] || '1' === $instance['show_facebook'] || '1' === $instance['show_googleplus'] || '1' === $instance['show_pinterest'] || '1' === $instance['show_linkedin'] ) {
				echo '<div class="">Or read more on</div><div class="social">';
				if ( '1' === $instance['show_twitter'] && '' != get_the_author_meta( 'twitter', $instance['user'] ) ) {
					printf( '<div class="%s" id="%s"><a href="http://twitter.com/%s"></a></div>', 'btn-tw', $this->get_field_id( 'show_twitter' ), get_the_author_meta( 'twitter', $instance['user'] ) );
				}
				if ( '1' === $instance['show_facebook'] && '' != get_the_author_meta( 'facebook', $instance['user'] ) ) {
					printf( '<div class="%s" id="%s"><a href="%s"></a></div>', 'btn-fb', $this->get_field_id( 'show_twitter' ), esc_url( get_the_author_meta( 'facebook', $instance['user'] ) ) );
				}
				if ( '1' === $instance['show_googleplus'] && '' != get_the_author_meta( 'googleplus', $instance['user'] ) ) {
					printf( '<div class="%s" id="%s"><a href="%s"></a></div>', 'btn-gp', $this->get_field_id( 'show_twitter' ), esc_url( get_the_author_meta( 'googleplus', $instance['user'] ) ) );
				}
				if ( '1' === $instance['show_linkedin'] && '' != get_the_author_meta( 'linkedin', $instance['user'] ) ) {
					printf( '<div class="%s" id="%s"><a href="%s"></a></div>', 'btn-li', $this->get_field_id( 'show_twitter' ), esc_url( get_the_author_meta( 'linkedin', $instance['user'] ) ) );
				}
				if ( '1' === $instance['show_pinterest'] && '' != get_the_author_meta( 'pinterest', $instance['user'] ) ) {
					printf( '<div class="%s" id="%s"><a href="%s"></a></div>', 'btn-pin', $this->get_field_id( 'show_twitter' ), esc_url( get_the_author_meta( 'pinterest', $instance['user'] ) ) );
				}
				echo '</div>';
			}

			echo $args['after_widget'];

		}

		/**
		 * Update a particular instance.
		 *
		 * This function should check that $new_instance is set correctly.
		 * The newly calculated value of $instance should be returned.
		 * If "false" is returned, the instance won't be saved/updated.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via form()
		 * @param array $old_instance Old settings for this instance
		 *
		 * @return array Settings to save or bool false to cancel saving
		 */
		function update( $new_instance, $old_instance ) {

			$new_instance['title']          = strip_tags( $new_instance['title'] );
			$new_instance['bio_text']       = current_user_can( 'unfiltered_html' ) ? $new_instance['bio_text'] : genesis_formatting_kses( $new_instance['bio_text'] );
			$new_instance['page_link_text'] = strip_tags( $new_instance['page_link_text'] );
			if ( ! isset( $new_instance['posts_link'] ) || $new_instance['posts_link'] != '1' ) {
				unset( $new_instance['posts_link'] );
			}
			if ( ! isset( $new_instance['show_twitter'] ) || $new_instance['show_twitter'] !== '1' ) {
				unset( $new_instance['show_twitter'] );
			}
			if ( ! isset( $new_instance['show_facebook'] ) || $new_instance['show_facebook'] !== '1' ) {
				unset( $new_instance['show_facebook'] );
			}
			if ( ! isset( $new_instance['show_linkedin'] ) || $new_instance['show_linkedin'] !== '1' ) {
				unset( $new_instance['show_linkedin'] );
			}
			if ( ! isset( $new_instance['show_googleplus'] ) || $new_instance['show_googleplus'] !== '1' ) {
				unset( $new_instance['posts_link'] );
			}
			if ( ! isset( $new_instance['show_pinterest'] ) || $new_instance['show_pinterest'] !== '1' ) {
				unset( $new_instance['show_pinterest'] );
			}

			return $new_instance;

		}

		/**
		 * Echo the settings update form.
		 *
		 * @param array $instance Current settings
		 */
		function form( $instance ) {

			//* Merge with defaults
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'yoast-theme' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_name( 'user' ); ?>"><?php _e( 'Select a user. The email address for this account will be used to pull the Gravatar image.', 'yoast-theme' ); ?></label><br />
				<?php wp_dropdown_users( array( 'who' => 'authors', 'name' => $this->get_field_name( 'user' ), 'selected' => $instance['user'] ) ); ?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Gravatar Size', 'yoast-theme' ); ?>:</label>
				<select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
					<?php
					$sizes = array( __( 'Small', 'yoast-theme' ) => 45, __( 'Medium', 'yoast-theme' ) => 65, __( 'Large', 'yoast-theme' ) => 85, __( 'Extra Large', 'yoast-theme' ) => 125 );
					$sizes = apply_filters( 'genesis_gravatar_sizes', $sizes );
					foreach ( (array) $sizes as $label => $size ) {
						?>
						<option value="<?php echo absint( $size ); ?>" <?php selected( $size, $instance['size'] ); ?>><?php printf( '%s (%spx)', $label, $size ); ?></option>
					<?php } ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'alignment' ); ?>"><?php _e( 'Gravatar Alignment', 'yoast-theme' ); ?>:</label>
				<select id="<?php echo $this->get_field_id( 'alignment' ); ?>" name="<?php echo $this->get_field_name( 'alignment' ); ?>">
					<option value="">- <?php _e( 'None', 'yoast-theme' ); ?> -</option>
					<option value="left" <?php selected( 'left', $instance['alignment'] ); ?>><?php _e( 'Left', 'yoast-theme' ); ?></option>
					<option value="right" <?php selected( 'right', $instance['alignment'] ); ?>><?php _e( 'Right', 'yoast-theme' ); ?></option>
				</select>
			</p>

			<fieldset>
				<legend><?php _e( 'Select which text you would like to use as the author description', 'yoast-theme' ); ?></legend>
				<p>
					<input type="radio" name="<?php echo $this->get_field_name( 'author_info' ); ?>" id="<?php echo $this->get_field_id( 'author_info' ); ?>_val1" value="bio" <?php checked( $instance['author_info'], 'bio' ); ?>/>
					<label for="<?php echo $this->get_field_id( 'author_info' ); ?>_val1"><?php _e( 'Author Bio', 'yoast-theme' ); ?></label><br />
					<input type="radio" name="<?php echo $this->get_field_name( 'author_info' ); ?>" id="<?php echo $this->get_field_id( 'author_info' ); ?>_val2" value="text" <?php checked( $instance['author_info'], 'text' ); ?>/>
					<label for="<?php echo $this->get_field_id( 'author_info' ); ?>_val2"><?php _e( 'Custom Text (below)', 'yoast-theme' ); ?></label><br />
					<label for="<?php echo $this->get_field_id( 'bio_text' ); ?>" class="screen-reader-text"><?php _e( 'Custom Text Content', 'yoast-theme' ); ?></label>
					<textarea id="<?php echo $this->get_field_id( 'bio_text' ); ?>" name="<?php echo $this->get_field_name( 'bio_text' ); ?>" class="widefat" rows="6" cols="4"><?php echo htmlspecialchars( $instance['bio_text'] ); ?></textarea>
				</p>
			</fieldset>

			<p>
				<label for="<?php echo $this->get_field_name( 'page' ); ?>"><?php _e( 'Choose your extended "About Me" page from the list below. This will be the page linked to at the end of the about me section.', 'yoast-theme' ); ?></label><br />
				<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page' ), 'show_option_none' => __( 'None', 'yoast-theme' ), 'selected' => $instance['page'] ) ); ?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'page_link_text' ); ?>"><?php _e( 'Extended page link text', 'yoast-theme' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'page_link_text' ); ?>" name="<?php echo $this->get_field_name( 'page_link_text' ); ?>" value="<?php echo esc_attr( $instance['page_link_text'] ); ?>" class="widefat" />
			</p>

			<p>
				<input id="<?php echo $this->get_field_id( 'posts_link' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'posts_link' ); ?>" value="1" <?php checked( $instance['posts_link'] ); ?>/>
				<label for="<?php echo $this->get_field_id( 'posts_link' ); ?>"><?php _e( 'Show Author Archive Link?', 'yoast-theme' ); ?></label>
			</p>

			<fieldset>
				<legend><?php _e( 'If you changed the user, hit Save before selecting Social Media!', 'yoast-theme' ); ?></legend>
				<p>
					<?php if ( '' !== get_the_author_meta( 'twitter', $instance['user'] ) ) { ?>
						<input id="<?php echo $this->get_field_id( 'show_twitter' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_twitter' ); ?>" value="1" <?php checked( $instance['show_twitter'] ); ?>/>
						<label for="<?php echo $this->get_field_id( 'show_twitter' ); ?>"><?php _e( 'Show link to Twitter', 'yoast-theme' ); ?></label>
						<br />
					<?php
					}

					if ( '' !== get_the_author_meta( 'googleplus', $instance['user'] ) ) {
						?>
						<input id="<?php echo $this->get_field_id( 'show_googleplus' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_googleplus' ); ?>" value="1" <?php checked( $instance['show_googleplus'] ); ?>/>
						<label for="<?php echo $this->get_field_id( 'show_googleplus' ); ?>"><?php _e( 'Show link to GooglePlus', 'yoast-theme' ); ?></label>
						<br />
					<?php
					}

					if ( '' !== get_the_author_meta( 'facebook', $instance['user'] ) ) {
						?>
						<input id="<?php echo $this->get_field_id( 'show_facebook' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_facebook' ); ?>" value="1" <?php checked( $instance['show_facebook'] ); ?>/>
						<label for="<?php echo $this->get_field_id( 'show_facebook' ); ?>"><?php _e( 'Show link to Facebook', 'yoast-theme' ); ?></label>
						<br />
					<?php
					}

					if ( '' !== get_the_author_meta( 'linkedin', $instance['user'] ) ) {
						?>
						<input id="<?php echo $this->get_field_id( 'show_linkedin' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_linkedin' ); ?>" value="1" <?php checked( $instance['show_linkedin'] ); ?>/>
						<label for="<?php echo $this->get_field_id( 'show_linkedin' ); ?>"><?php _e( 'Show link to LinkedIn', 'yoast-theme' ); ?></label>
						<br />
					<?php
					}

					if ( '' !== get_the_author_meta( 'pinterest', $instance['user'] ) ) {
						?>
						<input id="<?php echo $this->get_field_id( 'show_pinterest' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_pinterest' ); ?>" value="1" <?php checked( $instance['show_pinterest'] ); ?>/>
						<label for="<?php echo $this->get_field_id( 'show_pinterest' ); ?>"><?php _e( 'Show link to Pinterest', 'yoast-theme' ); ?></label>
						<br />
					<?php } ?>
				</p>
			</fieldset>
		<?php

		}

	}

	/**
	 * Register the widget
	 */
	function yst_register_yst_up_widget() {
		register_widget( 'YST_User_Profile_Widget' );
	}

	add_action( 'widgets_init', 'yst_register_yst_up_widget' );
}