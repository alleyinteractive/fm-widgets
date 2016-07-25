jQuery( function( $ ) {

	// When widgets are expanded, trigger the same event as a fm group expanding
	$( document.body ).bind( 'click.fm-widgets-toggle', function( e ) {
		var target = $( e.target ),
			widget;

		if ( target.parents( '.widget-top' ).length && ! target.parents( '#available-widgets' ).length ) {
			widget = target.closest( 'div.widget' );
			if ( widget.hasClass( 'open' ) ) {
				$( document ).trigger( 'fm_activate_tab' );
			}
		}
	});

	$( document ).bind( 'widget-added widget-updated', function() {
		$( document ).trigger( 'fm_activate_tab' );
		if ( fm && fm.init_display_if ) {
			$( '.display-if' ).each( fm.init_display_if );
		}
	});

});
