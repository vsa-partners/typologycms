/** --------------------------------------------------
 *	Patched version of Form.Observer
 *	Returns updated element as third param.
 */
 
Form.Observer = Class.create(Abstract.TimedObserver, {
	getValue: function() {
		return Form.serialize(this.element, true);
	}
	, execute: function() {
		var value = this.getValue();
		for (var prop in value) {
			if (value[prop] != this.lastValue[prop]) {
				this.callback(this.element, Object.toQueryString(value), this.element.down('[name="'+ prop +'"]'));        
				this.lastValue = value;
				break;
			}
		}
	}
});

