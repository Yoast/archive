let exclamationMarkInTitleAssessment = require( "../assessments/exclamationMarkInTitleAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

describe( "an assessment assessing the presence of at least one exclamation mark in the title", function() {
	it( "returns a score of 0 and an empty feedback string when the title is empty.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "" } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	} );
	it( "returns a score of 0 and an empty feedback string when a title ends with !.", () => {
		let mockPaper = new Paper( "This is a paper." );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	} );
	it( "returns a score of 0 and an empty feedback string when a title ends with ??!?!??.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "Title??!?!??" } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	} );
	it( "returns a score of 0 and an empty feedback string when the title has exclamation marks in the middle.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "Its true! Or is it?" } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 0 );
		expect( result.getText() ).toBe( "" );
	} );
	it( "returns a score of 3 when a title ends with ?.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "Title?" } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Why doesn't your title contain an exclamation mark?! Write your title like you mean it!!!!" );
	} );
	it( "returns a score of 3 when a title ends with no punctuation mark.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "Title" } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Why doesn't your title contain an exclamation mark?! Write your title like you mean it!!!!" );
	} );
	it( "returns a score of 3 when a title ends with a period.", () => {
		let mockPaper = new Paper( "This is a paper", { title: "Title." } );
		let result = exclamationMarkInTitleAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "Why doesn't your title contain an exclamation mark?! Write your title like you mean it!!!!" );
	} );
} );
