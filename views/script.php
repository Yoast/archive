<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
} 
?><script type="text/javascript">
(function($) {

	var YoastLicenseManager = (function () {

		var self = this;
		var $actionButton,
			$licenseForm, 
			$keyInput,
			$submitButtons;

		function init() {
			$licenseForm = $("#yoast-license-form").closest('form');
			$keyInput = $licenseForm.find("#yoast-license-key-field.yoast-license-obfuscate");
			$actionButton = $licenseForm.find('#yoast-license-toggler button');
			$submitButtons = $licenseForm.find('input[type="submit"], button[type="submit"]');

			$submitButtons.click( addDisableEvent );
			$actionButton.click( actOnLicense );
			$keyInput.click( setEmptyValue );
		}

		function setEmptyValue() {
			if( ! $(this).is('[readonly]') ) {
				$(this).val('');
			}
		}

		function actOnLicense() {	
			// fake input field with exact same name => value			
			$("<input />")
				.attr('type', 'hidden')
				.attr( 'name', $(this).attr('name') )
				.val( $(this).val() )
				.appendTo( $licenseForm );

			// change button text to show we're working..
			var text = ( $actionButton.hasClass('yoast-license-activate') ) ? "Activating..." : "Deactivating...";
			$actionButton.text( text );
		}

		function addDisableEvent() {
			$licenseForm.submit(disableButtons);
		}

		function disableButtons() {
			// disable submit buttons to prevent multiple requests
			$submitButtons.prop( 'disabled', true );
		}

		return {
			init: init
		}
	
	})();

	YoastLicenseManager.init();

})(jQuery);
</script>