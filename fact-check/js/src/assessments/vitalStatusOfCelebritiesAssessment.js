let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let vitalStatusOfCelebrities = require( "../researches/vitalStatusOfCelebrities" );

/**
 * Returns the score object based on the number of mentions of the vital status of celebrities in a text.
 * @param {Paper} paper The paper to find mentions of the vital status of celebrities in.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreVitalStatusOfCelebrities = function( paper ) {
	// Only trigger this assessment when a text contains at least 200 words.
	if ( wordCount( paper.getText() ) >= 200 ) {
		let celebrities = vitalStatusOfCelebrities( paper );
		if ( celebrities === null ) {
			return {
				score: 3,
				text: "Over 200 words in, and you haven't disputed the vital status of any celebrity yet! Is Elvis really dead?",
			};
		}
		if ( celebrities.length > 0 ) {
			return {
				score: 9,
				text: "Way to go! You know who's dead and who isn't!",
			};
		}
	}
	return {};
};

/**
 * Execute the Vital status of celebrity Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const vitalStatusOfCelebritiesAssessment = function( paper ) {
	let vitalStatusOfCelebritiesResult = scoreVitalStatusOfCelebrities( paper );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( vitalStatusOfCelebritiesResult.score );
	assessmentResult.setText( vitalStatusOfCelebritiesResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: vitalStatusOfCelebritiesAssessment,
};
