var tab_index = 0;

jQuery(document).ready(function(){

	jQuery('#sel_use_tab').live('change', function(){
		var option = jQuery(this).val();
		if (option == 'yes'){
			jQuery('#no_use_tab_container').hide();
			jQuery('#popup_action_div').show();
			var item_type = new Array('video_directory', 'file_download', 'description');
			for(var i = 0; i < 3; i++){
				add_tab_item(item_type[i]);
			}
			jQuery('#use_tab_wrapper').show();
		} else {
			jQuery('#no_use_tab_container').show();
			jQuery('#use_tab_wrapper').hide();
			jQuery('#popup_action_div').hide();
		}

		jQuery('.add_element').live('change', function(){
			var content = jQuery('#' + jQuery(this).val()).html();
			if ((jQuery('#use_tab_container .tab_item').index(jQuery(this).parents('.tab_item'))) != 0){
				content += '<span class="remove_tab">Remove</span>';
			}
			jQuery(this).parent().siblings('.tab_content').html(content);
		})
		
		jQuery('.remove_tab').live('click', function(){
			jQuery(this).parents('.tab_item').hide('fast');
		})
		
		jQuery('.add_tab').live('click',function(){
			add_tab_item('video_directory');
		})

	})
	
	jQuery('.add_directory').live('click', function(){
		var subscript_txt = '';
		var button_color = '';
		var button_text = '';
		var user_type = '';
		var insert_subscription = '';
		var insert = '';
		var tab_header = '';
		var tab_title;
		var tab_content = '';
		var tab_counter = 0;
		var error = false;
		var subscription = jQuery('#popup_action').val();
		
		if (subscription != 'none'){
			subscript_txt = jQuery('#subscript_txt').val();
			button_color = jQuery('#button_color').val();
			button_text = jQuery('#button_text').val();
			if (subscription == 'email'){
				user_type = jQuery('#user_type').val();
			}
			insert_subscription += '[' + subscription + ' text="' + subscript_txt + '" color="' + button_color + '" btntext="' + button_text + '"';
			if (user_type != ''){
				insert_subscription += ' usertype="' + user_type + '"';
			}
			insert_subscription += ']<br /><br />';
		}

		tab_header = '[ion_tabset';
		tab_content = '';

		jQuery('.tab_item').each(function(){
			if (jQuery(this).is(':visible')){
				var tab_option = '';
				var show_duration = '';
				var count = 0;
				var selectedArray = new Array();
				var sub_insert = '';
				var require = jQuery(this).find('.chk_allow_member').is(':checked');
			
				tab_counter++;
				tab_title = jQuery(this).find('.txt_tab_title').val();
				if (tab_title == ''){
					tab_title = jQuery(this).find('.tmp_title').html();
				}
			
				tab_header += ' tab' + tab_counter + '="' + tab_title + '"';
				tab_option = jQuery(this).find('.add_element').val();
				sub_insert = '';
			
				switch(tab_option){
					case 'video_directory':
						if (jQuery(this).find('.chk_show_duration').is(':checked')){
							show_duration = 'duration="on"';
						}
					
						jQuery(this).find('.added_video_directory_list option').each(function() {
							selectedArray[count] = jQuery(this).val();
							count++;
						});
					
						if(selectedArray.length > 0) {
							sub_insert = '[videodirectory id=\"';
							sub_insert += selectedArray;
							sub_insert += '\" ' + show_duration + ']';
						} else {
							alert('Please add the Video Directory.');
							jQuery(this).find('.video_directory_list').focus();
							error = true;
							return false;
						}
						break;
					case 'file_download':
						var download_text = jQuery(this).find('.txtra_download_text').val();
						if (download_text == ''){
							alert('Please input the download content!');
							jQuery(this).find('.txtra_download_text').focus();
							error = true;
							return false;
						}
					
						var download_title = jQuery(this).find('.txt_download_title').val();
						if (download_title == ''){
							alert('Please input the download text!');
							jQuery(this).find('.txt_download_title').focus();
							error = true;
							return false;
						}
					
						var download_url = jQuery(this).find('.txt_download_url').val();
						if (download_url == ''){
							alert('Please input the download url!');
							jQuery(this).find('.txt_download_url').focus();
							error = true;
							return false;
						}
//						if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(download_url)) {
//						} else {
//							alert('Please input the valide url!');
//							jQuery(this).find('.txt_download_url').focus();
//							jQuery(this).find('.txt_download_url').select();
//							error = true;
//							return false;
//						}
						sub_insert = download_text + '<a href="' + download_url + '">' + download_title + '</a>';					
						break;
					case 'description':
						var description_text = jQuery(this).find('.txtra_description_text').val();
						if (description_text == ''){
							alert('Please input the description text!');
							jQuery(this).find('.txtra_description_text').focus();
							error = true;
							return false;
						}
						sub_insert = description_text;					
						break;
				}
				if (require){
					tab_content += '[ion_tab id="tab' + tab_counter + '" require="required"]' + sub_insert + '[/ion_tab]<br /><br />';
				} else {
					tab_content += '[ion_tab id="tab' + tab_counter + '"]' + sub_insert + '[/ion_tab]<br /><br />';
				}
			}
		})
		
		if (error) return false;
		
		tab_header += ']<br/><br/>';
		insert = tab_header + insert_subscription + tab_content + '[/ion_tabset]';
	
		returnToTinyMCE(insert);
	})

})

function file_upload(obj){
	var formdata = new FormData();
	var input = jQuery(obj).siblings('.file_names'), file_name;

	var i = 0, len = input[0].files.length, reader, file;
	
	if (len == 0){
		alert('Please choose the file!');
		return;
	}
	
	jQuery(obj).parent().find('.response').html("Uploading . . ."); 
	file = input[0].files[0];
	
	if ( window.FileReader ) {
		reader = new FileReader();
		reader.readAsDataURL(file);
	}
	if (formdata) {
		formdata.append("images[]", file);
	}
	
	if (formdata) {
		jQuery.ajax({
			url: ajax_url + '/' + 'file_upload.php',
			type: "POST",
			data: formdata,
			processData: false,
			contentType: false,
			dataType : 'json',
			success: function (res) {
				jQuery(obj).parent().find('.response').html(res.message); 
				file_name = res.file_name;
				jQuery(obj).parent().find('.txt_download_url').val(upload_url + '/' +  file_name);
			}
		});
	}	
}

function add_directory(obj){
	var from = jQuery(obj).parent().parent().find('.video_directory_list option:selected');
	var to  = jQuery(obj).parent().parent().find('.added_video_directory_list');
	if (to.find('option').length == 0){
		from.clone().appendTo(to);
	} else {
		var insert_flag = true;
		to.find('option').each(function(){
			if (from.val() == jQuery(this).val()){
				insert_flag = false;
				return;
			}
		});
		if (insert_flag){
			from.clone().appendTo(to);
		}
	}	
}

function remove_directory(obj){
	jQuery(obj).parent().parent().find('.added_video_directory_list option:selected').remove();
}

function up_item(obj){
	jQuery(obj).parent().parent().find('.added_video_directory_list option:selected').each(function(){
		jQuery(this).insertBefore(jQuery(this).prev());
	});
}
	
function down_item(obj){
	jQuery(obj).parent().parent().find('.added_video_directory_list option:selected').each(function(){
		jQuery(this).insertAfter(jQuery(this).next());
	});
}

function insert_select(obj){
	var insert = '';
	var selectedArray = new Array();
	var count = 0;
	var show_duration = '';

	if (jQuery(obj).parent().siblings('.video_directory').find('.chk_show_duration').is(':checked')){
		show_duration = 'duration="on"';
	}
	
	jQuery(obj).parent().siblings('.video_directory').find('.added_video_directory_list option').each(function() {
		selectedArray[count] = jQuery(this).val();
		count++;
	});

	if(selectedArray.length > 0) { // Check if array is not empty
		insert += '[videodirectory id=\"';
		insert += selectedArray;
		insert += '\" ' + show_duration + ']';
		returnToTinyMCE(insert);
	}
}

function add_tab_item(item_type){
	tab_index++;
	var tab_item = jQuery('<div class="tab_item"/>');
	var tab_controller = jQuery('<div class="tab_controller"/>');
	var tab_content = jQuery('<div class="tab_content video_directory"/>');

	var controller_text = '<hr/>';
	controller_text += '<h4 class="tmp_title">Tab' + tab_index + '</h4>';
	controller_text += jQuery('#tmp_use_tab_controller').html();
	controller_text = controller_text.replace(item_type + '"',  item_type + '" selected="selected"');
	tab_controller.html(controller_text);
	tab_controller.appendTo(tab_item);
	
	var content_text = jQuery('#' + item_type).html();
	if (tab_index != 1){
		content_text += '<span class="remove_tab">Remove</span>';
	}
	tab_content.html(content_text);
	
	tab_content.appendTo(tab_item);
	tab_item.appendTo(jQuery('#use_tab_container'));
}

function insertTabs() {
	var subscript_txt = '';
	var button_color = '';
	var button_text = '';
	var user_type = '';
	var insert_subscription = '';
	var subscription = jQuery('#popup_action').val();
	if (subscription != 'none'){
		subscript_txt = jQuery('#subscript_txt').val();
		button_color = jQuery('#button_color').val();
		button_text = jQuery('#button_text').val();
		if (subscription == 'email'){
			user_type = jQuery('#user_type').val();
		}
		insert_subscription += '[' + subscription + ' text="' + subscript_txt + '" color="' + button_color + '" btntext="' + button_text + '"';
		if (user_type != ''){
			insert_subscription += ' usertype="' + user_type + '"';
		}
		insert_subscription += ']<br /><br />';
	}

	var insert = '[ion_tabset tab1=\"Title 1\" tab2=\"Title 2\" tab3=\"Title 3\"]<br /><br />';
	insert += insert_subscription;
	insert += '[ion_tab id="tab1"]Content #1[/ion_tab]<br /><br />';
	insert += '[ion_tab id="tab2"]Content #2[/ion_tab]<br /><br />';
	insert += '[ion_tab id="tab3"]Content #3[/ion_tab]<br /><br />';
	insert += '[/ion_tabset]';
	
	returnToTinyMCE(insert);
}

function returnToTinyMCE(insertValue){
	if(window.tinyMCE) {
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, insertValue);
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}

function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function show_subscription(obj){
	if (jQuery(obj).val() == 'subscribebar'){
		jQuery('#button_setting').show();
		jQuery('#user_roles').hide();
	} else if (jQuery(obj).val() == 'email'){
		jQuery('#button_setting').show();
		jQuery('#user_roles').show();
	} else {
		jQuery('#button_setting').hide();
	}
}
