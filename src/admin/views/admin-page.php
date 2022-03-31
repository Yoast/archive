<?php
/**
 * Joost Optimizations for WordPress plugin file.
 *
 * @package Yoast/Clicky/View
 */

namespace Joost_Optimizations\Admin\Views;

use Joost_Optimizations\Admin\Admin_Options;

?><div class="wrap">
	<h2>
		<?php /* <img id="plugin_icon" src="<?php echo esc_url( JOOST_OPTIMIZATIONS_PLUGIN_DIR_URL . 'images/clicky-32x32.png' ); ?>" class="alignleft" />  */ ?>
        Joost Optimizations <?php esc_html_e( 'Configuration', 'joost-optimizations' ); ?>
	</h2>

	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
		<?php
		settings_fields( Admin_Options::$option_group );
		?>
		<div id="yoast_wrapper">
			<h2 class="nav-tab-wrapper" id="yoast-tabs">
				<a class="nav-tab" id="basic-tab" href="#top#basic"><?php esc_html_e( 'Basic settings', 'joost-optimizations' ); ?></a>
				<a class="nav-tab" id="advanced-tab" href="#top#advanced"><?php esc_html_e( 'Advanced settings', 'joost-optimizations' ); ?></a>
			</h2>

			<div class="tabwrapper">
				<div id="basic" class="yoast_tab">
					<?php do_settings_sections( 'clicky' ); ?>
				</div>
				<div id="advanced" class="yoast_tab">
					<?php do_settings_sections( 'clicky-advanced' ); ?>
				</div>
			</div>
			<?php
			submit_button( __( 'Save Joost Optimizations settings', 'joost-optimizations' ) );
			?>
		</div>
	</form>
</div>