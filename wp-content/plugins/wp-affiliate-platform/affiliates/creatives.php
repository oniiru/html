<?php include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}

if(!aff_check_security())
{
    aff_redirect('index.php');
    exit;
}
  
include "header.php"; ?>

<!-- Load jQuery Lightbox -->
<script language="JavaScript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.pack.js"></script>
<script type="text/javascript">
    $(function() {
        $('#gallery a').lightBox();
    });
</script>

<?php
$aff_banners_table =  WP_AFF_BANNERS_TABLE;    	
$resultset = $wpdb->get_results("select * from $aff_banners_table ORDER BY name asc",OBJECT); 

echo '<div id="subnav"><li><a href="ads.php">'.AFF_NAV_BANNERS.'</a></li></div>';
echo '<div id="subnav"><li><a href="creatives.php">'.AFF_NAV_CREATIVES.'</a></li></div>';
echo '<div style="clear:both;"></div><br />';

echo "<h3 class='title'>".AFF_B_CREATIVE_PAGE_TITLE."</h3>";
echo "<p style='text-align:left;'>".AFF_B_CREATIVE_PAGE_MESSAGE."</p>";

if ($resultset) 
{
	echo '
	<table width="100%" id="gallery" class="widefat">
	<thead><tr>
	<th scope="col" class="tableheader">'.AFF_C_NAME.'</th>
	<th scope="col" class="tableheader">'.AFF_B_CODE.'</th>
	</tr></thead>
	<tbody>';
	    
    foreach ($resultset as $resultset) 
    {
    	if($resultset->creative_type =="3")
    	{ 	
    		$ad_code = str_replace("xxxx",$_SESSION['user_id'],$resultset->description);
    		$ad_code = str_replace("XXXX",$_SESSION['user_id'],$ad_code);
			echo '<tr>';
			echo '<td class="col2"><strong>'.$resultset->name.'</strong></td>';
	        echo '<td><textarea cols=65 rows=5>';
	      	echo $ad_code;
	      	echo "</textarea></td>";				
			echo '</tr>';
    	}
    }
	echo '</tbody>
	</table>';
}
else
{
	echo "<p class='message'>".AFF_B_NO_CREATIVE."</p>";
} 
?>

<?php include "footer.php"; ?>