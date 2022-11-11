let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let getLanguage = require( "yoastseo/js/helpers/getLanguage" );

let createPhrases = require( "../helpers/createPhrases" );

let englishSubjects = [ "finland", "bielefeld", "moon",  "cake" ];
let dutchSubjects = [ "finland", "bielefeld", "maan", "roggebrood" ];

let dutchDenials = [ " bestaat niet", " bestaat helemaal niet", " is een leugen", " bestaat ook niet", " is ook een leugen" ];
let englishDenials = [ " doesn't exist", " is a lie", " is also a lie" ];

/**
 * Gets the subjects and denial phrases based on the passed language.
 *
 * @param {string} language The text's language.
 * @returns {Object} The subjects and denial phrases.
 */
const getPhraseParts = function( language ) {
	switch( language ) {
		case "nl": return { subjects: dutchSubjects, denials: dutchDenials };
		default: return { subjects: englishSubjects, denials: englishDenials };
	}
};

/**
 * Gets a list of subject denial phrases.
 *
 * @param {string} language The language.
 * @returns {array} The combined phrases list containing subject denials.
 */
const createPhraseList = function( language ) {
	let phraseParts = getPhraseParts( language );
	return createPhrases( phraseParts.subjects, phraseParts.denials );
};

/**
 * Finds denials in a text.
 *
 * @param {string} text The text to check for denials.
 * @param {string} language The language of the text.
 * @returns {Array} A list of found denials.
 */
const findDenials = function( text, language ) {
	const denialPhrases = createPhraseList( language );
	const denialRegex = createRegexFromArray( denialPhrases );
	text = text.toLocaleLowerCase();
	return text.match( denialRegex );
};

/**
 * Checks a text for existence denials and returns a list of them.
 *
 * @param {Object} paper The paper to check for existence denials.
 * @returns {Array} An array with existence denials.
 */
module.exports = function( paper ) {
	let text = paper.getText();
	let locale = paper.getLocale();
	let language = getLanguage( locale );
	return findDenials( text, language );
};
