(function($){

	$(document).ready(function(){

		$('.datepicker').datepicker({
			dateFormat: 'MM d, yy',
			showButtonPanel: true
		});

		if( $('#level_id').length > 0 ) {

			updatePlans();
			$('#level_id').change(function(){
				updatePlans();
			});

			$('#plan_id').change(function(){
				updateAmount();
			});
		}
	});

	function updatePlans() {

		var val = $('#level_id').val();
		if(val == -1){
			$('#plan_id').html('<option value="-1">None</option>');
			return;
		}
		var option = $('#level_id option[value="' + val + '"]');
		var desc = option.data('desc');
		$('#level_desc').val(desc);

		var plans = option.data('plans');
		$('#plan_id').html('');
		if(plans) {
			$.each(plans, function(i, obj){
				var label = obj.name + ' ($' + obj.amount + ' / ' + obj.period + ' / ';
				if( obj.stripe_plan_id ) {
					label += 'recurring)';
				} else {
					label += 'single)';
				}
				$('#plan_id').append('<option value="' + obj.id + '">' + label + '</option>');
			});
		}

		updateAmount();
	}

	function updateAmount() {

		// Based upon the selected level fetch the current plans.
		var val = $('#level_id').val();
		var option = $('#level_id option[value="' + val + '"]');
		var plans = option.data('plans');
		var form = $('#level_id').closest('form');

		// Fetch the selected plan
		if( $('#plan_id').length > 0 ) {
			var plan_id = $('#plan_id').val();
			var plan = null;
			if(plans) {
				for(var i=0;i<plans.length;i++){
					if(plans[i].id==plan_id){
						plan = plans[i];
					}
				}
			}
			if(plan==null){
				alert("Plan not found! Amount not updated.")
				return;
			}

			// Update the amount
			var amount = plan.amount;
			$('.amount').val( parseInt( amount * 100 ) );
			$('.amountShown').val( parseFloat(amount).toFixed(2) );

			// Update the stripe plan data
			$('#plan', form).remove();
			if( plan.stripe_plan_id ) {
				$(form).append('<input id="stripe_plan" type="hidden" name="plan" value="' + plan.stripe_plan_id +  '" />');
			} else {
				$("#stripe_plan", form).remove();
			}
		}
	}

})(jQuery);