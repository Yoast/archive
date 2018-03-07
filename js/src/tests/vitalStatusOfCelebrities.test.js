let vitalStatusOfCelebrities = require( "../researches/vitalStatusOfCelebrities" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find the mention of governmental organizations in Dutch texts", function() {
	it( 'returns a list of vital status mentions of dead people', () => {
		let mockPaper = new Paper( "bla bla paul is dead bla bla." );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ " paul is dead" ] );
	});
	it( 'returns a list of vital status mentions of alive people', () => {
		let mockPaper = new Paper( "bla bla tupac didn't die bla bla." );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ " tupac didn't die" ] );
	});
});
