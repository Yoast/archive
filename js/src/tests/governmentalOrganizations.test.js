let governmentalOrganizations = require( "../researches/governmentalOrganizations" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find the mention of governmental organizations in Dutch texts", function() {
	it( 'returns a list of matched governmental organizations', () => {
		let mockPaper = new Paper( "Dit is duidelijk een cover-up door de AIVD.", { locale: "nl_NL" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( [ " aivd" ] );
	});
	it( 'returns null when there are no governmental organizations mentioned', () => {
		let mockPaper = new Paper( "Dit is een saaie tekst.", { locale: "nl_NL" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( null );
	});
});

describe( "a research to find the mention of governmental organizations in English texts", function() {
	it( 'returns a list of matched governmental organizations', () => {
		let mockPaper = new Paper( "It's a cover-up by the CIA. The FBI is involved as well!", { locale: "en_US" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( [ " cia", " fbi" ] );
	});
	it( 'returns null when there are no governmental organizations mentioned', () => {
		let mockPaper = new Paper( "This is a boring text.", { locale: "en_US" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( null );
	});
});

describe( "a research to find the mention of governmental organizations in a text with a non-Dutch and non-English locale", function() {
	it( 'returns a list of matched governmental organizations', () => {
		let mockPaper = new Paper( "La vache qui rit, CIA.", { locale: "fr_FR" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( [ " cia" ] );
	});
	it( 'returns null when there are no governmental organizations mentioned', () => {
		let mockPaper = new Paper( "La vache qui rit.", { locale: "fr_FR" } );
		let organizations = governmentalOrganizations( mockPaper );
		expect( organizations ).toEqual( null );
	});
});
