let allTheThingsAssessment = require( "../assessments/missingThingsAssessment" );
let Paper = require( "yoastseo/js/values/Paper.js" );

let filler = "this is a piece of filler text so we can reach the 250 word minimum. " +
	"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam dolor mauris, " +
	"bibendum eget elit sit amet, auctor interdum justo. Nunc dignissim porta turpis a efficitur. " +
	"Mauris fermentum tortor in est vestibulum ultrices. Nam vitae metus ligula. Nam ac volutpat augue. " +
	"vamus at rutrum arcu. Nulla facilisi. Etiam auctor dolor quam, in vestibulum dui blandit eget.	" +
	"Morbi augue augue, finibus eget congue non, sollicitudin at enim. Integer mattis orci at velit sollicitudin, " +
	"vel facilisis mi varius. Integer at nisi purus. Etiam id sem sit amet quam tempor consectetur. " +
	"Sed sit amet nisi ex. Vivamus ex ante, porta at turpis vitae, pellentesque condimentum dolor. " +
	"Maecenas molestie sem nisi, vel rutrum nisi tempor et. Ut elementum est orci, et rhoncus odio dictum id. " +
	"Fusce molestie mattis diam. Quisque convallis dignissim nisl ut rhoncus. " +
	"Pellentesque ac dui consequat, porttitor sem euismod, pellentesque tellus." +
	"Fusce ut dui semper, imperdiet ex a, placerat mi. Donec faucibus aliquet tristique. " +
	"Pellentesque posuere, nunc vel elementum feugiat, lorem erat condimentum felis, et feugiat orci nisi id ipsum. " +
	"Morbi iaculis, augue sit amet scelerisque aliquet, neque lacus vehicula tellus, id malesuada justo felis nec ligula. " +
	"In placerat tortor id commodo faucibus. Fusce ut aliquam massa. Donec egestas accumsan tellus non tincidunt. " +
	"Donec ac egestas sem. Praesent finibus elementum eros, sit amet sagittis nisi scelerisque et. " +
	"In et risus eu odio euismod vestibulum a vel tortor. Proin eros neque, volutpat in ante at, cursus auctor dui. " +
	"Donec orci sapien, scelerisque in condimentum eu, rutrum ut urna. Sed vel eleifend mauris. Maecenas. "

describe( "an assessment assessing the number of conspiracies mentioned", function() {
	it( 'returns a score of 3 when no conspiracies are mentioned', () => {
		let mockPaper = new Paper( "This is a simple text." +  filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't mentioned how the moon landing was staged. Don't you think you should tell people this?!" );
	});

	it ( 'returns a score of 3 when only the moon landing was mentioned', () => {
		let mockPaper = new Paper( "This is a simple text about the moon landing." + filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "People really should know that the earth is flat. There is a reason the moon landing was faked!" );
	});

	it ( 'returns a score of 3 when only the moon landing and flat earth was mentioned', () => {
		let mockPaper = new Paper( "This is a simple text about the moon landing and how the earth is flat ." + filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "While you are on the subject of why the earth is flat, why not mention how UFOs are involved?!" );
	});

	it ( 'returns a score of 6 when the last thing mentioned from the list is Denver Airport', () => {
		let mockPaper = new Paper( "This is a simple text about: moon landing earth is flat ufo area 51 denver airport." + filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You know the Illuminati are involved. Don't be afraid to mention them as well!!" );
	});


	it ( 'returns a score of 6 when the illuminati aren\'t mentioned, but chemtrails are which is further in the list than the illuminati', () => {
		let mockPaper = new Paper( "This is a simple text about: moon landing earth is flat ufo area 51 denver airport chemtrails." + filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You know the Illuminati are involved. Don't be afraid to mention them as well!!" );
	});

	it ( 'returns a score of 9 when all the things are mentioned', () => {
		let mockPaper = new Paper( "moon landing earth is flat ufo area 51 denver airport illuminati freemasons reptilians chemtrails ." + filler );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "You know what you are talking about!! Great you're willing to share your ultimate knowledge with your readers!!!" );
	});

	it( 'returns a score of 3 when no conspiracies are mentioned in a dutch text', () => {
		let mockPaper = new Paper( "This is a simple text." +  filler  ,{ locale: "nl_NL" } );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 3 );
		expect( result.getText() ).toBe( "You haven't mentioned how the moon landing was staged. Don't you think you should tell people this?!" );
	});

	it ( 'returns a score of 6 when the illuminati aren\'t mentioned, but chemtrails are which is further in the list than the illuminati in a Dutch text', () => {
		let mockPaper = new Paper( "Dit is een tekst over de maanlanding, de aarde is plat, area 51 ufo en denver airport en chemtrails." + filler  ,{ locale: "nl_NL" } );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 6 );
		expect( result.getText() ).toBe( "You know the Illuminati are involved. Don't be afraid to mention them as well!!" );
	});

	it ( 'returns a score of 9 when all the things are mentioned with a text in Dutch', () => {
		let mockPaper = new Paper( "maanlanding aarde is plat ufo area 51 denver airport illuminati vrijmetselaars reptilians chemtrails." + filler, { locale: "nl_NL" } );
		let result = allTheThingsAssessment.getResult( mockPaper );

		expect( result.getScore() ).toBe( 9 );
		expect( result.getText() ).toBe( "You know what you are talking about!! Great you're willing to share your ultimate knowledge with your readers!!!" );
	});
});
