let existenceDenialAssessment = require( "../assessments/existenceDenialAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

let fillerText = "Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. Dit is opvulling om aan 150 woorden te komen. ";

describe( "an assessment assessing existence denial in a Dutch text", function() {
	it( 'returns a score of 9 when there are multiple denials in a Dutch text', () => {
		let mockPaper = new Paper( "De maan bestaat niet. Finland bestaat ook niet." + fillerText, { locale: "nl_NL" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Good job! Not everything the government tells you about does exist!!!" );
	});
	it( 'returns a score of 6 when there is a single denial in a Dutch text', () => {
		let mockPaper = new Paper( "Dit is een tekst. Finland bestaat helemaal niet." + fillerText, { locale: "nl_NL" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "Sure, you're denying the existence of something. But there is more to deny, right?!" );
	});
	it( 'returns a score of 3 when there are no denials in a Dutch text', () => {
		let mockPaper = new Paper( "Dit is een saaie tekst." + fillerText, { locale: "nl_NL" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Don't you want to tell your readers about the lies they believe?! Does Finland really exist?! And what about the moon?!" );
	});
	it( 'returns a score of 0 when a text has less than 150 words', () => {
		let mockPaper = new Paper( "Dit is een te korte tekst. De maan bestaat ook niet.", { locale: "nl_NL" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	});
});

describe( "an assessment assessing existence denial in an English text", function() {
	it( 'returns a score of 9 when there are multiple denials in a English text', () => {
		let mockPaper = new Paper( "The moon doesn't exist. Finland is a lie either!" + fillerText, { locale: "en_US" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Good job! Not everything the government tells you about does exist!!!" );
	});
	it( 'returns a score of 6 when there is a single denial in an English text', () => {
		let mockPaper = new Paper( "The cake is a lie." + fillerText, { locale: "en_US" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "Sure, you're denying the existence of something. But there is more to deny, right?!" );
	});
	it( 'returns a score of 3 when there are no denials in an English text', () => {
		let mockPaper = new Paper( "This is a boring text." + fillerText, { locale: "en_US" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Don't you want to tell your readers about the lies they believe?! Does Finland really exist?! And what about the moon?!" );
	});
});

describe( "an assessment assessing existence denial in a text with a non-Dutch and non-English locale", function() {
	it( 'returns a score of 9 when there are multiple denials in a non-Dutch and non-English text', () => {
		let mockPaper = new Paper( "La vache qui rit, The moon doesn't exist. Finland is a lie either!" + fillerText, { locale: "fr_FR" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Good job! Not everything the government tells you about does exist!!!" );
	});
	it( 'returns a score of 6 when there is a single denial in a non-Dutch and non-English text', () => {
		let mockPaper = new Paper( "La vache qui rit, The cake is a lie." + fillerText, { locale: "fr_FR" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "Sure, you're denying the existence of something. But there is more to deny, right?!" );
	});
	it( 'returns a score of 3 when there are no denials in a non-Dutch and non-English text', () => {
		let mockPaper = new Paper( "La vache qui rit." + fillerText, { locale: "fr_FR" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Don't you want to tell your readers about the lies they believe?! Does Finland really exist?! And what about the moon?!" );
	});
});

describe( "an assessment assessing existence denial in a too short text", function() {
	it( 'returns a score of 0 when a text has less than 150 words', () => {
		let mockPaper = new Paper( "Dit is een te korte tekst. De maan bestaat ook niet.", { locale: "nl_NL" } );
		let result = existenceDenialAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	});
});
