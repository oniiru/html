<?php 
/*
* This is a template part
* produces search form.
* moved from content folder to theme root @since version 2.6
*/
$searchtext = get_option('ka_searchbartext'); 
?>
<form method="get" action="<?php echo home_url(); ?>/" class="search-form">
	<fieldset>
		<span class="text">	
			<input type="text" name="s" class="s" value="<?php echo $searchtext; ?>" onfocus="this.value=(this.value=='<?php echo $searchtext; ?>') ? '' : this.value;" onblur="this.value=(this.value=='') ? '<?php echo $searchtext; ?>' : this.value;" />
            <input type="submit" value="search" class="searchsubmit" />
		</span>
	</fieldset>
</form>