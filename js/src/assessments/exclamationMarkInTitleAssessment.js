let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let exclamationMarkInTitle = require( "../researches/exclamationMarkInTitle" );

/**
 * Returns the score object based on whether the title contains one or more exclamation marks.
 * @param {string} title The title to check for exclamation marks.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreExclamationMarkInTitle = function( title ) {
	let hasExclamationMark = exclamationMarkInTitle( title );
	// Only return a score and feedback text when there is a title and the title contains no exclamation mark.
	if ( title !== "" && ! hasExclamationMark ) {
		return {
			score: 3,
			text: "Why doesn't your title contain an exclamation mark?! Write your title like you mean it!!!!",
		};
	}
	return {};
};

/**
 * Execute the Exclamation Mark in Title Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const exclamationMarkInTitleAssessment = function( paper ) {
	let title = paper.getTitle();
	let factStatedResult = scoreExclamationMarkInTitle( title );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( factStatedResult.score );
	assessmentResult.setText( factStatedResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: exclamationMarkInTitleAssessment,
};
