let governmentalOrganizationsAssessment = require( "../assessments/governmentalOrganizationsAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

let fillerText = "Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. Dit is opvulling om aan 100 woorden te komen. ";

describe( "an assessment assessing the mentioning of governmental organizations in Dutch texts", function() {
	it( 'returns a score of 9 when there are multiple mentions of governmental organizations', () => {
		let mockPaper = new Paper( "Dit is duidelijk een cover-up door de AIVD. De BVD heeft er vast ook iets mee te maken!" + fillerText, { locale: "nl_NL" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Your knowledge of the activities of governmental organization is on point!" );
	});
	it( 'returns a score of 6 when there is a mention of a Dutch governmental organizations', () => {
		let mockPaper = new Paper( "Dit is duidelijk een cover-up door de AIVD." + fillerText, { locale: "nl_NL" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You've only mentioned a governmental organization once. We know you can do better!" );
	});
	it( 'returns a score of 6 when there is a mention of a general governmental organizations', () => {
		let mockPaper = new Paper( "De KGB wil dat niemand dit weet!" + fillerText, { locale: "nl_NL" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You've only mentioned a governmental organization once. We know you can do better!" );
	});
	it( 'returns a score of 3 when there are no mentions of governmental organizations', () => {
		let mockPaper = new Paper( "Dit is een saaie tekst." + fillerText, { locale: "nl_NL" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't even mentioned a governmental organization! How do you expect us to believe you're writing about facts?!" );
	});
});

describe( "an assessment assessing the mentioning of governmental organizations in English texts", function() {
	it( 'returns a score of 9 when there are multiple mentions of governmental organizations', () => {
		let mockPaper = new Paper( "It's a cover-up by the CIA. The FBI is involved as well!" + fillerText, { locale: "en_US" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Your knowledge of the activities of governmental organization is on point!" );
	});
	it( 'returns a score of 6 when there is a mention of a governmental organization', () => {
		let mockPaper = new Paper( "It's a cover-up by the CIA." + fillerText, { locale: "en_US" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You've only mentioned a governmental organization once. We know you can do better!" );
	});
	it( 'returns a score of 3 when there are no mentions of governmental organizations', () => {
		let mockPaper = new Paper( "This is a boring text." + fillerText, { locale: "en_US" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't even mentioned a governmental organization! How do you expect us to believe you're writing about facts?!" );
	});
	it( 'returns a score of 3 when there is only a Dutch organization mentioned', () => {
		let mockPaper = new Paper( "The AIVD is definitely involved!." + fillerText, { locale: "en_US" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't even mentioned a governmental organization! How do you expect us to believe you're writing about facts?!" );
	});
});

describe( "an assessment assessing the mentioning of governmental organizations in a text with a non-Dutch and non-English locale", function() {
	it( 'returns a score of 9 when there are multiple mentions of governmental organizations', () => {
		let mockPaper = new Paper( "La vache qui rit, FBI, CIA." + fillerText, { locale: "fr_FR" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "Your knowledge of the activities of governmental organization is on point!" );
	});
	it( 'returns a score of 6 when there is a mention of a governmental organization', () => {
		let mockPaper = new Paper( "La vache qui rit, FBI." + fillerText, { locale: "fr_FR" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You've only mentioned a governmental organization once. We know you can do better!" );
	});
	it( 'returns a score of 3 when there are no mentions of governmental organizations', () => {
		let mockPaper = new Paper( "La vache qui rit." + fillerText, { locale: "fr_FR" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't even mentioned a governmental organization! How do you expect us to believe you're writing about facts?!" );
	});
});

describe( "an assessment assessing the mentioning of governmental organizations in a too short text", function() {
	it( 'returns a score of 0 when a text has less than 100 words', () => {
		let mockPaper = new Paper( "Dit is een te korte tekst met AIVD erin.", { locale: "nl_NL" } );
		let result = governmentalOrganizationsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	});
});
