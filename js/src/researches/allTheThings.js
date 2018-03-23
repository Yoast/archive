let allTheThings = [ "moon landing", "earth is flat", "ufo", "area 51", "denver airport", "illuminati", "freemasons", "reptilians", "chemtrails"  ];
let toRegex = require( "yoastseo/js/stringProcessing/createWordRegex.js" );

/**
 * Finds the missing thing in the text. Checks against the list for things to match.
 *
 * @param {string} text The text to check for mentions in the list
 * @returns {string} The first thing missing in the list
 */
const findMissingThing = function( text ) {
	let i, missingMatch = "";

	for ( i = 0; i < allTheThings.length; i++ ) {
		let match = toRegex( allTheThings[ i ] );
		if( ! text.match( match ) ) {
			missingMatch =  allTheThings[ i ];
			break;
		}
	}
	return missingMatch;
};


/**
 * Checks a text againts a list of things you should write about.
 *
 * @param {Object} paper The paper to check for mentions of important things.
 * @returns {String} The first string in the array that isn't matched. Empty if no more things need to be matched.
 */
module.exports = function( paper ) {
	let text = paper.getText();
	return findMissingThing( text );
};
