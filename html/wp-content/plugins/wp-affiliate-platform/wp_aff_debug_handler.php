<?php

//**** This file needs to be included from a file that has access to "wp-load.php" ****
function wp_affiliate_log_debug($message,$success,$end=false)
{
	global $wp_aff_platform_config;
	$debug_enabled = false;
	if(get_option('wp_aff_enable_debug') == '1'){
		$debug_enabled = true;
	}
    if (!$debug_enabled) return;
	
    $debug_log_file_name = dirname(__FILE__).'/wp_affiliate_debug.log';
    // Timestamp
    $text = '['.date('m/d/Y g:i A').'] - '.(($success)?'SUCCESS :':'FAILURE :').$message. "\n";
    if ($end) {
    	$text .= "\n------------------------------------------------------------------\n\n";
    }
    // Write to log
    $fp=fopen($debug_log_file_name,'a');
    fwrite($fp, $text );
    fclose($fp);  // close file
}

function wp_aff_api_debug($message,$success,$end=false)
{
	wp_affiliate_log_debug($message,$success,$end);
}

function wp_aff_write_debug_array($array_to_write,$success,$end=false,$debug_log_file_name='')
{
	global $wp_aff_platform_config;
	$debug_enabled = false;
	if(get_option('wp_aff_enable_debug') == '1'){
		$debug_enabled = true;
	}	
    if (!$debug_enabled) return;
    
    // Timestamp
    $text = '['.date('m/d/Y g:i A').'] - '.(($success)?'SUCCESS :':'FAILURE :'). "\n";
	ob_start(); 
	print_r($array_to_write); 
	$var = ob_get_contents(); 
	ob_end_clean();     
    $text .= $var;
    
    if ($end) 
    {
    	$text .= "\n------------------------------------------------------------------\n\n";
    }

	if(empty($debug_log_file_name)){
    	$debug_log_file_name = dirname(__FILE__).'/wp_affiliate_debug.log';
	}    
    // Write to log
    $fp=fopen($debug_log_file_name,'a');
    fwrite($fp, $text );
    fclose($fp);  // close file
}

?>