/**
 * Customizer live preview.
 */
( function ( api ) {
	'use strict';

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			document.querySelectorAll( '.site-title__text' ).forEach( function ( el ) {
				el.textContent = to;
			} );
		} );
	} );
}( wp.customize ) );
