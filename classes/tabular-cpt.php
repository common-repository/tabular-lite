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
class WPGO_Tabular_CPT {

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

		// Register custom endpoint to get table data:
		// e.g. http://www.yoursite.dev/wp-json/tabular/v1
		//add_action( 'rest_api_init', array( &$this, 'register_custom_endpoint' ) );

		/* Register CPT and associated taxonomy. */
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_taxonomy' ) );

		/* Customize CPT columns on overview page. */
		add_filter( 'manage_wpgo_tabular_posts_columns', array( &$this, 'change_overview_columns' ) ); /* Which columns are displayed. */
		add_action( 'manage_wpgo_tabular_posts_custom_column', array( &$this, 'custom_column_content' ), 10, 2 ); /* The html output for each column. */
		add_filter( 'manage_edit-wpgo_tabular_sortable_columns', array( &$this, 'sort_custom_columns' ) ); /* Specify which columns are sortable. */

		/* Customize the CPT messages. */
		add_filter( 'post_updated_messages', array( &$this, 'update_cpt_messages' ) );
		add_filter( 'enter_title_here', array( &$this, 'update_title_message' ) );

		// Add an ID column to Tabular group admin page
		add_action( "manage_edit-wpgo_tabular_group_columns", array( &$this, 'add_id_column' ) );
		add_filter( "manage_edit-wpgo_tabular_group_sortable_columns", array( &$this, 'add_id_column' ) );
		add_filter( "manage_wpgo_tabular_group_custom_column", array( &$this, 'show_id_column' ), 10, 3 );
		add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'style_id_column' ) );

		add_action( 'admin_bar_menu', array( &$this, 'remove_view_toolbar_link'), 999 );

		/* Add meta boxes to tabular custom post type. */
		add_action( 'admin_init', array( &$this, 'tabular_cpt_meta_boxes_init' ) );

		register_activation_hook( $this->module_roots['__FILE__'], array( &$this, 'flush_rewrites' ) );

		/* Add dropdown filter on wpgo_tabular CPT edit.php to sort by taxonomy. */
		// These work OK but until I can figure out how to get the default taxonomy term to be associated
		// automatically with new CPT items then I will leave this feature out as the show all option doesn't
		// work properly.
		// add_action( 'restrict_manage_posts', array( &$this, 'taxonomy_filter_restrict_manage_posts' ) );
		// add_filter( 'parse_query', array( &$this, 'taxonomy_filter_post_type_request' ) );
	}

	public function flush_rewrites() {
		// call CPT/taxonomy registration functions here (it should also be hooked into 'init')
		$this->register_post_type();
		$this->register_taxonomy();
		flush_rewrite_rules();
	}

	// Register custom endpoint to get table data
	public function register_custom_endpoint() {

		register_rest_route(
			'tabular/v1',
			'/tables',
			[
				'methods' => 'GET',
				'callback' => array( $this, 'add_custom_endpoint' )
			]
		);
	}

	// Add custom endpoint to get table data
	public function add_custom_endpoint($data) {

		$args = [
			'numberposts' => -1,
			'post_type' => 'wpgo_tabular'
		];
		$posts = get_posts( $args );

		if( empty( $posts ) ) {
			return null;
		}

		return $posts;
	}

	// Remove the 'View Table' admin toolbar link
	public function remove_view_toolbar_link( $wp_admin_bar ) {

		global $post;

		if(isset($post)) {
			// @todo: this also seems to fire when on the Tabular main CPT index page in the admin which we don't really want
			if($post->post_type == 'wpgo_tabular') {
				$wp_admin_bar->remove_node( 'view' );
			}
		}
	}

	public function add_id_column( $columns ) {
		return $columns + array ( 'tax_id' => 'ID' );
	}

	public function style_id_column() {
		echo "<style>#tax_id{width:4em}</style>";
	}

	public function show_id_column( $v, $name, $id ) {
		return 'tax_id' === $name ? $id : $v;
	}

	/**
	 * Register Tabular post type.
	 *
	 * @since 0.1.0
	 */
	public function register_post_type() {

		/* Post type arguments. */
		$args = array(
			'public'              => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-editor-table',
			'supports'            => array(
				'title', 'author' //, 'thumbnail'
			),
			'labels'              => array(
				'name'               => __( 'Tabular Lite', 'tabular' ),
				'all_items'          => __( 'All Tables', 'tabular' ),
				'singular_name'      => __( 'Table', 'tabular' ),
				'add_new'            => __( 'Add New Table', 'tabular' ),
				'add_new_item'       => __( 'Add New Table', 'tabular' ),
				'edit_item'          => __( 'Edit Table', 'tabular' ),
				'new_item'           => __( 'New Table', 'tabular' ),
				'view_item'          => __( 'View Table', 'tabular' ),
				'search_items'       => __( 'Search Tables', 'tabular' ),
				'not_found'          => __( 'No Table Found', 'tabular' ),
				'not_found_in_trash' => __( 'No Table Found In Trash', 'tabular' ),
				'attributes'         => __( 'Table Attributes', 'tabular' ),
			)
		);

		/* Register post type. */
		register_post_type( 'wpgo_tabular', $args );
	}

	/**
	 * Register Tabular taxonomy.
	 *
	 * @since 0.1.0
	 */
	public function register_taxonomy() {

		/* Tabular taxonomy arguments. */
		$args = array(
			'hierarchical'  => true,
			'query_var'     => true,
			'show_tagcloud' => false,
			'sort'          => true,
			'rewrite'       => false,
			'labels'        => array(
				'name'              => __( 'Table Groups', 'tabular' ),
				'singular_name'     => __( 'Table Group', 'tabular' ),
				'edit_item'         => __( 'Edit Table Group', 'tabular' ),
				'update_item'       => __( 'Update Table Group', 'tabular' ),
				'add_new_item'      => __( 'Add New Group', 'tabular' ),
				'new_item_name'     => __( 'New Table Name', 'tabular' ),
				'all_items'         => __( 'All Tables', 'tabular' ),
				'search_items'      => __( 'Search Tables', 'tabular' ),
				'parent_item'       => __( 'Parent Table', 'tabular' ),
				'parent_item_colon' => __( 'Parent Table:', 'tabular' )
			)
		);

		/* Register the tabular taxonomy. */
		//register_taxonomy( 'wpgo_tabular_group', array( 'wpgo_tabular' ), $args );
	}

	/**
	 * Change the columns on the custom post types overview page.
	 *
	 * @since 0.1.0
	 */
	public function change_overview_columns( $cols ) {

		$cols = array(
			'cb'            => '<input type="checkbox">',
			'title'         => __( 'Table Name', 'tabular' ),
			//'image'         => __( 'Image', 'tabular' ),
			//'group'         => __( 'Group', 'tabular' ),
			'type'         => __( 'Table Type', 'tabular' ),
			'id'            => __( 'Table ID', 'tabular' ),
			'date'          => __( 'Date Created', 'tabular' )
		);

		return $cols;
	}

	/**
	 * Add some content to the custom columns from the custom post type.
	 *
	 * @since 0.1.0
	 */
	public function custom_column_content( $column, $post_id ) {

		switch ( $column ) {
			case "title":
				echo 'title';
				break;
			//case "image":
				/* If no featured image set, use gravatar if specified. */
			/*	if ( ! ( $image = get_the_post_thumbnail( $post_id, array( 32, 32 ) ) ) ) {
					$image = get_post_meta( $post_id, '_wpgo_tabular_cpt_image', true );
					if ( trim( $image ) == '' ) {
						$image = '<em>' . __( 'No image', 'tabular' ) . '</em>';
					} else {
						$image = get_avatar( $image, $size = '32' );
					}
				}
				echo $image;
				break;*/
			/*case "group":
				$taxonomy  = 'wpgo_tabular_group';
				$post_type = get_post_type( $post_id );
				$terms     = get_the_terms( $post_id, $taxonomy );
/*
				/* get_the_terms() only returns an array on success so need check for valid array. */
/*				if ( is_array( $terms ) ) {
					$str = "";
					foreach ( $terms as $term ) {
						$str .= "<a href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " . esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'group', 'edit' ) ) . "</a>, ";
					}
					echo rtrim( $str, ", " );
				} else {
					echo '<em>' . __( 'Not in any groups', 'tabular' ) . '</em>';
				}
				break;*/
			case "type":
				$tabular_cpt_type = get_post_meta( $post_id, '_wpgo_tabular_cpt_type', true );
				echo $tabular_cpt_type;
				break;
			case "id":
				echo $post_id;
				break;
		}
	}

	/**
	 * Make custom columns sortable.
	 *
	 * @since 0.1.0
	 */
	function sort_custom_columns() {

		return array(
			'title'   => 'title',
			//'type' => 'type',
			'date'    => 'date',
			'id'      => 'id'
		);
	}

	/**
	 * Initialise custom post type meta boxes.
	 *
	 * @since 0.1.0
	 */
	public function tabular_cpt_meta_boxes_init() {

		/* Add meta boxes to Tabular CPT editor. */

		add_meta_box( 'wpgo-tabular-preview', __( 'Table Preview', 'tabular' ), array( &$this, 'meta_box_preview' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-type', __( 'Select Table Type...', 'tabular' ), array( &$this, 'meta_box_table_type' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-template', __( 'Choose Table Style', 'tabular' ), array( &$this, 'meta_box_templates' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-js', __( 'Choose Table Code (JavaScript)', 'tabular' ), array( &$this, 'meta_box_js' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-config-js', __( 'Table Data', 'tabular' ), array( &$this, 'meta_box_config_js' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-css', __( 'Table Styles', 'tabular' ), array( &$this, 'meta_box_css' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-html', __( 'Table Markup (HTML)', 'tabular' ), array( &$this, 'meta_box_html' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-data', __( 'Edit Table Data', 'tabular' ), array( &$this, 'meta_box_data' ), 'wpgo_tabular', 'normal', 'high' );

		add_meta_box( 'wpgo-tabular-cpt_sc', __( 'Table Shortcode', 'tabular' ), array( &$this, 'meta_box_shortcode' ), 'wpgo_tabular', 'side', 'high' );

		/* Hook to save our meta box data when the post is saved. */
		add_action( 'save_post', array( &$this, 'save_meta_box_data' ) );
	}

	/**
	 * Display the Tabular table types meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_table_type( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_type = get_post_meta( $post->ID, '_wpgo_tabular_cpt_type', true );

		//$table_types = ['column', 'grouped_column', 'bar', 'pie', 'donut', 'scatter', 'candlestick', 'multi', 'stacked', 'grouped'];
		// @todo make this flat array similar to the template array to include a label
		$table_types = ['static', 'dynamic'];
		$default = 'static';
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<div id="pc-type-container">
					<?php foreach($table_types as $table_type) { ?>
					<?php
						// remove this (and related code below) when dynamic table working
						$disabled = $table_type == 'dynamic' ? 'disabled ' : '' ;
						$disabledtxt = !empty($disabled) ? ' <em>(coming soon)</em>' : '';
						//echo "[" . $disabled . "]";
					?>
					<div>
					<?php $default_checked = ($table_type == $default) ? 'checked="checked"' : ''; ?>
						<label for="wpgo_tabular_cpt_type_<?php echo $table_type; ?>">
							<input <?php echo $disabled; ?><?php echo $default_checked; ?> type="radio" name="wpgo_tabular_cpt_type" id="wpgo_tabular_cpt_type_<?php echo $table_type; ?>" value="<?php echo $table_type; ?>" <?php if ( isset ( $tabular_cpt_type ) ) checked( $tabular_cpt_type, $table_type ); ?>>
							<div class="pc-type-img"><img src="<?php echo $this->module_roots[uri] . '/images/thumbnails/' . $table_type . '.jpg'; ?>" alt="<?php echo ucfirst($table_type); ?>" /></div>
							<div class="pc-type-txt"><?php echo ucfirst($table_type) . $disabledtxt; ?></div>
						</label>
					</div>
					<?php } ?>
					</div>
					<div id="pc-create-table"></div>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display the Tabular templates meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_templates( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_type = get_post_meta( $post->ID, '_wpgo_tabular_template', true );
		$templates = WPGO_Tabular_Templates::$_templates;
		$template_keys = array_keys($templates);
		$default = 'base';
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<p class="decription">Can't find the table template you're looking for? Click <a href="https://wpgoplugins.com/contact-us/" target="_blank">here</a> to suggest a new template.</p>
					<div id="tabular-template-container">
						<?php foreach($template_keys as $template) { ?>
							<?php $imgPath = $this->module_roots[uri] . '/images/thumbnails/'; ?>
							<?php $imgURL = $imgPath . WPGO_Tabular_Templates::$_templates[$template]['thumb']; ?>
							<div>
								<?php $default_checked = ($template == $default) ? 'checked="checked"' : ''; ?>
								<label for="wpgo_tabular_template_<?php echo $template; ?>">
									<input <?php echo $default_checked; ?> type="radio" name="wpgo_tabular_template" id="wpgo_tabular_template_<?php echo $template; ?>" value="<?php echo $template; ?>" <?php if ( isset ( $tabular_cpt_type ) ) checked( $tabular_cpt_type, $template ); ?>>
									<div class="pc-type-img"><img title="<?php echo $templates[$template]['description']; ?>" src="<?php echo $imgURL; ?>" alt="<?php echo $templates[$template]['label']; ?>" /></div>
									<div class="pc-type-txt"><?php echo $templates[$template]['label']; ?></div>
								</label>
							</div>
						<?php } ?>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display the Tabular JavaScript meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_js( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_js =       get_post_meta( $post->ID, '_wpgo_tabular_cpt_js', true );
		$tabular_cpt_image =        get_post_meta( $post->ID, '_wpgo_tabular_cpt_image', true );
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<textarea id="wpgo_tabular_cpt_js" rows="15" style="width:100%;" name="wpgo_tabular_cpt_js" readonly><?php echo esc_attr( $tabular_cpt_js ); ?></textarea>
				</td>
			</tr>
			<tr style="display:none;">
				<td width="100"><?php _e( 'Gravatar E-mail', 'tabular' ); ?></td>
				<td>
					<input style="width:100%;" type="text" name="wpgo_tabular_cpt_image" value="<?php echo esc_attr( $tabular_cpt_image ); ?>">
				</td>
			</tr>
			<tr style="display:none;">
				<td>&nbsp;</td>
				<td>
					<p class="description"><?php printf( __( 'To upload an image, use the Tabular Image feature to the right (recommended %1$d x %2$d pixels), or enter a Gravatar e-mail above. Leave field blank to NOT show an image.', 'tabular' ), $w, $w ); ?></p>
				</td>
			</tr>
			</tbody>
		</table>
	<?php
	}

	/**
	 * Display the Tabular Config JavaScript meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_config_js( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_config_js = get_post_meta( $post->ID, '_wpgo_tabular_cpt_config_js', true );

		/*$arr = '[
		["", "Ford", "Tesla", "Toyota", "Honda"],
		["2017", 10, 11, 12, 13],
		["2018", 20, 11, 14, 13],
		["2019", 30, 15, 12, 13]
		]';
		$array = json_decode($arr);*/

		//echo "<pre>";
		//print_r($array);
		//echo "</pre>"
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<textarea id="wpgo_tabular_cpt_config_js" rows="15" style="width:100%;" name="wpgo_tabular_cpt_config_js" readonly><?php echo esc_attr( $tabular_cpt_config_js ); ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display the Tabular CSS meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_css( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_css = get_post_meta( $post->ID, '_wpgo_tabular_cpt_css', true );
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<textarea id="wpgo_tabular_cpt_css" rows="15" style="width:100%;" name="wpgo_tabular_cpt_css" readonly><?php echo esc_attr( $tabular_cpt_css ); ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display the Tabular HTML meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_html( $post, $args ) {

		/* Retrieve our custom meta box values */
		$tabular_cpt_html = get_post_meta( $post->ID, '_wpgo_tabular_cpt_html', true );
		?>

		<table width="100%">
			<tbody>
			<tr>
				<td colspan="2">
					<textarea rows="15" style="width:100%;" name="wpgo_tabular_cpt_html"><?php echo esc_attr( $tabular_cpt_html ); ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Display the Tabular data meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_data( $post, $args ) {

		/* Retrieve our custom meta box values */
		//$tabular_cpt_data = get_post_meta( $post->ID, '_wpgo_tabular_cpt_data', true );
		?>

		<div id="wpgo-tabular-edit"></div>
		<?php
	}

	/**
	 * Display the Tabular preview meta box.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_preview( $post, $args ) {

		// @todo don't render preview meta box or build preview table if the table type is dynamic

		$data = json_decode(get_post_meta( $post->ID, '_wpgo_tabular_cpt_config_js', true ));
		$template = get_post_meta( $post->ID, '_wpgo_tabular_template', true );

		// render table wrapper element
		echo WPGO_Tabular_Builder::render_table($post->ID, $data, $template, true);
	}

	/**
	 * Save the custom post type meta box input field settings.
	 *
	 * @since 0.1.0
	 */
	public function save_meta_box_data( $post_id ) {

		global $typenow;

		/* Only work for specific post type */
		if ( $typenow != 'wpgo_tabular' ) {
			return;
		}

		/* Save the meta box data as post meta, using the post ID as a unique prefix. */

		if( isset( $_POST[ 'wpgo_tabular_cpt_type' ] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_type', sanitize_text_field( $_POST[ 'wpgo_tabular_cpt_type' ] ) );
		}

		if( isset( $_POST[ 'wpgo_tabular_template' ] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_template', sanitize_text_field( $_POST[ 'wpgo_tabular_template' ] ) );
		}

		/*if ( isset( $_POST['wpgo_tabular_cpt_js'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_js', ( $_POST['wpgo_tabular_cpt_js'] ) );
		}*/

		if ( isset( $_POST['wpgo_tabular_cpt_config_js'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_config_js', sanitize_text_field( $_POST['wpgo_tabular_cpt_config_js'] ) );
		}

		/*if ( isset( $_POST['wpgo_tabular_cpt_css'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_css', ( $_POST['wpgo_tabular_cpt_css'] ) );
		}

		if ( isset( $_POST['wpgo_tabular_cpt_html'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_html', ( $_POST['wpgo_tabular_cpt_html'] ) );
		}*/

		//if ( isset( $_POST['wpgo_tabular_cpt_data'] ) ) {
		//	update_post_meta( $post_id, '_wpgo_tabular_cpt_data', esc_attr( $_POST['wpgo_tabular_cpt_data'] ) );
		//}

		/*if ( isset( $_POST['wpgo_tabular_cpt_company'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_company', sanitize_text_field( $_POST['wpgo_tabular_cpt_company'] ) );
		}

		if ( isset( $_POST['wpgo_tabular_cpt_company_url'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_company_url', sanitize_text_field( $_POST['wpgo_tabular_cpt_company_url'] ) );
		}*/

		if ( isset( $_POST['wpgo_tabular_cpt_image'] ) ) {
			update_post_meta( $post_id, '_wpgo_tabular_cpt_image', sanitize_text_field( $_POST['wpgo_tabular_cpt_image'] ) );
		}
	}

	/**
	 * Display meta box to show shortcode for the current tabular.
	 *
	 * @since 0.1.0
	 */
	public function meta_box_shortcode( $post, $args ) {

		$id = $post->ID;

		//$pc_terms = get_the_terms( $id, 'wpgo_tabular_group' );
		$description = __( 'Copy the shortcode above and paste into any post, or page to display the table.', 'tabular' );
		//$group_pc_html = '';

		/*if( !empty($pc_terms) ) {
			$group_sc = '';
			$pc_description = __( 'Copy and paste ONE of the shortcodes above into any post, or page, to display the table, or group of tables.', 'tabular' );

			foreach($pc_terms as $pc_term) {
				$group_sc .= "[pc group='{$pc_term->term_id}'] ";
			}
			$group_sc = trim($group_sc); // trim trailing space
			$group_sc = trim($group_sc); // trim trailing space

			if( count($pc_terms) > 1 ) {
				$group_label = "Group tabular shortcodes";
			} elseif( count($pc_terms) == 1 ) {
				$group_label = "Group tabular shortcode";
			}

			$group_pc_html = '<tr><td>
				<h4 style="margin: 5px 0;">'.$group_label.'</h4>
				<input style="width:100%;font-family: Courier New;" type="text" readonly name="wpgo_group_tabular_cpt_sc" value="'.$group_sc.'">
			</td></tr>';
		}*/

		$single_sc = "[tabular id='{$id}']";
		?>
		<table width="100%">
			<tbody>
			<tr>
				<td>
					<input style="width:100%;font-family: Courier New;" type="text" readonly name="wpgo_single_tabular_cpt_sc" value="<?php echo $single_sc; ?>">
				</td>
			</tr>
			<?php // echo $group_pc_html; ?>
			<tr>
				<td >
					<p style="margin-top: 7px;" class="description"><?php echo $description; ?></p>
				</td>
			</tr>
			</tbody>
		</table>
	<?php
	}

	/**
	 *
	 *
	 * @since 0.1.0
	 */
	public function update_cpt_messages( $messages ) {
		global $post, $post_ID;

		$messages['wpgo_tabular'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Table updated.', 'tabular' ), esc_url( get_permalink( $post_ID ) ) ),
			2  => __( 'Custom field updated.', 'tabular' ),
			3  => __( 'Custom field deleted.', 'tabular' ),
			4  => __( 'Table updated.', 'tabular' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Table restored to revision from %s', 'tabular' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Table published.', 'tabular' ), esc_url( get_permalink( $post_ID ) ) ),
			7  => __( 'Table saved.', 'tabular' ),
			8  => sprintf( __( 'Table submitted.', 'tabular' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9  => sprintf( __( 'Table scheduled for: %1$s.', 'tabular' ),
				// translators: Publish box date format, see http://php.net/date
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'tabular' ), strtotime( $post->post_date ) ) . '</strong>', esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Table draft updated.', 'tabular' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

	/**
	 * Update the title edit prompt message shown when editing a new table.
	 *
	 * @since 0.1.0
	 */
	public function update_title_message( $message ) {
		global $post;

		$pt = get_post_type( $post );
		if ( $pt == 'wpgo_tabular' ) {
			$message = __( 'Enter table title here', 'tabular' );
		}

		return $message;
	}

	/**
	 * Filter the request to just give posts for the given taxonomy.
	 *
	 * @since 0.1.0
	 */
	public function taxonomy_filter_restrict_manage_posts() {
		global $typenow;

		/* Only work for specific post type */
		if ( $typenow != 'wpgo_tabular' ) {
			return;
		}

		$post_types = get_post_types( array( '_builtin' => false ) );

		if ( in_array( $typenow, $post_types ) ) {
			$filters = get_object_taxonomies( $typenow );

			foreach ( $filters as $tax_slug ) {
				if ( ! isset( $_GET[$tax_slug] ) ) {
					$selected = '';
				} else {
					$selected = $_GET[$tax_slug];
				}

				$tax_obj = get_taxonomy( $tax_slug );
				wp_dropdown_categories( array(
					'taxonomy'     => $tax_slug,
					'name'         => $tax_obj->name,
					'orderby'      => 'name',
					'selected'     => $selected,
					'hierarchical' => $tax_obj->hierarchical,
					'show_count'   => true,
					'hide_empty'   => true
				) );
			}
		}
	}

	/**
	 * Add a filter to the query so the dropdown will work.
	 *
	 * @since 0.1.0
	 */
	public function taxonomy_filter_post_type_request( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' == $pagenow ) {
			$filters = get_object_taxonomies( $typenow );
			foreach ( $filters as $tax_slug ) {
				$var = & $query->query_vars[$tax_slug];
				if ( isset( $var ) ) {
					$term = get_term_by( 'id', $var, $tax_slug );
					$var  = $term->slug;
				}
			}
		}
	}
}