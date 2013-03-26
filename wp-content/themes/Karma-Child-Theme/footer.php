<div id="footer">
<?php truethemes_begin_footer_hook()// action hook, see truethemes_framework/global/hooks.php ?>
<?php
add_filter('pre_get_posts','wploop_exclude');
$footer_layout = get_option('ka_footer_layout');
$ka_footer_columns = get_option('ka_footer_columns');
$ka_scrolltoplink = get_option('ka_scrolltoplink');
$ka_scrolltoptext = get_option('ka_scrolltoplinktext');

if (($footer_layout == "full_bottom") || ($footer_layout == "full")){ ?>
<div class="footer-area">
<div class="footer-wrapper">
<div class="footer-holder">

<?php $footer_columns = range(1,$ka_footer_columns);$footer_count = 1;$sidebar = 6;
if($ka_footer_columns == 4){
	?><div class="one_fourth"><?php dynamic_sidebar($sidebar) ?></div>
		<div class="one_fourth"><?php dynamic_sidebar(++$sidebar) ?></div>
		<div class="one_half_last"><?php dynamic_sidebar(++$sidebar) ?></div>
<?php
}else{
foreach ($footer_columns as $footer => $column){
$class = ($ka_footer_columns == 1) ? '' : '';
$class = ($ka_footer_columns == 2) ? 'one_half' : $class;
$class = ($ka_footer_columns == 3) ? 'one_third' : $class;
$class = ($ka_footer_columns == 4) ? 'one_fourth' : $class;
$class = ($ka_footer_columns == 5) ? 'one_fifth' : $class;
$class = ($ka_footer_columns == 6) ? 'one_sixth' : $class; 
$lastclass = (($footer_count == $ka_footer_columns) && ($ka_footer_columns != 1)) ? '_last': '';
?><div class="<?php echo $class.$lastclass; ?>"><?php dynamic_sidebar($sidebar) ?></div><?php $footer_count++; $sidebar++; } 
}
?>



</div><!-- footer-holder -->
<div id="copyright"> &copy; SolidWize Engineering LLC </div>

</div><!-- end footer-wrapper -->
</div><!-- end footer-area -->
<?php } else {echo '<br />';} ?>
</div><!-- end footer -->


<?php if (($footer_layout == "full_bottom") || ($footer_layout == "bottom")){ ?>

<?php } ?>



<?php 
//codes to load scripts has been moved to truethemes_framework/global/javascript.php
wp_footer();
?>
<!-- begin olark code --><script type='text/javascript'>/*{literal}<![CDATA[*/window.olark||(function(i){var e=window,h=document,a=e.location.protocol=="https:"?"https:":"http:",g=i.name,b="load";(function(){e[g]=function(){(c.s=c.s||[]).push(arguments)};var c=e[g]._={},f=i.methods.length; while(f--){(function(j){e[g][j]=function(){e[g]("call",j,arguments)}})(i.methods[f])} c.l=i.loader;c.i=arguments.callee;c.f=setTimeout(function(){if(c.f){(new Image).src=a+"//"+c.l.replace(".js",".png")+"&"+escape(e.location.href)}c.f=null},20000);c.p={0:+new Date};c.P=function(j){c.p[j]=new Date-c.p[0]};function d(){c.P(b);e[g](b)}e.addEventListener?e.addEventListener(b,d,false):e.attachEvent("on"+b,d); (function(){function l(j){j="head";return["<",j,"></",j,"><",z,' onl'+'oad="var d=',B,";d.getElementsByTagName('head')[0].",y,"(d.",A,"('script')).",u,"='",a,"//",c.l,"'",'"',"></",z,">"].join("")}var z="body",s=h[z];if(!s){return setTimeout(arguments.callee,100)}c.P(1);var y="appendChild",A="createElement",u="src",r=h[A]("div"),G=r[y](h[A](g)),D=h[A]("iframe"),B="document",C="domain",q;r.style.display="none";s.insertBefore(r,s.firstChild).id=g;D.frameBorder="0";D.id=g+"-loader";if(/MSIE[ ]+6/.test(navigator.userAgent)){D.src="javascript:false"} D.allowTransparency="true";G[y](D);try{D.contentWindow[B].open()}catch(F){i[C]=h[C];q="javascript:var d="+B+".open();d.domain='"+h.domain+"';";D[u]=q+"void(0);"}try{var H=D.contentWindow[B];H.write(l());H.close()}catch(E){D[u]=q+'d.write("'+l().replace(/"/g,String.fromCharCode(92)+'"')+'");d.close();'}c.P(2)})()})()})({loader:(function(a){return "static.olark.com/jsclient/loader0.js?ts="+(a?a[1]:(+new Date))})(document.cookie.match(/olarkld=([0-9]+)/)),name:"olark",methods:["configure","extend","declare","identify"]});
/* custom configuration goes here (www.olark.com/documentation) */
olark.identify('4207-385-10-5356');/*]]>{/literal}*/</script>
<!-- end olark code -->
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0013/0103.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);

</script>



</body>
</html>