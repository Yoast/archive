let truthDensity = require( "../researches/truthDensity" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find the density of truth mentions", function() {
	it( 'returns the percentage of mentions when the truth is found.', () => {
		let mockPaper = new Paper( "The earth is flat. Everybody should know this. It is the truth!" );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 8 );
	});
	it( 'returns 0 because there is no truth mentioned in the text.', () => {
		let mockPaper = new Paper( "Everybody knows the earth isn't flat. This is a lie" );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 0 );
	});
});
