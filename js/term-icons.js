jQuery( document ).ready( function() {
    'use strict';
/*
    if ( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ) {
        jQuery( '#term-color' ).wpColorPicker();
    } else {
        jQuery( '#colorpicker' ).farbtastic( '#term-color' );
    }
*/
    jQuery( '.editinline' ).on( 'click', function() {
        var tag_id = jQuery( this ).parents( 'tr' ).attr( 'id' ),
			icon  = jQuery( 'td.icon i', '#' + tag_id ).attr( 'data-icon' );

        jQuery( ':input[name="term-icon"]', '.inline-edit-row' ).val( icon );
    } );
} );
