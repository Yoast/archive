let allTheThings = require( "../researches/missingThings" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "This will check if you've written all the things people need to know!", function() {
	it ( "returns the moonlanding as the next thing to write about", function() {
		let mockPaper = new Paper( "This is an ordinary text." );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "moon landing" );
	});

	it ( "returns the moonlanding as the next thing to write about", function() {
		let mockPaper = new Paper( "This is an ordinary text.", { locale: "en_US" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "moon landing" );
	});

	it ( "returns the flat earth as the next thing to write about when uppercases are used", function() {
		let mockPaper = new Paper( "This is an ordinary text about the MOON Landing.", { locale: "en_US" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "earth is flat" );
	});

	it ( "returns flat earth as the next thing to write about", function() {
		let mockPaper = new Paper( "The moon landing was staged" );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "earth is flat" );
	});

	it ( "returns ufo as the next thing to write about, because this is the first thing in the list that is missing.", function(){
		let mockPaper = new Paper( "The moon landing was staged, the earth is flat and run by the reptilians " );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "ufo" );
	});

	it ("returns an empty string when everything is written.", function() {
		let mockPaper = new Paper( "moon landing earth is flat ufo area 51 denver airport illuminati freemasons reptilians chemtrails" );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "" );
	});

	it ( "returns maanlanding as the next thing to write about with a dutch text", function() {
		let mockPaper = new Paper( "Dit is een simpele text.", { locale: "nl_NL" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "maanlanding" );
	});

	it ( "returns aarde is plat as the next thing to write about with a dutch text", function() {
		let mockPaper = new Paper( "Dit is een simpele text over de maanlanding die nep is.", { locale: "nl_NL" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "aarde is plat" );
	});

	it ( "returns maanlading as the next thing to write about with a dutch text", function() {
		let mockPaper = new Paper( "De aarde is plat.", { locale: "nl_NL" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "maanlanding" );
	});

	it ("returns an empty string when everything is written in a dutch text.", function() {
		let mockPaper = new Paper( "maanlanding aarde is plat ufo area 51 denver airport illuminati vrijmetselaars reptilians chemtrails", { locale: "nl_NL" } );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "" );
	});

	it ( "returns moon landing as the first thing to write about, since the german text will default to English", function() {
		let mockPaper = new Paper( "Dit is een simpele text.", { locale: "de_DE" } );
		let nextThing = allTheThings( mockPaper );
		expect( nextThing ).toEqual( "moon landing" );
	});
});
