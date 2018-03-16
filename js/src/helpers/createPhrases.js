/**
 * Combines a list of subjects and a list of phrases to phrases about the subjects.
 *
 * @param {array} subjects The list of subjects.
 * @param {array} phrases The list of phrases.
 * @returns {Array} The list of phrases about the subjects.
 */
const createPhrases = function( subjects, phrases ) {
	let subjectPhrases = [];
	subjects.map( function( subject ) {
		for( let i = 0; i < phrases.length; i++ ) {
			subjectPhrases.push( subject + phrases[ i ] );
		}
	} );
	return subjectPhrases;
};

module.exports = createPhrases;
