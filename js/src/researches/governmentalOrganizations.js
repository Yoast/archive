let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let dutchOrganizations = [ "AIVD", "BVD" ];
let belgianOrganizations = [ "Veiligheid van de Staat", "VSSE" ];
let generalOrganizations = [ "CIA", "FBI", "NSA", "Homeland security", "KGB", "TSA", "MI6", "ATF", "DEA", "GOP" ];

/**
 * Gets the list of governmental organizations based on the locale.
 *
 * @param {string} locale The locale to get the list of governmental organizations for.
 * @returns {array} The list of governmental organizations.
 */
const getOrganizations = function( locale ) {
	switch ( locale ) {
		// For Dutch texts, we'd like to check for both Dutch/Belgian organizations as well as general organizations.
		case "nl_NL":
		case "nl_NL_formal": return generalOrganizations.concat( dutchOrganizations );
		case "nl_BE": return generalOrganizations.concat( belgianOrganizations );
		default: return generalOrganizations;
	}
};

/**
 * Finds governmental organizations in a text.
 *
 * @param {Array} organizations The list of organizations.
 * @param {string} text The text to check for organizations.
 * @returns {Array} A list of found governmental organizations.
 */
const findOrganizations = function( organizations, text ) {
	const organizationRegex = createRegexFromArray( organizations );
	text = text.toLocaleLowerCase();
	return text.match( organizationRegex );
};

/**
 * Checks a text for governmental organizations and returns a list of them.
 *
 * @param {Object} paper The paper to check for governmental organizations.
 * @returns {Array} An array with organizations.
 */
module.exports = function( paper ) {
	let locale = paper.getLocale();
	let text = paper.getText();
	let organizations = getOrganizations( locale );
	return findOrganizations( organizations, text );
};
