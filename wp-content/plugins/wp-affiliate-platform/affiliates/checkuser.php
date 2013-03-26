<?php include_once ('misc_func.php');

if(isset($_GET['cmd']) && $_GET['cmd'] == 'check') 
{
    $user = mysql_real_escape_string($_GET['user']);

    if(empty($user) && strlen($user) <=3) {
        echo AFF_SI_ENTER_FIVE_CHAR;
        exit();
    }
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
    $result = $wpdb->get_results("SELECT refid FROM $affiliates_table_name where refid='$user'", OBJECT);
    if($result)
    {
    	echo AFF_SI_NOT_AVAILABLE;
    } 
    else 
    {
    	echo AFF_SI_AVAILABLE;
    }
} ?>