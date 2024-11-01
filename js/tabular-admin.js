/* Tabular Admin Scripts */

jQuery(document).ready(function($) {

	var hot;

	// select the shortcode when the text field is clicked (receives focus)
	$("input[name='wpgo_single_tabular_cpt_sc']").focus(function() { this.select(); } );

	// get post ID and make it globally available as 'pcPostID'
	window.pcPostID = $("input[type='hidden']#post_ID")[0].value;

	// add spinner to preview meta box heading
	$('#wpgo-tabular-preview h2').append('<span class="spinner"></span>');
	var spinner = $('#wpgo-tabular-preview h2 span.spinner');

	// initialize color pickers
	$( 'input.alpha-color-picker' ).alphaColorPicker();

	$('input[name="wpgo_tabular_template"]').change(update);

	// Tasks for page load
	init();

	function init() {

		// load data from meta box textarea
		var table_data = $('#wpgo_tabular_cpt_config_js').text();

		// render table
		hot = new Handsontable( document.getElementById('wpgo-tabular-edit'), {
			data: JSON.parse(table_data),
			rowHeaders: true,
			colHeaders: true,
			height: 258,
			//minSpareRows: 1,
			//minSpareCols: 1,
			//editor: false,
			//fragmentSelection: true,
			//disableVisualSelection: true,
			//contextMenu: ['row_above', 'row_below', 'col_left', 'col_right', 'remove_row', 'remove_col'],
			contextMenu: true,
			afterChange: update
		});
	}

	function update(changes, source) {

		// this function seems to run on page load, which we don't want as the hot object isn't fully initialized with data yet
		if (typeof hot === "object") {

			// get updated table data
			var newData = hot.getData();
			var table_template = $('input[name=wpgo_tabular_template]:checked').val();

			// update table data in the 'Edit Table Data' meta box textarea
			$('#wpgo_tabular_cpt_config_js').text(JSON.stringify(newData));

			spinner.addClass('is-active');
			//$('#pcct-submit, #add-ct').attr("disabled", true);

			data = {
				action             : 'tabular_build_table',
				tabular_nonce: tabular_ajax_vars.tabular_admin_nonce,
				table_data: newData,
				table_template: table_template,
				post_id: window.pcPostID
			};

			$.post(ajaxurl, data, function (response) {

				// update table data in the 'Edit Table Data' meta box textarea
				$('#wpgo-tabular-preview .inside').html(response);

				//$('#last-tr').before(response);
				////$('#pcct-ct-table > tbody:last').append(response);

				//$('#pcct-header-tag').after('<div style="display:none;" id="setting-error-settings_updated" class="updated settings-error"><p><strong>New content template added!</strong></p></div>');
				//$('#setting-error-settings_updated').slideDown();
				//$('#setting-error-settings_updated').delay(2000).slideUp();

				//$('#pcct-empty-ct').hide();
				//$('#pcct-empty-ct-submit').show();
				spinner.removeClass('is-active');
				//$('#pcct-submit, #add-ct').attr("disabled", false);
			});
		}
	}

	// monitor changes to data, label and other fields
	// @todo add the functionality to another function(s)?
	//wpgo_pc_generic_table_builder();
});