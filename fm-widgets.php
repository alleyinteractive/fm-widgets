<?php
/**
 * Plugin Name: FM Widget Context
 * Version: 0.1-alpha
 * Description: Adds a context for Fieldmanager to create complex widgets.
 * Author: Matthew Boynes
 * Author URI: https://www.alleyinteractive.com/
 * Plugin URI: https://github.com/alleyinteractive/fm-widgets
 * Text Domain: fm-widgets
 * Domain Path: /languages
 * @package Fm_Widgets
 */

define( 'FM_WIDGETS_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );

if ( class_exists( 'Fieldmanager_Field' ) && ! class_exists( 'FM_Widget' ) ) {
	require_once( __DIR__ . '/inc/class-fm-widget.php' );
}

function fm_widgets_calculated_context( $context ) {
	global $pagenow;

	if (
		'widgets.php' === $pagenow
		|| (
			defined( 'DOING_AJAX' ) && DOING_AJAX
			&& ! empty( $_POST['action'] )
			&& 'save-widget' === $_POST['action']
		)
	) {
		return [ 'widget', null ];
	}
	return $context;
}
add_filter( 'fm_calculated_context', 'fm_widgets_calculated_context' );
