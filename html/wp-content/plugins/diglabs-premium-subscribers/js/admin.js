(function($){
	
	$('input[name="type"]').change(function(){

		$(".single, .recurring").toggle();
	})
})(jQuery);