<?php

namespace Joost\Optimizations\Admin\Views;

use Joost\Optimizations\Admin\Admin_Options;

?><div class="wrap">
	<h2>Joost Optimizations <?php esc_html_e( 'Configuration', 'joost-optimizations' ); ?></h2>

	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
		<?php
		settings_fields( Admin_Options::$option_group );
		?>
		<div id="yoast_wrapper">
			<h2 class="nav-tab-wrapper" id="yoast-tabs">
				<a class="nav-tab" id="joost-basic-tab" href="#top#basic"><?php esc_html_e( 'Basic settings', 'joost-optimizations' ); ?></a>
				<a class="nav-tab" id="joost-rss-tab" href="#top#rss"><?php esc_html_e( 'RSS settings', 'joost-optimizations' ); ?></a>
			</h2>

			<div class="tabwrapper">
				<div id="joost-basic" class="yoast_tab">
					<?php do_settings_sections( 'joost-optimizations' ); ?>
				</div>
				<div id="joost-rss" class="yoast_tab">
					<?php do_settings_sections( 'joost-optimizations-rss' ); ?>
				</div>
			</div>
			<?php
			submit_button( __( 'Save Joost Optimizations settings', 'joost-optimizations' ) );
			?>
		</div>
	</form>
</div>
