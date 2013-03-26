<?php
//***** Installer *****/
function wp_affiliate_platform_run_activation()
{	
	global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {
    	// check if it is a network activation - if so, run the activation function for each blog id
    	if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                    $old_blog = $wpdb->blogid;
    		// Get all blog ids
    		$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    		foreach ($blogids as $blog_id) {
    			switch_to_blog($blog_id);
    			wp_affiliate_platform_run_installer();
    		}
    		switch_to_blog($old_blog);
    		return;
    	}	
    } 
    wp_affiliate_platform_run_installer();
}

function wp_affiliate_platform_run_installer()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	//***Installer variables***/
	$wp_affiliates_version = "4.6";//change the value of "WP_AFFILIATE_PLATFORM_DB_VERSION" if needed
	global $wpdb;
	$affiliates_table_name = $wpdb->prefix . "affiliates_tbl";
	$affiliates_clickthroughs_table_name = $wpdb->prefix . "affiliates_clickthroughs_tbl";
	$affiliates_sales_table_name = $wpdb->prefix . "affiliates_sales_tbl";
	$affiliates_payouts_table_name = $wpdb->prefix . "affiliates_payouts_tbl";
	$affiliates_banners_table_name = $wpdb->prefix . "affiliates_banners_tbl";
	$affiliates_leads_table_name = $wpdb->prefix . "affiliates_leads_tbl";
	$affiliates_relations_tbl_name = $wpdb->prefix . "affiliates_relations_tbl";
	
	//***Installer***/
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_table_name'") != $affiliates_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    pass varchar(128) NOT NULL default '',
	    company varchar(100) NOT NULL default '',
	    title varchar(5) NOT NULL default '',
	    firstname varchar(40) NOT NULL default '',
	    lastname varchar(40) NOT NULL default '',
	    website varchar(100) NOT NULL default '',
	    email varchar(100) NOT NULL default '',
	    payableto varchar(100) NOT NULL default '',
	    street varchar(100) NOT NULL default '',
	    town varchar(100) NOT NULL default '',
	    state varchar(100) NOT NULL default '',
	    postcode varchar(20) NOT NULL default '',
	    country varchar(100) NOT NULL default '',
	    phone varchar(30) NOT NULL default '',
	    fax varchar(30) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    paypalemail varchar(100) NOT NULL default '',
	    commissionlevel varchar(10) NOT NULL default '',
	    referrer varchar(30) NOT NULL default '',
	    tax_id varchar(128) NOT NULL default '',
	    account_details text NOT NULL,
	    sec_tier_commissionlevel varchar(10) NOT NULL default '',
	    PRIMARY KEY  (refid)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	// Add default options
	add_option("wp_aff_cookie_life", 21);
	add_option("wp_aff_commission_level", 25);
	add_option("wp_aff_site_title", "WP Affiliate Platform");
	
	add_option("wp_affiliates_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_clickthroughs_table_name'") != $affiliates_clickthroughs_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_clickthroughs_table_name . " (
	    refid varchar(128) default 'none',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    browser varchar(200) default 'No information',
	    ipaddress varchar(50) default 'No information',
	    referralurl varchar(200) default 'none detected (maybe a direct link)',
	    buy varchar(10) default 'NO',
	    campaign_id varchar(64) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_clickthrough_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_sales_table_name'") != $affiliates_sales_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_sales_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    browser varchar(200) NOT NULL default '',
	    ipaddress varchar(50) NOT NULL default '',
	    payment varchar(10) NOT NULL default '',
	    sale_amount varchar(10) NOT NULL default '',
	    txn_id varchar(64) NOT NULL default '',
	    item_id varchar(128) NOT NULL default '',
	    buyer_email varchar(128) NOT NULL default '',
	    campaign_id varchar(64) NOT NULL default '',
	    buyer_name varchar(128) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_sales_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_leads_table_name'") != $affiliates_leads_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_leads_table_name . " (
	    lead_id int(12) NOT NULL auto_increment,
	    buyer_email varchar(128) NOT NULL default '',
		refid varchar(128) NOT NULL default '',
		reference varchar(20) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    ipaddress varchar(50) NOT NULL default '',
	    buyer_name varchar(128) NOT NULL default '',
	    PRIMARY KEY  (lead_id)   
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_leads_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_payouts_table_name'") != $affiliates_payouts_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_payouts_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    payout_payment varchar(10) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_payouts_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_banners_table_name'") != $affiliates_banners_table_name)
	{
	$sql = "CREATE TABLE " . $affiliates_banners_table_name . " (
	    number int(12) NOT NULL auto_increment,
	    name varchar(50) NOT NULL default '',
	    ref_url varchar(255) NOT NULL default '',
	    link_text varchar(100) NOT NULL default '',
	    image varchar(255) NOT NULL default '',
	    description text NOT NULL,
	    creative_type varchar(4) NOT NULL default '0',
	    PRIMARY KEY  (number)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_banners_version", $wp_affiliates_version);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '$affiliates_relations_tbl_name'") != $affiliates_relations_tbl_name)
	{	
	$sql = "CREATE TABLE " . $affiliates_relations_tbl_name . " (
	    record_id int(12) NOT NULL auto_increment,
	    unique_ref varchar(128) NOT NULL default '',
		refid varchar(128) NOT NULL default '',
		reference varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    ipaddress varchar(50) NOT NULL default '',
	    additional_info text NOT NULL,
	    PRIMARY KEY  (record_id)   
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	add_option("wp_affiliates_rel_tbl_version", $wp_affiliates_version);	
	}	
	/*************************************/
	//************ Upgrade Path **********/
	/*************************************/

	include_once('wp_affiliate_config_class.php');
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	
	/*** Plugin update/upgrade path specific config tasks ***/
	$installed_ver = get_option( "wp_affiliates_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
		if($wp_aff_platform_config->getValue('wp_aff_using_wp_hash_pass_system') != '1')
		{
			//do the password hash conversion
			include_once(ABSPATH.WPINC.'/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);

			$resultset = $wpdb->get_results("SELECT * FROM $affiliates_table_name", OBJECT);
			if($resultset){
				foreach ($resultset as $row){
					$password = $row->pass;
					$password = $wp_hasher->HashPassword($password);
					$affiliate_id = $row->refid;
	        		$updatedb = "UPDATE $affiliates_table_name SET pass = '".$password."' WHERE refid = '".$affiliate_id."'";
	        		$results = $wpdb->query($updatedb);										
				}
			}			
			$wp_aff_platform_config->setValue('wp_aff_using_wp_hash_pass_system','1');
		}
	}	
	$wp_aff_platform_config->saveConfig();
	/*** End of plugin update specific config tasks ***/
	
	/*** plugin upgrade db tasks ***/
	$installed_ver = get_option( "wp_affiliates_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
	$sql = "CREATE TABLE " . $affiliates_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    pass varchar(128) NOT NULL default '',
	    company varchar(100) NOT NULL default '',
	    title varchar(5) NOT NULL default '',
	    firstname varchar(40) NOT NULL default '',
	    lastname varchar(40) NOT NULL default '',
	    website varchar(100) NOT NULL default '',
	    email varchar(100) NOT NULL default '',
	    payableto varchar(100) NOT NULL default '',
	    street varchar(100) NOT NULL default '',
	    town varchar(100) NOT NULL default '',
	    state varchar(100) NOT NULL default '',
	    postcode varchar(20) NOT NULL default '',
	    country varchar(100) NOT NULL default '',
	    phone varchar(30) NOT NULL default '',
	    fax varchar(30) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    paypalemail varchar(100) NOT NULL default '',
	    commissionlevel varchar(10) NOT NULL default '',
	    referrer varchar(30) NOT NULL default '',
	    tax_id varchar(128) NOT NULL default '',
	    account_details text NOT NULL,
	    sec_tier_commissionlevel varchar(10) NOT NULL default '',
	    PRIMARY KEY  (refid)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	
	// Add default options
	add_option("wp_aff_cookie_life", 21);
	add_option("wp_aff_commission_level", 25);
	add_option("wp_aff_contact_email", get_bloginfo('admin_email'));
	
	update_option("wp_affiliates_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_clickthrough_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
	$sql = "CREATE TABLE " . $affiliates_clickthroughs_table_name . " (
	    refid varchar(128) default 'none',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    browser varchar(200) default 'No information',
	    ipaddress varchar(50) default 'No information',
	    referralurl varchar(200) default 'none detected (maybe a direct link)',
	    buy varchar(10) default 'NO',
	    campaign_id varchar(64) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_clickthrough_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_sales_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
	$sql = "CREATE TABLE " . $affiliates_sales_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    browser varchar(200) NOT NULL default '',
	    ipaddress varchar(50) NOT NULL default '',
	    payment varchar(10) NOT NULL default '',
	    sale_amount varchar(10) NOT NULL default '',
	    txn_id varchar(64) NOT NULL default '',
	    item_id varchar(128) NOT NULL default '',
	    buyer_email varchar(128) NOT NULL default '',
	    campaign_id varchar(64) NOT NULL default '',
	    buyer_name varchar(128) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_sales_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_leads_version" );
	if( $installed_ver != $affiliates_leads_table_name )
	{
	$sql = "CREATE TABLE " . $affiliates_leads_table_name . " (
	    lead_id int(12) NOT NULL auto_increment,
	    buyer_email varchar(128) NOT NULL default '',
		refid varchar(128) NOT NULL default '',
		reference varchar(20) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    ipaddress varchar(50) NOT NULL default '',
	    buyer_name varchar(128) NOT NULL default '',
	    PRIMARY KEY  (lead_id)   
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_leads_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_payouts_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
	$sql = "CREATE TABLE " . $affiliates_payouts_table_name . " (
	    refid varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    payout_payment varchar(10) NOT NULL default ''
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_payouts_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_banners_version" );
	if( $installed_ver != $wp_affiliates_version )
	{
	$sql = "CREATE TABLE " . $affiliates_banners_table_name . " (
	    number int(12) NOT NULL auto_increment,
	    name varchar(50) NOT NULL default '',
	    ref_url varchar(255) NOT NULL default '',
	    link_text varchar(100) NOT NULL default '',
	    image varchar(255) NOT NULL default '',
	    description text NOT NULL,
	    creative_type varchar(4) NOT NULL default '0',
	    PRIMARY KEY  (number)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_banners_version", $wp_affiliates_version);
	}
	
	$installed_ver = get_option( "wp_affiliates_rel_tbl_version" );
	if( $installed_ver != $wp_affiliates_version )	
	{	
	$sql = "CREATE TABLE " . $affiliates_relations_tbl_name . " (
	    id int(12) NOT NULL auto_increment,
	    unique_ref varchar(128) NOT NULL default '',
		refid varchar(128) NOT NULL default '',
		reference varchar(128) NOT NULL default '',
	    date date NOT NULL default '0000-00-00',
	    time time NOT NULL default '00:00:00',
	    ipaddress varchar(50) NOT NULL default '',
	    blob text NOT NULL,
	    PRIMARY KEY  (id)   
		)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($sql);
	// Add default options
	update_option("wp_affiliates_rel_tbl_version", $wp_affiliates_version);
	}	
	/*** End plugin upgrade db tasks ***/
	
	/********************************************/
	/*** Setting default values at activation ***/
	/********************************************/
	update_option('wp_aff_platform_version', WP_AFFILIATE_PLATFORM_VERSION);
	
	add_option('wp_aff_currency_symbol', '$');
	add_option('wp_aff_currency', 'USD');
	add_option('wp_aff_cookie_life', '21');
	
	$wp_aff_senders_address = get_bloginfo('name')." <".get_option('admin_email').">";
	$wp_aff_signup_email_subject = "Affiliate Login Details";
	$wp_aff_signup_email_body = "Thank you for registering with us. Here are your login details...\n".        
	        "\nAffiliate ID: {user_name}".
	        "\nEmail: {email} \n".
	        "\nPasswd: {password} \n".
	        "\nYou can Log into the system at the following URL:\n{login_url}\n".           
	        "\nPlease log into your account to get banners and view your real-time statistics.\n".        
	        "\nThank You".
	        "\nAdministrator".
	        "\n______________________________________________________".
	        "\nTHIS IS AN AUTOMATED RESPONSE. ".
	        "\n***DO NOT RESPOND TO THIS EMAIL****";
	add_option('wp_aff_senders_email_address', stripslashes($wp_aff_senders_address));
	add_option('wp_aff_signup_email_subject', stripslashes($wp_aff_signup_email_subject));
	add_option('wp_aff_signup_email_body', stripslashes($wp_aff_signup_email_body));
		
	/*** Start of Add Default Options/Config ***/	
	include_once('wp_aff_utility_functions.php');
	
	$wpurl = get_option('wpurl');
	add_option('wp_aff_default_affiliate_landing_url',$wpurl);	
		
	$wp_pg_debug_file_name = ABSPATH . 'wp-content/plugins/'.WP_AFF_PLATFORM_FOLDER.'/wp_affiliate_debug.log';
	$wp_aff_platform_config->addValue('wp_affiliate_debug_file_name',$wp_pg_debug_file_name);

	$wp_aff_platform_config->addValue('wp_aff_index_title','Welcome to Affiliate Center');
	$wp_aff_index_body_tmp = wp_aff_default_index_body();
	$wp_aff_platform_config->addValue('wp_aff_index_body',$wp_aff_index_body_tmp);	
	
	$wp_aff_comm_notif_email_body = "Great news, you have just earned a commission!\n".
			"\nPlease log into your affiliate account to view the details.\n".
			"\nThank You";
	$wp_aff_platform_config->addValue('wp_aff_comm_notif_senders_address',$wp_aff_senders_address);
	$wp_aff_platform_config->addValue('wp_aff_comm_notif_email_subject',"You just earned a commission!");	
	$wp_aff_platform_config->addValue('wp_aff_comm_notif_email_body',stripslashes($wp_aff_comm_notif_email_body));	

	$wp_aff_platform_config->setValue('wp_aff_do_not_show_sc_warning', '');
	
	$wp_aff_platform_config->saveConfig();
	/*** End of Add Default Options ***/
	
	//***** End Installer *****/
}
?>