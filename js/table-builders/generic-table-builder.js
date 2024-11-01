/* Tabular - Generic table builder */

// Table builder code common to all builders

var wpgo_pc_generic_table_builder = function() {

	var $ = jQuery;

	var els = [
		{
			input: '#wpgo_tabular_min_width',
			type: 'text',
			css_rule: 'min-width'
		},
		{
			input: '#wpgo_tabular_max_width',
			type: 'text',
			css_rule: 'max-width'
		},
		{
			input: '#pc_table_centered_chk',
			type: 'checkbox',
			css_rule: 'margin',
			css_value: '0 auto'
		}
	];

	// render table on page load
	updateTable();

	// set legend offset input controls visibility on page load
	setLegendOffsetVisibility();

	// render table preview
	function updateTable(){
return;
		var fixedJS = document.querySelector('#wpgo_tabular_cpt_js').value;
		var data = $("#wpgo_tabular_cpt_data").val();
		//var series_num;

		// remove previous table
		$(".wpgo-tabular").empty();

		// update global table data var - used when calling eval()
		window['pc_data_' + window.pcPostID] = data;

		// used for color picker visibility and construction of data series array
		window.pc_num_series = d3.csvParse(data).columns.length - 1;

		// Hide all data series color pickers by default, then display the ones we have data for
		$('div[id*="wpgo_tabular_series_color_container_"]').css("display", "none");
		for (var i = 0; i < window.pc_num_series; i++) {
			$("#wpgo_tabular_series_color_container_" + (i + 1)).css("display", "block");
		}

		// sync config data series color pickers when table data/settings changes
		updateConfigColorPickerArray();

		// get table config only after the data series color array has been updated
		var configJS = document.querySelector('#wpgo_tabular_cpt_config_js').value;
		var d3TableCode = '(function (){' + configJS + fixedJS;

		// re-evaluate table code
		eval(d3TableCode);
	}

	// re-render table when 'Update' button clicked in 'Table Data' meta box
	$("#wpgo_tabular_cpt_update_data").click(function (e) {
			e.preventDefault();
			updateTable();
	});

	// update svg table legend check box
	$('#pc_legend_status_chk').on('change', function() {

		// set legend offset input controls visibility when check box clicked
		setLegendOffsetVisibility();

		var newBool = this.checked ? 'true' : 'false';

		updateConfigBoolValue(newBool, 'var legendVisible =');
		updateTable();
	});

	// update table legend offset
	$('#pc_legend_offset').on('input', function() {
		updateConfigNumberValue(this.value, 'var legendOffset =');
		updateTable();
	});

	// update svg table title label directly
	$('#wpgo_tabular_title_label').on('input', function() {

		// update table title directly
		d3.select('.pc-labels-g .table-label').text(this.value);

		// update config file text variable
		updateConfigTextValue(this.value, 'var tableTitle =');
	});

	// update svg x-axis table label directly
	$('#wpgo_tabular_x_axis_label').on('input', function() {
		d3.select('.pc-labels-g .x-axis-label').text(this.value);

		// update config file text variable
		updateConfigTextValue(this.value, 'var xLabel =');
	});

	// update svg y-axis table label directly
	$('#wpgo_tabular_y_axis_label').on('input', function() {
		d3.select('.pc-labels-g .y-axis-label').text(this.value);

		// update config file text variable
		updateConfigTextValue(this.value, 'var yLabel =');
	});

	// update margin
	$('input[id*="wpgo_tabular_margin_"]').on('input', function() {
		updateConfigMargin();
	});

	// update aspect ratio
	$('#wpgo_tabular_aspect_ratio').on('input', function() {
		//console.log('AR: ' + this.value);

		// update config file text variable
		updateConfigNumberValue(this.value, 'var aspectRatio =');
		updateTable();
	});

	// update table area bg color directly
	$('#pc_table_bg_color').wpColorPicker({
		change: function(event, ui){
			var new_col = ui.color.toString();
			// prefix selector with specific table ID
			var sel = wpgo_pc_utility.prefix_css_selector('.pc-table-bg-g .table-area');
			var newCSS = updateSingleCSSValue(new_col, sel, 'fill');
			$('#wpgo-tabular-admin-inline-css').html( newCSS.text() );
		}
	});

	// update table svg bg color directly
	$('#pc_svg_bg_color').wpColorPicker({
		change: function(event, ui){
			var new_col = ui.color.toString();
			// prefix selector with specific table ID
			var sel = wpgo_pc_utility.prefix_css_selector('.pc-svg-bg-g .svg-area');
			var newCSS = updateSingleCSSValue(new_col, sel, 'fill');
			$('#wpgo-tabular-admin-inline-css').html( newCSS.text() );
		}
	});

	// update min table width directly
	$('#wpgo_tabular_min_width').on('input', function() {
		var newCSS = updateMultiCSSValue(els, wpgo_pc_utility.prefix_css_selector('.wpgo-tabular'));
		$('#wpgo-tabular-admin-inline-css').html( newCSS.text() );
		updateTable();
	});

	// update max table width directly
	$('#wpgo_tabular_max_width').on('input', function() {
		var newCSS = updateMultiCSSValue(els, wpgo_pc_utility.prefix_css_selector('.wpgo-tabular'));
		$('#wpgo-tabular-admin-inline-css').html( newCSS.text() );
		updateTable();
	});

	// update table alignment check box
	$('#pc_table_centered_chk').on('change', function() {
		var newCSS = updateMultiCSSValue(els, '.wpgo-tabular');
		$('#wpgo-tabular-admin-inline-css').html( newCSS.text() );
		updateTable();
	});

	$('input[id*="wpgo_tabular_series_color"]').wpColorPicker({
		change: function(event, ui){
			var series_cp_num = event.target.id.substring(31);
			var new_col = ui.color.toString();

			$('.pc-svg rect.series' + series_cp_num).css( 'fill', new_col );
			updateConfigColorPickerArray();
		}
	});

	// UTILITY FUNCTIONS (@todo move to tabular-admin-utility.js?)

	function setLegendOffsetVisibility() {
		var status = $('#pc_legend_status_chk').prop('checked');

		if(status) {
			$(".pc-control-container.legend").css("display", "flex");
		} else {
			$(".pc-control-container.legend").css("display", "none");
		}
	}

	function updateMultiCSSValue(el, css_sel) {
		var sel = $('#wpgo_tabular_cpt_css');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(css_sel)) {

				var cssRules = '';
				el.forEach(function(itemj, j, arrj) {

					if(itemj.type === 'checkbox') { // get custom value for checkbox

						var status = $(itemj.input).prop('checked');
						if(status) {
							cssRules += itemj.css_rule + ': ' + itemj.css_value + '; ';
						}
					} else { // just use value from text box
						var val = $(itemj.input).val();
						//console.log(val);

						cssRules += itemj.css_rule + ': ' + val + '; ';
					}
				});
				arr[i] = css_sel + " { " + cssRules + " }";
			}
		});
		return sel.text(txtArr.join('\n'));
	}

	function updateSingleCSSValue(newVal, search_txt, cssRule) {
		//console.log(search_txt);
		var sel = $('#wpgo_tabular_cpt_css');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {
				arr[i] = search_txt + " { " + cssRule + ": " + newVal + "; }";
			}
		});
		return sel.text(txtArr.join('\n'));
	}

	function updateConfigColorPickerArray() {
		var search_txt = 'var seriesColors =';
		var sel = $('#wpgo_tabular_cpt_config_js');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {
				var seriesTxt = search_txt + " [";
				var comma = '';
				for (var j = 0; j < window.pc_num_series; j++) {
					comma = (j === (window.pc_num_series - 1)) ? "'];" : "', ";
					seriesTxt += "'" + $('#wpgo_tabular_series_color_' + (j + 1)).val() + comma;
				}
				arr[i] = seriesTxt;
			}
		});
		sel.text(txtArr.join('\n'));
	}

	function updateConfigTextValue(newText, search_txt) {
		var sel = $('#wpgo_tabular_cpt_config_js');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {
				arr[i] = search_txt + " '" + newText + "';";
			}
		});
		sel.text(txtArr.join('\n'));
	}

	function updateConfigNumberValue(newNum, search_txt) {
		var sel = $('#wpgo_tabular_cpt_config_js');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {
				arr[i] = search_txt + " " + newNum + ";";
			}
		});
		sel.text(txtArr.join('\n'));
	}

	function updateConfigBoolValue(newBool, search_txt) {
		var sel = $('#wpgo_tabular_cpt_config_js');
		var txtArr = sel.text().split('\n');
		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {
				arr[i] = search_txt + " " + newBool + ";";
			}
		});
		sel.text(txtArr.join('\n'));
	}

	function updateConfigMargin() {
		var top = $('#wpgo_tabular_margin_top').val();
		var right = $('#wpgo_tabular_margin_right').val();
		var bottom = $('#wpgo_tabular_margin_bottom').val();
		var left = $('#wpgo_tabular_margin_left').val();
		var search_txt = 'var margin =';
		var sel = $('#wpgo_tabular_cpt_config_js');
		var txtArr = sel.text().split('\n');

		txtArr.forEach(function (item, i, arr) {
			if (item.includes(search_txt)) {

				arr[i] = search_txt + " {top: " + top + ", right: " + right + ", bottom: " + bottom + ", left: " + left + "};";
			}
		});
		sel.text(txtArr.join('\n'));
		updateTable();
	}
};
