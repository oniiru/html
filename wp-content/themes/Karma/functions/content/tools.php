<?php $ka_searchbar = get_option('ka_searchbar');
$ka_crumbs = get_option('ka_crumbs'); ?>
<div class="main-area">
<div class="tools">
<div class="holder">
<div class="frame">
<?php if ( get_post_meta($post->ID, '_pagetitle_value', true) ) { ?>
<h1><?php echo get_post_meta($post->ID, "_pagetitle_value", $single = true); ?></h1><?php }else { ?>
<h1><?php if(have_posts()) : while(have_posts()) : the_post(); ?><?php the_title(); ?><?php endwhile; endif; ?></h1><?php } ?>
<?php if ($ka_searchbar == "true"){load_template(TEMPLATEPATH . '/functions/content/searchform.php');} ?>
<?php if ($ka_crumbs == "true"){ ?><?php $bc = new simple_breadcrumb; ?><?php } ?>
</div><!-- end frame -->
</div><!-- end holder -->
</div><!-- end tools -->