/* Tabular script for creating new tables only */
jQuery( document ).ready( function( $ ) {

	// get post ID and make it globally available as 'pcPostID'
	window.pcPostID = $("input[type='hidden']#post_ID")[0].value;

	// move the 'Publish' button, and spinner, and update text.
	$("input[type='submit']#publish").attr('value', 'Create Table').appendTo('#pc-create-table');
	$("#publishing-action span.spinner").css('float', 'none').appendTo('#pc-create-table');

	// update form fields on page load
	updateFields();

	// initialize color pickers
	// not really needed for create new table page
	//$( 'input.alpha-color-picker' ).alphaColorPicker();

	// update form fields when table type selection changes
	$('input[name=wpgo_tabular_cpt_type]').change(function(){
		updateFields();
	});

	// update input fields when table type selection changed
	function updateFields() {

		// Setup some vars

		// define initial table data var (with specific post id) on the global scope
		//window['pc_data_' + window.pcPostID] = $("#wpgo_tabular_cpt_data").val();

		// get currently selected table type
		var tableType = $('input[name=wpgo_tabular_cpt_type]:checked').val();

		// get sample data and defaults for currently selected table type
		var sampleTableData = wpgo_tabular_config[tableType];

		// don't proceed if sample data not found
		if(sampleTableData === undefined) {
			console.error("[updateFields] Data not found for table type: " + tableType);
			return;
		}

		wpgo_pc_utility.populate_form_fields(sampleTableData, tableType);

		//wpgo_pc_utility.build_config(sampleTableData, tableType);

		// add JS 'fixed' code to meta box
		$("#wpgo_tabular_cpt_js").text(sampleTableData.fixed);
	}

	// perform some custom tasks upon form submission
	$( "form#post" ).submit(function( event ) {

		// @todo don't really want to add this here but if I try to add it to updateFields() then, on page load, there are some strange side effects such as the form button looks like the form is submitting if you click anywhere on the admin page, which is undesirable.
		updateTitle();

		// for some reason we need to apply the text again once the 'Create Table' button is clicked, otherwise the text reverts to 'Publish'
		$("input[type='submit']#publish").attr('value', 'Creating Table...');

		// show the form submission spinner
		$("#pc-create-table span.spinner").css('visibility', 'visible');
	});

	// update title
	function updateTitle() {
		// update table title text
		var tableTypeText = $('input[name=wpgo_tabular_cpt_type]:checked ~ .pc-type-txt').text();
		$("input[type='text']#title").attr('value', tableTypeText + ' table');
	}
});