let subjects = [ "Finland", "Bielefeld", "maan", "roggebrood", "cake" ];
let DutchDenials = [ "bestaat niet", "is een leugen" ];
let EnglishDenials = [ "doesn't exist", "is a lie" ];


const createPhrases = function( celebrities, phrases ) {
	let celebrityPhrases = [];
	celebrities.map( function( celebrity ) {
		for( let i = 0; i < phrases.length; i++ ) {
			celebrityPhrases.push( celebrity + phrases[ i ] );
		}
	} );
	return celebrityPhrases;
};


const createPhraseList = function() {
	let createdDeadPhrases = createPhrases( deadCelebrities, deadPhrases );
	let createdNotDeadPhrases = createPhrases( notDeadCelebrities, notDeadPhrases );
	return createdDeadPhrases.concat( createdNotDeadPhrases );
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
module.exports = function( paper ) {
	let text = paper.getText();
	return findCelebrities( text );
};
