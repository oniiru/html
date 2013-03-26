<?php
/*
Template Name: Checkout Template
*/
?>

<?php get_header(); ?>
			<div id="content" class="clearfix row-fluid">
			
				<div id="main" class="span12 clearfix" role="main">

					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
													
						</header> <!-- end article header -->
					
						<section class="post_content">
						
							<?php		
								global $gateway, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $pmpro_levels, $tospage, $pmpro_currency_symbol, $pmpro_show_discount_code, $pmpro_error_fields;
								global $discount_code, $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth,$ExpirationYear;	

								//set to true via filter to have Stripe use the minimal billing fields
								$pmpro_stripe_lite = apply_filters("pmpro_stripe_lite", false);	
							?>

							<form class="pmpro_form" action="<?php if(!empty($_REQUEST['review'])) echo pmpro_url("checkout", "?level=" . $pmpro_level->id); ?>" method="post">
								<?php if($pmpro_msg) 
									{
								?>
									<div id="pmpro_message" class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
								<?php
									}
									else
									{
								?>
									<div id="pmpro_message" class="pmpro_message" style="display: none;"></div>
								<?php
									}
								?>
	
								<?php if($pmpro_review) { ?>
									<p>Almost done. Review the membership information and pricing below then <strong>click the "Complete Payment" button</strong> to finish your order.</p>
								<?php } ?>
		
								<table id="pmpro_pricing_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
								<thead>
									<tr>
										<th colspan="2">
											Membership Level
										</th>						
									</tr>
								</thead>
								<tbody>                
									<tr>
										<td width="100%">
											<table id="membership_types_container_table">
											<?php
												//Monthly/Yearly membership switch
												global $membershipGroups;
												foreach($membershipGroups as $gid=>$linkedMemberships) {
													if(in_array($pmpro_level->id, $linkedMemberships)) {
														foreach($linkedMemberships as $mid) {
															$levelDetails = get_pmpro_level_details($mid);
															//If membership was found, generate table rows with appropriate inputs and details specified
															if(!empty($levelDetails)) {
											?>
																<tr class="awesomeradio">
																	<td>
																		<input type="radio" name="level" id="level<?php echo $mid ?>" value="<?php echo $mid ?>" <?php echo (($mid==$pmpro_level->id)?' checked="checked"':'') ?>/>
																	</td>
																	<td>
																		<label class="awesomeradiotext" for="level<?php echo $mid ?>"><?php echo $levelDetails->name ?></label>
																		<?php if(!empty($levelDetails->description)): ?>
																		<br/>
																		<small><?php echo apply_filters("the_content", stripslashes($levelDetails->description)); ?></small>
																		<?php endif; ?>
																		<div id="membership_<?php echo $mid ?>_details" style="display: none;">
																		<?php echo pmpro_getLevelCost($levelDetails); ?>
																		<?php echo pmpro_getLevelExpiration($levelDetails); ?>
																		</div>
																	</td>
																</tr>
											<?php
															}
														}
													}
												}
												if($discount_code && pmpro_checkDiscountCode($discount_code)) {
													echo '<tr><td colspan="2">The <strong>' . $discount_code . '</strong> code has been applied to your order.</td></tr>';
												}
											?>
											</table>				
											<?php do_action("pmpro_checkout_after_level_cost"); ?>				
				
											<?php if($pmpro_show_discount_code) { ?>
				
												<?php if($discount_code && !$pmpro_review) { ?>
													<p id="other_discount_code_p" class="pmpro_small"><a id="other_discount_code_a" href="#discount_code">Click here to change your discount code</a>.</p>
												<?php } elseif(!$pmpro_review) { 
													if($pmpro_requirebilling){ ?>
													<p id="other_discount_code_p" class="pmpro_small">Have a discount code? <a id="other_discount_code_a" href="#discount_code">Click here</a>.</p>
													<?php } ?>
												<?php } elseif($pmpro_review && $discount_code) { ?>
													<p><strong>Discount Code:</strong> <?php echo $discount_code?></p>
												<?php } ?>
				
											<?php } ?>
										</td>
										<td width="60%">
											<div id="pmpro_level_cost" style="text-align: center; clear: both; padding: 20px 30px;">
												<?php echo pmpro_getLevelCost($pmpro_level)?>
												<?php echo pmpro_getLevelExpiration($pmpro_level)?>
											</div>
										</td>
									</tr>
									<?php if($pmpro_show_discount_code) { ?>
									<tr id="other_discount_code_tr" class="awesomediscount" style="display: none;">
										<td colspan="2">
											<div class="opaque_placeholder">
												<label for="discount_code">Discount Code </label>
												<input class="input <?php echo pmpro_getClassForField("discount_code");?>" id="discount_code" name="discount_code" type="text" size="20" value="<?php echo esc_attr($discount_code)?>" />
												<input type="button" id="discount_code_button" class="btn btn-success" name="discount_code_button" value="Apply" style="margin-left:20px; margin-top:6px"/>
											</div>
										</td>
									</tr>
									<tr class="awesomediscount">
										<td colspan="2">
											<p id="discount_code_message" class="pmpro_message" style="display: none;"></p>
										</td>
									</tr>
									<?php } ?>
								</tbody>
								</table>
	
								<?php if($pmpro_show_discount_code) { ?>
								<script>
									//update discount code link to show field at top of form
									jQuery('#other_discount_code_a').attr('href', 'javascript:void(0);');
									jQuery('#other_discount_code_a').click(function() {
										jQuery('.awesomediscount').show();
										jQuery('#other_discount_code_p').hide();		
										jQuery('#other_discount_code').focus();
									});
		
									//update real discount code field as the other discount code field is updated
									jQuery('#other_discount_code').keyup(function() {
										jQuery('#discount_code').val(jQuery('#other_discount_code').val());
									});
									jQuery('#other_discount_code').blur(function() {
										jQuery('#discount_code').val(jQuery('#other_discount_code').val());
									});
		
									//update other discount code field as the real discount code field is updated
									jQuery('#discount_code').keyup(function() {
										jQuery('#other_discount_code').val(jQuery('#discount_code').val());
									});
									jQuery('#discount_code').blur(function() {
										jQuery('#other_discount_code').val(jQuery('#discount_code').val());
									});
		
									//applying a discount code
									jQuery('#other_discount_code_button').click(function() {
										var code = jQuery('#other_discount_code').val();
										var level_id = jQuery('#level').val();
												
										if(code)
										{									
											//hide any previous message
											jQuery('.pmpro_discount_code_msg').hide();
				
											//disable the apply button
											jQuery('#other_discount_code_button').attr('disabled', 'disabled');				
				
											jQuery.ajax({
												url: '<?php echo home_url()?>',type:'GET',timeout:2000,
												dataType: 'html',
												data: "action=applydiscountcode&code=" + code + "&level=" + level_id + "&msgfield=pmpro_message",
												error: function(xml){
													alert('Error applying discount code [1]');
												
													//enable apply button
													jQuery('#other_discount_code_button').removeAttr('disabled');
												},
												success: function(responseHTML){
													if (responseHTML == 'error')
													{
														alert('Error applying discount code [2]');
													}
													else
													{
														jQuery('#pmpro_message').html(responseHTML);
													}		
						
													//enable invite button
													jQuery('#other_discount_code_button').removeAttr('disabled');										
												}
											});
										}																		
									});
									jQuery("input[type='radio']").change(function(){
									    if(jQuery(this).is(":checked")){
									        jQuery(this).closest().addClass("greenBackground"); 
									    }
										else{
									        jQuery(this).closest().removeClass("greenBackground");  
									    }
									});
								</script>
								<?php } ?>
								
								<table id="pmpro_user_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
								<thead>
									<tr>
										<th>
											<?php echo ($pmpro_requirebilling)?'Basic Information':'Account Information'; ?>
										</th>						
									</tr>
								</thead>
								<tbody>                
									<tr>
										<td>
											<div style="float: left; width:100%">
												<div class="opaque_placeholder" style="width:365px">
													<label for="bfirstname">First Name <?php if (pmpro_getClassForField("bfirstname")=='pmpro_required') echo '&nbsp;*' ?></label>
													<input id="bfirstname" name="bfirstname" type="text" class="input" size="30" value="<?php echo esc_attr($bfirstname)?>" /> 
												</div>
												<div class="opaque_placeholder" style="width:365px;margin-left:20px">
													<label for="blastname">Last Name <?php if (pmpro_getClassForField("blastname")=='pmpro_required') echo '&nbsp;*' ?></label>
													<input id="blastname" name="blastname" type="text" class="input" size="30" value="<?php echo esc_attr($blastname)?>" /> 
												</div>
											</div>
								<?php 
								/**
								 * Remove section "borders" if a visitor checks out for free
								 */
								if($pmpro_requirebilling):
								?>
										</td>
									</tr>
								</tbody>
								</table>
								<?php if(!$skip_account_fields && !$pmpro_review) { ?>
								<table id="pmpro_user_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
								<thead>
									<tr>
										<th>
											<div class="paymenttitles">Account Information</div>
										</th>						
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
								<?php } ?>	
								<?php else: ?>
											<div class="opaque_placeholder">
												<label for="bphone">Phone <?php if (pmpro_getClassForField("bphone") == 'pmpro_required') echo '&nbsp;*' ?></label>
												<input id="bphone" name="bphone" type="text" class="input" size="30" value="<?php echo esc_attr($bphone) ?>" /> 
												<?php echo formatPhone($bphone); ?>
											</div>
								<?php endif; ?>
								<?php if(!$skip_account_fields && !$pmpro_review) { ?>
											<div style="display: none;">
												<label for="username">Username</label>
												<input id="username" name="username" type="text" class="input <?php echo pmpro_getClassForField("username");?>" size="30" value="<?php echo esc_attr($username)?>" /> 
											</div>
				
											<?php
												do_action('pmpro_checkout_after_username');
											?>
											
											<div  class="opaque_placeholder" style="width:365px">
												<label for="bemail">E-mail Address <?php if (pmpro_getClassForField("bemail")=='pmpro_required') echo '&nbsp;*' ?></label>
												<input id="bemail" name="bemail" type="text" class="input" size="30" value="<?php echo esc_attr($bemail)?>" /> 
											</div>
											<?php
												$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);					
												if($pmpro_checkout_confirm_email)
												{
												?>
												<div>
													<label for="bconfirmemail">Confirm E-mail</label>
													<input id="bconfirmemail" name="bconfirmemail" type="text" class="input <?php echo pmpro_getClassForField("bconfirmemail");?>" size="30" value="<?php echo esc_attr($bconfirmemail)?>" /> 

												</div>	                        
												<?php
												}
												else
												{
												?>
												<input type="hidden" name="bconfirmemail_copy" value="1" />
												<?php
												}
											?>			
				
											<?php
												do_action('pmpro_checkout_after_email');
											?>
				
											<div class="opaque_placeholder" style="width:365px;margin-left:20px">
												<label for="password">Password <?php if (pmpro_getClassForField("password")=='pmpro_required') echo '&nbsp;*' ?></label>
												<input id="password" name="password" type="password" class="input" size="30" value="<?php echo esc_attr($password)?>" /> 
											</div>
											<?php
												$pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);					
												if($pmpro_checkout_confirm_password)
												{
												?>
												<div>
													<label for="password2">Confirm Password</label>
													<input id="password2" name="password2" type="password" class="input <?php echo pmpro_getClassForField("password2");?>" size="30" value="<?php echo esc_attr($password2)?>" /> 
												</div>
												<?php
												}
												else
												{
												?>
												<input type="hidden" name="password2_copy" value="1" />
												<?php
												}
											?>
				
											<?php
												do_action('pmpro_checkout_after_password');
											?>
				
											<div class="pmpro_hidden">
												<label for="fullname">Full Name</label>
												<input id="fullname" name="fullname" type="text" class="input <?php echo pmpro_getClassForField("fullname");?>" size="30" value="" /> <strong>LEAVE THIS BLANK</strong>
											</div>				

											<div class="pmpro_captcha">
											<?php 																								
												global $recaptcha, $recaptcha_publickey;										
												if($recaptcha == 2 || ($recaptcha == 1 && pmpro_isLevelFree($pmpro_level))) 
												{											
													echo recaptcha_get_html($recaptcha_publickey, NULL, true);						
												}								
											?>								
											</div>
				
											<?php
												do_action('pmpro_checkout_after_captcha');
											?>
				
										</td>
									</tr>
								</tbody>
								</table>   
								<?php } elseif($current_user->ID && !$pmpro_review) { ?>                        	                       										
		
									<p>You are logged in as <strong><?php echo $current_user->user_login?></strong>. If you would like to use a different account for this membership, <a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']);?>">log out now</a>.</p>
								<?php } ?>
	
								<?php					
									if($tospage && !$pmpro_review)
									{						
									?>
									<table id="pmpro_tos_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0">
									<thead>
									<tr>
										<th><?php echo $tospage->post_title?></th>
									</tr>
								</thead>
									<tbody>
										<tr class="odd">
											<td>								
												<div id="pmpro_license">
							<?php echo wpautop($tospage->post_content)?>
												</div>								
												<input type="checkbox" name="tos" value="1" /> I agree to the <?php echo $tospage->post_title?>
											</td>
										</tr>
									</tbody>
									</table>
									<?php
									}
								?>
	
								<?php do_action("pmpro_checkout_boxes"); ?>	
		
								<?php if(pmpro_getOption("gateway", true) == "paypal" && empty($pmpro_review)) { ?>
									<table id="pmpro_payment_method" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0" <?php if(!$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
									<thead>
										<tr>
											<th>Choose Your Payment Method</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<div>
													<input type="radio" name="gateway" value="paypal" <?php if(!$gateway || $gateway == "paypal") { ?>checked="checked"<?php } ?> />
														<a href="javascript:void(0);" class="pmpro_radio">Checkout with a Credit Card Here</a> &nbsp;
													<input type="radio" name="gateway" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
														<a href="javascript:void(0);" class="pmpro_radio">Checkout with PayPal</a> &nbsp;					
												</div>
											</td>
										</tr>
									</tbody>
									</table>
								<?php } ?>
	
								<?php  if(empty($pmpro_stripe_lite) || $gateway != "stripe") {  ?>
								<table id="pmpro_billing_address_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0" <?php if(!$pmpro_requirebilling || $gateway == "paypalexpress" || $gateway == "paypalstandard") { ?>style="display: none;"<?php } ?>>
								<thead>
									<tr>
										<th>Billing Address</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<div>
												<label for="bfirstname">First Name</label>
												<input id="bfirstname" name="bfirstname" type="text" class="input <?php echo pmpro_getClassForField("bfirstname");?>" size="30" value="<?php echo esc_attr($bfirstname)?>" /> 
											</div>	
											<div>
												<label for="blastname">Last Name</label>
												<input id="blastname" name="blastname" type="text" class="input <?php echo pmpro_getClassForField("blastname");?>" size="30" value="<?php echo esc_attr($blastname)?>" /> 
											</div>					
											<div>
												<label for="baddress1">Address 1</label>
												<input id="baddress1" name="baddress1" type="text" class="input <?php echo pmpro_getClassForField("baddress1");?>" size="30" value="<?php echo esc_attr($baddress1)?>" /> 
											</div>
											<div>
												<label for="baddress2">Address 2</label>
												<input id="baddress2" name="baddress2" type="text" class="input <?php echo pmpro_getClassForField("baddress2");?>" size="30" value="<?php echo esc_attr($baddress2)?>" />
											</div>
				
											<?php
												$longform_address = apply_filters("pmpro_longform_address", false);
												if($longform_address)
												{
											?>
												<div>
													<label for="bcity">City</label>
													<input id="bcity" name="bcity" type="text" class="input <?php echo pmpro_getClassForField("bcity");?>" size="30" value="<?php echo esc_attr($bcity)?>" /> 
												</div>
												<div>
													<label for="bstate">State</label>																
													<input id="bstate" name="bstate" type="text" class="input <?php echo pmpro_getClassForField("bcity");?>" size="30" value="<?php echo esc_attr($bstate)?>" /> 					
												</div>
												<div>
													<label for="bzipcode">Zip/Postal Code</label>
													<input id="bzipcode" name="bzipcode" type="text" class="input <?php echo pmpro_getClassForField("bzipcode");?>" size="30" value="<?php echo esc_attr($bzipcode)?>" /> 
												</div>					
											<?php
												}
												else
												{
												?>
												<div>
													<label for="bcity_state_zip">City, State Zip</label>
													<input id="bcity" name="bcity" type="text" class="input <?php echo pmpro_getClassForField("bcity");?>" size="14" value="<?php echo esc_attr($bcity)?>" />, 
													<?php
														$state_dropdowns = apply_filters("pmpro_state_dropdowns", false);							
														if($state_dropdowns === true || $state_dropdowns == "names")
														{
															global $pmpro_states;
														?>
														<select name="bstate" class=" <?php echo pmpro_getClassForField("bstate");?>">
															<option value="">--</option>
															<?php 									
																foreach($pmpro_states as $ab => $st) 
																{ 
															?>
																<option value="<?=$ab?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?=$st?></option>
															<?php } ?>
														</select>
														<?php
														}
														elseif($state_dropdowns == "abbreviations")
														{
															global $pmpro_states_abbreviations;
														?>
															<select name="bstate" class=" <?php echo pmpro_getClassForField("bstate");?>">
																<option value="">--</option>
																<?php 									
																	foreach($pmpro_states_abbreviations as $ab) 
																	{ 
																?>
																	<option value="<?=$ab?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?=$ab?></option>
																<?php } ?>
															</select>
														<?php
														}
														else
														{
														?>	
														<input id="bstate" name="bstate" type="text" class="input <?php echo pmpro_getClassForField("bstate");?>" size="2" value="<?php echo esc_attr($bstate)?>" /> 
														<?php
														}
													?>
													<input id="bzipcode" name="bzipcode" type="text" class="input <?php echo pmpro_getClassForField("bzipcode");?>" size="5" value="<?php echo esc_attr($bzipcode)?>" /> 
												</div>
												<?php
												}
											?>
				
											<?php
												$show_country = apply_filters("pmpro_international_addresses", false);
												if($show_country)
												{
											?>
											<div>
												<label for="bcountry">Country</label>
												<select name="bcountry" class=" <?php echo pmpro_getClassForField("bcountry");?>">
													<?php
														global $pmpro_countries, $pmpro_default_country;
														foreach($pmpro_countries as $abbr => $country)
														{
															if(!$bcountry)
																$bcountry = $pmpro_default_country;
														?>
														<option value="<?php echo $abbr?>" <?php if($abbr == $bcountry) { ?>selected="selected"<?php } ?>><?php echo $country?></option>
														<?php
														}
													?>
												</select>
											</div>
											<?php
												}
												else
												{
												?>
													<input type="hidden" name="bcountry" value="US" />
												<?php
												}
											?>
											<div>
												<label for="bphone">Phone</label>
												<input id="bphone" name="bphone" type="text" class="input <?php echo pmpro_getClassForField("bphone");?>" size="30" value="<?php echo esc_attr($bphone)?>" /> 
												<?php echo formatPhone($bphone); ?>
											</div>		
											<?php if($skip_account_fields) { ?>
											<?php
												if($current_user->ID)
												{
													if(!$bemail && $current_user->user_email)									
														$bemail = $current_user->user_email;
													if(!$bconfirmemail && $current_user->user_email)									
														$bconfirmemail = $current_user->user_email;									
												}
											?>
											<div>
												<label for="bemail">E-mail Address</label>
												<input id="bemail" name="bemail" type="text" class="input <?php echo pmpro_getClassForField("bemail");?>" size="30" value="<?php echo esc_attr($bemail)?>" /> 
											</div>
											<?php
												$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);					
												if($pmpro_checkout_confirm_email)
												{
												?>
												<div>
													<label for="bconfirmemail">Confirm E-mail</label>
													<input id="bconfirmemail" name="bconfirmemail" type="text" class="input <?php echo pmpro_getClassForField("bconfirmemail");?>" size="30" value="<?php echo esc_attr($bconfirmemail)?>" /> 

												</div>	                        
												<?php
													}
													else
													{
												?>
												<input type="hidden" name="bconfirmemail_copy" value="1" />
												<?php
													}
												?>
											<?php } ?>    
										</td>						
									</tr>											
								</tbody>
								</table>                   
								<?php } ?>
	
								<?php do_action("pmpro_checkout_after_billing_fields"); ?>		
	
								<?php
									$pmpro_accepted_credit_cards = pmpro_getOption("accepted_credit_cards");
									$pmpro_accepted_credit_cards = explode(",", $pmpro_accepted_credit_cards);
									if(count($pmpro_accepted_credit_cards) == 1)
									{
										$pmpro_accepted_credit_cards_string = $pmpro_accepted_credit_cards[0];
									}
									elseif(count($pmpro_accepted_credit_cards) == 2)
									{
										$pmpro_accepted_credit_cards_string = $pmpro_accepted_credit_cards[0] . " and " . $pmpro_accepted_credit_cards[1];
									}
									elseif(count($pmpro_accepted_credit_cards) > 2)
									{
										$allbutlast = $pmpro_accepted_credit_cards;
										unset($allbutlast[count($allbutlast) - 1]);
										$pmpro_accepted_credit_cards_string = implode(", ", $allbutlast) . ", and " . $pmpro_accepted_credit_cards[count($pmpro_accepted_credit_cards) - 1];
									}
								?>
								<?php if($pmpro_requirebilling): ?>
								<table id="pmpro_payment_information_fields" class="pmpro_checkout top1em" width="100%" cellpadding="0" cellspacing="0" border="0" <?php if(!$pmpro_requirebilling || $gateway == "paypalexpress" || $gateway == "paypalstandard") { ?>style="display: none;"<?php } ?>>
								<thead>
									<tr>
										<th><div class="paymenttitles">Payment Information</div><span class="pmpro_thead-msg">We accept <?php echo $pmpro_accepted_credit_cards_string?>.</span></th>
									</tr>
								</thead>
								<tbody>                    
									<tr valign="top">		
										<td>	
											<?php
												$sslseal = pmpro_getOption("sslseal");
												if($sslseal)
												{
												?>
													<div class="pmpro_sslseal"><?php echo stripslashes($sslseal)?></div>
												<?php
												}
											?>
											<?php if(empty($pmpro_stripe_lite) || $gateway != "stripe") { ?>
											<div>
												<label for="CardType">Card Type</label>
												<select id="CardType" <?php if($gateway != "stripe") { ?>name="CardType"<?php } ?> class=" <?php echo pmpro_getClassForField("CardType");?>">
													<?php foreach($pmpro_accepted_credit_cards as $cc) { ?>
														<option value="<?php echo $cc?>" <?php if($CardType == $cc) { ?>selected="selected"<?php } ?>><?php echo $cc?></option>
													<?php } ?>												
												</select> 
											</div>
											<?php } ?>
			
											<div class="opaque_placeholder" style="width:365px; margin-bottom:30px;">
												<label for="AccountNumber">Card Number <?php if (pmpro_getClassForField("AccountNumber")=='pmpro_required') echo '&nbsp;*' ?></label>
												<input id="AccountNumber" <?php if($gateway != "stripe") { ?>name="AccountNumber"<?php } ?> class="input" type="text" size="25" value="<?php echo esc_attr($AccountNumber)?>" /> 
											</div>
													
											<?php
												$pmpro_show_cvv = apply_filters("pmpro_show_cvv", true);
												if($pmpro_show_cvv) {
											?>
											<div class="opaque_placeholder" style="margin-left:20px;width:180px">
												<label style="width:35px !important" for="CVV">CVV <?php if (pmpro_getClassForField("CVV")=='pmpro_required') echo '&nbsp;*' ?></label>
												<input class="input" id="CVV" <?php if($gateway != "stripe") { ?>name="CVV"<?php } ?> type="text" size="4" value="<?php if(!empty($_REQUEST['CVV'])) { echo esc_attr($_REQUEST['CVV']); }?>" class="<?php echo pmpro_getClassForField("CVV");?>" />
												<small style="padding-left: 70px;"><a href="javascript:void(0);" onclick="javascript:window.open('<?php echo pmpro_https_filter(PMPRO_URL)?>/pages/popup-cvv.html','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');"><img style="margin-top:10px;" src="<?php echo get_stylesheet_directory_uri(); ?>/images/question.png"></a></small>
											</div>
											<?php } ?>
											<div class="form_date">
												<!--<label for="ExpirationMonth">Expiration Date</label>-->
												<div class="styledselect">
													
												<select id="ExpirationMonth" <?php if($gateway != "stripe") { ?>name="ExpirationMonth"<?php } ?> class=" <?php ///echo pmpro_getClassForField("ExpirationMonth");?>">
													<option value="01" <?php if($ExpirationMonth == "01") { ?>selected="selected"<?php } ?>>01 - January</option>
													<option value="02" <?php if($ExpirationMonth == "02") { ?>selected="selected"<?php } ?>>02 - February</option>
													<option value="03" <?php if($ExpirationMonth == "03") { ?>selected="selected"<?php } ?>>03 - March</option>
													<option value="04" <?php if($ExpirationMonth == "04") { ?>selected="selected"<?php } ?>>04 - April</option>
													<option value="05" <?php if($ExpirationMonth == "05") { ?>selected="selected"<?php } ?>>05 - May</option>
													<option value="06" <?php if($ExpirationMonth == "06") { ?>selected="selected"<?php } ?>>06 - June</option>
													<option value="07" <?php if($ExpirationMonth == "07") { ?>selected="selected"<?php } ?>>07 - July</option>
													<option value="08" <?php if($ExpirationMonth == "08") { ?>selected="selected"<?php } ?>>08 - August</option>
													<option value="09" <?php if($ExpirationMonth == "09") { ?>selected="selected"<?php } ?>>09 - September</option>
													<option value="10" <?php if($ExpirationMonth == "10") { ?>selected="selected"<?php } ?>>10 - October</option>
													<option value="11" <?php if($ExpirationMonth == "11") { ?>selected="selected"<?php } ?>>11 - November</option>
													<option value="12" <?php if($ExpirationMonth == "12") { ?>selected="selected"<?php } ?>>12 - December</option>
												</select>
											</div><div class="styledselect" style="margin-left:5px">
												
												<select id="ExpirationYear" <?php if($gateway != "stripe") { ?>name="ExpirationYear"<?php } ?> class=" <?php //echo pmpro_getClassForField("ExpirationYear");?>">
													<?php
														for($i = date("Y"); $i < date("Y") + 10; $i++)
														{
													?>
														<option value="<?php echo $i?>" <?php if($ExpirationYear == $i) { ?>selected="selected"<?php } ?>><?php echo $i?></option>
													<?php
														}
													?>
												</select> 
											</div>
											</div>
											<div class="opaque_placeholder" style="margin-left:15px;margin-top:13px">
												<label for="bphone">Phone <?php if (pmpro_getClassForField("bphone")=='pmpro_required') echo '&nbsp;*' ?></label>
												<input id="bphone" name="bphone" type="text" class="input" size="30" value="<?php echo esc_attr($bphone)?>" /> 
												<?php echo formatPhone($bphone); ?>
											</div>
										</td>			
									</tr>
								</tbody>
								</table>
								<?php endif; ?>
								<script>
									//checking a discount code
									jQuery('#discount_code_button').click(function() {
										var code = jQuery('#discount_code').val();
//										var level_id = jQuery('#level').val();
										var level_id = jQuery('input[name="level"]:checked').val();
										if(code)
										{									
											//hide any previous message
											jQuery('.pmpro_discount_code_msg').hide();				
				
											//disable the apply button
											jQuery('#discount_code_button').attr('disabled', 'disabled');
				
											jQuery.ajax({
												url: '<?php echo home_url()?>',type:'GET',timeout:2000,
												dataType: 'html',
												data: "action=applydiscountcode&code=" + code + "&level=" + level_id + "&msgfield=discount_code_message",
												error: function(xml){
													alert('Error applying discount code [1]');
						
													//enable apply button
													jQuery('#discount_code_button').removeAttr('disabled');
												},
												success: function(responseHTML){
													if (responseHTML == 'error')
													{
														alert('Error applying discount code [2]');
													}
													else
													{
														jQuery('#discount_code_message').html(responseHTML);
													}		
						
													//enable invite button
													jQuery('#discount_code_button').removeAttr('disabled');										
												}
											});
										}																		
									});
									
									jQuery(document).ready(function(){
										jQuery.each(jQuery('.opaque_placeholder'), function(){
											if(jQuery(this).find('input').val()){
												jQuery(this).find('label').hide();
											}
											jQuery(this).find('input').keydown(function(){
												jQuery(this).parent().find('label').hide();
											});
											jQuery(this).find('input').keyup(function(){
												if(jQuery(this).val()) {
													jQuery(this).parent().find('label').hide();
												} else {
													jQuery(this).parent().find('label').show();
												}
											});
										});
										
										jQuery('input[name="level"]').change(function(){
											if(jQuery('#discount_code').val()) {
												jQuery('#discount_code_button').click();
											} else {
												jQuery('#pmpro_level_cost').html(jQuery('#membership_' + jQuery('input[name="level"]:checked').val() + '_details').html());
											}
										});
									});
								</script>
	
								<?php
									if($gateway == "check")
									{
										$instructions = pmpro_getOption("instructions");			
										echo '<div class="pmpro_check_instructions">' . wpautop($instructions) . '</div>';
									}
								?>
	
								<?php do_action("pmpro_checkout_before_submit_button"); ?>			
		
								<div class="pmpro_submit">
									<?php if($pmpro_review) { ?>
			
										<span id="pmpro_submit_span">
											<input type="hidden" name="confirm" value="1" />
											<input type="hidden" name="token" value="<?php echo esc_attr($pmpro_paypal_token)?>" />
											<input type="hidden" name="gateway" value="<?echo $gateway; ?>" />
											<input type="submit" class="pmpro_btn pmpro_btn-submit-checkout btn btn-success btn-large" value="Complete Payment &raquo;" />
										</span>
				
									<?php } else { ?>
					
										<?php if($gateway == "paypal" || $gateway == "paypalexpress" || $gateway == "paypalstandard") { ?>
										<span id="pmpro_paypalexpress_checkout" <?php if(($gateway != "paypalexpress" && $gateway != "paypalstandard") || !$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
											<input type="hidden" name="submit-checkout" value="1" />		
											<input type="image" value="Checkout with PayPal &raquo;" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" />
										</span>
										<?php } ?>
			
										<span id="pmpro_submit_span" <?php if(($gateway == "paypalexpress" || $gateway == "paypalstandard") && $pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
											<input type="hidden" name="submit-checkout" value="1" />		
											<input type="submit" class="btn btn-success btn-large" style="margin-left:78px;padding-left: 50px !important;
padding-right: 50px !important;" value="<?php if($pmpro_requirebilling) { ?>Checkout<?php } else { ?>Confirm<?php } ?> &raquo;" />				
										</span>
									<?php } ?>
		
									<span id="pmpro_processing_message" style="visibility: hidden;">
										<?php 
											$processing_message = apply_filters("pmpro_processing_message", "Processing...");
											echo $processing_message;
										?>					
									</span>
								</div>	
		
							</form>

							<?php if($gateway == "paypal" || $gateway == "paypalexpress") { ?>
							<script>	
								//choosing payment method
								jQuery('input[name=gateway]').click(function() {		
									if(jQuery(this).val() == 'paypal')
									{
										jQuery('#pmpro_paypalexpress_checkout').hide();
										jQuery('#pmpro_billing_address_fields').show();
										jQuery('#pmpro_payment_information_fields').show();			
										jQuery('#pmpro_submit_span').show();
									}
									else
									{			
										jQuery('#pmpro_billing_address_fields').hide();
										jQuery('#pmpro_payment_information_fields').hide();			
										jQuery('#pmpro_submit_span').hide();
										jQuery('#pmpro_paypalexpress_checkout').show();
									}
								});
	
								//select the radio button if the label is clicked on
								jQuery('a.pmpro_radio').click(function() {
									jQuery(this).prev().click();
								});
							</script>
							<?php } ?>

							<script>	
								// Find ALL <form> tags on your page
								jQuery('form').submit(function(){
									// On submit disable its submit button
									jQuery('input[type=submit]', this).attr('disabled', 'disabled');
									jQuery('input[type=image]', this).attr('disabled', 'disabled');
									jQuery('#pmpro_processing_message').css('visibility', 'visible');
								});
	
								//add required to required fields
								jQuery('.pmpro_required').after('<span class="pmpro_asterisk"> *</span>');
	
								//unhighlight error fields when the user edits them
								jQuery('.pmpro_error').bind("change keyup input", function() {
									jQuery(this).removeClass('pmpro_error');
								});
							</script>
					
					
					
						</section> <!-- end article section -->
						
						<footer>
			
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					
				
					
					
				
			
				</div> <!-- end #main -->
    
				<?php //get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->

			<?php
				include (STYLESHEETPATH . '/footercheckout.php');
			?>	