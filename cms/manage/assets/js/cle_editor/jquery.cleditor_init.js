var CLE_EDITORS;

jQuery(document).ready(function() {

	jQuery.cleditor.defaultOptions.controls = 'bold italic style removeformat | bullets numbering | link unlink | pasteword | source';
	jQuery.cleditor.defaultOptions.colors = '387ab6 474747';
	jQuery.cleditor.defaultOptions.styles = [["Paragraph", "<p>"], ["Header 2", "<h2>"], ["Header 3", "<h3>"]];
	
	CLE_EDITORS = jQuery("TEXTAREA.editor").cleditor();

});

function refreshCLE() {

	if (CLE_EDITORS) {
		CLE_EDITORS.each(function() {
			this.refresh();
		});
	}

}