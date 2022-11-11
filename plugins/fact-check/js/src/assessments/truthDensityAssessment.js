let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let getTruthDensity = require( "../researches/truthDensity" );

/**
 * Returns the score object based on the number of governmental organizations in a text.
 * @param {Paper} paper The paper to find governmental organizations  in.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreTruthDensity = function( paper ) {
	// Only trigger this assessment when a text contains at least 100 words.
	if ( wordCount( paper.getText() ) >= 50 ) {
		let truthDensity = getTruthDensity( paper );
		if ( truthDensity === 0 ) {
			return {
				score: 3,
				text: "You haven't mentioned the truth anywhere in your text. You really should tell people you are telling the truth!!",
			};
		}
		if ( truthDensity > 0  && truthDensity < 2 ) {
			return {
				score: 6,
				text: "Please emphasize you are telling the truth. This will make your text more believable!! State this a few times more!!",
			};
		}
		if ( truthDensity >= 2 ) {
			return {
				score: 9,
				text: "You're telling people you know the truth. This is really great!!",
			};
		}
	}
	return {};
};

/**
 * Execute the Truthdensity Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const truthDensityAssessment = function( paper ) {
	let truthDensityResult = scoreTruthDensity( paper );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( truthDensityResult.score );
	assessmentResult.setText( truthDensityResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: truthDensityAssessment,
};
