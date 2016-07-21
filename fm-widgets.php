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
