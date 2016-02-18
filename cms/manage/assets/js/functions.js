/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
function confirmDelete(delUrl, message) {
		if (!confirm(message || 'Are you sure you want to delete this?')) return false;
		if (delUrl) document.location = delUrl;
}

function openWindow(url, params) {

		var height 		= (params && params.height) ? params.height : 300;
		var width 		= (params && params.width) ? params.width 	: 300;
		var scroll 		= (params && params.scroll) ? params.scroll	: 'no';
		var name 		= (params && params.name) ? params.name 	: 'PopWindow';
		var urlprops	= (params && params.props) ? params.props 	: '';

		var left		= (screen.width - width) / 2;
		var top 		= (screen.height - height) / 2;

		winprops 		= 'height='+height+',width='+width+',top='+top+',left='+left+',resizable=0,scrollbars='+scroll;
		
		openwindow = window.open(url+urlprops, name, winprops);
		openwindow.focus();
}

function highlight(item) { 
		new Effect.Highlight(item,{startcolor:'#ffec6b', endcolor:'#f6f6f6', duration:3.0});
}











var itemPicker = {

	/* -- PARAMS -- */
	
	adminPath		: null
	, module		: false
	, pickerPath	: '/picker'

	, multiItems	: []

	/* -- METHODS -- */

	, clear: function(destination, additional){	

			$(destination+'_display').update('( No Item Selected )');

			$(destination).value 	= '';
			
			// Additional node fields to clear
			if (additional && additional.length) {
				additional.each(function(f) {
					$(destination+ '['+f+']').value = '';
				});
			}
			
			$(destination+'_node').down('.file_field_buttons').hide();
			$(destination+'_node').select('.field_text A').first().replace('(No Item Selected)');

	}

	, open: function(options){	
			/*
			{'adminPath': '', 'module': 'page_template', 'destInput': '', 'destDisplay': '', 'submitForm': '', 'sType': '' }
			*/
			
			this.adminPath 	= ADMIN_PATH;
			this.module 	= MODULE;
			
			if (options.adminPath) 		this.adminPath = options.adminPath;
			if (options.module) 		this.module = options.module;

			uri = this.adminPath + this.module + this.pickerPath + '?';

			if (options.destination) 	uri += 'destination=' + options.destination + '&';
			if (options.submitForm)		uri += 'submitForm=' + options.submitForm + '&';
			if (options.select_type) 	uri += 'select_type=' + options.select_type + '&';
			if (options.parent_id) 		uri += 'parent_id=' + options.parent_id + '&';
			if (options.multi) 			uri += 'multi=' + options.multi + '&';
			
			// Route to openWindow function
			//openWindow(uri, {'scroll': 'auto', 'width' : 470, 'height' : 600});
			
			TNDR.Modal.title('hello').showFrame(uri, 470, 600);
			
			return;
	
	}


	
	, addMulti:  function(params) {
	
		params = params.evalJSON();
		itemPicker.multiItems.push(params);

	
	}
	
	, chooseMulti: function() {
	
		window.parent.itemPicker.updatePageMulti(itemPicker.multiItems, upload_destination);
	
	}

	, updatePageMulti: function(params, destination) {

		var origin 		= $(destination+'_node');
		
		if (!origin) return;
		
		var dupe_button = origin.down('SPAN[multi_param]');
		var dupe_param 	= dupe_button.getAttribute('multi_param').evalJSON();
		
		var num			= 1;
		
		$A(params).each(function(i, n) {
			dupe_param.file_id = (i.file_id || '');
			TNDR.Form.Actions.dupe(dupe_param, num);
			num++;
		});
		
		TNDR.Modal.hide();
	
	}
	
	, choose: function(params){
			
			if (!params) return;
		
			window.parent.itemPicker.updatePage(params);
		
	}


	, updatePage: function(params){

			if (!params) return;
		
			if (!$(params.destination)) alert('Error: itemPicker::update: unabled to find destination node "'+params.destination+'"');
		
			if ((params.title != undefined) && $(params.destination+'_display')) $(params.destination+'_display').update(params.title);
			if (params.destination && $(params.destination)) $(params.destination).value 	= params.id;
			
			// Show clear button
			if ($(params.destination+'_node') && (clear = $(params.destination+'_node').down('.button_clear'))) clear.show();
	
			node = $(params.destination).up('.form_node');
			if (node) highlight(node);		

			// Any other fields that need updating?
			if (params.update_fields) {
			
				Object.keys(params.update_fields).each(function(k) {
					if ($(params.destination+'['+k+']')) {
						$(params.destination+'['+k+']').value = params.update_fields[k];
					}
				});
			
			}

			if (params.submitForm) {
				button = $(params.submitForm);
				button.click();
			}

			TNDR.Modal.hide();

	}

	, togglePreview: function(button) {
		
			data_row = button.up('DIV.data');
			
			if (data_row) {
				preview = data_row.down('DIV.preview');
				if (preview) preview.toggle();
			}

	
	}


}


var diskBrowser = {

	/* -- PARAMS -- */
	
	adminPath		: null
	, module		: 'browse'
	, pickerPath	: '/picker'

	/* -- METHODS -- */
	
	/*

	, clear: function(destination, additional){	

			$(destination+'_display').update('( No Item Selected )');

			$(destination).value 	= '';
			
			// Additional node fields to clear
			if (additional && additional.length) {
				additional.each(function(f) {
					$(destination+ '['+f+']').value = '';
				});
			}

			$(destination+'_node').down('.button_clear').hide();

	}
	*/
	
	
	, open: function(options){	
			/*
			{'adminPath': '', 'destination': ''}
			*/
			
			this.adminPath 	= ADMIN_PATH;

			if (options.adminPath) 		this.adminPath = options.adminPath;

			uri = this.adminPath + this.module + this.pickerPath + '?';

			if (options.destination) 	uri += 'destination=' + options.destination + '&';

			if (options.path) {
				uri += 'path=' + options.path + '&';
			} else if ($F(options.destination)) {
				uri += 'path=' + $F(options.destination) + '&';
			} else if (options.default_path) {
				uri += 'path=' + options.default_path + '&';
			}
			
			// Route to openWindow function
			//openWindow(uri, {'scroll': 'auto', 'width' : 470, 'height' : 600});
			
			TNDR.Modal.showFrame(uri, 470, 600);
			
			return;
	
	}

	, choose: function(params){
			
			if (!params) return;
	
			window.parent.diskBrowser.updatePage(params);
	
//			opener.diskBrowser.updatePage(params);
//			window.close();			

	}


	, updatePage: function(params){

			if (!params) return;
		
			if (params.destination && $(params.destination))
				$(params.destination).value = params.value;
			
			node = $(params.destination).up('.form_node');
			if (node) highlight(node);		

			// Any other fields that need updating?
			if (params.update_fields) {
			
				Object.keys(params.update_fields).each(function(k) {
					if ($(params.destination+'['+k+']')) {
						$(params.destination+'['+k+']').value = params.update_fields[k];
					}
				});
			
			}

			if (params.submitForm) {
				button = $(params.submitForm);
				button.click();
			}

			TNDR.Modal.hide();

	}

}


var PathChecker = {

	/* -- PARAMS -- */
	
	adminPath		: '/'
	, module		: 'page'
	, parentPath	: '/'
	, messageField	: null
	, id			: null
	
	, source		: null
	, destination	: null

	/* -- METHODS -- */

	, initialize: function(source, destination, options) {

			this.adminPath			 	= ADMIN_PATH;
			this.module 				= MODULE;

			if (options.adminPath) 		this.adminPath = options.adminPath;
			if (options.module) 		this.module = options.module;
			if (options.parentPath) 	this.parentPath = options.parentPath;
			if (options.id) 			this.id = options.id;

			this.source					= $(source);
			this.destination			= $(destination);
	
			new Form.Element.Observer(
				this.source
				, 0.7
				, function(el, value) {
					PathChecker.fieldUpdated(el, value);
				}
			);
			
	}

	, fieldUpdated : function(el, value) {

			if (value.length) {

				var url = this.adminPath + this.module + '/ajax/checkUniquePath/';	
			
				new Ajax.Request(url, {
					method			: 'post'
					, parameters: {
						parent_path	: this.parentPath
						, file_name	: value
						, id		: this.id
					}
					, onSuccess		: function(transport) {
						PathChecker.isUnique_success(transport.responseText);
					}
				});

			}

	}
	

	, isUnique_success: function(result) {

			var result_data = result.evalJSON(true);

			this.destination.update(result_data.path);
	
			switch(result_data.unique) {
			
				case 'TRUE' :
					TNDR.Form.Validation.showFieldError(this.source, false);
					break;

				case 'FALSE' :
					TNDR.Form.Validation.showFieldError(this.source, 'NOT UNIQUE');
					break;
			
				default:
					// ERROR
					//console.log('Unknown result: '+result);
					break;
		
			}
	
	}
	
};


function toggleDropdown(num) {

		drop 	= (parseFloat(num)) ? $('dropdown_'+num) : $(num);
		button	= drop.down('a.button');
		links	= drop.down('ul.links');
		
		if (button.hasClassName('open')) {
			// Close it
			button.removeClassName('open');
			links.hide();
			
			// Clear listener
			Event.stopObserving(drop, 'mouseout');
			
		} else {

			// Open it
			button.addClassName('open');
			links.show();

			drop.observe('mouseout', function(e){
				//if (!e.relatedTarget.up('.dropdown_button')) toggleDropdown(e.findElement('DIV.dropdown_button'));							
			});

		}	

}


var RecallPublished = {

	/* -- PARAMS -- */
	
	adminPath		: '/'
	, module		: 'page'
	, destination	: 'published_display'
	, button		: ''

	/* -- METHODS -- */

	, initialize: function(options) {

			this.adminPath 	= ADMIN_PATH;
			this.module 	= MODULE;

			if (options && options.adminPath) 	this.adminPath = options.adminPath;
			if (options && options.module) 		this.module = options.module;
			if (options && options.button) 		this.button = options.button;

	}

	, recall : function(id) {

			url = this.adminPath + this.module + '/ajax/recallPublished/';	

			if (RecallPublished.button) {
				$(RecallPublished.button).update('<div class="processing_icon" style="width:75px;"> </div>');
			}
		
			new Ajax.Request(url, {
				method		: 'post'
				, parameters: 'id=' + id
				, onSuccess	: function(transport) {

					result = transport.responseText;
					
					if (result == 'SUCCESS') {
						
						$(RecallPublished.destination).update('Draft');

						TNDR.Modal.add('This page has been recalled.', 'message');
						TNDR.Modal.show(true);
						
						if (RecallPublished.button) $(RecallPublished.button).remove();
						
					} else {
						
						TNDR.Modal.add('There was a problem recalling this page. Please contact the system administrator.', 'message', false, false);
						TNDR.Modal.show(true);
						
					}

				}
			});

	}
};