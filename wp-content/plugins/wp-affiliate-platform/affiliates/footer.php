</div>
</div>
<!-- End Content -->

<!-- Start Footer -->
<div id="footer">

    <p style="float:left;"><a href="index.php" title="<?php echo get_option('wp_aff_site_title'); ?>"><?php echo get_option('wp_aff_site_title'); ?></a> &copy; <?php echo date("Y"); ?> - All rights reserved</p>
    <?php
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	if($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section')!='1'){
	    $aff_id = get_option('wp_aff_user_affilate_id');
		if(!empty($aff_id))
		{
			echo '<div style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="http://tipsandtricks-hq.com/?p=1474&ap_id='.$aff_id.'">WP Affiliate Platform</a></div>';
		}
		else
		{    
	    	echo '<p style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="http://tipsandtricks-hq.com/?p=1474">WP Affiliate Platform</a></p>';
	    }    		
	}  
    ?>
    <div class="clear"></div>

</div>
<!-- End Footer -->

</div>
<!-- End Container -->
</body>
</html>