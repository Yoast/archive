let createRegexFromArray = require( "yoastseo/js/stringProcessing/createRegexFromArray" );

let deadCelebrities = [ "paul", "paul mccartney" ];
let notDeadCelebrities = [ "elvis", "tupac", "makaveli", "2pac", "notorious b.i.g.", "notorious big", "biggie", "biggie smalls" ];
let deadPhrases = [ " is dead", " has died", " died", "'s death", " passed away", " deceased",
	" is deceased", " kicked the bucket", " has kicked the bucket" ];
let notDeadPhrases = [ " is not dead", " has not died", " hasn't died", " didn't die", " did not die", " didn't pass away",
	" did not pass away", " isn't deceased", " is not deceased", "is alive", "is still alive" ];

const createPhrases = function( celebrities, phrases ) {
	let celebrityPhrases = [];
	celebrities.map( function( celebrity ) {
		for( let i = 0; i < phrases.length; i++ ) {
			celebrityPhrases.push( celebrity + phrases[i] );
		}
	});
	return celebrityPhrases
};


const createPhraseList = function(){
	let createdDeadPhrases = createPhrases( deadCelebrities, deadPhrases );
	let createdNotDeadPhrases = createPhrases( notDeadCelebrities, notDeadPhrases );
	return createdDeadPhrases.concat( createdNotDeadPhrases )
};

/**
 * Finds mentions of dead celebrities in a text.
 *
 * @param {string} text The text to check for dead celebrities.
 * @returns {Array} A list of found dead celebrities.
 */
const findCelebrities = function( text ) {
	const celebrityPhrases = createPhraseList();
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
module.exports = function ( paper ) {
	let text = paper.getText();
	return findCelebrities( text );
};
