jQuery( document ).ready( function( $ ) {
    'use strict';

    if ( typeof $.wp === 'object' && typeof $.wp.wpDashiconPicker === 'function' ) {
        $( '#term-icon' ).wpDashiconPicker();
    }

	$( '.editinline' ).on( 'click', function() {
        var tag_id = $( this ).parents( 'tr' ).attr( 'id' ),
			icon   = $( 'td.icon i', '#' + tag_id ).data( 'icon' );

        $( ':input[name="term-icon"]', '.inline-edit-row' ).val( icon );
    } );
} );
