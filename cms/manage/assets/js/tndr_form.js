/**
 * Typology CMS Form JS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */

if (typeof(TNDR)) var TNDR = {};
 
document.observe('dom:loaded', function() {
	TNDR.Form = new TNDR_Form();
});

/** --------------------------------------------------
 * Typology FORM
 */

TNDR_Form = function() {
	
		var _form_class		= 'tndr_form';

		// Private Methods

		var _init = function (){

				this.UI 			= new TNDR_FormUI(_form_class);
				this.Validation 	= new TNDR_FormValidation(_form_class);
				this.Actions 		= new TNDR_FormActions(_form_class);
	
				$$('.'+_form_class).each(function(f) {
						
					// Add form change observer
					new Form.Observer(f, 0.3, _onFormChange.bind(this));			
	
					// Add form submit observer
					f.observe('submit', _onFormSubmit.bind(this));

					// Add form click observer
					f.observe('click', _onFormClick.bind(this));					
	
					//f.observe('mouseout', _onMouseOut.bind(this));
					//f.observe('mouseover', _onMouseOver.bind(this));
			
				}.bind(this));

		}.bind(this);



		var _onMouseOver = function(evnt) {
				this.UI.onMouseOver(evnt);
		};
		var _onMouseOut = function(evnt) {
				this.UI.onMouseOut(evnt);
		};

		var _onFormClick = function(evnt) {
				// Relay form change action to all modules
				this.UI.onFormClick(evnt);
				this.Actions.onFormClick(evnt);
		};

		var _onFormChange = function(frm, val, elem) {
				// Relay form change action to all modules
				this.UI.onFormChange(frm, val, elem);
				this.Validation.onFormChange(frm, val, elem);
				this.Actions.onFormChange(frm, val, elem);
		};

		var _onFormSubmit = function (e) {
				if (!this.Validation.validateSubmit(Event.element(e))) e.stop();
		};
		

		// Call Constructor - should happen last
		_init();

};

/** --------------------------------------------------
 * Typology FORM UI
 */

TNDR_FormUI = function(form_class) {


	// Private Variables
	var _form_class				= form_class;
	var _is_ie					= (navigator.userAgent.toLowerCase().indexOf('msie') + 1);

	var _input_item_radio_check = new Template('<div class="form_input_#{type}#{class_string}#{disabled}" type="#{type}" input_name="#{name}" input_value="#{input_value}">#{item_html}</div>');
	var _input_item_text 		= new Template('<div class="form_input_text #{class_string}"><div class="display_field">#{input_html}</div></div>');
	var _input_item_textarea 	= new Template('<div class="form_textarea #{class_string}"><div class="textarea_top_right"></div><div class="textarea_btm"><div class="textarea_btm_right">#{input_html}</div></div></div>');
	var _input_item_file 		= new Template('<div class="form_file">'
											 + '<input type="file" name="#{name}" class="hidden_field" />'					
											 + '<div class="fake">'
											 +'<div class="display display_field"></div>'
											 +'</div>'
											 + '</div>');
	var _input_select			= new Template('<div class="form_select #{class_string}">'	
											 + '<div class="display"><span class="display_field">#{display}</span></div>'
											 + '<input type="hidden" name="#{name}" value="#{value}" #{input_extra} />'
											 + '<div class="options" style="display: none;"><div class="options_inner"><ul>#{options}</ul></div></div>'
											 + '</div>');
	var _input_attributeSelect	= new Template('<div class="form_select form_attributeselect #{class_string}">'	
											 + '<div class="display"><span class="display_field">#{display}</span></div>'
											 + '<input type="hidden" name="#{name}" value="#{value}" #{input_extra} />'
											 + '<div class="options" style="display: none;"><div class="options_inner">'
											    +'<ul>#{options}</ul>'
											    +'<div class="new form_attributeselect_new">'
											        +'<label>Create:</label><input type="text" name="new_#{name}" class="form_attributeselect_newfield" />'
											        +'<div class="field_buttons">'
											            +'<div class="processing_icon" style="display:none;margin-right:27px"></div>'
											            +'<button data-group="#{data_group}" class="button button_outline_small form_attributeselect_newbutton" type="button"><span>ADD</span></button>'
											        +'</div>'
											    +'</div>'
											 +'</div></div>'
											 + '</div>');
	var _input_select_option	= new Template('<li option_value="#{value}" class="form_select_option">#{name}</li>');


	// Private Methods

	var _init = function() {

			$$('.'+_form_class).each(function(f) {
				this.applyStyles(f);
			}.bind(this));

	}.bind(this);


	// Public Methods

	this.applyStyles = function(f) {	
	
			f = $(f);

			// Initialize all checkboxes
			f.select('INPUT[type="checkbox"]', 'INPUT[type="radio"]').each(this.checkbox.applyStyle.bind(this));

			// Initialize all selects
			f.select('SELECT:not(.style_attribute)').each(this.select.applyStyle.bind(this));

			// Initialize all attribute selects
			f.select('SELECT.style_attribute').each(this.attributeSelect.applyStyle.bind(this));

			// Initialize all textareas
			f.select('TEXTAREA').each(this.textarea.applyStyle.bind(this));

			// Initialize all text fields
			f.select('INPUT[type="text"]:not(.form_attributeselect_newfield)', 'INPUT[type="file"]', 'INPUT[type="password"]').each(this.field.applyStyle.bind(this));

			if (_is_ie) {
				// IE Only focus/blur event listeners
				f.observe('focusin', this.onFocus.bind(this));
				f.observe('focusout', this.onBlur.bind(this));
			} else {
				// Firefox & Safari focus/blur event listeners			
				f.addEventListener('focus', this.onFocus.bind(this), true);
				f.addEventListener('blur', this.onBlur.bind(this), true);
			}
	
	
	};

	
	this.onFocus = function(evnt) {

			var elem = $(Event.element(evnt));
			
			if ((elem.nodeName == 'INPUT') && (elem.getAttribute('type') != 'file')) {
			    if ((x = elem.up('.form_input_text'))) x.addClassName('focus');
				// Do not stop popagation, will disable cursor in firefox
				//evnt.stopPropagation();
			}	
			
	};
	this.onBlur = function(evnt) {

			var elem = $(Event.element(evnt));
			
			if ((elem.nodeName == 'INPUT') && (elem.getAttribute('type') != 'file')) {
			    if ((x = elem.up('.form_input_text'))) {
			        x.removeClassName('focus');
    				//elem.up('.form_input_text').removeClassName('focus');
	    			evnt.stopPropagation();
	    		}
			}	
	
	};
	
	this.onMouseOver = function(evnt) {
	
	        if (Event.element(evnt)) {
	
                if ((elem = $(Event.element(evnt)).up('.form_file'))) {
                    evnt.stopPropagation();
                    elem.addClassName('hover');
                }
            
                if ((elem = $(Event.element(evnt)).up('.form_select'))) {
                    evnt.stopPropagation();
                    elem.addClassName('hover');
                }
            
            }
	
	};


	this.onMouseOut = function(evnt) {

	        if (Event.element(evnt)) {

                if ((elem = $(Event.element(evnt)).up('.form_file'))) {
                    evnt.stopPropagation();
                    elem.removeClassName('hover');
                }
        
                if ((elem = $(Event.element(evnt)).up('.form_select'))) {
                    evnt.stopPropagation();
                    elem.removeClassName('hover');
                }	
            
            }
	
	};

	
	this.onFormClick = function(evnt) {
			
			var elem = Event.element(evnt);
			
			if (elem.hasClassName('form_input_checkbox')) {
				evnt.stopPropagation();
				this.checkbox.onClick(elem, 'check');
			} else if (elem.hasClassName('form_input_radio')) {
				evnt.stopPropagation();
				this.checkbox.onClick(elem, 'radio');
			} else if (elem.hasClassName('form_select_option')) {
				evnt.stopPropagation();
				this.select.onOptionClick(elem);
			} else if (elem.hasClassName('form_attributeselect_newfield')) {
				evnt.stopPropagation();
				this.attributeSelect.onNewFieldClick(elem);
			} else if (elem.hasClassName('form_attributeselect_newbutton')) {
				evnt.stopPropagation();
				this.attributeSelect.onNewButtonClick(elem);
			} else if (elem.hasClassName('form_attributeselect')) {
				evnt.stopPropagation();
				this.attributeSelect.onClick(elem);
			} else if (elem.hasClassName('form_select')) {
				evnt.stopPropagation();
				this.select.onClick(elem);
			} else if ((dd = elem.up('DIV.form_select'))) {
				evnt.stopPropagation();
				this.select.onClick(dd);
			}
	
	};	

	
	this.onFormChange = function(frm, val, elem) {
	
			if (!elem) return;
	
			if (elem.type == 'file') {
				this.field.onChange(elem);
			} else if (elem.type == 'textarea') {
				this.textarea.onChange(elem);
			}
			
	};


	this.checkbox = {
	
		applyStyle: function(item) {
		
				var template_options = {
					class_string	: (item.checked ? ' '+item.type+'_checked' : '')
					, disabled		: (item.disabled ? ' disabled' : '')
					, item_html		: item.toHTML()
					
					, value 		: (item.checked ? item.value : '')
					, type			: item.type
					, input_value	: item.value
					, name			: item.name
					
					
				};	
				item.replace(_input_item_radio_check.evaluate(template_options));		
			
		}
		
		, onClick: function(elem, type) {

				var cb_type	= elem.getAttribute('type');
		
				if (elem.hasClassName('disabled')) {
					// Do nothing...		
				} else if (elem.hasClassName(cb_type+'_checked')) {
					this.uncheck(elem);
				} else {
	
					// If this is a radio, need to make sure all siblings are unchecked first
					if (type == 'radio') {
						$$('DIV[input_name="'+elem.getAttribute('input_name')+'"]').each(function(sib) {
							this.uncheck(sib);
						}.bind(this));
					}			
	
					this.check(elem);
				
				}
		
		}

		, check: function(elem) {
				var cb_type	= elem.getAttribute('type');
				var cb 		= elem.down('INPUT');
				cb.value 	= elem.getAttribute('input_value');
				cb.checked 	= true;
				elem.addClassName(cb_type+'_checked');
		}
		
		, uncheck: function(elem) {
				var cb_type	= elem.getAttribute('type');
				var cb 		= elem.down('INPUT');
				cb.value 	= '';
				cb.checked 	= false;
				elem.removeClassName(cb_type+'_checked');
		}
		
	
	}; // End checkbox


	this.attributeSelect = {
	
		applyStyle: function(item) {
		
				var options_html = '';
				var class_string = '';
				var display      = '';   

        		if (item.options.length) {

                    $A(item.options).each(function(opt) {
                        options_html += _input_select_option.evaluate({name: opt.text, value: opt.value});
                    }.bind(this));
                
                    display      = item.options[item.selectedIndex].innerHTML;                
                
                }

				var template_options = {
					value 			: item.value
					, name			: item.name
					, options		: options_html
					, display		: display
					, data_group    : item.getAttribute('data-group')
					//, input_extra	: input_extra
					//, class_string	: class_string
				};
				item.replace(_input_attributeSelect.evaluate(template_options));		
			
		}
		
		, onClick: function(elem, type) {

			// Make sure that all other dropdowns are closed first			
			$$('.form_select.open').invoke('removeClassName','open');

			elem.down('.options').toggle();
			elem.toggleClassName('open');

			elem.observe('mouseout', function(e) {
			
				if (!e.relatedTarget || !e.relatedTarget.up('.form_select')) {
					
					var sel = e.findElement('.form_select');
					
					sel.removeClassName('open');
					sel.down('.options').hide();
					
					e.stopObserving;
				}
			
			}.bind(this));

		}
		
		, onNewFieldClick: function() {
            // Don't actually need to do anything		
		}

		, onNewButtonClick: function(button) {
		    
		    var new_wrapper     = button.up('.new');
		    var new_input       = new_wrapper.down('INPUT[type=text]');
		    var processing_icon = new_wrapper.down('.processing_icon');
		    var new_input_val   = new_input.value;
			var sel             = button.up('.form_select');
			var list            = sel.down('UL');
			var group_id        = button.getAttribute('data-group');
			
		    if (!new_input_val.length) return false;

			button.hide();
			processing_icon.show();

	        var url		 		= ADMIN_PATH + MODULE + '/ajax/addAttributeValue';

			new Ajax.Request(url, {
				method		: 'post'
				, parameters: {
					value 		    : new_input_val
					, group 		: group_id
				}
				, onSuccess	: function(transport) {

                    var html            = '<li class="form_select_option" option_value="'+transport.responseText+'">'+new_input_val+'</li>';
            
                    list.insert({top: html});

                    sel.down('INPUT[type=hidden]').value = transport.responseText;
                    sel.down('.display SPAN').update(new_input_val);
                    sel.down('.options').hide();
                    new_input.value = '';		    

    	    		button.show();
	    	    	processing_icon.hide();


                }
            });
    
		
		}

		, onOptionClick: function(opt) {
			
			var val = opt.getAttribute('option_value');			
			var sel = opt.up('.form_select');

			sel.down('INPUT[type=hidden]').value = val;
			sel.down('.display SPAN').update(opt.innerHTML);
			sel.down('.options').hide();
			
		}
	
	}; // End attributeSelect
	
	
	this.select = {
	
		applyStyle: function(item) {
		
				var options_html = '';
				var class_string = '';
	
				$A(item.options).each(function(opt) {
					options_html += _input_select_option.evaluate({name: opt.text, value: opt.value});
				}.bind(this));
	
				// Extra stuff to add if this is a toggle_hide items
				var input_extra = '';
				if (item.hasClassName('toggle_hide')) {
					input_extra += 'class="toggle_hide"';
					if (item.getAttribute('showif')) input_extra += ' showif="'+item.getAttribute('showif')+'"';
					if (item.getAttribute('hideif')) input_extra += ' hideif="'+item.getAttribute('hideif')+'"';
					if (item.getAttribute('toshow')) input_extra += ' toshow="'+item.getAttribute('toshow')+'"';
					if (item.getAttribute('tohide')) input_extra += ' tohide="'+item.getAttribute('tohide')+'"';
				} else if (item.hasClassName('short')) {
					class_string += ' short';
				}

				var template_options = {
					value 			: item.value
					, name			: item.name
					, options		: options_html
					, display		: item.options[item.selectedIndex].innerHTML || item.value
					, input_extra	: input_extra
					, class_string	: class_string
				};
				item.replace(_input_select.evaluate(template_options));		
	
			
		}
		
		, onClick: function(elem, type) {

			// Make sure that all other dropdowns are closed first			
			$$('.form_select.open').invoke('removeClassName','open');

			elem.down('.options').toggle();
			elem.toggleClassName('open');

			elem.observe('mouseout', function(e) {
			
				if (!e.relatedTarget || !e.relatedTarget.up('.form_select')) {
					
					var sel = e.findElement('.form_select');
					
					sel.removeClassName('open');
					sel.down('.options').hide();
					
					e.stopObserving;
				}
			
			}.bind(this));

		}

		, onOptionClick: function(opt) {
			
			var val = opt.getAttribute('option_value');			
			var sel = opt.up('.form_select');

			sel.down('INPUT[type=hidden]').value = val;
			sel.down('.display SPAN').update(opt.innerHTML);
			sel.down('.options').hide();
			
		}
	
	}; // End select


	this.textarea = {

		max_height		: 300
		, min_height	: 35

		, applyStyle: function(item) {

				if (item.hasClassName('editor') || item.hasClassName('widgEditor') || item.hasClassName('hide')) {
					return;
				}

				this.textarea.onChange(item);

				var template_options = {
					input_html		: item.toHTML()
					, class_string	: (item.hasClassName('wide') ? 'wide' : '')
				};	
				item.replace(_input_item_textarea.evaluate(template_options));	
				
		}

		, onChange: function(elem) {
		
				if (elem.hasClassName('editor') || elem.hasClassName('widgEditor') || elem.hasClassName('hide')) {
					return;
				}
				
				// Adjust size
				var height	= (elem.scrollHeight > this.max_height) ? this.max_height : ((elem.scrollHeight < this.min_height) ? this.min_height : elem.scrollHeight);
				elem.setStyle({ height: height + 'px' });

		}
	
	}; // End textarea


	this.field = {
	
		applyStyle: function(item) {
		
				if (item.type == 'file') {

					var template_options = {
						name			: item.name
						, class_string	: item.className
					};
					item.replace(_input_item_file.evaluate(template_options));		

				} else {
					
					var template_options = {
						input_html		: item.toHTML()
					};	
					item.replace(_input_item_text.evaluate(template_options));		

				}			
		}

		, onChange: function(elem) {

			if (elem.type != 'file') return;

			var display_field = elem.parentNode.down('DIV.display');
			if (display_field) display_field.update(elem.value);

		}		

	
	}; // End text


	// Call Constructor - should happen last
	_init();
	
};


/** --------------------------------------------------
 * Typology FORM VALIDATION
 */

TNDR_FormValidation = function(form_class) {

	// Private Variables
	
	var _form_class				= form_class;
	var _validate_field_classes = $A(['required', 'validate_email', 'confirm', 'enable_tabs']);

	var _pattern_email 			= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var _pattern_date 			= /^\d{4}\/\d{1,2}\/\d{1,2}$/;


	// Public Methods

	this.onFormChange = function(frm, val, elem) {
		
			// Reset all errors
			_errors.reset();
			_errors.resetField(elem);
	
			// Validate updated field
			this.validateField(elem, 'field');	
	
			// Finished validation, display any errors
			_errors.displayFieldErrors(elem);

	};


	this.validateField = function(element, check_type) {
			
			if (element.type == 'hidden' || element.type == 'submit' || element.type == 'button') return;

			// Don't reset error field when form is submitted
			if (check_type == 'field') _errors.resetField(element);
			
			// Make sure we need to validate this field
			if (_validate_field_classes.any(element.hasClassName.bind(element))) {

				var title	= (check_type == 'field') ? 'This ' : _getFieldTitle(element)+' ';
				var value	= $F(element) ? $F(element).strip() : '';
	
				// Required?
				if (element.hasClassName('required')) {
					
					switch(element.type) {
						
						case 'radio':
							if (!$$('input[type=radio][name="'+ element.name +'"]:checked').length)
								_errors.add(title + 'is a required field.', element);
							break;
						
						default:
							if (!value.length) _errors.add(title + 'is a required field.', element); 							
							break;
					}
					
				}

				// Min Length
				if (element.hasClassName('validate_minlength')) {
					minlength = element.getAttribute('minlength')
					if (value.length < minlength) {
						_errors.add(title +' must be '+minlength+' charaters or longer', element);
					}
				}
	
				// Regex Email
				if (element.hasClassName('validate_email') && !_pattern_email.test(value))
					_errors.add(title + 'must be a valid email address.', element);
	
				/*
				// Confirm
				if (element.hasClassName('validate_confirm')) {
	
					confirm_item = $$('input[name="'+ element.getAttribute('confirm_with') +'"]').first();
					
					if (value != confirm_item.value) {
						this.errors.add(title +' does not match '+ _getFieldTitle(confirm_item));
					}
				
				}
	
				*/

			}	

	};


	this.validateSubmit = function(f) {
			
			
			// Reset all errors
			_errors.reset();

			// Validate all fields
			f.getElements().each(this.validateField.bind(this));

			// Any errors?
			if (_errors.bin.length > 0) {
				_errors.displaySubmitErrors();
				return false;
			} else {
				return true;
			}

	};
	
	
	this.showFieldError = function(field, error) {
	
			if (error == false) {
				// Reset
				_errors.resetField($(field));
			} else {
				_errors.add(error, $(field));
				_errors.displayFieldErrors($(field));
			}
	
	
	};
	
	
	this.geoCode = function(button) {

		var parent		= $(button).up('.row');
		var geocoder 	= new google.maps.Geocoder();
		var address 	= parent.down('.geocode');
		var note 		= parent.down('.note');

		var loading 	= parent.down('.loading');
		var button 		= parent.down('.button');

		note.update('');		

		if (!address.value.length) {
			note.update('<span class="error">Please enter an address</span>');		
			return;
		}
		
		loading.show();
		button.hide();

		geocoder.geocode( { 'address': address.value}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				setTimeout(function() {

					loading.hide();
					button.show();
	
					var lat 		= parent.down('.lat');
					var lng 		= parent.down('.long');
					
					lat.value = results[0].geometry.location.lat();
					lng.value = results[0].geometry.location.lng();
	
					note.update('('+results[0].geometry.location.lat()+','+results[0].geometry.location.lng()+')');		
				
				}, 1000);

			} else {
				note.update('<span class="error">Address could not be found. ('+status+')</span>');		
			}
		});
	
	};

	this.geoCodeClear = function(button) {

		var parent		= $(button).up('.row');
		var address 	= parent.down('.geocode');
		var note 		= parent.down('.note');
		var lat 		= parent.down('.lat');
		var lng 		= parent.down('.long');
						
		note.update('');

		address.value = '';
		lat.value = '';
		lng.value = '';
	
	};

	this.streetView = function(button) {
	
		var parent		= $(button).up('.row');
		var geocoder 	= new google.maps.Geocoder();
		var address 	= parent.down('.geocode');
		var note 		= parent.down('.note');

		var loading 	= parent.down('.loading');
		var button 		= parent.down('.button');

		note.update('');		

		if (!address.value.length) {
			note.update('<span class="error">Please enter an address</span>');		
			return;
		}
		
		loading.show();
		button.hide();

		geocoder.geocode( { 'address': address.value}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				setTimeout(function() {

					loading.hide();
					button.show();
	
                    var lat_field 		= parent.down('INPUT.lat');
                    var lng_field 		= parent.down('INPUT.long');
                    var heading_field 	= parent.down('INPUT.heading');
                    var pitch_field 	= parent.down('INPUT.pitch');
                    var zoom_field 		= parent.down('INPUT.zoom');

                    var map_area 	    = parent.down('.map');
        
                    map_area.show();
                    
                    lat_field.value     = results[0].geometry.location.lat();
                    lng_field.value     = results[0].geometry.location.lng();

                    if (!heading_field.value.length) heading_field.value = 270;
                    if (!zoom_field.value.length) zoom_field.value = 0;
                    if (!pitch_field.value.length) pitch_field.value = 10;

                    var startPos        = new google.maps.LatLng(lat_field.value,lng_field.value);

                    var panoramaOptions = {
                        position: startPos,
                        pov: {
                            heading: parseInt(heading_field.value),
                            zoom: parseInt(zoom_field.value),
                            pitch: parseInt(pitch_field.value)
                            }
                        };
                    

                    var panorama    = new google.maps.StreetViewPanorama(document.getElementById(map_area.getAttribute('id')), panoramaOptions);

                    var mapChangeEvent = function() {

                        lat_field.value         = panorama.getPosition().lat();
                        lng_field.value         = panorama.getPosition().lng();
                        heading_field.value     = Math.floor(panorama.getPov().heading);
                        pitch_field.value       = Math.floor(panorama.getPov().pitch);
                        zoom_field.value        = panorama.getPov().zoom;
            
                        var str = "<b>Lattitude:</b> "+lat_field.value+" / "	
                                + "<b> Longitude:</b> "+lng_field.value +" /"
                                + "<b> Heading:</b> "+heading_field.value+" /"
                                + "<b> Pitch:</b> "+pitch_field.value+" / "
                                + "<b> Zoom:</b> "+zoom_field.value;
              
                        note.update(str);
        
                    };

                    google.maps.event.addListener(panorama, 'position_changed', mapChangeEvent);
                    google.maps.event.addListener(panorama, 'pov_changed', mapChangeEvent);   
        
                    mapChangeEvent();
				
				}, 1000);

			} else {
				note.update('<span class="error">Address could not be found. ('+status+')</span>');		
			}
		});

        
	
	};


	// Private Functions

	var _getFieldTitle = function(element) {

			var title = element.getAttribute('title');
			
			if (!title || !title.length) {
				title = element.up('.form_row').down('LABEL').innerHTML;
				title = title.gsub(':', '');
			}
			
			return title;
	};


	var _errors = {
		
		bin 			: []
		, error_field 	: null		// Used to hold reference to field error div
	
		, reset: function() {
				this.bin = [];
		}

		, resetField: function(element) {
		
				if ((the_field = element.up('.field'))) {
		
					this.getErrorField(element);
					the_field.removeClassName('has_error');
				
				}
		}

		, add: function(msg, elem) {
				this.bin.push(msg);
				if (elem != null) elem.up('.field').addClassName('has_error');
		}
		
		, displayFieldErrors: function(field) {
		
				if (this.bin.length == 0) return;

				this.error_field.show();			
				this.bin.each(function(e) {
					this.error_field.insert({bottom: '<p>'+e+'</p>'});				
				}.bind(this));		
			
		}
		
		, displaySubmitErrors: function() {

				var msg = '<p>ERROR</p>';
				msg += '<p>There was a problem with your form. Please correct and submit again.</p>';
				msg += '<p><ul>';
				
				(this.bin.uniq()).each(function(e) {
					msg += '<li>'+e+'</li>';
				});

				msg += '</ul></p>';
	
				if (typeof(TNDR.Modal) != "undefined") {
					TNDR.Modal.add(msg, 'error', false, true).show();
				} else {
					alert('AlertDialog missing! \n\n'+msg);
				}
				
		}
		
		, getErrorField: function(element) {
				
				var field 			= element.up('.field');
				var err				= field.childElements().grep(new Selector('.error'));

				if (!err.length) {
					// Doesn't exist, insert it
					field.insert({bottom: '<div class="error"> </div>'});
					var err			= field.childElements().grep(new Selector('.error'));
				}

				// Ok, got it
				this.error_field = err[0];
			
				// Empty it out & hide
				this.error_field.hide();
				this.error_field.update('');

		}

	};
	

};


/** --------------------------------------------------
 * Typology FORM ACTIONS
 */

TNDR_FormActions = function(form_class) {

	// Private Variables
	
	var _form_class				= form_class;
	var _validate_field_classes = $A(['required', 'validate_email', 'confirm', 'enable_tabs']);

	var _pattern_numeric		= /[^0-9\s]/;
	var _pattern_alpanumeric	= /[^a-z0-9\s]/;
	var _pattern_file_name		= /[^a-z0-9_\-\s]/i;
	
	var _dupe_path		 		= '/ajax/dupeNode';
	
	var _sort_items				= [];
	
	// Private Methods

	var _init = function() {

			$$('.'+_form_class).each(function(f) {
				this.applyActions(f);
			}.bind(this));

	}.bind(this);


	var _transform = {
	
		fileName: function(elem) {
				if (!$F(elem)) return;
				elem.value = $F(elem).toLowerCase().gsub(' ', '_').gsub(_pattern_file_name, '');
		}		
		, numeric: function(elem) {
				if (!$F(elem)) return;
				elem.value = $F(elem).gsub(_pattern_numeric, '');
		}		
		, alphaNumeric: function(elem) {
				if (!$F(elem)) return;
				elem.value = $F(elem).gsub(_pattern_alpanumeric, '');
		}		
		, lowerCase: function(elem) {
				if (!$F(elem)) return;
				elem.value = $F(elem).toLowerCase();
		}		
			

	};


	var _toggleHide = function(element) {

		var _toggleHideAction = function(elements, action) {
	
			if (elements == null) return;
	
			elements.split(',').each(function(element) {
	
				element_children = $(element).select('INPUT', 'SELECT');
				
				switch(action) {
				
					case 'show':
						$(element).show();
						element_children.invoke('enable');
						break;
					case 'hide':
						$(element).hide();
						element_children.invoke('disable');
						break;
					default:
						break;
				}	
			
			});
		
		}

		var hideif 	= element.getAttribute('hideif') || null;
		var showif 	= element.getAttribute('showif') || null;

		var tohide 	= element.getAttribute('tohide') || null;
		var toshow 	= element.getAttribute('toshow') || null;

		var current	= $F(element);

		if (showif && showif.length) {

			showif = showif.split(',');

			if (showif.include(current)) {
				// Show value matched
				if (tohide) _toggleHideAction(tohide, 'hide');
				if (toshow) _toggleHideAction(toshow, 'show');
			} else {
				if (tohide) _toggleHideAction(tohide, 'show');
				if (toshow) _toggleHideAction(toshow, 'hide');
			}				
			
		} else if (hideif && hideif.length) {

			hideif = hideif.split(',');

			/*
			console.log('hideif');
			console.log(hideif);
			
			console.log('current');
			console.log(current);
			*/

			if (hideif.include(current)) {
				// Hide value matched
				if (tohide) _toggleHideAction(tohide, 'hide');
				if (toshow) _toggleHideAction(toshow, 'show');
			} else {
				if (tohide) _toggleHideAction(tohide, 'show');
				if (toshow) _toggleHideAction(toshow, 'hide');
			}

		}
		
	};


	var _hideDupeIcon = function(){
		$$('.btn_dupe').each(function(b){
			b.down('A').hide();
		});
	};


	var _showDupeIcon = function(){
		$$('.btn_dupe').each(function(b){
			b.down('A').show();
		});
	};


	// Public Methods

	this.req_item_id			= '';
	this.req_item_path			= '';
	this.req_parent_id			= '';
	this.req_parent_path		= '';


	this.applyActions = function (f) {
	
			f = $(f);	

			f.select('.toggle_hide').each(_toggleHide.bind(this));

			f.select('.action_calendar').each(function(element) {
					Calendar.setup({dateField : element.getAttribute('id')});						
			});			

			f.select('TEXTAREA.allow_tabs').each(function(item) {
				item.observe("keydown", function(e, elem) {
					TNDR.Form.Actions.allowTabs(e, Event.element(e));
				});
			});
	
	};

	this.allowTabs = function(e, elem) {

			var tab 	= "\t";
			var tab_key	= 9;
		
			if (!e.shiftKey && (e.keyCode == tab_key)) {
		
				if (!elem.id) { alert('Error: enableTabs requires TEXTAREA to have an id spefified.'); return false; }			
		
				var scroll_pos	= elem.scrollTop;
				var cursor_pos	= (elem.selectionStart+1);
		
				// Cancel default event action
				e.returnValue 	= false;
		
				if (elem.setSelectionRange) { // Non-IE
					elem.value = elem.value.substring(0, elem.selectionStart) + tab + elem.value.substring(elem.selectionEnd, elem.value.length);
					setTimeout("$('"+elem.id+"').focus();", 0);
					elem.setSelectionRange(cursor_pos, cursor_pos);
				} else { // IE
					document.selection.createRange().text = tab;
				}
				
				// Renew scroll position
				elem.scrollTop = scroll_pos;	
			}

	};


	this.toggleFieldset = function(set) {
			$(set).toggleClassName('closed');
	};


	this.toggleSection = function(button) {
			var section = $(button).up('.form_section').toggleClassName('form_section_closed');
	};


	this.onFormClick = function(evnt) {
			
			// console.log('TNDR_FormActions::onFormClick');

			var elem = Event.element(evnt);

			// Toggle hide
			
			if (elem.hasClassName('form_input_checkbox') 
				|| elem.hasClassName('form_input_radio')) {
				evnt.stopPropagation();
				var box = elem.down('input');
				if (box.hasClassName('toggle_hide')) _toggleHide(box);
			} else if (elem.hasClassName('form_select_option')) {
				var list = elem.up('.form_select').down('INPUT');
				if (list.hasClassName('toggle_hide')) _toggleHide(list);
			}
	
	
	};

	
	this.onFormChange = function(frm, val, elem) {

			//console.log('TNDR_FormActions::onFormChange');
		
			switch (elem.type) {
			
				case 'textarea':

					// Resize Textareas
//					this.textfield.resize(element);	

					break;

				case 'text':
						
					// Transforms
					if (elem.hasClassName('transform_filename')) _transform.fileName(elem);

					if (elem.hasClassName('transform_numeric')) _transform.numeric(elem);

					if (elem.hasClassName('transform_alpha_numeric')) _transform.alphaNumeric(elem);

					if (elem.hasClassName('transform_lowercase')) _transform.lowerCase(elem);
					
					break;
		
				default:
					return;
					break;
			
			}			

	};

	this.toggleHideStatic = function(elements, action) {

			if (elements == null) return;
	
			elements.split(',').each(function(element) {
	
				element_children = $(element).select('INPUT', 'SELECT');
				
				switch(action) {
				
					case 'show':
						$(element).show();
						element_children.invoke('enable');
						break;
					case 'hide':
						$(element).hide();
						element_children.invoke('disable');
						break;
					default:
						break;
				}	
			
			});
	
	};

	this.dupeTemplate = function(button, node, child_class) {
			
			var template 	= $(node+'_template');
			var parent 	    = $(node+'_parent');
			var child		= button.up('.'+child_class);
			var children 	= parent.select('.'+child_class);			
			var html        = template.innerHTML;
			var new_pos     = children.length+1;
			
			html = html.split('%%').join(new_pos);

			child.insert({after: html});
			
			TNDR.Form.UI.applyStyles(node+'['+new_pos+']_node');

	};

	this.dupeStatic = function(button, parent_class, child_class, id_name) {
			
			var parent 		= button.up('.'+parent_class);
			var child		= button.up('.'+child_class);
			var children 	= parent.select('.'+child_class);

			var new_child	= child.toHTML();
			
			// Replace ids
			if (id_name) {
				var id_new		= id_name + (children.length + 1);
				var id_replace	= new RegExp(id_name.replace(/([\[\]])/g,'\\$1') + "[0-9]", 'g');
				var new_child	= new_child.replace(id_replace, id_new);
				
			}

			// Add to page
			child.insert({after: new_child});
		
			// Clear values, might be too agressive		
			if ($(id_new+'_node')) {
				$(id_new+'_node').select('INPUT').invoke('clear');
				$(id_new+'_node').select('.dupe_clear').invoke('update');
				// This messes up if there is any field elements inside, it will double up style.
				//TNDR.Form.UI.applyStyles(id_new+'_node');
			}


	};

	
	this.dupe = function(options) {

			// Set local variables
			this.req_item_id 		= options.item_id;
			this.req_item_path 		= options.item_path;
			this.req_parent_id 		= options.parent_id;
			this.req_parent_path 	= options.parent_path;	

			// How many of this element already exist?
			count = $A($$('DIV[path="'+options.item_path+'"]')).length;

			file_id 				= options.file_id ? options.file_id : '';

			url = ADMIN_PATH + MODULE + _dupe_path;

			_hideDupeIcon();

			new Ajax.Request(url, {
				method		: 'post'
				, parameters: {
					id 				: CURRENT_ID
					, item_id 		: TNDR.Form.Actions.req_item_id
					, item_path 	: TNDR.Form.Actions.req_item_path
					, parent_id 	: TNDR.Form.Actions.req_parent_id
					, parent_path	: TNDR.Form.Actions.req_parent_path					
					, file_id		: file_id
					, sortkey		: (count+1)
				}
				, onSuccess	: function(transport) {

					if ((insert_item = $(TNDR.Form.Actions.req_item_id + '_node'))) {

						var insert_id = TNDR.Form.Actions.generateRandom();
						insert_item.insert({after: '<div id="'+insert_id+'">' + transport.responseText+'</div>'});
						var added_content 	= $(insert_id).down();
						
					} else if (TNDR.Form.Actions.req_parent_id == 'fields[content][data]') {

						$('tab_content').insert({bottom: transport.responseText});					
						var siblings		= insert_parent.childElements();
						var added_content	= siblings[siblings.length - 1];

					} else {

						var insert_parent 		= $(TNDR.Form.Actions.req_parent_id + '_node');
						var insert_descendant 	= insert_parent.firstDescendant();
						if (insert_descendant.hasClassName('section_content')) {
							insert_parent = insert_descendant;
						}

						insert_parent.insert({bottom: transport.responseText});					

						var siblings		= insert_parent.childElements();
						var added_content	= siblings[siblings.length - 1];

					}

					_showDupeIcon();					
					
					TNDR.Form.UI.applyStyles(added_content);
					TNDR.Form.Actions.applyActions(added_content);
					
					widgInit();
					
				}
			});

	}

	this.generateRandom = function() {

			var length 		= 10;
			var text 		= '';
		    var possible 	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

			for( var i=0; i < length; i++ )
				text += possible.charAt(Math.floor(Math.random() * possible.length));

			return text;

	};


	this.remove = function(item_id){
			if (!confirm("Are you sure you want to delete this?")) return false;
			$(item_id + '_node').remove();
	};


	this.sort = function(options){

			// Set local variables
			this.req_item_id 		= options.item_id;
			this.req_item_path 		= options.item_path;
			this.req_parent_id 		= options.parent_id;
			this.req_parent_path 	= options.parent_path;	

			var sort_node 			= $(this.req_item_id + '_node');
			var parent_item 		= sort_node.up('DIV');

			if (_sort_items.include(this.req_item_path)) {
				// Turn off sorting
				
				Sortable.destroy(parent_item);
				_sort_items.pluck(this.req_item_path);

				parent_item.removeClassName('sorting');
				
			} else {

				_sort_items.push(this.req_item_path);
			
				parent_item.addClassName('sorting');
					
				new Effect.Highlight(parent_item,{startcolor:'#8dffb3', endcolor:'#f6f6f6', duration:1.0});
	
				Sortable.create(parent_item, {tag:'div'});
			
			}
			
	};


	// Call Constructor - should happen last
	_init();

};
