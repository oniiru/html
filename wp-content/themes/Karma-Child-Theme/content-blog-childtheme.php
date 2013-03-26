<?php
/*
* This file is a template part
* produces WordPress Loop for use in index.php (posts page)
* moved to theme root from contents folder @since version 2.6
*/
 
$ka_blogtitle = get_option('ka_blogtitle');
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
<div class="custom_blog_wrap full_width <?php echo $post_classes;?>">

<div class="post_title full_width">

<?php truethemes_begin_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php if ($linkpost == ''){ ?>
<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php }else{ ?><h2><a href="<?php echo $linkpost; ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2><?php } ?>
<?php if ($ka_posted_by != "true") {?><p class="posted-by-text"><span><?php the_date('F jS, Y'); ?> | <?php 

	# Author name display - IAN
	
	 $first_name = get_the_author_meta("first_name", get_the_author_ID());
	 $last_name = get_the_author_meta("last_name", get_the_author_ID());
	 if(!empty($first_name) || !empty($last_name)) {
		 echo $first_name;
		 	if(!empty($first_name) || !empty($last_name)) 
				echo ' ';
		 echo $last_name;
	 } else the_author_meta("nickname");
	?> | </span><?php comments_popup_link( 'Leave a comment', '1 Comment' ,'% Comments' ); ?></p><?php }else{}?>

<?php truethemes_end_post_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_title -->


<div class="custom_post_content full_width no_bg" <?php echo $ka_post_date_result; ?>>

<?php truethemes_begin_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<?php
//function to generate internal image, external image or video for content-blog.php, content-blog-single.php, and archive.php
//please find it in truethemes_framework/global/basic.php

$html = truethemes_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url);

echo $html;
?>

<?php custom_limit_content(80,  false, '', $post->ID , $ka_blogbutton) ?>

<?php if(function_exists('selfserv_shareaholic')) { selfserv_shareaholic(); } ?>

<?php
global $post; 
$post_title = get_the_title($post->ID);
$permalink = get_permalink($post->ID);
if ($ka_dragshare == "true"){ echo "<a class='post_share sharelink_small' href='$permalink' rel='prettySociable;title:$post_title;excerpt:'>Share</a>"; }

?>

<?php truethemes_end_post_content_hook();// action hook, see truethemes_framework/global/hooks.php ?>

</div><!-- end post_content -->



</div><!-- end blog_wrap -->


<?php endwhile; else: ?>
<h2>Nothing Found</h2>
<p>Sorry, it appears there is no content in this section.</p>
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