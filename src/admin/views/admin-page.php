<?php

namespace Yoast\WP\Crawl_Cleanup\Admin\Views;

use Yoast\WP\Crawl_Cleanup\Admin\Admin_Options;
use Yoast\WP\Crawl_Cleanup\Options\Options;

/** @var Options $options */
$options = Options::instance();

?><div class="wrap">
	<h2>Yoast Crawl Cleanup <?php esc_html_e( 'Configuration', 'yoast-crawl-cleanup' ); ?></h2>

	<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
		<input id="yoast_return_tab" type="hidden" name="yoast_crawl_cleanup[return_tab]" value="<?php echo esc_attr( $options->return_tab ); ?>" />
		<?php
		settings_fields( Admin_Options::$option_group );
		?>
		<div id="yoast_wrapper">
			<h2 class="nav-tab-wrapper" id="yoast-tabs">
				<a class="nav-tab" id="yoast-basic-tab" href="#top#basic"><?php esc_html_e( 'Basic', 'yoast-crawl-cleanup' ); ?></a>
				<a class="nav-tab" id="yoast-rss-tab" href="#top#rss"><?php esc_html_e( 'RSS', 'yoast-crawl-cleanup' ); ?></a>
				<a class="nav-tab" id="yoast-gutenberg-tab" href="#top#gutenberg"><?php esc_html_e( 'Gutenberg', 'yoast-crawl-cleanup' ); ?></a>
				<a class="nav-tab" id="yoast-advanced-tab" href="#top#advanced"><?php esc_html_e( 'Advanced', 'yoast-crawl-cleanup' ); ?></a>
			</h2>

			<div class="tabwrapper">
				<div id="yoast-basic" class="yoast_tab">
					<?php do_settings_sections( 'yoast-crawl-cleanup' ); ?>
				</div>
				<div id="yoast-rss" class="yoast_tab">
					<?php do_settings_sections( 'ycc-rss' ); ?>
				</div>
				<div id="yoast-gutenberg" class="yoast_tab">
					<?php do_settings_sections( 'ycc-gutenberg' ); ?>
				</div>
				<div id="yoast-advanced" class="yoast_tab">
					<?php do_settings_sections( 'ycc-advanced' ); ?>
				</div>
			</div>
			<?php
			submit_button( __( 'Save Yoast Crawl Cleanup settings', 'yoast-crawl-cleanup' ) );
			?>
		</div>
	</form>
</div>
