let factStatedAssessment = require( "../assessments/factStatedAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "an assessment assessing the stating of facts", function() {
	it( 'returns a score of 9 when a text has one sentence', () => {
		let mockPaper = new Paper( "This is a sentence." );
		let result = factStatedAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "You've stated a fact. Congratulations!" );
	});
	it( 'returns a score of 9 when a text has multiple sentences', () => {
		let mockPaper = new Paper( "This is a sentence. This is a second sentence. This is a third sentence." );
		let result = factStatedAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "You've stated a fact. Congratulations!" );
	});
	it( 'returns a score of 0 and an empty feedback string when a text has no sentences', () => {
		let mockPaper = new Paper( "" );
		let result = factStatedAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	});
});
