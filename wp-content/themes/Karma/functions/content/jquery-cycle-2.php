<?php 
$jcycle_timeout = get_option('ka_jcycle_timeout');
$jcycle_pause_hover = get_option('ka_jcycle_pause_hover');
if ($jcycle_pause_hover == "true") {$jcycle_pause_hover_results = '1';}else {$jcycle_pause_hover_results = '0';}
echo '<script type="text/javascript">
//<![CDATA[
TTjquery(window).load(function() {
	TTjquery(\'.home-banner-wrap ul\').css("background-image", "none");
	TTjquery(\'.jqslider\').css("display", "block");
	TTjquery(\'.big-banner #main .main-area\').css("padding-top", "16px");
    TTjquery(\'.home-banner-wrap ul\').after(\'<div class="jquery-pager">&nbsp;</div>\').cycle({
		fx: \'fade\',
		timeout: '.$jcycle_timeout.',
		height: \'auto\',
		pause: '.$jcycle_pause_hover_results.',
		pager: \'.jquery-pager\',
		cleartypeNoBg: true

	});
});
//]]>
</script>';
?>