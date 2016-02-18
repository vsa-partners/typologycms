/**
 * Typology Modal JS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com 
 *
 */


document.observe('dom:loaded', function() {

	TNDR.Modal = new TNDR_Modal();

	TNDR.Modal.addInitialMessages();

});

TNDR_Modal = function() {

	// Private Variables
	var T						= TNDR;
	var _alerts 				= '';
	var _type					= '';
	var _fade_duration			= 4;
	var _fade_delay				= 5;
	var _fade_effect			= null;
	var _do_fade				= true;
	var _button					= null;
	var _is_ie					= (navigator.userAgent.toLowerCase().indexOf('msie') + 1);
	
	var _modal_id				= 'tndr_modal';
	var _overlay_id				= 'tndr_overlay';	
	
	var _modal_title			= false;
	
	/* SIMPLE, Keep for backup.
	var _modal_html		 		= '<div id="'+_overlay_id+'" style="display: none;"> </div>'
								+ '<div id="'+_modal_id+'" style="display: none;">'
								+ '<div class="close"><a href="#" onclick="TNDR.Modal.hide(); return false;">X</a></div>'
								+ '<div class="inner"> </div>'
								+ '</div>';
	*/
	
	var _modal_html				= '<div id="'+_overlay_id+'" style="display: none;"> </div>'
								+ '<div id="'+_modal_id+'" style="display: none;">'
								+ '<table cellpadding="0" cellspacing="0" border="0">'
									+'<tr>'
									+'<td colspan="3" class="dialog_close">'
										+ '<a href="#" onclick="TNDR.Modal.hide(); return false;">X</a>'
									+'</td>'
									+'</tr>'
									+'<tr>'
									+'<td class="dialog_border border_tl"> </td>'
									+'<td class="dialog_border border_t"> </td>'
									+'<td class="dialog_border border_tr"> </td>'
									+'</tr>'
									+'<tr>'
									+'<td class="dialog_border border_l"> </td>'
									+'<td class="dialog_content">'
										+ '<div class="inner"> </div>'
									+'</td>'
									+'<td class="dialog_border border_r"> </td>'
									+'</tr>'
									+'<tr>'
									+'<td class="dialog_border border_bl"> </td>'
									+'<td class="dialog_border border_b"> </td>'
									+'<td class="dialog_border border_br"> </td>'
									+'</tr>'
								+'</table></div>';
	
	var _button_html			= '<div class="button_row"><button type="button" class="button button_outline" onclick="TNDR.Modal.hide();"><span>OK</span></button></div>';
	
	var _default_frame_w		= 400;
	var _default_frame_h		= 400;
	var _default_frame_scroll	= 'no';
	
	// Private functions

	var _init = function() {

			// Insert Modal HTML in DOM
			$$('BODY').invoke('insert', {bottom: _modal_html});
			
			// Check for any modal messages that need to be displayed
	};


	this.addInitialMessages = function() {

			if (typeof(MODAL_MESSAGES) == 'object') {
			
				MODAL_MESSAGES.each(function(msg) {
					this.add(msg, 'message', null, true);
				}.bind(this));
				
				this.show();				
			
			}

	};
	

	this.add = function(message, type, fade, button) {	

			_type = type;

			_alerts += '<div class="alert_'+type+'">'+message+'</div>';

			if (fade != undefined) 		_do_fade = fade;
			if (button != undefined) 	_button = button;
			
			return this;

	};

	this.showHTML = function(html) {
			this.add(html, 'html', false).show();
	};

	this.showImage = function(image) {

			if (!image.length) return;
			
			this.add('<img src="' + image + '"/>', 'message', false).show();
	};


	this.showFrame = function(url, w, h, s) {
	
			var width 		= w || _default_frame_w; 
			var height 		= h || _default_frame_h; 	
			var scroll 		= s || _default_frame_scroll; 	

			dialog_content 	= '<iframe width="'+width+'" height="'+height+'" frameborder="0" scrolling="'+scroll+'" name="dialog_iframe" class="dialog_iframe" src="'+url+'"> </iframe>';

			_do_fade		= false;
	
			this.toggleCloseBtn(true);
			this.doShow(dialog_content);

	};


	this.show = function() {		
			
			if (! _alerts.length) return;
	
			this.doShow('<div class="content">'+_alerts+'</div>');

			// Add listener to modal to close modal. Only do this for alerts.
			if (_type != 'html') {
				$(_modal_id).observe('click', this.hide.bindAsEventListener(this));		
			}

	};

	
	this.doShow = function(content) {

			// Set the button
			if (_button) content += _button_html;

			$(_modal_id).show().down('.inner').update(content).focus();
			$(_overlay_id).show();

			// Activate event listeners for closing modal
			$(_overlay_id).observe('click', this.hide.bindAsEventListener(this));		
			document.observe('keydown', function(e) {
				if (e.keyCode != Event.KEY_RETURN && e.keyCode != Event.KEY_ESC) return;
				TNDR.Modal.hide();
			});

			// If enabled, automaticlaly fade out the modal after a specified period
			if (_do_fade === true && Effect) {
				_fade_effect = Effect.Fade($('alert_box'), {delay:_fade_delay, duration: _fade_duration, afterFinish: function() {
					TNDR.Modal.hide();
				}});
			}
			
			// If this is IE6 we need to hide all the select items, they will show through
			this.toggleSelects('hide');
			
			this._reset();

	};

	
	this.hide = function() {
	
			if (_fade_effect) _fade_effect.cancel();	

			$(_modal_id).hide().down('.inner').update('');
			$(_overlay_id).hide();

			// Reset local variables
			_alerts = '';
			this.toggleCloseBtn(false);
	
			// If this is IE6 we need to unhide all select items
			this.toggleSelects('show');
			
			// Cancel all event listeners
			Event.stopObserving(document, 'keydown');
			Event.stopObserving($(_overlay_id));
			Event.stopObserving($(_modal_id));
			
			_button = false;
	};

	this._reset = function() {
		
			this._modal_title = false;
	
	}


	// Simple helpers

	this.toggleCloseBtn = function(show) {
		
			if (show) {
				$(_modal_id).addClassName('window');
			} else {
				$(_modal_id).removeClassName('window');
			}
	
	};
	this.toggleSelects = function(visibility){
			if (!_is_ie) return;
			$$('SELECT').invoke(visibility);
	};

	this.title = function(string){
			
			return this;
	};

	// Call Constructor - should happen last
	_init();

};