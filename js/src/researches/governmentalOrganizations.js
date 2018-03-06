let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let dutchOrganizations = [ "AIVD", "BVD" ];
let generalOrganizations = [ "CIA", "FBI", "NSA", "Homeland security", "KGB" ];

/**
 * Gets the list of governmental organizations based on the locale.
 * 
 * @param {string} locale The locale to get the list of governmental organizations for.
 * @returns {array} The list of governmental organizations
 */
const getOrganizations = function( locale ) {
	switch ( locale ) {
		// For Dutch texts, we'd like to check for both general organizations as well as general organizations.
		case "nl_NL": return generalOrganizations.concat( dutchOrganizations );
		// Todo: Add Belgian organizations?
		default: return generalOrganizations;
	}
};

/**
 * Finds governmental organizations in a text.
 * 
 * @param {Array} organizations The list of organizations.
 * @param {string} text The text to check for organizations.
 * @returns {*|Promise<any>|RegExpMatchArray|Promise<Response>}
 */
const findOrganizations = function( organizations, text ) {
	// todo to lowercase
	const organizationRegex = createRegexFromArray( organizations );
	let matches = text.match( organizationRegex );
	return text.match( organizationRegex );
};

/**
 * Checks a text for governmental organization and returns a list of them.
 *
 * @param {Object} paper The paper to check for governmental organizations.
 * @returns {Array} An array with organization.
 */
module.exports = function ( paper ) {
	let locale = paper.getLocale();
	let text = paper.getText();
	let organizations = getOrganizations ( locale );
	return findOrganizations( organizations, text );
};
