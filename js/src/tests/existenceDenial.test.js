let existenceDenial = require( "../researches/existenceDenial" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find denials in texts", function() {
	it( 'returns a list of denials in an English text', () => {
		let mockPaper = new Paper( "The moon doesn't exist, you know.", { locale: "en_US" } );
		let denials = existenceDenial( mockPaper );
		expect( denials ).toEqual( [ " moon doesn't exist" ] );
	});
	it( 'returns null for an English text without denials', () => {
		let mockPaper = new Paper( "Everything is true", { locale: "en_US" } );
		let denials = existenceDenial( mockPaper );
		expect( denials ).toEqual( null );
	});
	it( 'returns a list of denials in a Dutch text', () => {
		let mockPaper = new Paper( "De maan bestaat niet, weet je.", { locale: "nl_NL" } );
		let denials = existenceDenial( mockPaper );
		expect( denials ).toEqual( [ " maan bestaat niet" ] );
	});
	it( 'returns null for a Dutch text without denials', () => {
		let mockPaper = new Paper( "Alles is waar", { locale: "nl_NL" } );
		let denials = existenceDenial( mockPaper );
		expect( denials ).toEqual( null );
	});
});
