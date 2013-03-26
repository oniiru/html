<?php
/*
* This file is a template part
* produces WordPress Loop for use in index.php (posts page)
* moved to theme root from contents folder @since version 2.6
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
 
$linkpost = get_post_meta($post->ID, "_jcycle_url_value", $single = true);
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

<?php
//required by theme check plugin
//need to use post_class()  http://codex.wordpress.org/Template_Tags/post_class
//or http://codex.wordpress.org/Function_Reference/get_post_class
//these are special style class, use browser view source to see.
$array_post_classes = get_post_class(); 
$post_classes = '';
foreach($array_post_classes as $post_class){
$post_classes .= " ".$post_class;
}
?>
<div class="blog_wrap <?php echo $post_classes;?>">

<div class="post_title">

<?php truethemes_begin_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php if ($linkpost == ''){ ?>
<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php }else{ ?><h2><a href="<?php echo $linkpost; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2><?php } ?>
<?php if ($ka_posted_by != "true") {?><p class="posted-by-text"><span><?php _e('Posted by:', 'truethemes_localize') ?></span> <?php the_author_posts_link(); ?></p><?php }else{}?>

<?php truethemes_end_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_title -->


<div class="post_content" <?php echo $ka_post_date_result; ?>>

<?php truethemes_begin_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php
//function to generate internal image, external image or video for content-blog.php, content-blog-single.php, and archive.php
//please find it in truethemes_framework/global/basic.php

$html = truethemes_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url);

echo $html;
?>

<?php limit_content(80,  true, ''); ?>
<a class="ka_button small_button" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><span><?php echo $ka_blogbutton; ?></span></a>
<?php if(function_exists('selfserv_shareaholic')) { selfserv_shareaholic(); } ?>

<?php if ($ka_post_date != "true") { ?>
<div class="post_date">
	<span class="day"><?php the_time('j'); ?></span>
    <br />
    <span class="month"><?php echo strtoupper(get_the_time('M')); ?></span>
</div><!-- end post_date -->

<div class="post_comments">
	<a href="<?php echo the_permalink().'#post-comments'; ?>"><span><?php comments_number('0', '1', '%'); ?></span></a>
</div><!-- end post_comments -->
<?php
global $post; 
$post_title = get_the_title($post->ID);
$permalink = get_permalink($post->ID);
if ($ka_dragshare == "true"){ echo "<a class='post_share sharelink_small' href='$permalink' data-gal='prettySociable'>Share</a>"; }

?>
<?php }else{}?>

<?php truethemes_end_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_content -->


<div class="post_footer">

<?php truethemes_begin_post_footer_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div class="post_cats"><p><span><?php _e('Categories:', 'truethemes_localize') ?></span> <?php the_category(', '); ?></p></div><!-- end post_cats -->

<?php if (get_the_tags()) : ?>
<div class="post_tags"><p><span><?php _e('Tags:', 'truethemes_localize') ?></span>  <?php the_tags('', ', '); ?></p></div><!-- end post_tags -->
<?php endif; ?>

<?php truethemes_end_post_footer_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_footer -->

</div><!-- end blog_wrap -->


<?php endwhile; else: ?>
<h2><?php _e('Nothing Found', 'truethemes_localize') ?></h2>
<p><?php _e('Sorry, it appears there is no content in this section.', 'truethemes_localize') ?></p>
<?php endif; ?>
<?php
if(function_exists('wp_pagenavi')) { 
wp_pagenavi(); 
}else{
//not using this function, just for theme check plugin requirement.
//do not remove this.. 
paginate_links(); 
} 
?>