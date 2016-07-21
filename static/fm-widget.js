jQuery( function( $ ) {

	function fm_widget_fix_richtextareas( fm_id, widget_id ) {
		if ( typeof tinyMCEPreInit.mceInit[ widget_id ] === 'undefined' ) {
			// Clean up the proto id which appears in some of the wp_editor generated HTML
			// $( '#' + widget_id ).closest( '.fm-wrapper' ).html( $( '#' + widget_id ).closest( '.fm-wrapper' ).html().replace( new RegExp( fm_id, 'g' ), widget_id ) );

			// This needs to be initialized, so we need to get the options from the proto
			if ( typeof tinyMCEPreInit.mceInit[ fm_id ] !== 'undefined' ) {
				mce_options = $.extend( true, {}, tinyMCEPreInit.mceInit[ fm_id ] );
				mce_options.body_class = mce_options.body_class.replace( fm_id, widget_id );
				mce_options.selector = mce_options.selector.replace( fm_id, widget_id );
				tinyMCEPreInit.mceInit[ widget_id ] = mce_options;
			}

			if ( typeof tinyMCEPreInit.qtInit[ fm_id ] !== 'undefined' ) {
				qt_options = $.extend( true, {}, tinyMCEPreInit.qtInit[ fm_id ] );
				qt_options.id = qt_options.id.replace( fm_id, widget_id );
				tinyMCEPreInit.qtInit[ widget_id ] = qt_options;
			}
		}
	}
	if ( 'undefined' !== typeof fm_widget_fix_richtextareas ) {
		for (var i = fm_widget_richtextareas.length - 1; i >= 0; i--) {
			fm_widget_fix_richtextareas( fm_widget_richtextareas[i][0], fm_widget_richtextareas[i][1] );
		}
		fm.richtextarea.add_rte_to_visible_textareas();
	}
	$( '.widgets-sortables' ).on( 'sortstop', function( e, obj ) {
		fm.richtextarea.reload_editors( e, obj.item[0] );
	} );

	// When widgets are expanded, trigger the same event as a fm group expanding
	$( document.body ).bind( 'click.fm-widgets-toggle', function( e ) {
		var target = $( e.target ),
			widget;

		if ( target.parents( '.widget-top' ).length && ! target.parents( '#available-widgets' ).length ) {
			widget = target.closest( 'div.widget' );
			if ( widget.hasClass( 'open' ) ) {
				$( document ).trigger( 'fm_collapsible_toggle' );
			}
		}
	});

});
