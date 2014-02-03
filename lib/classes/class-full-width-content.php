<?php

/**
 * Class Yoast_Full_Width
 */
class Yoast_Full_Width_Content extends Yoast_Layout {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();

		// Do hooks
		$this->hooks();

		// Setup the layout
		$this->setup();
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_action( 'genesis_after_entry_content', array( $this, 'image_full_width' ), 15 );
	}

	/**
	 * Setup layout
	 */
	private function setup() {
		add_image_size( 'fullwidth-thumb', 290, 193, true );
	}

	/**
	 * Show the post thumbnail in the full width archives
	 */
	public function image_full_width() {

		if ( ! is_front_page() ) {
			return;
		}

		if ( ! get_theme_mod( 'yst_content_archive_thumbnail' ) ) {
			return;
		}

		$thumbnail = get_the_post_thumbnail( null, 'fullwidth-thumb' );
		if ( $thumbnail ) {
			echo '<div class="thumb full-width-thumb">';
			echo $thumbnail;
			echo '</div>';
		}
	}
} 