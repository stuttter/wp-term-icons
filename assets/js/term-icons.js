jQuery( document ).ready( function() {
    'use strict';

    if ( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpDashiconPicker === 'function' ) {
        jQuery( '#term-icon' ).wpDashiconPicker();
    }

	jQuery( '.editinline' ).on( 'click', function() {
        var tag_id = jQuery( this ).parents( 'tr' ).attr( 'id' ),
			icon  = jQuery( 'td.icon i', '#' + tag_id ).attr( 'data-icon' );

        jQuery( ':input[name="term-icon"]', '.inline-edit-row' ).val( icon );
    } );
} );
