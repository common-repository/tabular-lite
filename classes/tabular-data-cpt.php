<?php

/**
 * Tabular data custom post type.
 *
 * This class registers the tabular data post type to store table data only.
 *
 * Class name suffix _CPT stands for [C]ustom_[P]ost_[T]ype.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_Data_CPT {

	protected $module_roots;

	/**
	 * Tabular class constructor.
	 *
	 * Contains hooks that point to class methods to initialise the custom post type etc.
	 *
	 * @since 0.1.0
	 */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;
	}

	// Register custom route and endpoints to get table data
	public function register_custom_route() {

		register_rest_route( 'wpgo-tabular/v1', '/tables', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'add_tables_endpoint' )
		) );

		register_rest_route( 'wpgo-tabular/v1', '/tables/(?P<id>\d+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'add_table_endpoint' )
		) );

		register_rest_route( 'wpgo-tabular/v1', '/table-data/(?P<id>\d+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'add_table_data_endpoint' )
		) );

		register_rest_route( 'wpgo-tabular/v1', '/sample-table-data/bar-table', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => array( $this, 'sample_bar_table_data_endpoint' )
		) );
	}

	// @todo add all sample data to separate class. or just add these callbacks to the class
	// Add custom endpoint to get table data
	public function sample_bar_table_data_endpoint($request) {

		//$file = 'report.csv';
		//header( "Content-Type: ;charset=utf-8" );
		//header( "Content-Disposition: attachment;filename=\"$file\"" );
		//header( "Pragma: no-cache" );
		//header( "Expires: 0" );

		//$csv = fopen('php://output', 'w');

		$csv_meta = trim(get_post_meta('5653', '_wpgo_tabular_cpt_data', true));
		return $csv_meta;
		//print_r($csv_meta);

		//$arr = explode('\r\n', $csv_meta);
		//print_r($arr);
		//exit();
		//$txt = "";
		//foreach($arr as $ar) {
		//	$ar = trim($ar, "\r\n");

		//	$txt = $txt . $ar;
		//}

		//$csv = $csv_meta;

		// Download it
		//fclose( $csv );
		//exit();


		//print_r($text);
		//$json = json_encode($csv_meta);
		//$csv_meta_arr = explode('\r\n', $csv_meta);

		//ob_start();

		//foreach($csv_meta_arr as $meta) {
		//	echo $meta;
		//}

		//$csv = ob_get_contents();

		//ob_end_clean();
		//echo "Hello";
		//exit();
		//return $txt;
	}

	// Add custom endpoint to get table data
	public function add_table_data_endpoint($request) {

		// Here we are grabbing the 'id' path variable from the $request object. WP_REST_Request implements ArrayAccess, which allows us to grab properties as though it is an array.
		$id = (string) $request['id'];

		$post_meta = get_post_meta( $id, '_wpgo_tabular_cpt_data', true );

		if( empty( $post_meta ) ) {
			return new WP_Error( 'rest_table_data_invalid', 'The table data does not exist.', array( 'status' => 404 ) );
		}

		return rest_ensure_response($post_meta);
	}

	// Add custom endpoint to get table data
	public function add_table_endpoint($request) {

		// Here we are grabbing the 'id' path variable from the $request object. WP_REST_Request implements ArrayAccess, which allows us to grab properties as though it is an array.
		$id = (string) $request['id'];

		$post = get_post( $id );

		if( empty( $post ) ) {
			return new WP_Error( 'rest_table_invalid', 'The table does not exist.', array( 'status' => 404 ) );
		}

		return rest_ensure_response($post);
	}

	// Add custom endpoint to get table data
	public function add_tables_endpoint($data) {

		$args = [
			'numberposts' => -1,
			'post_type' => 'wpgo_tabular'
		];
		$posts = get_posts( $args );

		if( empty( $posts ) ) {
			return null;
		}

		return rest_ensure_response($posts);
	}
}