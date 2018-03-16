let factStatedAssessment = require( "../assessments/allTheThingsAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "an assessment assessing the number of conspiracies mentioned", function() {
	it( 'returns a score of 3 when no conspiracies are mentioned', () => {
		let mockPaper = new Paper( "This is a simple text." );
		let result = factStatedAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't mentioned how the moon landing was staged. Don't you think you should tell people this?!" );
	});
});
