<?php

/**
 * Generic Widget for use with Fieldmanager.
 */
abstract class FM_Widget extends WP_Widget {

	protected $fm;

	protected $richtext_ids = [];

	abstract public function group_name();

	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'fm_widget', [ $this, 'get_fm' ] );
		}
	}

	protected function fieldmanager_field() {
		return new Fieldmanager_Group( [
			'name' => $this->group_name(),
			'children' => $this->fieldmanager_children(),
		] );
	}

	protected function fieldmanager_children() {
		return [];
	}

	public function get_fm() {
		if ( ! isset( $this->fm ) ) {
			$this->add_hacks();
			$this->fm = $this->fieldmanager_field();
		}
		return $this->fm;
	}

	protected function add_hacks() {
		// echo "\n\n<!-- DEBUG: {$this->get_field_id( $this->group_name() )} -->\n\n";
		add_action( 'wp_tiny_mce_init', [ $this, 'output_richtext_id_maps' ] );
	}

	public function output_richtext_id_maps() {
		$map = $this->map_richtext_ids( $this->get_fm() );
		?>
		<script type="text/javascript">
			var fm_widget_richtextareas = fm_widget_richtextareas || [];
			<?php foreach ( $map as $ids ) : ?>
				fm_widget_richtextareas.push( <?php echo wp_json_encode( $ids ) ?> );
			<?php endforeach ?>
		</script>
		<?php
	}

	public function map_richtext_ids( $fm ) {
		$map = [];
		if ( $fm instanceof Fieldmanager_RichTextArea ) {
			foreach ( $this->richtext_ids as $id ) {
				$map[] = [
					$fm->get_element_id(),
					str_replace( $this->get_fm()->get_element_id(), $id, $fm->get_element_id() )
				];
			}
		} elseif ( $fm instanceof Fieldmanager_Group ) {
			foreach ( $fm->children as $field ) {
				$map = array_merge( $map, $this->map_richtext_ids( $field ) );
			}
		}

		return $map;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $data ) {
		$fm = $this->get_fm();
		$this->richtext_ids[] = esc_attr( $this->get_field_id( $fm->name ) );

		// echo "\n\n<!-- DEBUG: {$this->get_field_id( $this->group_name() )} -->\n\n";

		add_filter( 'fm_element_markup_start', [ $this, 'fix_array_positions' ], 10, 2 );

		// The markup coming from Fieldmanager is properly escaped. We have to
		// change the field names and ids, so we can't output directly.
		$markup_escaped = $fm->element_markup( $data );
		$markup_escaped = str_replace(
			'name="' . $fm->get_form_name(),
			'name="' . esc_attr( $this->get_field_name( $fm->name ) ),
			$markup_escaped
		);
		$markup_escaped = preg_replace(
			'/(id\s*=\s*[\'"](?:wp-)?)' . preg_quote( $fm->get_element_id(), '/' ) . '/i',
			'$1' . esc_attr( $this->get_field_id( $fm->name ) ),
			$markup_escaped
		);
		echo $markup_escaped;

		// echo str_replace(
		// 	[ 'name="' . $fm->get_form_name(), 'id="' . $fm->get_element_id() ],
		// 	[ 'name="' . esc_attr( $this->get_field_name( $fm->name ) ), 'id="' . esc_attr( $this->get_field_id( $fm->name ) ) ],
		// 	$markup_escaped
		// );

		remove_filter( 'fm_element_markup_start', [ $this, 'fix_array_positions' ], 10 );
	}

	public function fix_array_positions( $out, $fm ) {
		// Check the top-most group against our group name
		$tree = $fm->get_form_tree();
		$fm = reset( $tree );

		// if the top most group is our group, replace the array positions
		if ( $this->group_name() === $fm->name ) {
			$out = preg_replace_callback(
				'/(data-fm-array-position=")([1-9]\d*)(")/',
				function( $matches ) {
					return $matches[1] . ( intval( $matches[2] ) + 2 ) . $matches[3];
				},
				$out
			);
		}

		return $out;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_value, $old_value ) {
		$fm = $this->get_fm();

		// the data is stored one level deeper than FM expects
		$new_value = ! empty( $new_value[ $fm->name ] ) ? $new_value[ $fm->name ] : [];
		$old_value = ! empty( $old_value[ $fm->name ] ) ? $old_value[ $fm->name ] : [];

		return $fm->presave_all( $new_value, $old_value );
	}

	public function enqueue_assets( $hook_suffix ) {
		if ( 'widgets.php' === $hook_suffix ) {
			wp_enqueue_script( 'fm-widget-js', FM_WIDGETS_URL . '/static/fm-widget.js', [ 'jquery', 'admin-widgets' ], '0.1' );
		}
	}
}
