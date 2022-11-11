/**
 * Checks whether a title contains an exclamation mark.
 *
 * @param {string} title The title to check for exclamation marks.
 * @returns {boolean} True when containing one or multiple exclamation marks.
 */
const hasExclamationMark = function( title ) {
	let exclamationRegex = new RegExp( /!/, "g" );
	let match = title.match( exclamationRegex );

	// Avoid returning null by using '!!'.
	return ( !! match && match.length > 0 );
};

/**
 * Checks whether a title contains an exclamation mark.
 *
 * @param {string} title The title to check for exclamation marks.
 * @returns {boolean} Whether or not the title contains an exclamation mark.
 */
module.exports = function( title ) {
	return hasExclamationMark( title );
};
