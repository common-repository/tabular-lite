<?php

/**
 * Tabular custom post type.
 *
 * This class registers the tabular post type and the taxonomy for associated groups.
 *
 * Class name suffix _CPT stands for [C]ustom_[P]ost_[T]ype.
 *
 * @since 0.1.0
 */
class WPGO_Tabular_Enqueue_Scripts {

	protected $module_roots;

	/**
	 * Tabular class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;

		// Front end scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

		// Admin scripts
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts'), 10, 1 );
	}

	/**
	 * Register front end scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		// @todo delete this function or leave a note to say front end scripts/styles are added in the shortcode function?

		// @todo Will need to add more scripts to render the table on the front end (add d3 table JS as inline code, as well as inline CSS?)
		// @todo Also, need to ONLY enqueue these on pages/posts/widgets that use the table shortcode.
		//wp_enqueue_style( 'wpgo-tabular', $this->module_roots['uri'] . '/css/tabular.css' );
		//wp_enqueue_script( 'wpgo-d3', $this->module_roots['uri'] . '/js/pcd3.js' , array(), '', true );
	}

	/**
	 * Register admin only scripts.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_admin_scripts( $hook ) {

		global $post;

		$id = $post->ID;
		//$js = get_post_meta( $id, '_wpgo_tabular_cpt_js', true );

		// add table css inline
		//$css = get_post_meta( $id, '_wpgo_tabular_cpt_css', true );

		//$html = get_post_meta( $id, '_wpgo_tabular_cpt_html', true );
		//$data = get_post_meta( $id, '_wpgo_tabular_cpt_data', true );

		if ( 'wpgo_tabular' === $post->post_type ) {

			// Scripts for existing tables only
			if ( $hook == 'post.php' ) {

				// STYLES

				// add this to the front end only?
				//wp_enqueue_style( 'wpgo-tabular', $this->module_roots['uri'] . '/css/tabular.css' );

				wp_enqueue_style(
					'wpgo-tabular-hot-admin-css',
					$this->module_roots['uri'] . '/js/handsontables/handsontable.full.min.css'
				);

				wp_enqueue_style(
					'wpgo-tabular-admin',
					$this->module_roots['uri'] . '/css/tabular-admin.css'
				);

				wp_enqueue_style(
					'wpgo-tabular-templates',
					$this->module_roots['uri'] . '/css/tabular-templates.css'
				);

				/*wp_add_inline_style(
					'wpgo-tabular-admin',
					$css
				);*/

				// SCRIPTS

				wp_enqueue_script(
					'wpgo-tabular-hot-admin-js',
					$this->module_roots['uri'] . '/js/handsontables/handsontable.full.min.js',
					array('jquery'),
					'',
					true
				);

				/*wp_enqueue_script(
					'wpgo-d3-generic-table-builder',
					$this->module_roots['uri'] . '/js/table-builders/generic-table-builder.js',
					array( 'wpgo-tabular-admin-utility' ),
					'',
					true
				);*/

				// Load in table builder code for selected table type. For new tables this will be loaded via tabular-new.js.
				// @todo delete this?
				/*$tableType = get_post_meta( $id, '_wpgo_tabular_cpt_type' );
				if ( ! empty( $tableType ) ) {
					wp_enqueue_script(
						'wpgo-d3-table-builder',
						$this->module_roots['uri'] . '/js/table-builders/pc-' . $tableType[0] . '-table-builder.js',
						array( 'wpgo-d3-generic-table-builder' ),
						'',
						true
					);
				}*/

				wp_enqueue_script(
					'wpgo-d3-admin',
					$this->module_roots['uri'] . '/js/tabular-admin.js',
					array( 'wpgo-tabular-admin-utility' ),
					'',
					true
				);

				wp_localize_script( 'wpgo-d3-admin', 'tabular_ajax_vars', array(
					'tabular_admin_nonce' => wp_create_nonce( 'tabular_admin_nonce' )
				) );
			}

			// Scripts for new tables only
			if ( $hook == 'post-new.php' ) {
				wp_enqueue_style(
					'wpgo-tabular-admin',
					$this->module_roots['uri'] . '/css/tabular-admin-new.css'
				);

				wp_enqueue_script(
					'wpgo-d3-sample-data',
					$this->module_roots['uri'] . '/js/tabular-sample-data.js',
					array( 'wpgo-tabular-admin-utility' ),
					'',
					true
				);
				wp_enqueue_script(
					'wpgo-d3-admin-new',
					$this->module_roots['uri'] . '/js/tabular-admin-new.js',
					array( 'wpgo-d3-sample-data' ),
					'',
					true
				);
			}

			// Scripts for new AND existing tables
			if ( $hook == 'post.php' || $hook == 'post-new.php' ) {

				/*wp_enqueue_script(
					'wpgo-d3',
					$this->module_roots['uri'] . '/js/pcd3.js',
					array(),
					'',
					true
				);*/

				wp_enqueue_script(
					'wpgo-tabular-admin-utility',
					$this->module_roots['uri'] . '/js/tabular-admin-utility.js',
					array(
						'jquery'
					),
					'',
					true
				);

				// RGBA color picker
				wp_enqueue_script(
					'wpgo-pc-alpha-color-picker-js',
					$this->module_roots['uri'] . '/js/alpha-color-picker/alpha-color-picker.js',
					array( 'jquery', 'wp-color-picker' ),
					null,
					true
				);
				wp_enqueue_style(
					'wpgo-pc-alpha-color-picker-css',
					$this->module_roots['uri'] . '/js/alpha-color-picker/alpha-color-picker.css',
					array( 'wp-color-picker' )
				);
			}
		}
	}
}