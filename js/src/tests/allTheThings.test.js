let allThethings = require( "../researches/allTheThings" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "This will check if you've written all the things people need to know!", function(){
	it ( "returns the moonlanding as the next thing to write about", function(){
			let mockPaper = new Paper( "This is an ordinary text." );
			let things = allTheThings( mockPaper );
			expect( things ).toEqual( "the moon landing" );
		});
	});
});