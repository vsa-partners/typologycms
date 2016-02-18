(function($) {      

//  console.log($.cleditor);
 
  // Define the button
  $.cleditor.buttons.pasteword = {
    name: "pasteword"
    , image: "button_pasteword.gif"
    , title: "Paste from Word"
    , command: "inserthtml"
    , popupName: "PasteWord"
    , popupClass: "cleditorPrompt"
    , popupContent: 'Paste your text here:<br><textarea style="width: 150px;height: 50px;"></textarea><br><input type="button" value="Insert">'
    , buttonClick: pasteFromWord
  };      
 
  // Handle the hello button click event
  function pasteFromWord(e, data) {
      
    // Wire up the submit button click event
    $(data.popup).children(":button")
      .unbind("click")
      .bind("click", function(e) {      
 
        // Get the editor
        var editor = data.editor;      
        
        // Restore the internet explorer selection
        //editor.restoreSelection();
 
        // Get the entered name
        var pasted_text = '<p>' + $(data.popup).find("textarea").val() + '</p>';

        // Insert into the document
        editor.execCommand(data.command, pasted_text, null, data.button);
 
        // Hide the popup and set focus back to the editor
        editor.hidePopups();
        editor.focus();
 
      });
 
  }
      
 
})(jQuery);