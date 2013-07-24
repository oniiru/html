	<?php global $wpalchemy_media_access; ?>
	<div class="my_meta_control">
	
		<p>
			<?php $mb->the_field('description'); ?>
			<textarea placeholder="Description" rows="3" name="<?php $mb->the_name(); ?>" /><?php $mb->the_value(); ?></textarea>
		</p>
	
		<label>Difficulty Level</label>
		<?php $mb->the_field('difficulty'); ?>
		<select name="<?php $mb->the_name(); ?>">
			<option value="">Select...</option>
			<option value="Beginner"<?php $mb->the_select_state('Beginner'); ?>>Beginner</option>
			<option value="Beg/Int"<?php $mb->the_select_state('Beg/Int'); ?>>Beg/Int</option>
			<option value="Intermediate"<?php $mb->the_select_state('Intermediate'); ?>>Intermediate</option>
			<option value="Int/Adv"<?php $mb->the_select_state('Int/Adv'); ?>>Int/Adv</option>
			<option value="Advanced"<?php $mb->the_select_state('Advanced'); ?>>Advanced</option>
		</select><br><br>
		
		<label>Color</label>
		<?php $mb->the_field('color'); ?>
		<select name="<?php $mb->the_name(); ?>">
			<option value="">Select...</option>
			<option value="#463051"<?php $mb->the_select_state('#463051'); ?>>Purple</option>
			<option value="#335543"<?php $mb->the_select_state('#335543'); ?>>Green</option>
			<option value="#252525"<?php $mb->the_select_state('#252525'); ?>>Dark Grey</option>
			<option value="#254661"<?php $mb->the_select_state('#254661'); ?>>Blue</option>
			<option value="#5e1212"<?php $mb->the_select_state('#5e1212'); ?>>Red</option>
			<option value="#a14b1b"<?php $mb->the_select_state('#a14b1b'); ?>>Orange</option>
			
		</select><br><br>
		
		<label>Who has access</label>
		<?php $mb->the_field('access'); ?>
		<select name="<?php $mb->the_name(); ?>">
			<option value="Anyone"<?php $mb->the_select_state('Anyone'); ?>>Anyone</option>
			<option value="Free"<?php $mb->the_select_state('Free'); ?>>Free</option>
			<option value="Paid"<?php $mb->the_select_state('Paid'); ?>>Paid</option>
			
		</select><br><br>
		
		
		<p>
			<?php $mb->the_field('vidembed'); ?>
			<input type="text" name="<?php $mb->the_name(); ?>" placeholder="The Vimeo ID (just the number)" value="<?php $mb->the_value(); ?>">
		</p>
		
		<p>
			<?php $mb->the_field('totallength'); ?>
			<input type="text" name="<?php $mb->the_name(); ?>" placeholder="Total Length of Project in Min." value="<?php $mb->the_value(); ?>">
		</p>
		
		
		
		<?php $mb->the_field('projectimage'); ?>
		    <?php $wpalchemy_media_access->setGroupName('nn')->setInsertButtonLabel('Insert'); ?>
			
		    <p>
				<label>Project Image</label>
		        <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
		        <?php echo $wpalchemy_media_access->getButton(); ?>
		    </p>
			
			<?php $mb->the_field('backgroundimage'); ?>
			    <?php $wpalchemy_media_access->setGroupName('nn2')->setInsertButtonLabel('Insert'); ?>
			
			    <p>
					<label>Background Image</label>
			        <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
			        <?php echo $wpalchemy_media_access->getButton(); ?>
			    </p>
				
				<p>
					<?php $mb->the_field('bgadjustments'); ?>
					<input type="text" value="<?php $mb->the_value(); ?>" placeholder="Adjust the background image if needed" rows="3" name="<?php $mb->the_name(); ?>" />
				</p>
 
		  
	
	
	
	
	
	
</div>