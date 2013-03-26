<?php 
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

//use truethemes image croping script, function moved to functions/global/basic.php
$image_src = truethemes_crop_image($thumb,$external_image_url,$image_width,$image_height);
?>



<div class="single_blog_wrap">
<div class="post_title">
<h2><?php the_title(); ?></h2>
<?php if ($ka_posted_by != "true") {?><p class="posted-by-text"><span>Posted by:</span> <?php the_author_posts_link(); ?></p><?php }?>
</div><!-- end post_title -->


<div class="post_content" <?php echo $ka_post_date_result; ?>>

<?php
//function to generate internal image, external image or video for content-blog.php, content-blog-single.php, and archive.php
//please find it in functions/global/basic.php

$html = truethemes_generate_blog_image($image_src,$image_width,$image_height,$blog_image_frame,$linkpost,$permalink,$video_url);

echo $html;
?>


<?php the_content(); ?>
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
<?php if ($ka_dragshare == "true"){ echo '<a class="post_share sharelink_small" href="#" rel="prettySociable">Share</a>'; }?>
<?php }else{}?>


<?php if ($ka_related_posts == "true"){ 
echo '<br class="clear" /><br class="clear" />';
echo do_shortcode("[related_posts_content limit=\"".$ka_related_posts_count."\" title=\"".$ka_related_posts_title."\"]"); 

}?>
</div><!-- end post_content -->



<div class="post_footer">
<div class="post_cats"><p><span>Categories:</span> <?php the_category(', '); ?></p></div><!-- end post_cats -->

<?php if (get_the_tags()) : ?>
<div class="post_tags"><p><span>Tags:</span>  <?php the_tags('', ', '); ?></p></div><!-- end post_tags -->
<?php endif; ?>
</div><!-- end post_footer -->




<?php if ($ka_blogauthor == "true"){ ?>
<div class="comment-wrap" id="about-author-wrap">
  <div class="comment-content">
  	<div class="comment-gravatar"><?php echo get_avatar(get_the_author_meta('email'),$size='80',$default=get_template_directory_uri().'/images/_global/default-grav.jpg' ); ?>
  	</div><!-- end comment-gravatar -->
  
  	<div class="comment-text">
    <p class="comment-author-about">About the Author</p>
    <?php the_author_meta('description'); ?>
    </div><!-- end comment-text -->
    
  </div><!-- end comment-content -->
</div><!-- end comment-wrap -->
<?php } else {} ?>
</div><!-- end single_blog_wrap -->



<?php comments_template('', true); ?>
<?php endwhile; else: ?>
<h2>Nothing Found</h2>
<p>Sorry, it appears there is no content in this section.</p>
<?php endif; ?>
<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>