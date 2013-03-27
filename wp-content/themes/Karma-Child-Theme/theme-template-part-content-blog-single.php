<?php
/*
* This file is a template part
* produces WordPress Loop for use in single.php
* moved to theme root from contents folder @since version 2.6
* 
*/
 
$ka_blogtitle = get_option('ka_blogtitle');
$ka_searchbar = get_option('ka_searchbar');
$ka_crumbs = get_option('ka_crumbs');
$ka_blogbutton = get_option('ka_blogbutton');
$ka_blogauthor = get_option('ka_blogauthor');
$ka_related_posts = get_option('ka_related_posts');
$ka_related_posts_title = get_option('ka_related_posts_title');
$ka_related_posts_count = get_option('ka_related_posts_count');
$ka_posted_by = get_option('ka_posted_by');
$ka_post_date = get_option('ka_post_date');
if ($ka_post_date != "false"){ $ka_post_date_result = 'style="background:none !important;"';}else{$ka_post_date_result = '';}
$ka_dragshare = get_option('ka_dragshare');
$blog_image_frame = get_option('ka_blog_image_frame');

if (have_posts()) : while (have_posts()) : the_post(); 
//retrieve all post meta of posts in the loop.
 
$external_image_url = get_post_meta($post->ID,'truethemes_external_image_url',true);
$video_url = get_post_meta($post->ID,'truethemes_video_url',true);
$permalink = get_permalink($post->ID);
//prepare to get image
$thumb = get_post_thumbnail_id();
$image_width = 538;
$image_height = 218;

//use truethemes image croping script, function moved to truethemes_framework/global/basic.php
$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=366394870051631";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<div class="single_blog_wrap">
<div class="post_title">

<?php truethemes_begin_single_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<h2><?php the_title(); ?></h2>
<?php if ($ka_posted_by != "true") {?><p class="posted-by-text"><span><?php _e('Posted by:', 'truethemes_localize') ?></span> <?php the_author_posts_link(); ?></p><?php }?>

<?php truethemes_end_single_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_title -->


<div class="post_content" <?php echo $ka_post_date_result; ?>>

<?php truethemes_begin_single_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php
//function to generate internal image, external image or video for content-blog.php, content-blog-single.php, and archive.php
//please find it in truethemes_framework/global/basic.php

$html = truethemes_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url);

echo $html;
?>


<?php 

the_content(); 

if(function_exists('truethemes_link_pages')){
//Will always use this function for <!--nextpage-->
//function modified from wp_link_pages() to provide more style class
//so that we can style the page links.
truethemes_link_pages();
}else{
//WordPress default page number links function for <!--nextpage-->
//This is for theme check plugin requirement.
//Do not remove this function checks, but comments can be removed..
wp_link_pages();
} 

?>

<?php if(function_exists('selfserv_shareaholic')) { selfserv_shareaholic(); } ?>

<?php if ($ka_post_date != "true") { ?>
<div class="post_date">
	<span class="day"><?php the_time('j'); ?></span>
    <br />
    <span class="month"><?php echo strtoupper(get_the_time('M')); ?></span>
</div><!-- end post_date -->


<div id="rssbar"><p>Subscribe via <a href="http://solidwize.com/feed">RSS</a>, Email <span style="margin-left:245px;">or</span></p><div id="facebookthing"><div class="fb-like" data-href="http://facebook.com/solidwize" data-send="false" data-layout="button_count" data-width="70" data-show-faces="false" data-font="tahoma"></div>
</div><div id="rssthanks">Thanks!</div>
<div id="rssemail">	<form action="http://solidwize.us4.list-manage.com/subscribe/post" method="post" target="AweberFormSubmitFrame">
<div style="display: none;">
<input type="hidden" name="u" value="494e8ca59ce929723b6b66b09" />
<input type="hidden" name="id" value="e6f87d622e" />
</div>
           <!-- #first_step -->

            <div id="first_step">

<iframe id="AweberFormSubmitFrame" style="display: none" name="AweberFormSubmitFrame" src="about:blank"></iframe>	
	<br>
               <div class="form">
                <input type="text" name="MERGE0" id="MERGE0" value="email address"> 
     		  <input class="submit" type="submit" name="submit" id="submit" value="Submit" /></div> 
            </div>       

           
</form>
</div></div>
<br>
<?php if ($ka_dragshare == "true"){ echo "<a class='post_share sharelink_small' href='$permalink' data-gal='prettySociable'>Share</a>"; }?>
<?php }else{}?>

<?php if ($ka_related_posts == "true"){ 
echo '<br class="clear" /><br class="clear" />';
echo do_shortcode("[related_posts_content limit=\"".$ka_related_posts_count."\" title=\"".$ka_related_posts_title."\"]"); 

}?>


<?php truethemes_end_single_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_content -->


<div class="post_footer">

<?php truethemes_begin_single_post_footer_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div class="post_cats"><p><span><?php _e('Categories:', 'truethemes_localize') ?></span> <?php the_category(', '); ?></p></div><!-- end post_cats -->

<?php if (get_the_tags()) : ?>
<div class="post_tags"><p><span><?php _e('Tags:', 'truethemes_localize') ?></span>  <?php the_tags('', ', '); ?></p></div><!-- end post_tags -->
<?php endif; ?>

<?php truethemes_end_single_post_footer_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_footer -->




<?php if ($ka_blogauthor == "true"){ ?>
<div class="comment-wrap" id="about-author-wrap">
  <div class="comment-content">
  	<div class="comment-gravatar"><?php echo get_avatar(get_the_author_meta('email'),$size='80',$default=get_template_directory_uri().'/images/_global/default-grav.jpg' ); ?>
  	</div><!-- end comment-gravatar -->
  
  	<div class="comment-text">
    <p class="comment-author-about"><?php _e('About the Author:', 'truethemes_localize') ?></p>
    <?php the_author_meta('description'); ?>
    </div><!-- end comment-text -->
    
  </div><!-- end comment-content -->
</div><!-- end comment-wrap -->
<?php } else {} ?>
</div><!-- end single_blog_wrap -->



<?php 
/*
* Add check on whether to disable comments througout site.
* @since version 2.6 development.
*/
global $ttso;
$show_post_comments = $ttso->ka_post_comments;
if($show_post_comments !='false'):
comments_template('', true); 
endif;
?>

<?php endwhile; else: ?>
<h2><?php _e('Nothing Found', 'truethemes_localize') ?></h2>
<p><?php _e('Sorry, it appears there is no content in this section.', 'truethemes_localize') ?></p>
<?php endif; ?>
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>