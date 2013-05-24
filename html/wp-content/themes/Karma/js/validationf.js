/**
 * Universal Form Validator Script - Based off of formvalidate.js - reprogrammed 
 * to be more autonymous, object oriented and JavaScript library independent. To 
 * run, just call this in whatever DOMReady event your library uses.
 * 
 * @author 		Dave Shepard
 * @updated_at	August 3, 2009
 * 
 * Classes:
 * ============================================================================
 * CLASS:			HTML TAG:				RESULTS:
 * noDisable		form					Prevent disabling of the submit button
 * required			input/select/textarea	Require this field
 * amount			input/select			Validate as a numerical amount
 * ignoreZeroVal	input/select			Ignore a zero value when validating an amount
 * group_*****		input[type="checkbox"]	Group checkboxes together
 * 
 * Class Parameters:
 * ============================================================================
 * @param {Object} form_elem Element ID String or an HTML Element
 * @param {Object} message_overrides JSON object containing field name attribute specific error messages
 */
function FormValidator(form_elem,message_overrides){
	this.messages = {
		// Default message, primarily used for text fields
		_default: "Please make sure you enter something for this field",

		// Invalid email address
		email: "Please make sure you enter a valid email (<em>user@domain.com</em>)",

		// Mismatch password and password confirmation
		password_confirm: "Your passwords do not match",

		// Single checkbox validation, primarily used for Terms and Conditions agreement
		checkbox: "Please make sure you have agreed to the above terms",

		// Checkbox group select
		checkbox_group: "Please make sure you select at least one option",

		// File field validation
		file: "Please make sure you choose a file to upload",

		// Amount validation
		amount: "Please enter a value greater than 0",

		// Drop-down validation
		select: "Please make a choice for this field",
		
		// Field NAME specific message overrides. Key name should match the "name" attribute of the field
		overrides: {
			phone: "Please enter a valid phone number"
		}
	}
	
	this.classes = {
		// Prevent submit disable on form tag
		no_disable: "noDisable",
		
		// Validate field as an amount
		amount: "amount",
		
		// Amount field which allows for values of 0
		ignore_zero: "ignoreZeroVal",
		
		// Checkbox group prefix
		group: "group_",
		
		// Required element
		required: "required",
		
		// Error class applied to error messages and element parents
		error: "error"
	}
		
	var self = this;
	
	this.getElements = function(){
		var elements = {
			inputs: this.form.getElementsByTagName("input"),
			selects: this.form.getElementsByTagName("select"),
			textareas: this.form.getElementsByTagName("textarea")
		}
		return elements;
	}
	
	this.getRequired = function(){
		var collection = [];
		for(elements in this.form_elements){
			var E = this.form_elements[elements];
			for(var i=0;i<E.length;i++){
				if(E[i].className.include(this.classes.required)){
					collection.push(E[i]);
				}
			}
		}
		return collection;
	}
	
	this.getSubmitButtons = function(){
		var collection = [];
		for(elements in this.form_elements){
			var E = this.form_elements[elements];
			for(var i=0;i<E.length;i++){
				if(E[i].type == "submit" || E[i].type == "image"){
					collection.push(E[i]);
				}
			}
		}
		return collection;
	}
	
	this.isEmail = function(e){
		return [e.className,e.name,e.id].join().toLowerCase().include('email');
	}
	this.isPasswordConfirm = function(e){
		return e.id.include("_confirm") && e.type == "password";
	}
	this.isGroupCheckbox = function(e){
		return e.className.include(this.classes.group) && e.type == "checkbox";
	}
	
	this.isValid = function(e){
		var valid = true;
		
		switch(e.nodeName) {
			case "INPUT":
			case "TEXTAREA":
				switch (e.type) {
					case "checkbox":
						// Check if checkbox has a group indicator and check against the group for at least one checked box
						if (!e.className.include(this.classes.group)) {
							valid = e.checked;
						}
						else {
							var eClass = e.className.split(" ");
							for (var i = 0; i < eClass.length; i++) {
								if (eClass[i].include(this.classes.group)) {
									var eGroupClass = eClass[i];
								}
							}
							
							for (var i = 0, eGroup = []; i < this.elements.inputs.length; i++) {
								var el = this.elements.inputs[i];
								if (el.className.include(eGroupClass)) {
									eGroup.push(el);
								}
							}
							
							var isChecked = false;
							for (var i = 0; i < eGroup.length; i++) {
								var el = eGroup[i];
								if (el.checked == true) {
									isChecked = true;
								}
							}
							
							if (isChecked == false) {
								valid = false;
							}
						}
					break;

					case "file":
					case "text":
					case "password":
					case "textarea":
					default:
						var value = e.value.trim();
						
						if (value.length > 0) {
							// Check for email validation
							if (this.isEmail(e)) {
								if (!value.match(/^\w+([\+\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/)) {
									valid = false;
								}
							}
							
							// Check for password validation
							if (e.type == "password") {
								if (e.id.include("_confirm")) {
									var pair_id = e.id.substr(0, e.id.indexOf("_confirm"));
									if (e.value != document.getElementById(pair_id).value) {
										valid = false;
									}
								}
							}
							
							// Check if this is a field that should take an amount
							if (e.className.include(this.classes.amount)) {
								var cleanValue = value.replace(/,/g, "");
								if (isNaN(cleanValue)) {
									valid = false;
								}
								else {
									// Check if this field should return false for zero values
									if (!e.className.include(this.classes.ignore_zero)) {
										if (parseFloat(cleanValue) === 0) {
											valid = false;
										}
									}
								}
							}
						}
						else {
							valid = false;
						}
					break;
						
				}
			break;
			
			case "SELECT":
				var value = e.value.trim();
				if (value.length > 0) {
					// Check if this is a field that should take an amount
					if (e.className.include(this.classes.amount)) {
						var cleanValue = value.replace(/,/g, "");
						if (isNaN(cleanValue)) {
							valid = false;
						}
						else {
							// Check if this field should return false for zero values
							if (!e.className.include(this.classes.ignore_zero)) {
								if (parseFloat(cleanValue) === 0) {
									valid = false;
								}
							}
						}
					}
				} else {
					valid = false;
				}
			break;
		}
		return valid;
	}
	
	this.hasError = function(e){
		return document.getElementById(e.name + "___error");
	}
	
	this.addError = function(e){
		var msg = this.messages._default;
		
		if(this.isEmail(e)) msg = this.messages.email;
		if(this.isPasswordConfirm(e)) msg = this.messages.password_confirm;
		if(e.type == "checkbox"){
			if (this.isGroupCheckbox(e)) {
				msg = this.messages.checkbox_group;
			} else {
				msg = this.messages.checkbox;
			}
		}
		if(e.type == "file") msg = this.messages.file;
		if(e.className.include(this.classes.amount)) msg = this.messages.amount;
		if(e.nodeName == "select") msg = this.messages.select;

		if(typeof(this.messages.overrides[e.name]) != "undefined"){
			msg = this.messages.overrides[e.name];
		}

		if(!e.parentNode.className.include(this.classes.error)) ___DOM.addClassName(e.parentNode,this.classes.error);
		
		if (!(err = this.hasError(e))) {
			var errorMsg = document.createElement("SPAN");
			errorMsg.id = e.name + "___error";
			errorMsg.className = this.classes.error;
			___DOM.insertAfter(e,errorMsg);
			errorMsg.innerHTML = msg;
		} else {
			err.innerHTML = msg;
		}
	}
	
	this.removeError = function(e){
		if(err = this.hasError(e)){
			if(e.parentNode.className.include(this.classes.error)) ___DOM.removeClassName(e.parentNode,this.classes.error);
			e.parentNode.removeChild(err);
		}
	}
	
	this.validate = function(silent){
		for(var i=0, errors = 0;i<this.required_elements.length;i++){
			var e = this.required_elements[i];
			if(!this.isValid(e)){
				errors++;
				if(!silent) this.addError(e);
			} else {
				this.removeError(e);
			}
		}
		
		return errors < 1;
	}
	
	this.assignEvents = function(){
		___DOM.addEvent(this.form,"submit",function(e){
			var ev = (!e) ? window.event : e;
			if(!self.validate()){
				ev.preventDefault ? ev.preventDefault() : ev.returnValue = false;
				return false;
			}
		});
		
		for(var i=0;i<this.required_elements.length;i++){
			var e = this.required_elements[i];
			switch(e.nodeName){
				case "INPUT":
					switch(e.type){
						case "password":							
						case "text":
							___DOM.addEvent(e,"blur",function(){
								if(this.value.trim().length > 0){
									if (!self.isValid(this)) {
										self.addError(this);
									} else {
										self.removeError(this);
									}
									self.controlSubmit();
								}
							});
							
							___DOM.addEvent(e,"keyup",function(){
								var element = this;
								if (this.timer) clearTimeout(element.timer);
								this.timer = setTimeout(function(){
									if (self.isValid(element)) {
										self.removeError(element);
									}
									self.controlSubmit();
								},100);
								return true;									
							});
						break;
						case "checkbox":
							___DOM.addEvent(e,"change",function(){
								if (!self.isValid(this)) {
									self.addError(this);
								} else {
									self.removeError(this);
								}
								self.controlSubmit();
							});
						break;
						case "file":
							var element = e;
							___FileTimers[element.name] = setInterval(function(){
								if(self.isValid(element)){
									if(___FileTimers[element.name]) clearInterval(___FileTimers[element.name]);
									self.removeError(element);
								}
								self.controlSubmit();
							},500);
							
							___DOM.addEvent(e,"blur",function(){
								if(!self.isValid(this)){
									self.addError(this);
								} else {
									self.removeError(this);
								}
								self.controlSubmit();
							});
						break;
					}
				break;
				case "SELECT":
					___DOM.addEvent(e,"blur",function(){
						if (!self.isValid(this)) {
							self.addError(this);
						} else {
							self.removeError(this);
						}
						self.controlSubmit();
					});
				break;
				case "TEXTAREA":
					___DOM.addEvent(e,"blur",function(){
						if (!self.isValid(this)) {
							self.addError(this);
						} else {
							self.removeError(this);
						}
						self.controlSubmit();
					});
					
					___DOM.addEvent(e,"keyup",function(){
						var element = this;
						if (this.timer) clearTimeout(element.timer);
						this.timer = setTimeout(function(){
							if (self.isValid(element)) {
								self.removeError(element);
							}
							self.controlSubmit();
						},100);

						return true;									
					});
				break;
			}
		}
	}
	
	this.controlSubmit = function(){
		if (!this.form.className.include(this.classes.no_disable)) {
			if (!this.validate(true)) {
				for (var i = 0; i < this.submit_buttons.length; i++) {
					var e = this.submit_buttons[i];
					e.disabled = true;
					___DOM.addClassName(e,'disabled');
					if (e.type == "image") {
						oldSrc = e.src;
						if (oldSrc.include("_i.")) {
							e.src = oldSrc.replace("_i.", "_d.");
						}
					}
				}
			}
			else {
				for (var i = 0; i < this.submit_buttons.length; i++) {
					var e = this.submit_buttons[i];
					e.disabled = false;
					___DOM.removeClassName(e,'disabled');
					if (e.type == "image") {
						oldSrc = e.src;
						e.src = oldSrc.replace("_d.", "_i.");
					}
				}
			}
		}
	}

	if(typeof(message_overrides) == "object"){
		this.messages.overrides = message_overrides;
	}
	this.form = typeof(form_elem) == "string" ? document.getElementById(form_elem) : form_elem;
	this.form_elements = this.getElements();
	this.required_elements = this.getRequired();
	this.submit_buttons = this.getSubmitButtons();
	this.assignEvents();
	this.controlSubmit();
}

var ___FileTimers = {};


/**
 * DOM Extension Utility functions for handy methods normally included in a JavaScript library
 */
var ___DOM = {
	addClassName: function(elem,c){
	    if (!elem.className.indexOf(c) != -1) {
	    	elem.className += (elem.className ? ' ' : '') + c;
		}
	},
	
	removeClassName: function(elem,c){
		elem.className = elem.className.replace(new RegExp("(^|\\s+)" + c + "(\\s+|$)"), ' ').trim();
	},
	
	insertAfter: function(elem,n){
		elem.parentNode.insertBefore(n,elem.nextSibling);
		return n;
	},
	
	/**
	 * Cross-browser event compatibility script courtesy of sstchur
	 * Original post: http://blog.stchur.com/2006/10/12/fixing-ies-attachevent-failures/
	 */
	evtHash: [],
	
	ieGetUniqueID: function(_elem){
		if (_elem === window) { return 'theWindow'; }
		else if (_elem === document) { return 'theDocument'; }
		else { return _elem.uniqueID; }
	},

	addEvent: function(_elem, _evtName, _fn, _useCapture){
		if(typeof _useCapture == 'undefined') _useCapture = false;
		if (typeof _elem.addEventListener != 'undefined') {
	  		_elem.addEventListener(_evtName, _fn, _useCapture);
		}
		else if (typeof _elem.attachEvent != 'undefined') {
			var key = '{FNKEY::obj_' + ___DOM.ieGetUniqueID(_elem) +
				'::evt_' + _evtName + '::fn_' + _fn + '}';
			var f = ___DOM.evtHash[key];

			if (typeof f != 'undefined') { return; }

			f = function(){
				_fn.call(_elem);
			};

			___DOM.evtHash[key] = f;
			_elem.attachEvent('on' + _evtName, f);

			// attach unload event to the window to clean up possibly IE memory leaks
			window.attachEvent('onunload', function(){
				_elem.detachEvent('on' + _evtName, f);
			});
			
			key = null;
			//f = null;   /* DON'T null this out, or we won't be able to detach it */
		}
		else {
			_elem['on' + _evtName] = _fn;
		}
   },

	removeEvent: function(_elem, _evtName, _fn, _useCapture){
		if(typeof _useCapture == 'undefined') _useCapture = false;
		if (typeof _elem.removeEventListener != 'undefined') {
			_elem.removeEventListener(_evtName, _fn, _useCapture);
		}
		else if (typeof _elem.detachEvent != 'undefined') {
			var key = '{FNKEY::obj_' + ___DOM.ieGetUniqueID(_elem) +
				'::evt' + _evtName + '::fn_' + _fn + '}';
			var f = ___DOM.evtHash[key];

			if (typeof f != 'undefined') {
				_elem.detachEvent('on' + _evtName, f);
				delete ___DOM.evtHash[key];
			}
			
			key = null;
			//f = null;   /* DON'T null this out, or we won't be able to detach it */
		}
	}
}

if(typeof(String.prototype.trim) != "function") {
	String.prototype.trim = function(){
		return this.replace(/^\s+|\s+$/g, "");
	}
}
if(typeof(String.prototype.include) != "function") {
	String.prototype.include = function(str){
		return this.indexOf(str) != -1;
	}
}