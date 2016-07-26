Fieldmanager Widgets
====================

This plugin adds a context for [Fieldmanager](http://fieldmanager.org/) to help create widgets simply and easily, regardless of their complexity.

**This is in early stages of development and there are definitely bugs. Please [file any issues in GitHub](/alleyinteractive/fm-widgets/issues), and PRs are very welcome!**

This plugin adds a new class, `FM_Widget`, which extends `WP_Widget`. To add a new widget using Fieldmanager to build the fields, you create a new class [as you would normally](https://codex.wordpress.org/Widgets_API#Developing_Widgets), but extend `FM_Widget` instead of `WP_Widget`. Do not add `form()` and `update()` methods, `FM_Widget` takes care of both. Instead, add a single method `fieldmanager_children()` to define your fields (these will be added as children of a parent group). Add your `__construct()` and `widget()` methods as normal for `WP_Widget`, and you're good to go! Here's a sample class:

```php
<?php

if ( class_exists( '\FM_Widget' ) ) {

	/**
	 * Demo Widget.
	 */
	class \FM_Demo extends \FM_Widget {

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'fm-demo', // Base ID
				__( 'Widget Title', 'text-domain' ), // Name
				[ 'description' => __( 'This is a description of the widget', 'text-domain' ) ] // Widget arguments
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			// Output the widget on the frontend
		}

		/**
		 * Define the fields that should appear in the widget.
		 *
		 * @return array Fieldmanager fields.
		 */
		protected function fieldmanager_children() {
			return [
				'media' => new \Fieldmanager_Media( __( 'Image', 'text-domain' ) ),
				'autocomplete' => new \Fieldmanager_Autocomplete( [
					'label' => __( 'Autocomplete', 'text-domain' ),
					'datasource' => new \Fieldmanager_Datasource_Post,
				] ),
				'repeatable' => new \Fieldmanager_TextField( [
					'label' => __( 'Repeatable', 'text-domain' ),
					'limit' => 0,
				] ),
			];
		}
	}

	/**
	 * Register our new widget.
	 */
	add_action( 'widgets_init', function() {
		register_widget( '\FM_Demo' );
	} );

}
```
