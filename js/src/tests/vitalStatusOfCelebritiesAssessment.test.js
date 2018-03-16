let vitalStatusOfCelebritiesAssessment = require( "../assessments/vitalStatusOfCelebritiesAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

let fillerText = "Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. Dit is opvulling om aan 200 woorden te komen. ";

describe( "an assessment assessing the mentioning of governmental organizations in Dutch texts", function() {
	it( 'returns a score of 9 when the vital status of a celebrity is disputed in a Dutch text', () => {
		let mockPaper = new Paper( "Paul is dood!" + fillerText, { locale: "nl_NL" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Way to go! You know who's dead and who isn't!" );
	});
	it( 'returns a score of 3 when the vital status of no celebrity is disputed in a Dutch text', () => {
		let mockPaper = new Paper( "Dit is een saaie tekst." + fillerText, { locale: "nl_NL" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Over 200 words in, and you haven't disputed the vital status of any celebrity yet! Is Elvis really dead?" );
	});
});

describe( "an assessment assessing the mentioning of governmental organizations in English texts", function() {
	it( 'returns a score of 9 when the vital status of a celebrity is disputed in an English text', () => {
		let mockPaper = new Paper( "Elvis is alive!" + fillerText, { locale: "en_US" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Way to go! You know who's dead and who isn't!" );
	});
	it( 'returns a score of 3 when vital status of no celebrity is disputed in an English text', () => {
		let mockPaper = new Paper( "This is a boring text." + fillerText, { locale: "en_US" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Over 200 words in, and you haven't disputed the vital status of any celebrity yet! Is Elvis really dead?" );
	});
});

describe( "an assessment assessing the mentioning of governmental organizations in a text with a non-Dutch and non-English locale", function() {
	it( 'returns a score of 9 when there are multiple mentions of governmental organizations', () => {
		let mockPaper = new Paper( "La vache qui rit, Elvis is alive." + fillerText, { locale: "fr_FR" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Way to go! You know who's dead and who isn't!" );
	});
	it( 'returns a score of 3 when the vital status of no celebrity is disputed in a non-Dutch and non-English text', () => {
		let mockPaper = new Paper( "La vache qui rit." + fillerText, { locale: "fr_FR" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Over 200 words in, and you haven't disputed the vital status of any celebrity yet! Is Elvis really dead?" );
	});
});

describe( "an assessment assessing there is no dispute of the vital status of a celebrity in a too short text", function() {
	it( 'returns a score of 0 when a text has less than 200 words', () => {
		let mockPaper = new Paper( "Dit is een te korte tekst met AIVD erin.", { locale: "nl_NL" } );
		let result = vitalStatusOfCelebritiesAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	});
});
