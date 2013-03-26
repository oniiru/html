<?php get_header(); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
<?php
$ka_404title = get_option('ka_404title');
$ka_404message = get_option('ka_404message');
$ka_404sitemap = get_option('ka_404sitemap');
$ka_searchbar = get_option('ka_searchbar');
$ka_crumbs = get_option('ka_crumbs');
?>
<div class="main-area">
<div class="tools">
<div class="holder">
<div class="frame">

<?php truethemes_before_article_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<h1><?php echo $ka_404title; ?></h1>
<?php if ($ka_searchbar == "true"){get_template_part('searchform','childtheme');} else {} ?>
<?php if ($ka_crumbs == "true"){ $bc = new simple_breadcrumb;} else {} ?>


<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>


</div><!-- end frame -->
</div><!-- end holder -->
</div><!-- end tools -->



<div class="main-holder">
<div id="content" class="content_full_width">
<div class="four_error">
<div class="four_message">
<h1 class="four_o_four"><?php echo $ka_404title;?></h1>
<?php echo $ka_404message;?>
</div><!-- end four_message -->
</div><!-- end four_error -->

</div><!-- end content -->
</div><!-- end main-holder -->
</div><!-- main-area -->



<?php get_footer(); ?>