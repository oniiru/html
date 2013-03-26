<?php

function aff_handle_date_form()
{
	?>
    <link rel="stylesheet" type="text/css" href="<?php echo WP_AFF_DATE_CSS_URL; ?>">
    <script src="<?php echo WP_AFF_COM_JS_URL; ?>"></script>
    <script src="<?php echo WP_AFF_CAL_JS_URL; ?>"></script>
    <script>window.dhx_globalImgPath="<?php echo WP_AFF_DATE_IMG_URL; ?>";</script>
    	
    <br />
    <strong>Select a date range (yyyy-mm-dd) and hit the Display Data button to view history</strong>
    <br /><br />
    	
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <strong>Start Date:  </strong>
    <input type="text" id="start_date" name="start_date" class="css1" readonly="true" size="12">
	<script>
    	mCal = new dhtmlxCalendarObject("start_date");   
    	mCal.setSkin("simplegrey");
    	mCal.draw();
	</script>
	
    <strong>End Date: </strong>
    <input type="text" id="end_date" name="end_date" class="css1" readonly="true" size="12">
	<script>
    	mCal = new dhtmlxCalendarObject("end_date");   
    	mCal.setSkin("simplegrey");
    	mCal.draw();
	</script>
	<br />	
    	
	<div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Display Data'); ?> &raquo;" />
    </div>
    
    </form>	
    <?php
}

function aff_detect_ie()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function aff_handle_date_form_in_ie()
{
	?>
    <br />
    <strong>Select a date range (yyyy-mm-dd) and hit the Display Data button to view history</strong>
    		
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <br /><br />
    <strong>Start Date:  </strong>
    <input type="text" id="start_date" name="start_date" class="css1" size="12">
	
    <strong>End Date: </strong>
    <input type="text" id="end_date" name="end_date" class="css1" size="12">
	<br />	
    	
	<div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Display Data'); ?> &raquo;" />
    </div>
    
    </form>
    <?php	
}

function aff_handle_date_form_with_affiliate_id_field()
{
	?>
    <link rel="stylesheet" type="text/css" href="<?php echo WP_AFF_DATE_CSS_URL; ?>">
    <script src="<?php echo WP_AFF_COM_JS_URL; ?>"></script>
    <script src="<?php echo WP_AFF_CAL_JS_URL; ?>"></script>
    <script>window.dhx_globalImgPath="<?php echo WP_AFF_DATE_IMG_URL; ?>";</script>
    	
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">    	
    <br />
    <strong>1. Enter the Affiliate ID</strong>
    <input name="wp_aff_referrer" type="text" size="30" value="<?php echo $_POST['wp_aff_referrer']; ?>" />
    
    <br /><br />
    <strong>2. Select a date range (yyyy-mm-dd) and hit the Display Data button</strong>
    <br /><br />
    	
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <strong>Start Date:  </strong>
    <input type="text" id="start_date" name="start_date" class="css1" readonly="true" size="12">
	<script>
    	mCal = new dhtmlxCalendarObject("start_date");   
    	mCal.setSkin("simplegrey");
    	mCal.draw();
	</script>
	
    <strong>End Date: </strong>
    <input type="text" id="end_date" name="end_date" class="css1" readonly="true" size="12">
	<script>
    	mCal = new dhtmlxCalendarObject("end_date");   
    	mCal.setSkin("simplegrey");
    	mCal.draw();
	</script>
	<br />	
    	
	<div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Display Data'); ?> &raquo;" />
    </div>
    
    </form>	
    <?php
}

function aff_handle_date_form_in_ie_with_affiliate_id_field()
{
	?>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <br />
    <strong>1. Enter the Affiliate ID</strong>
    <input name="wp_aff_referrer" type="text" size="30" value="<?php echo $_POST['wp_aff_referrer']; ?>" />
    
    <br /><br />
    <strong>2. Select a date range (yyyy-mm-dd) and hit the Display Data button</strong>
    		    
    <input type="hidden" name="info_update" id="info_update" value="true" />
           
    <br /><br />
    <strong>Start Date:  </strong>
    <input type="text" id="start_date" name="start_date" class="css1" size="12">
	
    <strong>End Date: </strong>
    <input type="text" id="end_date" name="end_date" class="css1" size="12">
	<br />	
    	
	<div class="submit">
        <input type="submit" name="info_update" value="<?php _e('Display Data'); ?> &raquo;" />
    </div>
    
    </form>
    <?php	
}
?>
