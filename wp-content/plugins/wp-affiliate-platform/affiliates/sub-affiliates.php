<?php include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
//include "./lang/$language";

if(!aff_check_security())
{
    aff_redirect('index.php');
    exit;
}
  
include "header.php"; 

if (get_option('wp_aff_use_2tier'))
{
	echo '<div id="subnav"><li><a href="clicks.php">'.AFF_NAV_CLICKS.'</a></li></div>';
	echo '<div id="subnav"><li><a href="sub-affiliates.php">'.AFF_NAV_SUB_AFFILIATES.'</a></li></div>';
	echo '<div style="clear:both;"></div><br />';
}
?>

<img src="images/tier-affiliates.jpg" alt="tier affiliates icon" />

<?php 
$affiliates_table_name = WP_AFF_AFFILIATES_TABLE;

clicks_history($affiliates_table_name);

include "footer.php"; 
 
function clicks_history($affiliates_table_name)
{

    include ("reports.php");

	global $wpdb;
	
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        echo '<p><strong>';
        echo AFF_C_DISPLAYING_REFERRALS.' <font class="blue">'.$start_date.'</font> '.AFF_AND.' <font class="blue">'. $end_date;
        echo '</font></strong></p>';
		        	
		$curr_date = (date ("Y-m-d"));		
   
		$resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '".$_SESSION['user_id']."' AND date BETWEEN '$start_date' AND '$end_date'",OBJECT);
		
		if ($resultset)
		{		
			echo "<table id='reports'>";
		    echo "<tr><th>".AFF_TIER_SUB_AFFILIATES_DATE_JOINED."</th><th>".AFF_TIER_SUB_AFFILIATES_ID."</th>";
		    echo "</tr>";
		
		    foreach ($resultset as $resultset) 
		    {
		        print "<tr>";
		      	print "<td class='reportscol col1'>";
		      	print $resultset->date;
		      	print "</td>";
		      	print "<td class='reportscol'>";
		      	print $resultset->refid;
		      	print "</td>";
		      	print "</tr>";
		    }
		    print "</table>";
		}
		else
		{
			echo "<br><br>".AFF_TIER_SUB_AFFILIATES_NO_RECORDS;
		}
    		
	}
	else
	{	
		$resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '".$_SESSION['user_id']."' ORDER BY date DESC LIMIT 20", OBJECT);  
		   	
		if ($resultset) 
		{
			echo '<strong>';
			echo AFF_TIER_SUB_AFFILIATES_UNDER_YOU;
			echo '</strong>';
			echo "<br>".AFF_TIER_SUB_AFFILIATES_20;
			print "<br><br>";
		
		    echo "<table id='reports'>";
		    echo "<tr><th>".AFF_TIER_SUB_AFFILIATES_DATE_JOINED."</th><th>".AFF_TIER_SUB_AFFILIATES_ID."</th>";
		    echo "</tr>";
		
		    foreach ($resultset as $resultset) 
		    {
		        print "<tr>";
		      	print "<td class='reportscol col1'>";
		      	print $resultset->date;
		      	print "</td>";
		      	print "<td class='reportscol'>";
		      	print $resultset->refid;
		      	print "</td>";
		      	print "</tr>";
		    }
		    print "</table>";
		}
		else
		{
			echo "<br><br>".AFF_TIER_SUB_AFFILIATES_NO_RECORDS;
		}
	}	
}  
?>
