<div class="wrap">
	<div id="epo-icon"><br /></div>
	<?php echo "<h2>" . __( 'Easy Post Order - Queue', 'epo-queues' ) . "</h2>"; ?>
	
	<div id="epo-frame">
		<div id="epo-settings-column" class="metabox-holder">
			<div id="epo-post-type-form" class="postbox">				
				<h3 class="hndle"><span>Select Post Type</span></h3>
				
				<div class="inside">
					<p class="howto">
						Easy Post Order stores custom order for each post type.
					</p>
					
					<p>
						<label for="select_ptype" class="howto">
						<span>Post Type</span>
						<select id="select_ptype" name="select_ptype">
							<?php epo_generate_ptype_options(); ?>							
						</select>
						</label>
					</p>
					
					<p>
						<label for="select_cat" class="howto">
						<span>Categories</span>
						<select id="select_cat" name="select_cat">				
						</select>
						</label>
					</p>
					
					<div id="epo-cats" class="tabs-panel">
						
					</div>
				</div>
				
			</div><!-- #end epo-post-type-form -->		
			
			<div id="epo-instructions" class="postbox">				
				<h3 class="hndle"><span>How to Use</span></h3>
				
				<div class="inside">
					<p class="howto">
						Use EPO shortcode in your page. e.g.:
						<code>[EPO post_type="portfolio" posts_per_page="-1" loop="portfolio" category="cat-slug-1"]</code>
						Detailed instructions at <a href="http://www.bluehornetstudios.com/easy-post-order-faqs" target="_blank">Blue Hornet Studios</a>.
					</p>						
				</div>
				
			</div><!-- #end epo-instructions -->
			
		</div><!-- #end menu-settings-column -->
		
		<div id="epo-management-liquid">
			
			<div id="epo-header">
				<span id="now-editing">Now Editing: </span><span id="selected-ptype-display"><em>Please select from the left menu the post type you would like to custom sort.</em></span>
				<input id="save_epo_top" class="save_epo button-primary" type="submit" value="Save Order" />
			</div>
			
			<div id="post-body">
				<div id="epo-output"></div>
				
				<ul id="epo-queue"></ul>
			</div>
			
			<div id="epo-footer">
				<input id="save_epo_bottom" class="save_epo button-primary" type="submit" value="Save Order" />
			</div>
			
		</div><!-- #end menu-management-liquid -->	
	</div><!-- #end nav-menus-frame -->	
</div>
