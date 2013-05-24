<?php $blocked = BrowserBlocker_GetBlocked(); ?>
<form name="bb_form" id="bb_form" action="" method="post">
<input type="hidden" name="bb_update" value="simple" />
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
					<option value="Equals">Equals</option>
					<option value="Greater Than">Greater Than</option>
					<option value="Less Than">Less Than</option>
					<option value="Equal Or Greater">Equal Or Greater</option>
					<option value="Equal Or Less">Equal Or Less</option>
				</select>
				<input type="text" id="bb_blocked_ver" name="bb_blocked_ver" class="bb_floater" value="6.0" onfocus="if (this.value == '6.0') {this.value = '';}" onblur="if (this.value == '') {this.value = '6.0';}" />
					<img id="bb_add_browser" src="<?php echo $img_url."plus-circle.png" ?>" title="Add Blocked Browser" class="bb_floater">
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