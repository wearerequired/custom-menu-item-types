/**
 * Custom Menu Item Types
 *
 * Copyright (c) 2016 required+
 * Licensed under the GPLv2+ license.
 */
jQuery( document ).ready(function( $ ) {
	$( '#update-nav-menu' ).bind( 'click', function( e ) {
		if ( e.target && e.target.className && -1 != e.target.className.indexOf( 'item-edit' ) ) {
			$( "input[value='#line_break'][type=text]" ).parent().parent().parent().each(function(){
				var item = $( this ).attr( 'id' ).substring( 19 );
				$( this ).children( 'p:not( .field-move )' ).remove(); // remove default fields we don't need
				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-title-' + item,
					name:  'menu-item-title[' + item + ']',
					value: rcmit_data.line_break_title
				});
				$( this ).append( h );

				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-url-' + item,
					name:  'menu-item-url[' + item + ']',
					value: '#line_break'
				});
				$( this ).append( h );

				// a hidden field which exits only if our jQuery code has been executed
				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-rcmit-detect-' + item,
					name:  'menu-item-rcmit-detect[' + item + ']',
					value: 1
				});
				$( this ).append( h );
			});
			$( "input[value='#custom_headline'][type=text]" ).parent().parent().parent().each(function(){
				var item = $( this ).attr( 'id' ).substring( 19 );
				$( this ).children( 'p:not( .field-move, .field-css-classes, .field-title )' ).remove(); // remove default fields we don't need

				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-url-' + item,
					name:  'menu-item-url[' + item + ']',
					value: '#custom_headline'
				});
				$( this ).append( h );

				// a hidden field which exits only if our jQuery code has been executed
				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-rcmit-detect-' + item,
					name:  'menu-item-rcmit-detect[' + item + ']',
					value: 1
				});
				$( this ).append( h );

			});
			$( "input[value='#custom_headline'][type=text]" ).parent().parent().parent().each(function(){
				var item = $( this ).attr( 'id' ).substring( 19 );
				$( this ).children( 'p:not( .field-move, .field-css-classes, .field-title )' ).remove(); // remove default fields we don't need

				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-url-' + item,
					name:  'menu-item-url[' + item + ']',
					value: '#custom_headline'
				});
				$( this ).append( h );

				// a hidden field which exits only if our jQuery code has been executed
				h = $( '<input>' ).attr({
					type:  'hidden',
					id:    'edit-menu-item-rcmit-detect-' + item,
					name:  'menu-item-rcmit-detect[' + item + ']',
					value: 1
				});
				$( this ).append( h );

			});

		}
	});
});
