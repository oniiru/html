<?php
include_once ('misc_func.php');
if(!isset($_SESSION)){@session_start();}
//include "./lang/$language";
include "header.php";

if(aff_check_security())
{
    aff_redirect('members_only.php');
    exit;
} ?>

    <?php $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title'); ?>

    <h3 class="title"><?php echo ("$wp_aff_index_title"); ?></h3>

    <div id="aff-box-body">

    <?php
    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');//get_option('wp_aff_index_body');
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    echo $wp_aff_index_body; ?>

    </div>

    <div id="aff-box-content">

    <img src="images/user_signup.png" class="center" alt="Affiliate Sign up icon" />
    <div id="aff-box-action">
    <div style="float: left;">
        <a href="register.php"><img src="images/signup_round_40.png" /></a>
    </div>
    <div class="action-head"><a href="register.php"><?php echo AFF_SIGN_UP; ?></a></div>
    <div class="action-tag"><a href="register.php"><?php echo AFF_SIGN_UP_CLICK; ?></a></div>
    </div>

    <img src="images/login_icon_128.png" class="center" alt="Affiliate Login icon" />
    <div id="aff-box-action">
    <div style="float: left;">
        <a href="login.php"><img src="images/login_icon_round_48.png" /></a>
    </div>
        <div class="action-head"><a href="login.php"><?php echo AFF_LOGIN; ?></a></div>
        <div class="action-tag"><a href="login.php"><?php echo AFF_LOGIN_CLICK; ?></a></div>
    </div>
    </div>

<div class="clear"></div>

<?php include "footer.php"; ?>