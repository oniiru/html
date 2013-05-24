<?php
echo '<script type="text/javascript" src="'.KARMA_JS.'/jquery.cycle.all.min.js"></script>';

// jQuery1 Slider
$jcycle_timeout = get_option('ka_jcycle_timeout');
$jcycle_pause_hover = get_option('ka_jcycle_pause_hover');
if ($jcycle_pause_hover == "true") {$jcycle_pause_hover_results = '1';}else {$jcycle_pause_hover_results = '0';}
echo '<script type="text/javascript">
//<![CDATA[
TTjquery(window).load(function() {
	TTjquery(\'.home-bnr-jquery ul\').css("background-image", "none");
	TTjquery(\'.jqslider\').css("display", "block");
    TTjquery(\'.home-bnr-jquery ul\').after(\'<div class="jquery-pager">&nbsp;</div>\').cycle({
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






// jQuery 2 Slider
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







// Testimonial Slider
$testimonial_timeout = get_option('ka_testimonial_timeout');
$testimonial_pause_hover = get_option('ka_testimonial_pause_hover');
if ($testimonial_pause_hover == "true") {$testimonial_pause_hover_results = '1';}else {$testimonial_pause_hover_results = '0';}
echo '<script type="text/javascript">
//<![CDATA[
TTjquery(document).ready(function() {
	function onAfter(curr, next, opts, fwd) {
var index = opts.currSlide;
TTjquery(\'#prev,#prev2,#prev3,#prev4,#prev5\')[index == 0 ? \'hide\' : \'show\']();
TTjquery(\'#next,#next2,#next3,#next4,#next5\')[index == opts.slideCount - 1 ? \'hide\' : \'show\']();
//get the height of the current slide
var $ht = TTjquery(this).height();
//set the container\'s height to that of the current slide
TTjquery(this).parent().animate({height: $ht});
}
    TTjquery(\'.testimonials\').after(\'<div class="testimonial-pager">&nbsp;</div>\').cycle({
		fx: \'fade\',
		timeout: '.$testimonial_timeout.',
		height: \'auto\',
		pause: '.$testimonial_pause_hover_results.',
		pager: \'.testimonial-pager\',
		before: onAfter,
		cleartypeNoBg: true

	});
});

//]]>
</script>';



?>