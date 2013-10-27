<?php $blocked = BrowserBlocker_GetBlocked(); ?>
<form name="bb_form" id="bb_form" action="" method="post">
<input type="hidden" name="bb_update" value="advanced" />
<ul>
	<li id="bb_enable_row">
		<dl>
			<dt><label for"bb_enable" class="labels"><img src="<?php echo $img_url."tick.png" ?>" class="icon" title="Enable">&nbsp;Enable</label></dt>
			<dd><? 
					$update_fields = array(
						'Disabled' => '0', 'Enabled' => '1');
					BrowserBlocker_Make_Select(get_option('Browser_Blocker_Enabled'), $update_fields, "", "bb_enable", "bb_enable"); 
				?>
				&nbsp;&nbsp;Enable Browser Blocker? <br /><span class="bb_example">ie: Enable Browser Blocker after you have set your preferred settings.</span></dd>
		</dl>
	</li>
	<li id="bb_attribute_row">
		<dl>
			<dt><label for"bb_attribute" class="labels"><img src="<?php echo $img_url."yin-yang.png" ?>" class="icon" title="Give a Little Love">&nbsp;Credit Link</label></dt>
			<dd>
				<input type="checkbox" name="bb_attribute" id="bb_attribute" class="checkboxr" <?php echo ( get_option('Browser_Blocker_Credit')=='1' ) ? ' checked="checked"' : '' ?> >
				&nbsp;&nbsp;Share the Love <br /><span class="bb_example">ie: Display Footer with Links to Wordpress.org and Macnative.com</span>
			</dd>
		</dl>
	</li>
	<li id="bb_splash_img_row">
		<dl>
			<dt>
				<label for"bb_splash_img" class="labels">
					<img src="<?php echo $img_url."image.png" ?>" class="icon" title="Splash Image">&nbsp;Splash Image
				</label>
			</dt>
			<dd>
				<input type="input" size="25" name="bb_splash_img" id="bb_splash_img" value="<?php echo stripslashes(get_option('Browser_Blocker_Splash_Img')) ?>">
				&nbsp;&nbsp;Your Custom Splash Page Image <br /><span class="bb_example">ie: Add a Custom Splash Page Image URL - <span class="bb_example bb_example2">http://www.example.com/image.png</span><br />Recommended size 300px x 285px </span>
			</dd>
		</dl>
	</li>
	<li id="bb_text_row">
		<dl>
			<dt>
				<label for"bb_text" class="labels">
					<img src="<?php echo $img_url."edit.png" ?>" class="icon" title="Text">&nbsp;Title Text
				</label>
			</dt>
			<dd>
				<input type="input" size="25" name="bb_text" id="bb_text" value="<?php echo stripslashes(get_option('Browser_Blocker_Title')) ?>">
				&nbsp;&nbsp;Custom Title Text <br /><span class="bb_example">ie: You may specify custom title for your Splash Page.</span> 
			</dd>
		</dl>
	</li>
	<li id="bb_msg_row">
		<dl>
			<dt>
				<label for"bb_msg" class="labels">
					<img src="<?php echo $img_url."script-text.png" ?>" class="icon" title="Message">&nbsp;Message
				</label>
			</dt>
			<dd>
				<textarea name="bb_msg" id="bb_msg" rows="5" cols="40"><?php echo stripslashes(get_option('Browser_Blocker_Msg')) ?></textarea>
				<br /><span class="bb_example">ie: Add a custom message for the splash page</span>
			</dd>
		</dl>
	</li>
	<li id="bb_footer_row">
		<dl>
			<dt>
				<label for"bb_code" class="labels">
					<img src="<?php echo $img_url."document-code.png" ?>" class="icon" title="Message">&nbsp;Custom Code
				</label>
			</dt>
			<dd>
				<textarea name="bb_code" id="bb_code" rows="5" cols="40"><?php echo stripslashes(get_option('Browser_Blocker_Code')) ?></textarea>
				<br /><span class="bb_example">ie: Add a custom code to the footer of the splash page (ie: analytics)</span>
			</dd>
		</dl>
	</li>
	<li id="bb_bypass_row">
		<dl>
			<dt><label for"bb_bypass" class="labels"><img src="<?php echo $img_url."application-arrow.png" ?>" class="icon" title="Browser Tag Line">&nbsp;Allow Bypass</label></dt>
			<dd>
				<input type="checkbox" name="bb_bypass" id="bb_bypass" class="checkboxr" <?php echo ( get_option('Browser_Blocker_Bypass')=='1' ) ? ' checked="checked"' : '' ?> >
				&nbsp;&nbsp;Allow Blocked Browser Bypass <br /><span class="bb_example">ie: Allow users to bypass the block page and proceed to the site.</span>
			</dd>
		</dl>
	</li>
	<li id="bb_text2_row">
		<dl>
			<dt>
				<label for"bb_bypass_text" class="labels">
					<img src="<?php echo $img_url."edit.png" ?>" class="icon" title="Text">&nbsp;Bypass Text
				</label>
			</dt>
			<dd>
				<input type="input" size="25" name="bb_bypass_text" id="bb_bypass_text" value="<?php echo stripslashes(get_option('Browser_Blocker_BPtext')) ?>">
				&nbsp;&nbsp;Bypass Link Text <br /><span class="bb_example">ie: Specify what you would like the bypass link text to say.</span> 
			</dd>
		</dl>
	</li>
	<li id="bb_downld_row">
		<dl>
			<dt>
				<label for"bb_downld" class="labels">
					<img src="<?php echo $img_url."globe-green.png" ?>" class="icon" title="Displayed Downloads">&nbsp;Browser Options
				</label>
			</dt>
			<dd>
				<ul>
					<?php
					$browsers = explode('~', get_option('Browser_Blocker_Display_Browsers'));
					?>
					<li class="plain"><input type="checkbox" name="browser_1" id="browser_1" value="true" <?php
					if(in_array('1',$browsers)){
						echo "checked";
					}
					?>> Download Chrome</li>
					<li class="plain"><input type="checkbox" name="browser_2" id="browser_2" value="true" <?php
					if(in_array('2',$browsers)){
						echo "checked";
					}
					?>> Download Firefox</li>
					<li class="plain"><input type="checkbox" name="browser_3" id="browser_3" value="true" <?php
					if(in_array('3',$browsers)){
						echo "checked";
					}
					?>> Download Safari</li>
					<li class="plain"><input type="checkbox" name="browser_4" id="browser_4" value="true" <?php
					if(in_array('4',$browsers)){
						echo "checked";
					}
					?>> Download Internet Explorer</li>
					<li class="plain"><input type="checkbox" name="browser_5" id="browser_5" value="true" <?php
					if(in_array('5',$browsers)){
						echo "checked";
					}
					?>> Download Opera</li>
				</ul>
				<br /><span class="bb_example">ie: Select the Web Browsers that you would like splash page viewers to be able to select from.</span>
			</dd>
		</dl>
	</li>
	<li id="bb_desctext_row">
		<dl>
			<dt><label for"bb_desctext" class="labels"><img src="<?php echo $img_url."edit-small-caps.png" ?>" class="icon" title="Browser Tag Line">&nbsp;Desc. Text</label></dt>
			<dd>
				<input type="checkbox" name="bb_desctext" id="bb_desctext" class="checkboxr" <?php echo ( get_option('Browser_Blocker_DwnldDesc')=='1' ) ? ' checked="checked"' : '' ?> >
				&nbsp;&nbsp;Show Download Description Text <br /><span class="bb_example">ie: Display the text between the Browser title and the Download button.</span>
			</dd>
		</dl>
	</li>
	<li id="bb_pgs_row">
		<dl>
			<dt>
				<label for"bb_pgs" class="labels">
					<img src="<?php echo $img_url."blogs-stack.png" ?>" title="Pages">&nbsp;Pages
				</label>
			</dt>
			<dd>
				<?php
					$bb_pages = get_option('Browser_Blocker_Pages');
				?>
				<select multiple size="7" name="bb_pages[]"> 
					<?php
					if(in_array('all',$bb_pages)){?>
					<option selected value="all">Whole Site</option>
					<?php	
					}else{
					?>
					<option value="all">Whole Site</option>
				 <?php }
				  $pages = get_pages(); 
				  foreach ( $pages as $pagg ) {
					if(in_array($pagg->ID,$bb_pages)){
						$option = '<option selected value="' . $pagg->ID . '">';
					}else{
				  		$option = '<option value="' . $pagg->ID . '">';
					}
					$option .= $pagg->post_title;
					$option .= '</option>';
					echo $option;
				  }
				 ?>
				</select>
				&nbsp;&nbsp;Select Pages to Block (or "Whole Site" to block entire site) <br /><span class="bb_example">ie: Windows - CTRL-Click, Mac CMD-Click to select multiple pages</span>
			</dd>
		</dl>
	</li>
	<li id="bb_blocked_row">
		<dl>
			<dt>
				<label for"bb_blocked" class="labels">
					<img src="<?php echo $img_url."block.png" ?>" class="icon" title="Blocked Browsers">&nbsp;Block Browsers
				</label>
			</dt>
			<dd>
				<select id="bb_blocked" name="bb_blocked" class="bb_floater">
					<optgroup label="Web Browsers">
						<option value="Internet Explorer">Internet Explorer</option>
						<option value="MSN Browser">MSN Browser</option>
						<option value="Firefox">Firefox</option>
						<option value="Safari">Safari</option>
						<option value="Chrome">Chrome</option>
						<option value="Opera">Opera</option>
						<option value="Konqueror">Konqueror</option>
						<option value="Firebird">Firebird</option>
						<option value="Iceweasel">Iceweasel</option>
						<option value="Shiretoko">Shiretoko</option>
						<option value="Mozilla">Mozilla</option>
						<option value="Amaya">Amaya</option>
						<option value="iCab">iCab</option>
						<option value="IceCat">IceCat</option>
					</optgroup>
					<optgroup label="Mobile Devices">
						<option value="Pocket Internet Explorer">Pocket Internet Explorer</option>
						<option value="Opera Mini">Opera Mini</option>
						<option value="iPhone">iPhone</option>
						<option value="iPod">iPod</option>
						<option value="iPad">iPad</option>
						<option value="Android">Android</option>
						<option value="BlackBerry">BlackBerry</option>
						<option value="Nokia Browser">Nokia Browser</option>
						<option value="Nokia S60 OSS Browser">Nokia S60 OSS Browser</option>
					</optgroup>
					<optgroup label="Web Robots">
						<option value="GoogleBot">GoogleBot</option>
						<option value="Yahoo! Slurp">Yahoo! Slurp</option>
						<option value="MSN Bot">MSN Bot</option>
					</optgroup>
					<optgroup label="Depricated Browsers">
						<option value="Netscape Navigator">Netscape Navigator</option>
						<option value="Galeon">Galeon</option>
						<option value="NetPositive">NetPositive</option>
						<option value="Phoenix">Phoenix</option>
					</optgroup>
				</select>
				<select id="bb_direction" name="bb_direction" class="bb_floater">
					  <option value="Is">equals</option>
					  <option value="Greater Than">greater than</option>
					  <option value="Less Than">less than</option>
				</select>
				<input type="text" id="bb_blocked_ver" name="bb_blocked_ver" class="bb_floater" value="6.0" onfocus="if (this.value == '6.0') {this.value = '';}" onblur="if (this.value == '') {this.value = '6.0';}" /><img id="bb_add_browser" src="<?php echo $img_url."plus-circle.png" ?>" title="Add Blocked Browser" class="bb_floater">
				&nbsp;&nbsp;Block This Browser <div style="clear:both;"></div><br />
				<input type="hidden" name="bb_versions_detail" id="bb_versions_detail" value="<?php if(isset($blocked["browser"][0])){echo count($blocked["browser"]); }else{ echo "0"; } ?>">
				<table id="bb_versions" class="bb_versions <?php if(!isset($blocked["browser"][0])){ echo "hidden"; } ?>" cellspacing="0">
					<tr class="odd">
						<th class="first">Browser</th>
						<th>Comparison</th>
						<th>Version</th>
						<th class="last">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
					</tr>
					<?php
					for($x = 0; $x < count($blocked["browser"]); $x++){
						echo "<tr id='row".$x."'><td class='first'><input type='hidden' name='bb_browser_".$x."' id='bb_browser_".$x."' value='".$blocked["browser"][$x]."'>".$blocked["browser"][$x]."</td><td><input type='hidden' name='bb_direction_".$x."' id='bb_direction_".$x."' value='".$blocked["direction"][$x]."'>".$blocked["direction"][$x]."</td><td><input type='hidden' name='bb_version_".$x."' id='bb_version_".$x."' value='".$blocked["version"][$x]."'>".$blocked["version"][$x]."</td><td class='last removeRow'><img src='".$img_url."cross-circle.png' title='Remove Browser Version' /></td></tr>";
					}
					?>
				</table>
			</dd>
		</dl>
	</li>
</ul>

<div class="inside">
<p>
	<input type="submit" name="submit" value="Save Options &raquo;" class="button-primary" />&nbsp;&nbsp;&nbsp;<input type="submit" name="clear" value="Clear Options &raquo;" class="button-primary" />
</p>
</form>