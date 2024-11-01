<?php

/**
 * Tabular templates class.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_Templates {

	public static $_templates = array(
		'base' =>  array(
			'label'         =>  'Base',
			'description'   =>  'Table styles inherited from currently active theme.',
			'thumb'         =>  'base.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'simple' =>  array(
			'label'         =>  'Simple',
			'description'   =>  'Basic table style. Simple but elegant.',
			'thumb'         =>  'simple.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'border' =>  array(
			'label'         =>  'Border',
			'description'   =>  'Collapsed single pixel border around each table cell.',
			'thumb'         =>  'border.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'box' =>  array(
			'label'         =>  'Box',
			'description'   =>  'Table with padded boxes around each table cell.',
			'thumb'         =>  'box.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'color-box' =>  array(
			'label'         =>  'Color Box',
			'description'   =>  'Minimal table with boxes around each table cell, plus a splash of color.',
			'thumb'         =>  'color-box.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'zebra-1' =>  array(
			'label'         =>  'Zebra Striped 1',
			'description'   =>  'Table body rows have alternating background colors.',
			'thumb'         =>  'zebra1.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'zebra-2' =>  array(
			'label'         =>  'Zebra Striped 2',
			'description'   =>  'Table body rows have alternating background colors.',
			'thumb'         =>  'zebra2.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'zebra-3' =>  array(
			'label'         =>  'Zebra Striped 3',
			'description'   =>  'Table body rows have alternating background colors.',
			'thumb'         =>  'zebra3.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'row-hover' =>  array(
			'label'         =>  'Row Hover',
			'description'   =>  'Rows are highlighted when hovered over.',
			'thumb'         =>  'row-hover.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
		'thick-box' =>  array(
			'label'         =>  'Thick Box',
			'description'   =>  'Table with a thick border around each cell.',
			'thumb'         =>  'thick-box.jpg',
			'default_data'  =>  '',
			'theadRows'     =>  1,
			'tfootRows'     =>  0
		),
	);

	/*public function __construct() {
		add_action( 'wp_ajax_tabular_build_table', array( &$this, 'tabular_build_table' ) );
	}*/

	// Process the Ajax request to build table
	/*function tabular_build_table() {

		if ( ! isset( $_POST['tabular_nonce'] ) || ! wp_verify_nonce( $_POST['tabular_nonce'], 'tabular_admin_nonce' ) ) {
			die( 'You don\'t have permission to access the Ajax on this page!' );
		}

		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		$table_data = isset( $_POST['table_data'] ) ? $_POST['table_data'] : array();
		$table_template = isset( $_POST['table_template'] ) ? $_POST['table_template'] : array();

		// Render the table
		self::render_table($post_id, $table_data, $table_template);
		die();
	}*/
}