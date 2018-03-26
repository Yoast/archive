let exclamationMarkInTitle = require( "../researches/exclamationMarkInTitle" );

describe( "a research to check titles for exclamation marks", function() {
	it( "returns true when a title ends with !.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Title!" );
		expect( hasExclamationMark ).toBe( true );
	} );
	it( "returns true when a title ends with ??!?!??.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Title??!?!??" );
		expect( hasExclamationMark ).toBe( true );
	} );
	it( "returns true when the title has an exclamation mark in the middle.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Its true! Or is it?" );
		expect( hasExclamationMark ).toBe( true );
	} );
	it( "returns true when the title has an exclamation mark in the middle and at the end.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Its true! Or is it!" );
		expect( hasExclamationMark ).toBe( true );
	} );
	it( "returns false when a title ends with ?.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Title?" );
		expect( hasExclamationMark ).toBe( false );
	} );
	it( "returns false when a title ends with no punctuation mark.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Title" );
		expect( hasExclamationMark ).toBe( false );
	} );
	it( "returns false when a title ends with a period.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "Title." );
		expect( hasExclamationMark ).toBe( false );
	} );
	it( "returns false when the title is empty.", () => {
		let hasExclamationMark = exclamationMarkInTitle( "" );
		expect( hasExclamationMark ).toBe( false );
	} );
} );
