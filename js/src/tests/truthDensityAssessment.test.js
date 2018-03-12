let truthDensityAssessment = require( "../assessments/truthDensityAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

let fillerText = "This is some filler text, to make sure we reach the 50 words.  This is some filler text, to make sure we reach the 50 words.  This is some filler text, to make sure we reach the 50 words.  This is some filler text, to make sure we reach the 50 words.  This is some filler text, to make sure we reach the 50 words. ";

describe( "an assessment assessing the density of mentioning the truth in texts", function() {
	it('returns a score of 3 when there are no mentions of the truth in the text', () => {
		let mockPaper = new Paper("This is a lie" + fillerText);
		let result = truthDensityAssessment.getResult(mockPaper);

		expect(result.getScore()).toBe( 3 );
		expect(result.getText()).toBe("You haven't mentioned the truth anywhere in your text. You really should tell people you are telling the truth!!");
	});
	it( "returns a score of 6 when the truth is mentioned, but the density is less than 2%", function(){
		let mockPaper = new Paper(  "This is the truth " + fillerText);
		let result = truthDensityAssessment.getResult(mockPaper);

		expect(result.getScore()).toBe( 6 );
		expect(result.getText()).toBe("Please emphasize you are telling the truth. This will make your text more believable!! State this a few times more!!");
	})

	it( "returns a score of 9 when the truth is mentioned, but the density is 2% or more", function(){
		let mockPaper = new Paper(  "This is the truth this is true the shocking truth" + fillerText);
		let result = truthDensityAssessment.getResult(mockPaper);

		expect(result.getScore()).toBe( 9 );
		expect(result.getText()).toBe("You're telling people you know the truth. This is really great!!");
	})
})
