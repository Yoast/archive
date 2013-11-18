<?php
/**
 * Color Settings
 *
 * This file registers the color settings to the Theme Settings.
 *
 * @package      Yoast Theme
 * @author       Taco Verdonschot <taco@yoast.com>
 * @copyright    Copyright (c) 2013, Yoast BV
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */


/**
 * Set defaults
 *
 * @param $defaults
 *
 * @return mixed
 */
function yst_colourscheme_defaults( $defaults ) {

	$defaults['yst_colourscheme'] = '/assets/css/cs_WarmBlue.css';

	return $defaults;
}

add_filter( 'genesis_theme_settings_defaults', 'yst_colourscheme_defaults' );

/**
 * Register Metabox
 *
 * @param string $_genesis_theme_settings_pagehook
 */

function yst_register_colourscheme_box( $_genesis_theme_settings_pagehook ) {
	add_meta_box( 'yst-colourscheme-settings', 'Yoast Theme Colour Scheme', 'yst_colourscheme_settings_box', $_genesis_theme_settings_pagehook, 'main', 'high' );
}

add_action( 'genesis_theme_settings_metaboxes', 'yst_register_colourscheme_box' );

/**
 * Create Metabox
 */

function yst_colourscheme_settings_box() {
	// before options
	$output = $fileloc = '';

	?>
	<p><?php _e( 'Select your colour scheme:', 'yoast-theme' ); ?><br />
		<select name="<?php echo GENESIS_SETTINGS_FIELD; ?>[yst_colourscheme]">

			<?php
			// create options
			foreach ( glob( CHILD_DIR . "/assets/css/cs_*.css" ) as $file ) {
				preg_match( '/(.+)?cs_(.+).css/', $file, $matches );
				if ( isset ( $matches[2] ) ) {
					$cs = $matches[2];
				}

				$dir    = str_replace( '/', '\/', CHILD_DIR );
				$needle = "/(.+)?" . $dir . "(.+)/";
				preg_match( $needle, $file, $fileloc );

				if ( genesis_get_option( 'yst_colourscheme' ) == $fileloc[2] ) {
					$output .= '<option value="' . $fileloc[2] . '" SELECTED>';
				}
				else {
					$output .= '<option value="' . $fileloc[2] . '">';
				}
				$output .= $cs . '</option>';
			}
			echo $output;

			// after options
			?>
		</select></p>
<?php
}