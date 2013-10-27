jQuery(document).ready(function() {
	
	jQuery("#epo-queue").sortable();
	jQuery("#select_ptype").val(0);
	
	jQuery('.save_epo').click(function(e){
		var arrayData = jQuery('#epo-queue').sortable('toArray');
		arrayData = jQuery.toJSON(arrayData);
		
		jQuery.post("admin-ajax.php", { 
			action: "epo_save",
			jsonArr: arrayData,
			cat: jQuery('#select_cat option:selected').val(),
			'cookie': encodeURIComponent(document.cookie)	
		}, function(str) { 
			jQuery("#epo-output").fadeIn();
			jQuery("#epo-output").html(str);
			jQuery("#epo-output").fadeOut(5000);
		});
	});
	
	jQuery('#select_ptype').change(function(){
		if(jQuery('#select_ptype').val() == 0) {
			var selectedType = '<em>Please select from the left menu the post type you would like to custom sort.</em>';
		} else {
			var selectedType = '<span>' + jQuery('#select_ptype :selected').text() + '</span>';
		}
		jQuery("#selected-ptype-display").html(selectedType);
		
		jQuery.post("admin-ajax.php", { 
			action: "epo_switch_type",
			ptype: jQuery('#select_ptype').val(),
			'cookie': encodeURIComponent(document.cookie)	
		}, function(str) { 
			jQuery("#epo-queue").html(str);
		});
		
		jQuery.post("admin-ajax.php", {
			action: "epo_populate_cats",
			ptype: jQuery('#select_ptype').val(),
			'cookie': encodeURIComponent(document.cookie)
		}, function(str1) {
			jQuery("#select_cat").html(str1);
		});
	});
	
	jQuery("#select_cat").change(function(){
		jQuery.post("admin-ajax.php", {
			action: "epo_switch_cats",
			ptype: jQuery('#select_ptype').val(),
			cat: jQuery('#select_cat').val(),
			catName: jQuery('#select_cat option:selected').text(),
			'cookie': encodeURIComponent(document.cookie)
		}, function(str2) {
			jQuery("#epo-queue").html(str2);
		});
	});
	
});

