<?php include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}

if(!aff_check_security())
{
    aff_redirect('index.php');
    exit;
}
//[rel=lightbox]  
include "header.php"; ?>

<!-- Load jQuery Lightbox -->
<script language="JavaScript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.pack.js"></script>
<script type="text/javascript">
    $(function() {
        $('#gallery a[rel=lightbox]').lightBox();
    });
</script>

<?php
$aff_banners_table =  WP_AFF_BANNERS_TABLE;    	
$resultset = $wpdb->get_results("select * from $aff_banners_table ORDER BY name asc",OBJECT); 

echo '<div id="subnav"><li><a href="ads.php">'.AFF_NAV_BANNERS.'</a></li></div>';
echo '<div id="subnav"><li><a href="creatives.php">'.AFF_NAV_CREATIVES.'</a></li></div>';
echo '<div style="clear:both;"></div><br />';

echo "<h3 class='title'>".AFF_B_BANNER_PAGE_TITLE."</h3>";
echo "<p style='text-align:left;'>".AFF_B_BANNERS_PAGE_MESSAGE."</p>";
print "<p><strong><font face=arial>".AFF_B_BANNERS;
print "</strong></p>";

if ($resultset) 
{
	echo '
	<table width="100%" id="gallery" class="widefat">
	<thead><tr>
	<th scope="col" class="tableheader">'.AFF_B_BANNER_NAME.'</th>
	<th scope="col" class="tableheader">'.AFF_B_BANNER_LINK.'</th>
	<th scope="col" class="tableheader">'.AFF_B_CODE.'</th>
	</tr></thead>
	<tbody>';
	    
    foreach ($resultset as $resultset) 
    {
    	if($resultset->creative_type =="0")
    	{
			$separator='?';
			$url=$resultset->ref_url;
			if(strpos($url,'?')!==false) 
			{
				$separator='&';
			}	
		    if (empty($resultset->image))
		    {
		        // Text Link
		        $aff_url = $resultset->ref_url.$separator."ap_id=".$_SESSION['user_id'];
		        $code = "<a href=\"$aff_url\" target=\"blank\">$resultset->link_text</a>";
	            $banner = "<a href=\"$aff_url\" target=\"blank\">$resultset->link_text</a>";
		    }
		    else
		    {
		      	//Banner image
		      	$aff_url = $resultset->ref_url.$separator."ap_id=".$_SESSION['user_id'];
				$code = "<a href=\"$aff_url\" target=\"_blank\"><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a>";
	            $banner = "<a href=\"$resultset->image\" rel=\"lightbox\"><img src=\"$resultset->image\" alt=\"$resultset->link_text\" border=\"0\" /></a>";
		    }    	
			echo '<tr>';
			echo '<td class="col2"><strong>'.$resultset->name.'</strong></td>';
			echo '<td class="col3"><strong>'.$banner.'</strong></td>';
	        echo '<td><textarea cols=60 rows=4>';
	      	echo $code;
	      	echo "</textarea></td>";				
			echo '</tr>';
    	}
    }
	echo '</tbody>
	</table>';
}
else
{
	echo "<p class='message'>".AFF_B_NO_BANNER."</p>";
} ?>

<?php include "footer.php"; ?>