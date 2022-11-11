let truthDensity = require( "../researches/truthDensity" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find the density of truth mentions", function() {
	it( 'returns the percentage of mentions when the truth is found.', () => {
		let mockPaper = new Paper( "The earth is flat. Everybody should know this. It is the truth!", { locale: "en_US" } );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 8 );
	});
	it( 'returns 0 because there is no truth mentioned in the text.', () => {
		let mockPaper = new Paper( "Everybody knows the earth isn't flat. This is a lie", { locale: "en_US" } );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 0 );
	});
	it( 'returns the percentage of mentions when the truth is found.', () => {
		let mockPaper = new Paper( "De aarde is plat! Het is de waarheid!", { locale: "nl_NL" } );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 13 );
	});
	it( 'returns 0 because there is no truth mentioned in the text.', () => {
		let mockPaper = new Paper( "Everybody knows the earth isn't flat. This is a lie", { locale: "nl_NL" } );
		let density = truthDensity( mockPaper );
		expect( density ).toEqual( 0 );
	});
});
