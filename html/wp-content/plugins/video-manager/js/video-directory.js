// JavaScript Document
//class fancyboxCustom 

jQuery(document).ready( function() {
	jQuery('.postbox h3.directory-name').click( function() {
		jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
	});

	jQuery('.show_iframe').fancybox({
		'onComplete':function(){
			var container = jQuery(jQuery(jQuery('#fancybox-content').html()).html());
			var content = jQuery(container).html();
			var containerID = jQuery(container).attr('id');
			if(content.length) {
				this.originalID = containerID;
				this.originalHTML = content;
			}
			var targetWidth = jQuery('#fancybox-content').innerWidth();
			var currentWidth = jQuery(content).attr('width');
			if(targetWidth<currentWidth) {
				var currentHeight = jQuery(content).attr('height');
				var q = targetWidth/currentWidth;
				var targetHeight = Math.round(q*currentHeight) + '';
				var heightPattern = new RegExp("height[\\s]*=[\\s]*[\\'\\\"]{1}" + currentHeight + "[\\'\\\"]{1}");
				content = content.replace(heightPattern, 'height="' + targetHeight + '"');
				var widthPattern = new RegExp("width[\\s]*=[\\s]*[\\'\\\"]{1}" + currentWidth + "[\\'\\\"]{1}");
				content = content.replace(widthPattern, 'width="' + targetWidth + '"');
				jQuery('#' + containerID).html(content);
			}
		},
		'onClosed': function(){
			if(this.originalHTML && this.originalID) {
				jQuery('#' + this.originalID).html(this.originalHTML);
			}
		}
	});
	
	jQuery('.show_jw_player').fancybox();
	
	jQuery( ".ion_tabs" ).tabs({
		create: function(event, ui){
			jQuery(this).wrap('<div class="ion-css" />');
		}
	});
	
//	jQuery("#update-0").fancybox({
//		'titlePosition'		: 'inside',
//		'transitionIn'		: 'none',
//		'transitionOut'		: 'none',
//		'overlayColor'		: '#000',
//		'overlayOpacity'	: 0.5
//	});
	
	jQuery('.noaccess').click(function(){
		var popup_id = jQuery(this).attr('href').split('-');
		var url = jQuery('#ajax-url').val();
		var content;
		
		popup_id = popup_id[1];
		content = jQuery('#no-access-content-' + popup_id).html();

		jQuery.fancybox({
			content: function(){
				return '<div id="no-access">' + content + '<div class="error"></div></div>';
			},
			onComplete : function(){
				jQuery('#email9999 button').bind('click', function(){
					var email = jQuery(this).siblings('input').val();
					if (!isValidEmailAddress_videomanager(email)){
						jQuery('.error').html('<strong>ERROR: </strong>Invalid email address!').show();
						jQuery(this).siblings('input').select();
						return false;
					};
					
					var user_type = jQuery('#user_type_' + popup_id).val();
					var postData = {
						action : 'video_manager_ajax_register',
						user_login : email, 
						user_email : email, 
						user_type : user_type,  
						video_manager_ajax : true
					};
					jQuery.post(url, postData, function(data){
						if (data.result){
							jQuery('.error').hide();
							alert(data.message);
							jQuery.fancybox.close();
							return true;
						} else {
							jQuery('.error').html(data.error).show();
							return false;
						}
					}, "json");
				})
			
			}
		});
	})
	
	jQuery('.emailvids').bind('click', function(){
		var url = jQuery('#ajax-url').val();
		var email = jQuery('#user_email').val();
		if (!isValidEmailAddress_videomanager(email)){
			alert('Invalid email address!');
			jQuery('#user_email').select();
			return false;
		};
					
		var user_type = jQuery('#user_type').val();
		var postData = {
			action : 'video_manager_ajax_register',
			user_login : email, 
			user_email : email, 
			user_type : user_type,  
			video_manager_ajax : true
		};
		jQuery.ajax({
			type: 'post',
			dataType: "json",
			url: url,
			data: postData
		}).done(function( msg ) {
			if (msg.result == true){
				alert(msg.message);
			} else {
				alert(msg.error);
			}
		});
	})
	
	function isValidEmailAddress_videomanager(emailAddress) {
		var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
		return pattern.test(emailAddress);
	};
	
});