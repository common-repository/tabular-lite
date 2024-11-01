<?php

/**
 * Tabular builder class.
 *
 * Utility class to help build the table HTML.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_Builder {

	public function __construct() {
		add_action( 'wp_ajax_tabular_build_table', array( &$this, 'tabular_build_table' ) );
	}

	// Process the Ajax request to build table
	function tabular_build_table() {

		if ( ! isset( $_POST['tabular_nonce'] ) || ! wp_verify_nonce( $_POST['tabular_nonce'], 'tabular_admin_nonce' ) ) {
			die( 'You don\'t have permission to access the Ajax on this page!' );
		}

		$post_id = isset( $_POST['post_id'] ) ? sanitize_key( $_POST['post_id'] ) : 0;
		$table_template = isset( $_POST['table_template'] ) ? $_POST['table_template'] : array();

		// each element of this nested array is sanitized in render_table()
		$table_data = isset( $_POST['table_data'] ) ? $_POST['table_data'] : array();

		//echo "<pre>";
		//print_r($table_data);
		//echo "</pre>";

		// Render the table
		echo self::render_table($post_id, $table_data, $table_template, true);
		die();
	}

	/**
	 * Build table HTML.
	 *
	 * @since 0.1.0
	 */
	public static function render_table($id, $data, $template, $preview = false) {

		// @todo remember, for templates that use <tfoot> needs to be before <tbody> in the markup

		$templateArr = WPGO_Tabular_Templates::$_templates[$template];
		$tdescription = $templateArr['description'];
		$theadRows = $templateArr['theadRows'];
		$tfootRows = $templateArr['tfootRows'];
		$endRow = count($data);

		//echo 'dessy: ' . $tdescription . '<br>';
		//echo 'endRow: ' . $endRow . '<br>';
		//echo 'theadRows: ' . $theadRows . '<br>';
		//echo "<pre>";
		//print_r($data);
		//print_r(WPGO_Tabular_Templates::$_templates[$template]);
		//echo "</pre>";

		// turn on output buffering
		ob_start();

		if( is_array($data) ) {

			if($preview) echo "<div style='margin: 10px 5px 5px 5px;'>";
			echo "<table class='tabular " . $template . "-template'>\n";

			// Render table header
			if( $theadRows != 0 ) {
				echo "\t<thead>\n";
				for ($rows = 0; $rows < $theadRows; $rows++) {
					echo "\t\t<tr>\n";
					foreach( $data[$rows] as $cell ) {
						echo "\t\t\t<th>" . wp_kses( $cell, array() ) . "</th>\n";
					}
					echo "\t\t</tr>\n";
				}
				echo "\t</thead>\n";
			}

			// Render table footer
			if( $tfootRows != 0 ) {
				echo "\t<tfoot>\n";
				for ( $rows = $theadRows; $rows < ( $theadRows + $tfootRows ); $rows ++ ) {
					echo "\t\t<tr>\n";
					foreach ( $data[ $rows ] as $cell ) {
						echo "\t\t\t<td>" . wp_kses( $cell, array() ) . "</td>\n";
					}
					echo "\t\t</tr>\n";
				}
				echo "\t</tfoot>\n";
			}

			// Render table body
			echo "\t<tbody>\n";
			for ( $rows = ($theadRows + $tfootRows); $rows < $endRow; $rows ++ ) {
				echo "\t\t<tr>\n";
				foreach ( $data[ $rows ] as $cell ) {
					echo "\t\t\t<td>" . wp_kses( $cell, array() ) . "</td>\n";
				}
				echo "\t\t</tr>\n";
			}
			echo "\t</tbody>\n";

			echo "</table>\n";
			if($preview) echo "</div>";

			if($preview) :
				echo "<hr style='margin:25px 0 5px;border-top: 1px solid #f1f1f1;border-bottom: none;'>";
				echo "<h4 style='display:inline-block;margin:5px 5px 2px;'>" . $templateArr['label'] . " Table: </h4>";
				echo "<p style='display:inline-block;margin:0 0 15px 0;' class='description'>" . $tdescription . "</p>";
			endif;

		} else {
			echo "<p class='description'>Error: Table not recognised.</p>";
		}

		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}
new WPGO_Tabular_Builder();