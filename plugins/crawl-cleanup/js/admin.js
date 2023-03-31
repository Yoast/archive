jQuery( document ).ready( function( $ ) {
	$( "#yoast-tabs" ).find( "a" ).click( function() {
		$( "#yoast-tabs" ).find( "a" ).removeClass( "nav-tab-active" );
		$( ".yoast_tab" ).removeClass( "active" );

		var id = $( this ).attr( "id" ).replace( "-tab", "" );
		$( "#" + id ).addClass( "active" );
		$( this ).addClass( "nav-tab-active" );
		$( "#yoast_return_tab" ).val( id );
	} );

	// Init.
	var activeTab = $( "#yoast_return_tab" ).val();
	if ( document.location.hash.search( "#top#" ) !== -1 ) {
		activeTab = "yoast-" + document.location.hash.replace( "#top#", "" );
	}
	$( "#" + activeTab ).addClass( "active" );
	$( "#" + activeTab + "-tab" ).addClass( "nav-tab-active" ).click();
} );
