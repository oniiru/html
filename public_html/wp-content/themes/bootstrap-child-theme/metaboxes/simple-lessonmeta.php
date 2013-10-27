<div class="my_meta_control">
 
	<p>
		<?php $mb->the_field('lessontitle'); ?>
		<input type="text" placeholder="Title" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
	</p> 
	<p>
		<?php $mb->the_field('description'); ?>
		<textarea name="<?php $mb->the_name(); ?>" placeholder="description" rows="3"><?php $mb->the_value(); ?></textarea>
	</p>
	


</div>