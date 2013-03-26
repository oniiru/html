<div class="modalinner">
	<div class="container headerShort">
		<h3>Business Solutions</h3>
	</div>
	<div id="contact_modal_container" class="container content">
		<p class="blurb">
			Need a Multi-User account? Contact me for more details on business plans and discounts..
		</p>
		<ul class="contactOptions">
					<li class="phone">
						Call Me: <span>877.688.7563</span>
					</li>
					<li class="email">
						Email Me: <a href="mailto:Rohit@SolidWize.com"> Rohit@SolidWize.com</a>
					</li>
				</ul>
		<form action="" method="post" id="contact_form" class="noDisable">
			<div class="hiddenFields" style="display:none;">
				<input type="hidden" name="form-type" value="contact" />
				<input type="hidden" name="vid" id="vid2" value="" />
			</div>
			<div class="colLeft">
				<label>Your Name <span class="req">*</span></label>
				<input type="text" class="formfield required" name="name" value="" />
				<label>Email Address <span class="req">*</span></label>
				<input type="text" class="formfield required" name="email" value="" />
				<label>Phone Number</label>
				<input type="text" class="formfield" name="phone" value="" />
				<label>Company</label>
				<input type="text" class="formfield" value="" name="company" value="" />
			</div>
			<div class="colRight">
				<label>Message</label>				<textarea class="formfield" name="message" ></textarea>
				
				<input type="submit" name="submit" class="contact signupbutton" value="Submit"  />
			</div>
		</form>
	</div><a href="#" class="close">Close Modal</a>
	<div id="success">
		<h3>I have received your submission!</h3>
		<p>
			Thank you for contacting SolidWize. I will be in touch with you shortly.
		</p>
		<h3 class="follow">Follow Me:</h3>
		<div class="modal-share">
			<a href="http://twitter.com/solidwize" class="twitter" target="_blank">Twitter</a><a href="http://www.facebook.com/pages/SolidWize/144043889008178" target="_blank" class="facebook">Facebook</a>
		</div>
	</div>
	<script type="text/javascript">
		if( jQuery('#contact_form').length ){				formValidation.init();		jQuery('#contact_form div.hiddenFields').append('<input type="hidden" name="cerberus" value="1" id="cerberus" />');		window.validateForms['contact_form'] = new FormValidator('contact_form',{name: 'Name Required', email: 'Email Address Required'});			if ( jQuery('#contact_form input:hidden[name="cerberus"]').val() == '1' ) {			jQuery('#contact_form').submit(function(s){				s.preventDefault();	return false;			});			jQuery('#contact_form input[name="submit"]').click(function(e){				if(window.validateForms['contact_form'].validate()){					var submitButton = jQuery(this);					var loading = '<div id="processing_form"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/box/ajax-loader.gif" alt="Processing" /> Processing Submission...</div>';					submitButton.hide();					submitButton.parent().append(loading);												var success;					e.preventDefault();					var parameters = jQuery('#contact_form').serialize();					jQuery.ajax({						url:"<?php bloginfo('home');?>/solidwize/wp-admin/admin-ajax.php",						data:'action=contact_pro&'+ parameters,												type: 'POST',						complete: function(response){							var responseJSON = jQuery.parseJSON(response.responseText);							if( responseJSON['contactf'] == 202 ) {								jQuery('#processing_form').remove();								submitButton.show();															jQuery('#contact_modal_container').fadeOut(function(){									jQuery('#success').fadeIn();								});							} else { 								jQuery('#processing_form').remove();								submitButton.show();											}							jQuery(':input, #contact_form').not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');															}					});							return false;				}			});		}			}</script>
</div>