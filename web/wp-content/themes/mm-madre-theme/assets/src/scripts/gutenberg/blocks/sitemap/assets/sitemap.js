var target = window.location.hash;
window.location.hash = '';

jQuery( 'document' ).ready( function ( $ ) {
	if ( target != '' ) {
		var aTag = $( "a[name='" + target.replace( '#', '' ) + "']" );
		$( 'html,body' ).animate(
			{ scrollTop: aTag.offset().top - 165 },
			'slow'
		);
	}
} );
