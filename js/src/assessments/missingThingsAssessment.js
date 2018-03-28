/* eslint-disable complexity */

let AssessmentResult = require( "yoastseo/js/values/AssessmentResult" );
let wordCount = require( "yoastseo/js/stringProcessing/countWords" );
let allTheThings = require( "../researches/missingThings" );

/**
 * Returns the score object based on the missing thing.
 *
 * @param {Paper} paper The paper to check for conspiracies.
 * @returns {{score: number, text:  string}} The object containing the score and feedback text.
 */
const scoreAllTheThings = function( paper ) {
	let result = {};

	// Only trigger this assessment when a text contains at least 250 words.
	if ( wordCount( paper.getText() ) >= 250 ) {
		let missingThing = allTheThings( paper );

		switch ( missingThing ) {
			case "moon landing":
			case "maanlanding":
				result.score = 3;
				result.text = "You haven't mentioned how the moon landing was staged. Don't you think you should tell people this?!";
				break;
			case "earth is flat":
			case "aarde is plat":
				result.score = 3;
				result.text = "People really should know that the earth is flat. There is a reason the moon landing was faked!";
				break;
			case "ufo":
				result.score = 3;
				result.text = "While you are on the subject of why the earth is flat, why not mention how UFOs are involved?!";
				break;
			case "area 51":
				result.score = 3;
				result.text = "How can you talk about UFOs without talking about Area 51?!";
				break;
			case "denver airport":
				result.score = 3;
				result.text = "Area 51 isn't the only suspicious place. What about Denver Airport? That place is as shady as can be!";
				break;
			case "illuminati":
				result.score = 6;
				result.text = "You know the Illuminati are involved. Don't be afraid to mention them as well!!";
				break;
			case "freemason":
			case "vrijmetselaar":
				result.score = 6;
				result.text = "Do you really think the Illuminati are alone? Of course not! The Freemasons are in on it too!";
				break;
			case "reptilian":
				result.score = 6;
				result.text = "Our earth is controlled by Reptillians! You know that. Please don't be too hesitant too tell that!!!";
				break;
			case "chemtrail":
				result.score = 6;
				result.text = "Did you see those lines in the sky? Chemtrails! They are spreading doom over the world. " +
					"Everyone should know this. Tell them!!";
				break;
			default:
				result.score = 9;
				result.text = "You know what you are talking about!! Great you're willing to share your ultimate knowledge with your readers!!!";
				break;
		}
	}
	return result;
};

/**
 * Execute the All the things Assessment and return a result.
 * @param {Paper} paper The Paper object to assess.
 * @returns {AssessmentResult} The result of the assessment, containing both a score and a feedback text.
 */
const allThethingsAssessment = function( paper ) {
	let allTheThingsResult = scoreAllTheThings( paper );
	let assessmentResult = new AssessmentResult();
	assessmentResult.setScore( allTheThingsResult.score );
	assessmentResult.setText( allTheThingsResult.text );

	return assessmentResult;
};

module.exports = {
	getResult: allThethingsAssessment,
};
