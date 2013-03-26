(function($){
	
	$(document).ready(function(){

		$('.edit').click(function(){

			var section = $(this).closest('.section');
			$('div.form', section).slideToggle('slow');

		});

		$('form.confirm').submit(function(e){

			if(confirm("Are you sure?")!=true) {
				e.preventDefault();
			}
		});
	
		// Auto-populate month and year drop downs
		showMonths();
		showYears();
		
		// Automatically add the autocomplete='off' attribute to all the input fields
		$("#dlps_stripe input").attr("autocomplete", "off");
		
		// Sanitize and validate all input elements
		$("#dlps_stripe input").blur(function(){
			var input = $(this);
			validate(input);
		});
		
		// Bind to the submit for the form
	    $('#dlps_stripe').submit(function(event) {
	    	
	    	var form = $(this);

	    	// If the token has already been fetched, then
	    	//	submit the form.
	    	//
	    	if( $('#token', form).length > 0 ) {
	    		return true;
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
	    	params['name'] = $('.cname', form).val();
	    	params['number'] 	= $('.card', form).val();
	    	params['cvc']		= $('.cvc', form).val();
	    	params['exp_month'] = $('#exp_month', form).val();
	    	params['exp_year']	= $('#exp_year', form).val();
	    	
	        progress('Validating card data…');
	        
			// Send the card data securely to Stripe and get the
			//	token representing the card.
			var stripePublishable = $(".pubkey", form).val();
			Stripe.setPublishableKey(stripePublishable);
	        Stripe.createToken(params, function(status, response){
	                	        	
			    unlock_form(form);
			    if (response.error) {
			    	// Show the error and unlock the form.
			    	progress(response.error.message);
			    } else {

			    	// Add the token to the form and re-submit
			    	//
				    form.append("<input id='token' name='token' type='hidden' value='" + response['id'] + "' />");
				    form.submit();
			    }
			});

	    	return false;
	    });
	});
	
	// Helper function to display progress messages.
	function progress(msg, form){
		$('#dlps_progress').html(msg);
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
	
	// Validation helpers.
	function validateForm(form) {
		var isValid = true;
		$("input", form).each(function(){
			if(!validate($(this))) {
				return false;
			}
		});
		return true;
	}
	function validate(elem) {
		var error = $("#dlps_validation_error");
		var dd = elem.closest('dd');
		var dt = dd.prev();
		var label = $('label', dt).html(); 
		var value = $.trim(elem.val());
		if(!value.length){
			error.html(label + ' required.').show();
			return false;
		}
		error.hide();
		return true;
	}

	// Automatically populate the month and year selections.
	function showMonths() {
		var months = $("#exp_month"),
			month = new Date().getMonth() + 1;
		for(var i=1;i<=12;i++){
			months.append($("<option value='"+(i<10?"0":"")+i+"' "+(month===i ? "selected" : "")+">"+(i<10?"0":"")+i+"</option>"));
		}
	}
	function showYears() {
		var years = $("#exp_year"),
		    year = new Date().getFullYear();
		
		for (var i = 0; i < 12; i++) {
		    years.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
		}
	}

})(jQuery);