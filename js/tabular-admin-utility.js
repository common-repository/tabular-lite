/* Tabular utility functions */

var wpgo_pc_utility = {

	prefix_css_selector: function(sel) {
		// prefix a css selector with a specific table ID
		var space = ' ';
		if(sel === '.wpgo-tabular') { space = ''; }
		return '.pc-' + window.pcPostID + space + sel;
	},
	populate_form_fields: function(sampleTableData, tableType) {

		var $ = jQuery;

		// Note, some fields associated with CSS are updated via build_config()

		// update table data text box
		//$("#wpgo_tabular_cpt_data").text(sampleTableData.data);

		// update table title text box
		$('#wpgo_tabular_title_label').val(sampleTableData.title);

		$('#wpgo_tabular_cpt_config_js').text(JSON.stringify(wpgo_tabular_data.sample1));

		// update table x axis text box
		//$('#wpgo_tabular_x_axis_label').val(sampleTableData.xAxis);

		// update table y axis text box
		//$('#wpgo_tabular_y_axis_label').val(sampleTableData.yAxis);

		// update table margin top text box
		//  wpgo_tabular_margin_top
		//$('#wpgo_tabular_margin_top').val(sampleTableData.margin.top);

		// update table margin right text box
		//$('#wpgo_tabular_margin_right').val(sampleTableData.margin.right);

		// update table margin bottom text box
		//$('#wpgo_tabular_margin_bottom').val(sampleTableData.margin.bottom);

		// update table margin left text box
		//$('#wpgo_tabular_margin_left').val(sampleTableData.margin.left);

		// update table aspect ratio text box
		//$('#wpgo_tabular_aspect_ratio').val(sampleTableData.aspectRatio);

		// update table legend offset text box
		//$('#pc_legend_offset').val(sampleTableData.legendOffset);

		// update table min width text box
		//$('#wpgo_tabular_min_width').val(wpgo_pc_generic_settings.table_width.min);

		// update table max width text box
		//$('#wpgo_tabular_max_width').val(wpgo_pc_generic_settings.table_width.max);

		// Enable display legend checkbox by default
		//$('#pc_legend_status_chk').prop( "checked", true );

		// Enable center table checkbox by default
		/*if(wpgo_pc_generic_settings.table_margin === '0 auto') {
			$('#pc_table_centered_chk').prop("checked", true);
		} else {
			$('#pc_table_centered_chk').prop("checked", false);
		}*/
	},
	build_config: function(sampleTableData, tableType) {

		var $ = jQuery;

		// This function builds the config text and populates some form fields. i.e. the color picker inputs

		var colArrayStr = '[';
		sampleTableData.seriesColors.forEach(function(item, i) {

			// build color series array string (and don't add comma on last iteration)
			var comma = (i === sampleTableData.seriesColors.length - 1) ? "" : ", ";
			colArrayStr += "'" + item + "'" + comma;

			// add color value to color picker input element and make visible
			$('#wpgo_tabular_series_color_' + (i + 1)).val(item);
			$('#wpgo_tabular_series_color_container_' + (i + 1)).css('display','block');
		});
		colArrayStr += ']';
		//console.log(sampleTableData.seriesColors.length);

		// build the initial table CSS
		var cssStr = '';
		sampleTableData.table_css.forEach(function(item, i) {

			// prefix selector with specific table ID
			cssStr += wpgo_pc_utility.prefix_css_selector(item.selector) + ' { ';
			item.css.forEach(function(item, i) {
				cssStr += item.rule + ': ' + item.value + '; ';
			});
			cssStr += ' }\n';

			// if we have an element name then add css value to relevant input element and make visible
			if(item.el !== '') {
				$(item.el).val(item.css[0].value);
			}
		});
		// add table CSS code to meta box
		$("#wpgo_tabular_cpt_css").text(cssStr);

		// get legend display status from checkbox value
		var legend_status = $('#pc_legend_status_chk').prop('checked') ? 'true' : 'false';

		var config = "// === CONFIG START ===\n" +
			"var seriesColors = " + colArrayStr + ";\n" +
			"var tableTitle = '" + sampleTableData.title + "';\n" +
			"var xLabel = '" + sampleTableData.xAxis + "';\n" +
			"var yLabel = '" + sampleTableData.yAxis + "';\n" +
			"var yFormat = 's';\n" +
			"var titleVisible = true;\n" +
			"var xLabelVisible = true;\n" +
			"var yLabelVisible = true;\n" +
			"var legendVisible = " + legend_status + ";\n" +
			"var tableEl = '.pc-" + window.pcPostID + "';\n" +
			"var aspectRatio = " + sampleTableData.aspectRatio + ";\n" +
			"var yTicks = 5;\n" +
			"var rectSize = 16;\n" +
			"var xLblOffset = 20;\n" +
			"var yLblOffset = 20;\n" +
			"var tableLblOffset = 10;\n" +
			"var legendOffset = " + sampleTableData.legendOffset + ";\n" +
			"var margin = {top: " + sampleTableData.margin.top + ", right: " + sampleTableData.margin.right + ", bottom: " + sampleTableData.margin.bottom + ", left: " + sampleTableData.margin.left + "};\n" +
			"var x0_padding = 0.1;\n" +
			"var x1_padding = 0.05;\n" +
			"var csv_data = pc_data_" + window.pcPostID + ";\n" +
			"// === CONFIG END ===\n";

		// update table config textarea
		$('#wpgo_tabular_cpt_config_js').text(config);
	},
	color_picker_visibility: function(sampleTableData, tableType) {
		// Not needed?
	}
};