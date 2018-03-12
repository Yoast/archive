let allTheThings = require( "../researches/allTheThings" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "This will check if you've written all the things people need to know!", function(){
	it ( "returns the moonlanding as the next thing to write about", function(){
		let mockPaper = new Paper( "This is an ordinary text." );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "moon landing" );
	});

	it ( "returns flat earth as the next thing to write about", function(){
		let mockPaper = new Paper( "The moon landing was staged" );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "earth is flat" );
	});

	it ( "returns ufo as the next thing to write about", function(){
		let mockPaper = new Paper( "The moon landing was staged, the earth is flat and run by the reptilians " );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "ufo" );
	});

	it ("returns an empty string when everything is written.", function(){
		let mockPaper = new Paper( "moon landing earth is flat ufo area 51 denver airport illuminati freemasons reptilians chemtrails" );
		let thing = allTheThings( mockPaper );
		expect( thing ).toEqual( "" );
	});
});