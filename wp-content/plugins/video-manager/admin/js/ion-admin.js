// JavaScript Document

jQuery(document).ready( function() {
	jQuery('.postbox h3').click( function() {
		jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
	});
	
	jQuery('.video_options_btn').click( function() {
		var id = jQuery(this).attr('name');
		jQuery(jQuery('#show-option-'+id).toggleClass('hidden'));
		return false;
	});
	
	jQuery( "#videos-to-edit" ).sortable({
		placeholder: "ion-sortable-placeholder"
	});
	
	jQuery('.video-edit').click( function() {
		jQuery(jQuery(this).closest('li').get(0)).toggleClass('open-sesame');
	});
	
	jQuery('.video-item-delete').click( function() {
		var id = jQuery(this).attr('title');
		
		jQuery(jQuery(this).closest('li').get(0)).remove();
		jQuery('#for-deletion').prepend('<input name="for_deletion[]" type="hidden" value="'+id+'" />');
		jQuery('#update-msg').html('Click <strong>Update</strong> for changes to take effect.');
		
		return false;
	});
	
	jQuery('#popup-action').bind('change', function(){
		var popup_action = jQuery(this).val();

		if (popup_action == 'sign'){
			jQuery('#popup-button-setting').show();
			jQuery('#popup-user-roles').hide();
		} else if (popup_action == 'email'){
			jQuery('#popup-button-setting').show();
			jQuery('#popup-user-roles').show();
		} else {
			jQuery('#popup-button-setting').hide();
			jQuery('#popup-user-roles').hide();
		}
		popup_add_content(popup_action);
		
		function popup_add_content(action){
			var content = jQuery('#popup-content').val();
			var defaultSignupContent = jQuery('#popup-default-signup-content').val();
			var defaultEmailContent = jQuery('#popup-default-email-content').val();
			
			if (action == 'sign'){
				if (content.indexOf('email9999') > 0){
					content = content.substr(0, content.indexOf('email9999') - 10);
				}
				if (content.indexOf('signup9999') < 0){
					content = content + "\n" + defaultSignupContent;
				}
			} else if (action == 'email'){
				if (content.indexOf('signup9999') > 0){
					content = content.substr(0, content.indexOf('signup9999') - 10);
				}
				if (content.indexOf('email9999') < 0){
					content = content + "\n" + defaultEmailContent;
				}
			} else {
				if (content.indexOf('signup9999') > 0){
					content = content.substr(0, content.indexOf('signup9999') - 10);
				}
				if (content.indexOf('email9999') > 0){
					content = content.substr(0, content.indexOf('email9999') - 10);
				}
			}
			jQuery('#popup-content').val(content);
		}
	})
	
	jQuery('#popup-setting-restore').bind('click', function(){
		var defaultContent = jQuery('#popup-default-content').val();
		jQuery('#popup-content').val(defaultContent);
		jQuery('#popup-button-setting').hide();
		jQuery('#popup-user-roles').hide();
		jQuery('#popup-action').val('none');
	})

	jQuery('#select_popups').bind('change', function(){
		jQuery('#switch_popup').val('on');
		jQuery('#popup-setting_form').submit();
	})
	
	jQuery('a#popup-content-preview').fancybox({
		content: function(){
			var content = jQuery('#popup-content').val();
			var color = jQuery('#popup-button-color').val();
			var text = jQuery('#popup-button-text').val();
			var signin = jQuery('#sign-in').val();
			var signup = jQuery('#sign-up').val();
			content = '<div id="no-access">' + content + '</div>';
			content = content.replace(/\[color\]/gi, color);
			content = content.replace(/\[button text\]/gi, text);
			content = content.replace(/\[signin url\]/gi, signin);
			content = content.replace(/\[signup url\]/gi, signup);
			
			return content;
		},
		'hideOnContentClick': true
	});
});