	<?php global $wpalchemy_media_access; ?>
	<div class="my_meta_control">

		<p class="soft-warning" style="display:none;color:red">Remember to click save to save your new sort order! </p>
	
	
		<p>
			<?php $mb->the_field('description'); ?>
			<input type="text" placeholder="Sub-heading" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
			
		</p>
	
		<label>Type of Lesson</label>
		<?php $mb->the_field('Type'); ?>
		<select name="<?php $mb->the_name(); ?>">
			<option value="">Select...</option>
			<option value="a"<?php $mb->the_select_state('a'); ?>>Video Lesson</option>
			<option value="b"<?php $mb->the_select_state('b'); ?>>Quiz</option>
			<option value="c"<?php $mb->the_select_state('c'); ?>>Other</option>
		</select><br><br>
		
		<p>
			<?php $mb->the_field('vidembed'); ?>
			<input type="text" name="<?php $mb->the_name(); ?>" placeholder="The Vimeo ID (just the number)" value="<?php $mb->the_value(); ?>">
		</p>
		
		
		
		<?php $mb->the_field('filesets'); ?>
		    <?php $wpalchemy_media_access->setGroupName('nn')->setInsertButtonLabel('Insert'); ?>
			
		    <p>
				<label>The Fileset</label>
		        <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
		        <?php echo $wpalchemy_media_access->getButton(); ?>
		    </p>
 
		  
		
 	<label>Techniques and Toolsets:</label>
	<?php while($mb->have_fields_and_multi('techniques')): ?>
		
	<?php $mb->the_group_open(); ?>
 
		<?php $mb->the_field('title'); ?>
		<p><input style="display:inline-block; width:500px" type="text" placeholder="Technique/Toolset Title:" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
 
		<?php $mb->the_field('Min'); ?>
		<p style="margin-left:30px"><input style="display:inline-block; width:100px" type="text" placeholder="Min." name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
		<?php $mb->the_field('Sec'); ?>
		
		<p><input style="display:inline-block; width:100px" type="text" placeholder="Sec." name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
 
	
			<a href="#" class="dodelete button">Remove</a>
	
 
	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
 
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-techniques button">One More!</a></p>
	<p></p>
	<input type="submit" class="button" name="save" value="Save`">
	<a style="" href="#" class="dodelete-techniques button">Remove All</a>
	
	
	
	<script type="text/javascript">
	// <![CDATA[]
	jQuery(function($) {
		$("#wpa_loop-techniques").sortable({
			change: function(){
				
				$('.soft-warning').show();
			}
		
		});
		
		
	});
	// ]]>
	</script>
	
	
</div>