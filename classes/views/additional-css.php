<?php
/**
 * YoastSEO_AMP_Glue plugin file.
 *
 * @package   YoastSEO_AMP_Glue
 * @copyright 2016 Yoast BV
 * @license   GPL-2.0+
 */

?>
td, th {
	text-align: left;
}

a, a:active, a:visited {
	text-decoration: <?php echo ( ( $this->options['underline'] === 'underline' ) ? 'underline' : 'none' ); ?>;
}
