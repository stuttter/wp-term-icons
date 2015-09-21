/**
 * Dashicons Picker
 *
 * Based on: https://github.com/bradvin/dashicons-picker/
 */

/* global wpDashiconPickerL10n */
( function ( $, undef ) {

	var DashiconPicker,

		// HTML stuff
		_before = '<a tabindex="0" class="wp-dashicon-result" />',
		_after  = '<div class="wp-picker-holder" />',
		_wrap   = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small hidden" />';

	// jQuery UI Widget constructor
	DashiconPicker = {
		options: {
			defaultDashicon: false,
			change:          false,
			clear:           false,
			hide:            true,
			width:           255
		},
		_create: function() {

			var self = this,
				el   = self.element;

			$.extend( self.options, el.data() );

			// keep close bound so it can be attached to a body listener
			self.close = $.proxy( self.close, self );

			self.initialValue = el.val();

			// Set up HTML structure, hide things
			el.addClass( 'wp-dashicon-picker' ).hide().wrap( _wrap );
			self.wrap            = el.parent();
			self.toggler         = $( _before ).insertBefore( el ).css( { backgroundImage: self.initialValue } ).attr( 'title', wpDashiconPickerL10n.pick ).attr( 'data-current', wpDashiconPickerL10n.current );
			self.pickerContainer = $( _after ).insertAfter( el );
			self.button          = $( _button );

			if ( self.options.defaultDashicon ) {
				self.button.addClass( 'wp-picker-default' ).val( wpDashiconPickerL10n.defaultString );
			} else {
				self.button.addClass( 'wp-picker-clear' ).val( wpDashiconPickerL10n.clear );
			}

			el.wrap( '<span class="wp-picker-input-wrap" />' ).after(self.button);
			
/*
			el.iris( {
				target: self.pickerContainer,
				hide:   self.options.hide,
				width:  self.options.width,
				change: function( event, ui ) {
					self.toggler.css( { backgroundImage: ui.dashicon.toString() } );
					// check for a custom cb
					if ( $.isFunction( self.options.change ) ) {
						self.options.change.call( this, event, ui );
					}
				}
			} );
*/
			el.val( self.initialValue );
			self._addListeners();
			if ( ! self.options.hide ) {
				self.toggler.click();
			}
		},
		_addListeners: function() {
			var self = this;

			// prevent any clicks inside this widget from leaking to the top and closing it
			self.wrap.on( 'click.wpdashiconpicker', function( event ) {
				event.stopPropagation();
			});

			self.toggler.click( function(){
				if ( self.toggler.hasClass( 'wp-picker-open' ) ) {
					self.close();
				} else {
					self.open();
				}
			});

			self.element.change( function( event ) {
				var me = $( this ),
					val = me.val();
				// Empty = clear
				if ( val === '' || val === '#' ) {
					self.toggler.css( 'backgroundImage', '' );
					// fire clear callback if we have one
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				}
			});

			// open a keyboard-focused closed picker with space or enter
			self.toggler.on( 'keyup', function( event ) {
				if ( event.keyCode === 13 || event.keyCode === 32 ) {
					event.preventDefault();
					self.toggler.trigger( 'click' ).next().focus();
				}
			});

			self.button.click( function( event ) {
				var me = $( this );
				if ( me.hasClass( 'wp-picker-clear' ) ) {
					self.element.val( '' );
					self.toggler.css( 'backgroundImage', '' );
					if ( $.isFunction( self.options.clear ) ) {
						self.options.clear.call( this, event );
					}
				} else if ( me.hasClass( 'wp-picker-default' ) ) {
					self.element.val( self.options.defaultDashicon ).change();
				}
			});
		},
		open: function() {
			this.element.show().focus();
			this.button.removeClass( 'hidden' );
			this.toggler.addClass( 'wp-picker-open' );
			$( 'body' ).trigger( 'click.wpdashiconpicker' ).on( 'click.wpdashiconpicker', this.close );
		},
		close: function() {
			this.element.hide();
			this.button.addClass( 'hidden' );
			this.toggler.removeClass( 'wp-picker-open' );
			$( 'body' ).off( 'click.wpdashiconpicker', this.close );
		},
		// $("#input").wpDashiconPicker('dashicon') returns the current dashicon
		// $("#input").wpDashiconPicker('dashicon', '#bada55') to set
		dashicon: function( newDashicon ) {
			if ( newDashicon === undef ) {
				//return this.element.iris( 'option', 'dashicon' );
			}

			//this.element.iris( 'option', 'dashicon', newDashicon );
		},
		//$("#input").wpDashiconPicker('defaultDashicon') returns the current default dashicon
		//$("#input").wpDashiconPicker('defaultDashicon', newDefaultDashicon) to set
		defaultDashicon: function( newDefaultDashicon ) {
			if ( newDefaultDashicon === undef ) {
				return this.options.defaultDashicon;
			}

			this.options.defaultDashicon = newDefaultDashicon;
		}
	};

	$.widget( 'wp.wpDashiconPicker', DashiconPicker );

}( jQuery ) );
