let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let truths = [ "the truth", "the shocking truth", "uncovering the truth", "it is true", "this is true" ];

/**
 * Finds governmental organizations in a text.
 *
 * @param {Array} organizations The list of organizations.
 * @param {string} text The text to check for organizations.
 * @returns {*|Promise<any>|RegExpMatchArray|Promise<Response>}
 */
const findTruths = function( truths, text ) {
	const truthRegex = createRegexFromArray( truths );
	return text.toLocaleLowerCase().match( truthRegex );
};

const calculateTruthDensity= function( text ) {
	let truthCount = findTruths( truths, text );
	return ( truthCount.length / wordCount( text ) * 100 );
}

/**
 * Checks a text for governmental organization and returns a list of them.
 *
 * @param {Object} paper The paper to check for governmental organizations.
 * @returns {Array} An array with organization.
 */
module.exports = function ( paper ) {
	let locale = paper.getLocale();
	let text = paper.getText();
	return calculateTruthDensity( text );
};
