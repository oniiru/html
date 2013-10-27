<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="language" content="en" />
    <meta name="robots" content="follow, all" />
    <meta name="description" content="affiliate program" />
    <meta name="keywords" content="affiliate" />
    <link rel="Shortcut Icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="style.css" />

    <title><?php echo get_option('wp_aff_site_title'); ?></title>

</head>

<body>
<!-- Start Container -->
<div id="container">

<!-- Start Header -->
<div id="header">

<h1><?php echo get_option('wp_aff_site_title'); ?></h1>

</div>
<!-- End Header -->

<!-- Start Navbar -->
<div id="navbar">

<?php if(aff_check_security()) { ?>

<ul id="nav">
    <li><a href="members_only.php"><?php echo AFF_NAV_HOME; ?></a></li>
    <li><a href="details.php"><?php echo AFF_NAV_EDIT_PROFILE; ?></a></li>
    <li><a href="clicks.php"><?php echo AFF_NAV_REFERRALS; ?></a></li>
    <li><a href="sales.php"><?php echo AFF_NAV_SALES; ?></a></li>
    <li><a href="payments.php"><?php echo AFF_NAV_PAYMENT_HISTORY; ?></a></li>
    <li><a href="ads.php"><?php echo AFF_NAV_ADS; ?></a></li>
    <li><a href="contact.php"><?php echo AFF_NAV_CONTACT; ?></a></li>
    <li><a href="logout.php"><?php echo AFF_NAV_LOGOUT; ?></a></li>
</ul>

<?php  } ?>

</div>
<!-- End Navbar -->

<!-- Start Content -->
<div id="content">
<div id="inside">