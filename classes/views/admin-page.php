<?php

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}



$yform = Yoast_Form::get_instance();

$yform->admin_header( true, 'wpseo_amp', false, 'wpseo_amp_settings' );

// data-default-color="#0a89c0"
?>

	<h2 class="nav-tab-wrapper" id="wpseo-tabs">
		<a class="nav-tab" id="posttypes-tab" href="#top#posttypes"><?php _e( 'Post types', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="design-tab" href="#top#design"><?php _e( 'Design', 'wordpress-seo' ); ?></a>
		<a class="nav-tab" id="analytics-tab" href="#top#analytics"><?php _e( 'Analytics', 'wordpress-seo' ); ?></a>
	</h2>

	<div class="tabwrapper">

		<div id="posttypes" class="wpseotab">
			<h2><?php _e( 'Post types that have AMP support', 'wordpress-seo' ); ?></h2>
			<p><?php _e( 'Generally you\'d want this to be your news post types. Post is enabled by default, feel free to enable other ones.', 'wordpress-seo' ); ?></p>
			<?php
			$post_types = apply_filters( 'wpseo_sitemaps_supported_post_types', get_post_types( array( 'public' => true ), 'objects' ) );
			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $pt ) {
					$yform->toggle_switch(
						'post_types-' . $pt->name . '-amp',
						array( 'on' => __( 'Enabled', 'wordpress-seo' ), 'off' => __( 'Disabled', 'wordpress-seo' ) ),
						$pt->labels->name . ' (<code>' . $pt->name . '</code>)'
					);
				}
			}

			?>
		</div>

		<div id="design" class="wpseotab">
			<h3><?php _e( 'Images', 'wordpress-seo' ); ?></h3>

			<?php
			$yform->media_input( 'amp_site_icon', __( 'AMP icon', 'wordpress-seo' ) ); ?>
			<p class="desc"><?php _e( 'Must be at least 32px x 32px', 'wordpress-seo' ); ?></p>
			<br/>

			<?php
			$yform->media_input( 'default_image', __( 'Default image', 'wordpress-seo' ) ); ?>
			<p class="desc"><?php _e( 'Used when a post doesn\'t have an image associated with it. The image must be 696px wide at least.', 'wordpress-seo' ); ?></p>
			<br/>

			<h3><?php _e( 'Text colors', 'wordpress-seo' ); ?></h3>

			<?php
			$this->color_picker( 'header-color', __( 'AMP Header color', 'wordpress-seo' ) );
			$this->color_picker( 'text-color', __( 'Text color', 'wordpress-seo' ) );
			$this->color_picker( 'headings-color', __( 'Color for headings', 'wordpress-seo' ) );
			$this->color_picker( 'meta-color', __( 'Color for post meta info', 'wordpress-seo' ) );
			?>

			<h3><?php _e( 'Links', 'wordpress-seo' ); ?></h3>
			<?php
			$this->color_picker( 'link-color', __( 'Link color', 'wordpress-seo' ) );
			$this->color_picker( 'link-color-hover', __( 'Hover color', 'wordpress-seo' ) );
			?>

			<label class="checkbox"><?php _e( 'Underline', 'wordpress-seo' ); ?></label>
			<?php $yform->light_switch( 'underline', '', array(
				__( 'Underline', 'wordpress-seo' ),
				__( 'No underline', 'wordpress-seo' )
			) ); ?>

			<h3><?php _e( 'Blockquotes', 'wordpress-seo' ); ?></h3>
			<?php
			$this->color_picker( 'blockquote-text-color', __( 'Blockquote text color', 'wordpress-seo' ) );
			$this->color_picker( 'blockquote-bg-color', __( 'Blockquote background color', 'wordpress-seo' ) );
			$this->color_picker( 'blockquote-border-color', __( 'Blockquote border color', 'wordpress-seo' ) );
			?>

			<h3><?php _e( 'Extra CSS', 'wordpress-seo' ); ?></h3>
			<?php $yform->textarea( 'extra-css', __( 'Extra CSS', 'wordpress-seo' ), array(
				'rows' => 5,
				'cols' => 100
			) ); ?>

			<h3><?php printf( __( 'Extra code in %s.', 'wordpress-seo' ), '<code>head</code>' ); ?></h3>
			<?php $yform->textarea( 'extra-head', __( 'Extra code', 'wordpress-seo' ), array(
				'rows' => 5,
				'cols' => 100
			) ); ?>

		</div>

		<div id="analytics" class="wpseotab">
			<h2><?php _e( 'AMP Analytics', 'wordpress-seo' ); ?></h2>

			<?php
			if ( class_exists( 'Yoast_GA_Options' ) ) {
				echo '<p>', __( 'Because your Google Analytics plugin by Yoast is functioning, your AMP pages will also be tracked.', 'wordpress-seo' ), '</p>';
				$UA = Yoast_GA_Options::instance()->get_tracking_code();
				if ( $UA === null ) {
					echo '<p>', __( 'Make sure to connect your Google Analytics plugin properly.', 'wordpress-seo' ), '</p>';
				} else {
					echo '<p>', sprintf( __( 'Pagevies will be tracked using the following account: %s.', 'wordpress-seo' ), '<code>' . $UA . '</code>' ), '</p>';
				}

				echo '<p>', __( 'If you want, you can override the default AMP tracking code with your own by putting it below:', 'wordpress-seo' ), '</p>';
				$yform->textarea( 'analytics-extra', __( 'Analytics code', 'wordpress-seo' ), array(
					'rows' => 5,
					'cols' => 100
				) );
			} else {
				echo '<p>', __( 'If you want, you can add a valid tracking code below:', 'wordpress-seo' ), '</p>';
				$yform->textarea( 'analytics-extra', __( 'Analytics code', 'wordpress-seo' ), array(
					'rows' => 5,
					'cols' => 100
				) );
			}
			?>
		</div>
	</div>

<?php
$yform->admin_footer();
