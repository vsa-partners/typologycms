/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
 var TreeNav = {

	/* -- PARAMS -- */

	ajaxPath			: '/ajax/treeNavChildren/'
	, ajaxStatusPath 	: '/ajax/toggleStatus/'
	, sessionPath		: '/ajax/menuAction/'
	, rowIcons			: true
	, timeout			: 400
	
	// Pass along for ajax requests
	, type				: 'list'
	, destination		: ''
	, menu				: ''
	
	/* -- METHODS -- */

	, init: function(tree, options) {
	
			if (options) {
				// Handle options passed in
				if (options.ajaxPath) 		this.ajaxPath 		= options.ajaxPath;
				if (options.rowIcons) 		this.rowIcons 		= options.rowIcons;
				if (options.type) 			this.type 			= options.type;
				if (options.destination)	this.destination 	= options.destination;
				if (options.select_type)	this.select_type 	= options.select_type;
				if (options.menu)			this.menu 			= options.menu;
			}

			ul = $(tree);
		
			// Register listeners.
			// Takingthis out, why is this conditional here? Breaking it.
			//if (this.type == 'tree') {
				ul.observe('click', this.onClick.bindAsEventListener(this));
				ul.observe('mouseover', this.onMouseOver.bindAsEventListener(this));			
				ul.observe('mouseout', this.onMouseOut.bindAsEventListener(this));			
			//}
			
	}

	, onMouseOver: function(evnt) {
	
			var evnt_element = $(Event.element(evnt));
	
			if (evnt_element.hasClassName('node')) {
				
				// Toggle node icon active status
				evnt.stop();
				evnt_element.addClassName('node-over');

			} else if ((data_node = evnt_element.up('.data'))) {

				// Toggle data row active status
				evnt.stop();
				data_node.addClassName('data-over');

			}
	
	}

	, onMouseOut: function(evnt) {

			var evnt_element = $(Event.element(evnt));
	
			if (evnt_element.hasClassName('node')) {

				// Toggle node icon active status
				evnt.stop();
				evnt_element.removeClassName('node-over');

			} else if ((data_node = evnt_element.up('.data'))) {

				// Toggle data row active status
				evnt.stop();
				data_node.removeClassName('data-over');

			}
	
	}

	, onClick: function(evnt) {

			var evnt_element = $(Event.element(evnt));
			
			if (evnt_element.hasClassName('status')) {
				
				// Publish page
				evnt.stop();
				this._toggleStatusIcon(evnt_element);

			} else if (evnt_element.hasClassName('node')) {

				// Show/hide children elements
				evnt.stop();
				this._toggleChildren(evnt_element);
				
			}

	
	}


	, _toggleChildren: function(parent_elem) {

			parent_li 	= parent_elem.up('li');			
			children	= parent_li.down('div.children UL');
	 
			if (parent_elem.hasClassName('node-open')) {

				// Close node
				parent_elem.removeClassName('node-open');
				parent_elem.addClassName('node-closed');
				
				this._addSessionVar(parent_li.id, 'close');
				children.hide();
			
			} else if (parent_elem.hasClassName('node-closed')) {
				
				// Open node
				this._addSessionVar(parent_li.id, 'open');
				
				if (children.empty()) {
					// Load children page data via ajax request
					this._addChildren(parent_elem, children, parent_li);
				}

				parent_elem.removeClassName('node-closed');
				parent_elem.addClassName('node-open');

				children.show();
			
			}
			
			return;			
	
	}
	
	, _addChildren: function(elem, children, parent) {
	
			elem.addClassName('node-processing');

			var uri 	= ADMIN_PATH + MODULE + this.ajaxPath +this.menu;
			var params 	= { 
						id 				: parent.id 
						, type			: this.type
						, destination	: this.destination
						, select_type	: this.select_type
						, menu			: this.menu
						};

			setTimeout(function() {

				new Ajax.Request(uri, {
					method			: 'post'
					, parameters	: params
					, onSuccess		: function(transport) {

	                    var html            = transport.responseText;

	                    if (html == 'AUTHENTICATION REQUIRED') {

	                    	// You seem to have been logged out, redirect back to login screen.
	                    	document.location.href = ADMIN_PATH;

	                	} else {
	                		children.update(html);
							elem.removeClassName('node-processing');
							TreeNav.registerRowListeners(parent.id);
	                	}

	                }
	            });

			} , this.timeout);


	}

	, _addSessionVar: function(id, action) {
	
			uri = ADMIN_PATH + MODULE + this.sessionPath;

			new Ajax.Request(uri, {
				parameters: { id : id, action	: action, menu : this.menu }
			});


	}

	
	, _toggleStatusIcon: function(status_icon) {
		
			// Can only publish pages with this button, can't recall
			if (!status_icon.hasClassName('published_0')) return false;
	
			status_icon.removeClassName('published_0').addClassName('published_1');
	
			uri = ADMIN_PATH + MODULE + TreeNav.ajaxStatusPath;
	
			new Ajax.Request(uri, {
				parameters: { id : status_icon.up('LI').id, publish	: 1	}
			});
		
	}


};