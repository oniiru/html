<?php
include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
//include "./lang/$language"; 
?>
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
<div id="navbar"></div>
<!-- End Navbar -->

<!-- Start Content -->
<div id="content">
<div id="inside">

<h2 class="title"><?php echo AFF_THANK_YOU; ?></h2>
<h3><?php echo AFF_REGO_COMPLETE; ?></h3>
<p class="message"><?php echo AFF_REGO_COMPLETE_MESSAGE; ?>.</p>

<?php include "footer.php"; ?>