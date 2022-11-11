/* global YoastSEO: true */
/* eslint-disable global-require */
( function() {
	const factStatementAssessment  = require( "./assessments/factStatedAssessment" );
	const governmentalOrganizationsAssessment  = require( "./assessments/governmentalOrganizationsAssessment" );
	const truthDensityAssessment  = require( "./assessments/truthDensityAssessment" );
	const vitalStatusOfCelebritiesAssessment  = require( "./assessments/vitalStatusOfCelebritiesAssessment" );
	const allTheThingsAssessment  = require( "./assessments/missingThingsAssessment" );
	const existenceDenialAssessment  = require( "./assessments/existenceDenialAssessment" );
	const exclamationMarkInTitleAssessment  = require( "./assessments/exclamationMarkInTitleAssessment" );
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
		app.registerAssessment( "governmentalOrganizations", governmentalOrganizationsAssessment, "YoastFactCheckPlugin" );
		app.registerAssessment( "truthDensity", truthDensityAssessment, "YoastFactCheckPlugin" );
		app.registerAssessment( "vitalStatusOfCelebrities", vitalStatusOfCelebritiesAssessment, "YoastFactCheckPlugin" );
		app.registerAssessment( "allTheThings", allTheThingsAssessment, "YoastFactCheckPlugin" );
		app.registerAssessment( "existenceDenial", existenceDenialAssessment, "YoastFactCheckPlugin" );
		app.registerAssessment( "exclamationMarkInTitle", exclamationMarkInTitleAssessment, "YoastFactCheckPlugin" );
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
