/*
	InputFocus for jQuery (version 0.9)
	Copyright (c) 2009 Simone D'Amico
	http://www.simonedamico.it/2009/08/jquery-inputfocus-evidenziare-i-campi-input-e-textarea-di-una-form/
	
	Licensed under the MIT license:
		http://www.opensource.org/licenses/mit-license.php

	Any and all use of this script must be accompanied by this copyright/license notice in its present form.
*/
jQuery.fn.inputfocus=function(params){params=jQuery.extend({focus_class:"focus",value:""},params);this.each(function(){jQuery(this).focus(function(){jQuery(this).addClass(params.focus_class);this.value=(this.value==params.value)?'':this.value;});jQuery(this).blur(function(){jQuery(this).removeClass(params.focus_class);this.value=(this.value=='')?params.value:this.value;});});return this;};