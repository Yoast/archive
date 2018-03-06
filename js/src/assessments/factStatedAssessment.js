let getSentences = require( "yoastseo/js/stringProcessing/getSentences" );
let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );

/**
 * Returns the score object based on the number of sentences/facts in a text.
 * @param {string} text The text find facts in.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text
 */
const scoreFactStatement = function( text ) {
	let sentences = getSentences( text );
	console.log( sentences );
	if ( sentences.length > 0 ) {
		return {
			score: 9,
			text: "You've stated a fact",
		};
	}
	return {};
};

/**
 * Execute the Fact Stated Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const factStatedAssessment = function( paper ) {
	let factStatementResult = scoreFactStatement( paper.getText() );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( factStatementResult.score );
	assessmentResult.setText( factStatementResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: factStatedAssessment,
};
