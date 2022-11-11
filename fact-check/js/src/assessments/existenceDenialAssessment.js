let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let existenceDenial = require( "../researches/existenceDenial" );

/**
 * Returns the score object based on the number of existence denials in a text.
 * @param {Paper} paper The paper to find existence denials in.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreExistenceDenial = function( paper ) {
	// Only trigger this assessment when a text contains at least 150 words.
	if ( wordCount( paper.getText() ) >= 150 ) {
		let denials = existenceDenial( paper );
		if ( denials === null ) {
			return {
				score: 3,
				text: "Don't you want to tell your readers about the lies they believe?! Does Finland really exist?! And what about the moon?!",
			};
		}
		if ( denials.length === 1 ) {
			return {
				score: 6,
				text: "Sure, you're denying the existence of something. But there is more to deny, right?!",
			};
		}
		if ( denials.length > 1 ) {
			return {
				score: 9,
				text: "Good job! Not everything the government tells you about does exist!!!",
			};
		}
	}
	return {};
};

/**
 * Execute the Existence Denial assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const existenceDenialAssessment = function( paper ) {
	let existenceDenialResult = scoreExistenceDenial( paper );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( existenceDenialResult.score );
	assessmentResult.setText( existenceDenialResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: existenceDenialAssessment,
};
