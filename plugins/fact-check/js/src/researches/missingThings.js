let allTheThingsEnglish = [ "moon landing", "earth is flat", "ufo", "area 51", "denver airport",
	"illuminati", "freemason", "reptilian", "chemtrail"  ];
let alltheThingsDutch = [ "maanlanding", "aarde is plat", "ufo", "area 51", "denver airport",
	"illuminati", "vrijmetselaar", "reptilian", "chemtrail" ];
let getLanguage = require( "yoastseo/js/helpers/getLanguage" );

/**
 * Gets the list of all the things to match based on language
 *
 * @param {string} language The language to use to return the list with things.
 * @returns {array} A list with things in the given language.
 */
const getThings = function( language ) {
	switch( language ) {
		case "nl": return alltheThingsDutch;
		default: return allTheThingsEnglish;
	}
};

/**
 * Finds the missing thing in the text. Checks against the list for things to match.
 *
 * @param {string} text The text to check for mentions in the list.
 * @param {string} language The language of the text.
 * @returns {string} The first thing missing in the list
 */
const findMissingThing = function( text, language ) {
	let i, missingMatch = "";

	let things = getThings( language );

	for ( i = 0; i < things.length; i++ ) {
		let match = new RegExp( things[ i ], "i" );
		if( ! text.match( match ) ) {
			missingMatch =  things[ i ];
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
	let locale = paper.getLocale();
	let language = getLanguage( locale );
	let text = paper.getText();

	return findMissingThing( text, language );
};
