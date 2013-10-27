<?php
?>
    <br />    
    <script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/lib/date/date.js'; ?>"></script>
    <!--[if IE]><script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/lib/date/jquery.bgiframe.min.js'; ?>"></script><![endif]-->
    <script type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/lib/date/jquery.datePicker-v2.js'; ?>"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo WP_AFF_PLATFORM_URL.'/affiliates/lib/date/datePicker.css'; ?>">
<script type="text/javascript"> 
/* <![CDATA[ */
	jQuery(document).ready(function($) {
	    $(function() {
		    $('.date-pick').datePicker({startDate:"2008-01-01"});
	    });
	});
/*]]>*/  
</script>
	    
	<?php echo AFF_SELECT_DATE_RANGE; ?>

    <form id="dateform" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

        <div id="startdate">
            <label for="start_date"><strong><?php echo AFF_START_DATE_TEXT; ?>:  </strong></label>
            <input type="text" id="start_date" name="start_date" class="date-pick" size="12">
        </div>
        <div id="enddate">
            <label for="end_date"><strong><?php echo AFF_END_DATE_TEXT; ?>: </strong></label>
            <input type="text" id="end_date" name="end_date" class="date-pick" size="12">
        </div>

    <div class="clear"></div>
	<div class="submit">
        <input type="submit" class="button" name="info_update" value="<?php echo AFF_DISPLAY_DATA_BUTTON_TEXT; ?>" />
    </div>

    </form>
<?php     
?>