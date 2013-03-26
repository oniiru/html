<?php get_header(); ?>
<?php $ka_blogtitle = get_option('ka_blogtitle');
$ka_crumbs = get_option('ka_crumbs'); ?>
</div><!-- header-area -->
</div><!-- end rays -->
</div><!-- end header-holder -->
</div><!-- end header -->

<?php truethemes_before_main_hook();// action hook, see truethemes_framework/global/hooks.php ?>

<div id="main">
    <div class="main-area">
      <div class="tools">
        <div class="holder">
          <div class="frame">
          
  <?php truethemes_before_article_title_hook();// action hook, see truethemes_framework/global/hooks.php ?>        
          
            <h1><?php echo $ka_blogtitle; ?></h1>
            <?php if ($ka_crumbs == "true"){ $bc = new simple_breadcrumb;} else {} ?>
          
<?php truethemes_after_searchform_hook();// action hook, see truethemes_framework/global/hooks.php ?>          
          
          
          </div><!-- end frame -->
        </div><!-- end holder -->
      </div><!-- end tools -->
    
    <div class="main-holder">			
      <div id="content" class="full_content_blog">
        <?php get_template_part('content-blog-single','childtheme'); ?>  
      </div><!-- end content -->          
    </div><!-- end main-holder -->
  </div><!-- main-area -->
<?php get_footer(); ?>