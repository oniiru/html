<?php $searchtext = get_option('ka_searchbartext'); ?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>/" class="search-form">
	<fieldset>
		<span class="text">
			<input type="submit" class="submit" value="search" id="searchsubmit" />
			<input type="text" name="s" id="s" value="<?php echo $searchtext; ?>" onfocus="this.value=(this.value=='<?php echo $searchtext; ?>') ? '' : this.value;" onblur="this.value=(this.value=='') ? '<?php echo $searchtext; ?>' : this.value;" />
		</span>
	</fieldset>
</form>