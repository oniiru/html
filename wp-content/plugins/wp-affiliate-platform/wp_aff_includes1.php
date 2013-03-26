<?php
function wp_aff_admin_submenu_css()
{
	?>
	<style type="text/css">
	.affiliateSubMenu{
	list-style:none;
	margin:0 0 5px 0;
	padding:0;
	height:2em;
	font-size:14px;
	clear:both;
	background:#ECECEC none repeat scroll 0 0;
	}

	.affiliateSubMenu li{
	float:left;
	padding:0;
	margin:0;
	}

	.affiliateSubMenu li a{
	display:block;
	float:left;
	margin:0 0 0 12px;
	padding:0 5px;
	text-decoration:none;
	line-height:200%;
	}
	.affiliateSubMenu li.current{
     border-top:2px solid #ECECEC;
     background:#F9F9F9;
	}
	</style>
	<?php
}

function wp_aff_misc_admin_css()
{
?>
<style type="text/css">
.wp_affiliate_red_box {
background: #FFEBE8;
border: 1px solid #CC0000;
color: #3F0202;
margin: 10px 0px 10px 0px;
padding: 5px 5px 5px 10px;
text-shadow: 1px 1px #FFFFFF;
}

.wp_affiliate_yellow_box {
background: #FFF6D5;
border: 1px solid #D1B655;
color: #3F2502;
margin: 10px 0px 10px 0px;
padding: 5px 5px 5px 10px;
text-shadow: 1px 1px #FFFFFF;
}

.wp_affiliate_grey_box{
background: #ECECEC;
border: 1px solid #CFCFCF;
color: #363636;
margin: 10px 0px 15px 0px;
padding: 5px 5px 5px 10px;
text-shadow: 1px 1px #FFFFFF;	
}
</style>
<?php
}
?>
