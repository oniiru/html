(function($){
	$(document).ready(function(){
	
		// Hide the receipt
		$(".stripe-payment-receipt").hide();
	
		// Auto-populate month and year drop downs
		showMonths();
		showYears();
		
		// Automatically add the autocomplete='off' attribute to all the input fields
		$(".stripe-payment-form input").attr("autocomplete", "off");
		
		// Sanitize and validate all input elements
		$(".stripe-payment-form input").blur(function(){
			var input = $(this);
			sanitize(input);
			validate(input);
		});
		
		$('.stripe-payment-form .amountShown').blur(function(){
			var form = $(this).closest('form');
			var val_in_cents = Math.floor($(this).val()*100);
			$('.amount', form).val(val_in_cents);
		});
		
		// Initial validation of the amount
		$('.stripe-payment-form .amountShown').blur();
		
		// Bind to the submit for the form
	    $('.stripe-payment-form').submit(function(event) {
	    	
	    	var form = $(this);
		    var data = form.serializeArray();
					
			// Set the public key for use by Stripe.com
			var stripePublishable = $(".pubkey", form).val();
			Stripe.setPublishableKey(stripePublishable);
	    
	    	// Check for configuration errors
	    	if($('.stripe-payment-config-errors', form).length>0) {
	    		alert('Fix the configuration errors before continuing.');
	    		return false;
	    	}    	
	    
			// Lock the form so no change or double submission occurs
			lock_form(form);    
	    
	    	// Trigger validation
	    	if(!validateForm(form)) {
	    		// The form is not valid…exit early
	    		unlock_form(form);
	    		return false;
	    	}
	   	
	    	// Get the form values
	    	var params = {};
	    	if($('.cardName', form).length != 0) {
	    		params['name'] = $('.cardName', form).val();
	    	} else {
	    		params['name'] 		= $('.fname', form).val() + ' ' + $('.lname', form).val();
	    	}
	    	params['number'] 	= $('.cardNumber', form).val();
	    	params['cvc']		= $('.cardCvc', form).val();
	    	params['exp_month'] = $('.cardExpiryMonth', form).val();
	    	params['exp_year']	= $('.cardExpiryYear', form).val();
	    	
	        // Get the charge amount and convert to cents
	        var amount = $('.amount', form).val();
	        	
	        // Validate card information using Stripe.com.
	        //	Note: createToken returns immediately. The card
	        //	is not charged at this time (only validated).
	        //	The card holder info is HTTPS posted to Stripe.com
	        //	for validation. The response contains a 'token'
	        //	that we can use on our server.
	        progress('Validating card data…', form);
	        
	        
	        Stripe.createToken(params, function(status, response){
	                	        	
			    if (response.error) {
			    	// Show the error and unlock the form.
			    	progress(response.error.message, form);
			    	unlock_form(form);
			    	return false;
			    }
			    
			    // Collect additional info to post to our server.
			    //	Note: We are not posting any card holder info.
			    //	We only include the 'token' provided by Stripe.com.
			    var charge = {};
			    for(var i=0;i<data.length;i++){
			    	var name = data[i].name;
			    	var val = data[i].value;
			    	charge[name] = val;
			    }
			    charge['token']			= response['id'];
			    charge['action']		= 'stripe_plugin_process_card';
			    progress('Submitting charge…', form);
			    var url = stripe_blog_url + '/wp-admin/admin-ajax.php';
			    $.post(url, charge, function(response){
			    	// Try to parse the response (expecting JSON).
			    	try {
			    		response = JSON.parse(response);
			    	} catch (err) {
			    		if(window.console && window.console.log) {
			    			console.log(err, response);
			    		}
			    		// Invalid JSON.
			    		if(!$(response).length) {
			    			response = { error: 'Server returned empty response during charge attempt'};
			    		} else {
			    			response = {error: 'Server returned invalid response:<br /><br />' + response};
			    		}
			    	}

			    	if(window.console && window.console.log) {
				    	console.log(response);
			    	}
			    				    				    				    	  	
			    	if(response['success']){
			    		// Card was successfully charged. Replace the form with a
			    		//	dynamically generated receipt.
			    		showReceipt(response, form);
	   		    		progress('success', form);
			    	} else {
			    		// Show the error.
			    		progress('Error - ' + response['error'], form);
			    	}
			    	// Unlock the form.
			    	unlock_form(form);
			    });
	        });
	        
	        // Do not submit the form.
	        return false;
	    });
	});
	
	// Show the receipt
	function showReceipt(response, form) {
		var formWrap = form.closest('.stripe-form-wrap');
		formWrap.hide();
		var rcpt = formWrap.nextAll(".stripe-payment-receipt").hide();
		var html = rcpt.html();
		for(var propName in response){
			var token = '{' + propName + '}';
			var val = response[propName];
			html = html.replace(new RegExp(token, 'g'), val);
		}
		rcpt.html(html).show();
	}
	
	// Lock and unlock the form. This prevents changes or 
	//	double submissions during payment processing.
	function lock_form(form) {
		$("input", form).not('.disabled').attr("disabled", "disabled");
		$("select", form).attr("disabled", "disabled");
		$("button", form).attr("disabled", "disabled");
	}
	function unlock_form(form) {
		$("input", form).not('.disabled').removeAttr("disabled");
		$("select", form).removeAttr("disabled");
		$("button", form).removeAttr("disabled");
	}
	
	// Helper function to display progress messages.
	function progress(msg, form){
		$('.stripe-payment-form-row-progress span.message', form).html(msg);
	}
	
	// Validation helpers.
	function validateForm(form) {
		var isValid = true;
		$("input", form).each(function(){
			sanitize($(this));
			isValid = validate($(this)) && isValid;
		});

		// Check password confirmations
		if( $("#pword1").length != 0 ) {
			var pword1 = $("#pword1", form).val();
			var pword2 = $("#pword2", form).val();
			if(pword1 != pword2 ) {
				var row = $('#pword2').closest('.stripe-payment-form-row');
				$('.error', row).html('Not a match');
				isValid = false;
			}
		}

		return isValid;
	}
	function sanitize(elem) {
		var value = $.trim(elem.val());
		if(elem.hasClass("number")){
			value = value.replace(/[^\d]+/g, '');
		}
		if(elem.hasClass("amountShown")){
	        value = value.replace(/[^\d\.]+/g, '');
	        if(value.length) value = parseFloat(value).toFixed(2);
		}
		elem.val(value);
	}
	function validate(elem) {
		var row = elem.closest('.stripe-payment-form-row');
		var error = $('.error', row);
		var value = $.trim(elem.val());
		if(elem.hasClass("required") && !value.length){
			error.html('Required.');
			return false;
		}
		if(elem.hasClass("amountShown") && value<0.50){
			error.html('Minimum charge is $0.50');
			return false;
		}
		if(elem.hasClass("email") && !validateEmail(value)) {
			error.html('Invalid email.');
			return false;
		}
		error.html('');
		return true;
	}
	function validateEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}
	
	// Automatically populate the month and year selections.
	function showMonths() {
		var months = $(".card-expiry-month"),
			month = new Date().getMonth() + 1;
		for(var i=1;i<=12;i++){
			months.append($("<option value='"+(i<10?"0":"")+i+"' "+(month===i ? "selected" : "")+">"+(i<10?"0":"")+i+"</option>"));
		}
	}
	function showYears() {
		var years = $(".card-expiry-year"),
		    year = new Date().getFullYear();
		
		for (var i = 0; i < 12; i++) {
		    years.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
		}
	}
})(jQuery);