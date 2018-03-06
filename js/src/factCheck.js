/* global YoastSEO: true */
( function() {
	// eslint-disable-next-line global-require
	const factStatementAssessment  = require( "./assessments/factStatedAssessment" );
	/**
     * Adds the Yoast Fact Check plugin to the YoastSEO Analyzer.
     *
     * @param {Object} app YoastSEO's app.
     *
     * @returns {void}
     */
	function YoastFactCheckPlugin( app ) {
		app.registerPlugin( "YoastFactCheckPlugin", { status: "ready" } );

		app.registerAssessment( "factStated", factStatementAssessment, "YoastFactCheckPlugin" );
	}

	// Adds eventListener on page load to load the Fact Check.
	if ( typeof YoastSEO !== "undefined" && typeof YoastSEO.app !== "undefined" ) {
		// eslint-disable-next-line no-new
		new YoastFactCheckPlugin( YoastSEO.app );
	} else {
		jQuery( window ).on(
			"YoastSEO:ready",
			function() {
				// eslint-disable-next-line no-new
				new YoastFactCheckPlugin( YoastSEO.app );
			}
		);
	}
}() );
