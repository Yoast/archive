let vitalStatusOfCelebrities = require( "../researches/vitalStatusOfCelebrities" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "a research to find vital status mentions in texts", function() {
	it( 'returns a list of vital status mentions of dead people in an English text', () => {
		let mockPaper = new Paper( "Maybe you won't believe it, but Paul is dead.", { locale: "en_US" } );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ " paul is dead" ] );
	});
	it( 'returns a list of vital status mentions of alive people in an English text', () => {
		let mockPaper = new Paper( "Tupac didn't die, okay!", { locale: "en_US" } );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ "tupac didn't die" ] );
	});
	it( 'returns a list of vital status mentions of alive people in a Dutch text', () => {
		let mockPaper = new Paper( "Tupac is helemaal niet dood, hoor.", { locale: "nl_NL" } );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ "tupac is helemaal niet dood" ] );
	});
	it( 'returns a list of vital status mentions of dead people in a Dutch  text', () => {
		let mockPaper = new Paper( "Je zult het misschien niet geloven, maar Paul is dood.", { locale: "nl_NL" } );
		let celebrities = vitalStatusOfCelebrities( mockPaper );
		expect( celebrities ).toEqual( [ " paul is dood" ] );
	});
});
