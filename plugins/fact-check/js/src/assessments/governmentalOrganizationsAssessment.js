let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let governmentalOrganizations = require( "../researches/governmentalOrganizations" );

/**
 * Returns the score object based on the number of governmental organizations in a text.
 * @param {Paper} paper The paper to find governmental organizations in.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreGovernmentalOrganizations = function( paper ) {
	// Only trigger this assessment when a text contains at least 100 words.
	if ( wordCount( paper.getText() ) >= 100 ) {
		let organizations = governmentalOrganizations( paper );
		if ( organizations === null ) {
			return {
				score: 3,
				text: "You haven't even mentioned a governmental organization! How do you expect us to believe you're writing about facts?!",
			};
		}
		if ( organizations.length === 1 ) {
			return {
				score: 6,
				text: "You've only mentioned a governmental organization once. We know you can do better!",
			};
		}
		if ( organizations.length > 1 ) {
			return {
				score: 9,
				text: "Your knowledge of the activities of governmental organization is on point!",
			};
		}
	}
	return {};
};

/**
 * Execute the Governmental Organization Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const governmentalOrganizationsAssessment = function( paper ) {
	let governmentalOrganizationResult = scoreGovernmentalOrganizations( paper );

	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( governmentalOrganizationResult.score );
	assessmentResult.setText( governmentalOrganizationResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: governmentalOrganizationsAssessment,
};
