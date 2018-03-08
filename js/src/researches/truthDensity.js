let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let truths = [ "the truth", "the shocking truth", "uncovering the truth", "it is true", "this is true" ];

/**
 * Finds truth mentions in a text.
 *
 * @param {Array} truths The list of truths.
 * @param {string} text The text to check for truths.
 * @returns {array} A list of found truths.
 */
const findTruths = function( truths, text ) {
	const truthRegex = createRegexFromArray( truths );
	let formattedText = text.toLocaleLowerCase();
	return formattedText.match( truthRegex ) || [];
};

/**
 * Calculates the density of truths in a text.
 *
 * @param {string} text The text to check for truths.
 * @returns {number} the percentage of truths in the text.
 */
const calculateTruthDensity = function( text ) {
	let truthCount = findTruths( truths, text );
	let numberOfTruths = truthCount.length;
	let numberOfWords = wordCount( text );
	return Math.round( numberOfTruths / numberOfWords * 100 );
};

/**
 * Checks a text for truths and returns the density of them.
 *
 * @param {Object} paper The paper to check for governmental organizations.
 * @returns {Array} An array with organization.
 */
module.exports = function( paper ) {
	let locale = paper.getLocale();
	let text = paper.getText();
	return calculateTruthDensity( text, locale );
};
