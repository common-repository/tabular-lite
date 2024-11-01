<?php

/**
 * Tabular shortcodes class.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_Shortcodes {

	// @todo load the other PC shortcode CSS file if shortcode used on the page. ATM it is always loaded!
	private $_success;
	//private $_d3_cdn = 'https://d3js.org/d3.v4.js';
	protected $module_roots;

	/**
	 * Registers the framework shortcodes, and allows them to be used in widgets.
	 *
	 * @since 0.1.0
	 */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;

	    // Initialize class properties.
		$this->_success = array();

		// Register [pc] shortcode
		add_shortcode( 'tabular', array( &$this, 'tabular_shortcode' ) );
	}

	/**
	 * Tabular shortcode function.
	 *
	 * Example usage: [tabular id="123"]
	 *
	 * Where id is a single tabular post id.
	 *
	 */
	public function tabular_shortcode( $atts ) {

		/* Get tabular attributes from the shortcode. */
		extract( shortcode_atts( array(
			'id'    => '',
			/*'group' => '',
			'num'   => '-1',
			'rnd'   => false,
			'no_excerpt' => '0',
			'no_company' => '0',
			'no_name' => '0',
			'no_image' => '0',
			'no_link' => '0',
			'render' => '',
			'template' => '',*/
		), $atts ) );

		if( empty($atts['id']) ) {
			return "<p>Error: Please specify a valid Tabular post ID.</p>";
		}

		$data = json_decode(get_post_meta( $atts['id'], '_wpgo_tabular_cpt_config_js', true ));
		$template = get_post_meta( $atts['id'], '_wpgo_tabular_template', true );

		//$data = get_post_meta( $atts['id'], '_wpgo_tabular_cpt_data', true );
		//$table_fixed_js = get_post_meta( $id, '_wpgo_tabular_cpt_js', true );
		//$table_config_js = get_post_meta( $id, '_wpgo_tabular_cpt_config_js', true );

		//$css = get_post_meta( $id, '_wpgo_tabular_cpt_css', true );
		//$html = 'RENDER TABLE (' . $atts['id'] . ')';
		//$table_js = "(function (){" . $table_config_js . $table_fixed_js;

		//echo "<pre>";
		//echo $table_js;
		//echo "</pre>";

		// Only add table scripts/styles to pages shortcode is used on
		//wp_enqueue_script( 'wpgo-d3', $this->module_roots['uri'] . '/js/pcd3.js' , array(), '', true );
		//wp_localize_script( 'wpgo-d3', 'pc_data_' . $id, $data );
		//wp_add_inline_script( 'wpgo-d3', $table_js );

		wp_enqueue_style( 'wpgo-tabular', $this->module_roots['uri'] . '/css/tabular-templates.css' );
		//wp_add_inline_style( 'wpgo-tabular', $css );

		return WPGO_Tabular_Builder::render_table($atts['id'], $data, $template);
		//return html_entity_decode($html);
	}
}