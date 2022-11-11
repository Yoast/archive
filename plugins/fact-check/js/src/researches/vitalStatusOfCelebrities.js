let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );
let getLanguage = require( "yoastseo/js/helpers/getLanguage" );

let createPhrases = require( "../helpers/createPhrases" );

let deadCelebrities = [ "paul", "paul mccartney" ];
let notDeadCelebrities = [ "elvis", "elvis presley", "tupac", "makaveli", "2pac", "notorious b.i.g.", "notorious big", "biggie", "biggie smalls" ];
let deadPhrasesEn = [ " is dead", " has died", " died", " 's death", " passed away", " deceased",
	" is deceased", " kicked the bucket", " has kicked the bucket" ];
let deadPhrasesNl = [ " is dood", " is gestorven", " is overleden" ];
let notDeadPhrasesEn = [ " is not dead", " is not really dead", " isn't really dead", " isn't dead", " has not died",
	" hasn't died", " didn't die", " did not die", " didn't pass away", " did not pass away", " isn't deceased",
	" is not deceased", " is alive", " is still alive" ];
let notDeadPhrasesNl = [ " is niet dood", " is niet gestorven", " is niet overleden", " is helemaal niet overleden",
	" is helemaal niet dood", " is helemaal niet gestorven", "leeft" ];

/**
 * Gets the vital status phrases based on the passed language.
 *
 * @param {string} language The text's language.
 * @returns {Object} The dead and not dead phrases.
 */
const getVitalStatusPhrases = function( language ) {
	switch( language ) {
		case "nl": return { dead: deadPhrasesNl, notDead: notDeadPhrasesNl };
		default: return { dead: deadPhrasesEn, notDead: notDeadPhrasesEn };
	}
};

/**
 * Gets a list of phrases about the vital status of celebrities.
 * Combines the phrases about dead celebrities and alive celebrities to one single list.
 *
 * @param {string} language The language.
 * @returns {array} The combined phrases list about dead celebrities and alive celebrities.
 */
const createPhraseList = function( language ) {
	let phrases = getVitalStatusPhrases( language );
	let createdDeadPhrases = createPhrases( deadCelebrities, phrases.dead );
	let createdNotDeadPhrases = createPhrases( notDeadCelebrities, phrases.notDead );
	return createdDeadPhrases.concat( createdNotDeadPhrases );
};

/**
 * Finds mentions of dead and alive celebrities in a text.
 *
 * @param {string} text The text to check for dead and alive celebrities.
 * @param {string} language The language of the text.
 * @returns {Array} A list of found dead and alive celebrities.
 */
const findCelebrities = function( text, language ) {
	const celebrityPhrases = createPhraseList( language );
	const celebritiesRegex = createRegexFromArray( celebrityPhrases );
	text = text.toLocaleLowerCase();
	return text.match( celebritiesRegex );
};

/**
 * Checks a text for (not) dead celebrities and returns a list of them.
 *
 * @param {Object} paper The paper to check for (not) dead celebrities.
 * @returns {Array} An array with celebrity phrases.
 */
module.exports = function( paper ) {
	let text = paper.getText();
	let locale = paper.getLocale();
	let language = getLanguage( locale );
	return findCelebrities( text, language );
};
