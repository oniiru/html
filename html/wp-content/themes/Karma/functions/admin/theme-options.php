<?php

add_action('init','of_options');

if (!function_exists('of_options')) {
function of_options(){

// VARIABLES
$themename = "Karma";
$shortname = "ka";

// Populate siteoptions option in array for use in theme
global $of_options;
$of_options = get_option('of_options');
$GLOBALS['template_path'] = KARMA_FRAMEWORK;


//Access the WordPress Categories via an Array
$of_categories = array();  
$of_categories_obj = get_categories('hide_empty=0');
foreach ($of_categories_obj as $of_cat) {
$of_categories[$of_cat->cat_ID] = $of_cat->cat_name;}
$categories_tmp = array_unshift($of_categories, "Select a category:");    


//Access the WordPress Pages via an Array
$of_pages = array();
$of_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($of_pages_obj as $of_page) {
$of_pages[$of_page->ID] = $of_page->post_name; }
$of_pages_tmp = array_unshift($of_pages, "Select the Blog page:");       


// Image Alignment radio box
$options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 


// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 


//More Options
$uploads_arr = wp_upload_dir();
$all_uploads_path = $uploads_arr['path'];
$all_uploads = get_option('of_uploads');
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");


//Footer Columns Array
$footer_columns = array("1","2","3","4","5","6");


//Paths for "type" => "images"
$url =  get_template_directory_uri() . '/functions/admin/images/color-schemes/';
$footerurl =  get_template_directory_uri() . '/functions/admin/images/footer-layouts/';
$fonturl =  get_template_directory_uri() . '/functions/admin/images/fonts/';
$framesurl =  get_template_directory_uri() . '/functions/admin/images/image-frames/';
$logourl =  get_template_directory_uri() . '/functions/admin/images/logo-builder/';


//Access the WordPress Categories via an Array
$exclude_categories = array();  
$exclude_categories_obj = get_categories('hide_empty=0');
foreach ($exclude_categories_obj as $exclude_cat) {
$exclude_categories[$exclude_cat->cat_ID] = $exclude_cat->cat_name;}










/*-----------------------------------------------------------------------------------*/
/* Create Site Options Array */
/*-----------------------------------------------------------------------------------*/
$options = array();

$options[] = array( "name" => "General Settings",
			"type" => "heading");
			

$options[] = array( "name" => "Website Logo",
			"desc" => "Upload a custom logo for your Website.",
			"id" => $shortname."_sitelogo",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => "Login Screen Logo",
			"desc" => "Upload a custom logo for your Wordpress login screen.",
			"id" => $shortname."_loginlogo",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => "Favicon",
			"desc" => "Upload a 16px x 16px image that will represent your website's favicon.<br /><br /><em>To ensure cross-browser compatibility, we recommend converting the favicon into .ico format before uploading. (<a href=\"http://www.favicon.cc/\">www.favicon.cc</a>)</em>",
			"id" => $shortname."_favicon",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => "Logo Builder - Select an Icon",
			"desc" => "Select an icon to be used for your logo.<br><br><em>note: you should only select an icon if you won't be uploading a custom logo.</em>",
			"id" => $shortname."_logo_icon",
			"std" => "nologo",
			"type" => "images",
			"options" => array(
				'custom-logo-1.png' => $logourl . 'logo-1.png',
				'custom-logo-2.png' => $logourl . 'logo-2.png',
				'custom-logo-3.png' => $logourl . 'logo-3.png',
				'custom-logo-4.png' => $logourl . 'logo-4.png',
				'custom-logo-5.png' => $logourl . 'logo-5.png',
				'custom-logo-6.png' => $logourl . 'logo-6.png',
				'custom-logo-7.png' => $logourl . 'logo-7.png',
				'custom-logo-8.png' => $logourl . 'logo-8.png',
				'custom-logo-9.png' => $logourl . 'logo-9.png'
				));
				
$options[] = array( "name" => "Logo Builder - Text",
			"desc" => "Enter the text to be used for your logo.<br><br><em>note: you should only enter logo text if you won't be uploading a custom logo.</em>",
			"id" => $shortname."_logo_text",
			"std" => "",
			"type" => "text");
			
$options[] = array( "name" => "Hide Meta Boxes",
			"desc" => "This functionality hides meta boxes in the Dashboard to help Wordpress feel more like a CMS. This includes: Comments, Discussion, Trackbacks, Custom Fields, Author, and Slug. <em>Un-check this box to disable this functionality.</em>",
			"id" => $shortname."_hidemetabox",
			"std" => "true",
			"type" => "checkbox");
			
									   
$options[] = array( "name" => "Tracking Code",
			"desc" => "Paste Google Analytics (or other) tracking code here.",
			"id" => $shortname."_google_analytics",
			"std" => "",
			"type" => "textarea");
			
			
			
//filter to allow developer to add new options to general settings.			
$options = apply_filters('theme_option_general_settings',$options);			
			
			
			
			
			
$options[] = array( "name" => "Styling Options",
			"type" => "heading");
		
$options[] = array( "name" => "Website Color Scheme",
			"desc" => "Select the primary color scheme for your website.",
			"id" => $shortname."_main_scheme",
			"std" => "",
			"type" => "images",
			"options" => array(
				'karma-dark' => $url . 'main-karma-dark.jpg',
				'karma-coffee' => $url . 'main-karma-coffee.jpg',
				'karma-teal-grey' => $url . 'main-karma-teal-grey.jpg',
				'karma-blue-grey' => $url . 'main-karma-blue-grey.jpg',
				'karma-autumn' => $url . 'main-karma-autumn.jpg',
				'karma-teal' => $url . 'main-karma-teal.jpg',
				'karma-grey' => $url . 'main-karma-grey.jpg',
				'karma-cherry' => $url . 'main-karma-cherry.jpg',
				'karma-purple' => $url . 'main-karma-purple.jpg',
				'karma-silver' => $url . 'main-karma-silver.jpg',
				'karma-fire' => $url . 'main-karma-fire.jpg',
				'karma-violet' => $url . 'main-karma-violet.jpg',
				'karma-royal-blue' => $url . 'main-karma-royal-blue.jpg',
				'karma-golden' => $url . 'main-karma-golden.jpg',
				'karma-periwinkle' => $url . 'main-karma-periwinkle.jpg',
				'karma-cool-blue' => $url . 'main-karma-cool-blue.jpg',
				'karma-lime-green' => $url . 'main-karma-lime-green.jpg',
				'karma-pink' => $url . 'main-karma-pink.jpg',
				'karma-sky-blue' => $url . 'main-karma-sky-blue.jpg',
				'karma-forest-green' => $url . 'main-karma-forest-green.jpg'
				
				
				));
$options[] = array( "name" => "Secondary Color Scheme",
			"desc" => "Select a secondary color scheme only if you wish to override the default secondary color.",
			"id" => $shortname."_secondary_scheme",
			"std" => "default",
			"type" => "images",
			"options" => array(
				'default' => $url . 'secondary-default.jpg',
				'secondary-coffee' => $url . 'secondary-coffee.jpg',
				'secondary-cherry' => $url . 'secondary-cherry.jpg',
				'secondary-autumn' => $url . 'secondary-autumn.jpg',
				'secondary-fire' => $url . 'secondary-fire.jpg',
				'secondary-golden' => $url . 'secondary-golden.jpg',
				'secondary-lime-green' => $url . 'secondary-lime-green.jpg',
				'secondary-purple' => $url . 'secondary-purple.jpg',
				'secondary-pink' => $url . 'secondary-pink.jpg',
				'secondary-violet' => $url . 'secondary-violet.jpg',
				'secondary-periwinkle' => $url . 'secondary-periwinkle.jpg',
				'secondary-teal' => $url . 'secondary-teal.jpg',
				'secondary-forest-green' => $url . 'secondary-forest-green.jpg',
				'secondary-teal-grey' => $url . 'secondary-teal-grey.jpg',
				'secondary-blue-grey' => $url . 'secondary-blue-grey.jpg',
				'secondary-royal-blue' => $url . 'secondary-royal-blue.jpg',
				'secondary-cool-blue' => $url . 'secondary-cool-blue.jpg',
				'secondary-sky-blue' => $url . 'secondary-sky-blue.jpg',
				'secondary-silver' => $url . 'secondary-silver.jpg',
				'secondary-dark' => $url . 'secondary-dark.jpg',
				'secondary-grey' => $url . 'secondary-grey.jpg'
				));
									
$options[] = array( "name" => "Custom CSS",
			"desc" => "Use this area to add custom CSS to your website.",
			"id" => $shortname."_custom_css",
			"std" => "",
			"type" => "textarea");
			
			
			
//filter to allow developer to add in new options for styling options.			
$options = apply_filters('theme_option_styling_settings',$options);			
			
			
			
			
			
$options[] = array( "name" => "Typography",
			"type" => "heading");
			
			
$options[] = array( "name" => "Google Web Fonts",
			"desc" => "Select a font face to be used for your website's headings.<br><br>Font names (from left to right):<br>- (none)<br>- Droid Sans<br>- Cabin<br>- Cantarell<br>- Cuprum<br>- Oswald<br>- Neuton<br>- Oritron<br>- Arvo<br>- Kreon<br>- Indie Flower<br>- Josefin Sans",
			"id" => $shortname."_google_font",
			"std" => "nofont",
			"type" => "images",
			"options" => array(
				'nofont' => $fonturl . 'no-font.png',
				'Droid Sans' => $fonturl . '1-droid-sans.png',
				'Cabin' => $fonturl . '2-cabin.png',
				'Cantarell' => $fonturl . '3-cantarell.png',
				'Cuprum' => $fonturl . '4-cuprum.png',
				'Oswald' => $fonturl . '5-oswald.png',
				'Neuton' => $fonturl . '6-neuton.png',
				'Orbitron' => $fonturl . '7-orbitron.png',
				'Arvo' => $fonturl . '8-arvo.png',
				'Kreon' => $fonturl . '9-kreon.png',
				'Indie Flower' => $fonturl . '10-indie-flower.png',
				'Josefin Sans' => $fonturl . '11-josefin-sans.png'
				));
				
				
$options[] = array( "name" => "Custom Google Web Font",
			"desc" => "Enter a custom font name If you prefer to use a font that's not listed above.<br><br>Here is the complete list of available <a href=\"http://www.google.com/webfonts\" target=\"_blank\">Google Web Fonts</a>.",
			"id" => $shortname."_custom_google_font",
			"std" => "",
			"type" => "text");
			
			
//allow developer to add in new options to typography settings.			
$options = apply_filters('theme_option_typography_settings',$options);	


			
			
			
			
$options[] = array( "name" => "Interface Options",
			"type" => "heading");
			
$options[] = array( "name" => "Toolbar",
			"desc" => "A toolbar is displayed above the main navigation by default. <em>Un-check this box to disable the toolbar.</em>",
			"id" => $shortname."_toolbar",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "Dropdown Navigation",
			"desc" => "<em>Check this box</em> to disable the dropdown navigation.",
			"id" => $shortname."_dropdown",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "Navigation Descriptions",
			"desc" => "<em>Check this box</em> to disable the navigation descriptions.",
			"id" => $shortname."_nav_description",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "Search Bar",
			"desc" => "A search bar is displayed by default. <em>Un-check this box to disable the search bar.</em>",
			"id" => $shortname."_searchbar",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "Search Bar Text",
			"desc" => "Customize the text that is displayed in the search bar.",
			"id" => $shortname."_searchbartext",
			"std" => "Search",
			"type" => "text");
			
$options[] = array( "name" => "Breadcrumbs",
			"desc" => "Breadcrumbs are displayed by default. <em>Un-check this box to disable the breadcrumbs.</em>",
			"id" => $shortname."_crumbs",
			"std" => "true",
			"type" => "checkbox");
			
			
$options[] = array( "name" => "Footer Columns",
			"desc" => "Select the number of columns you would like to display in the footer.",
			"id" => $shortname."_footer_columns",
			"std" => "4",
			"type" => "select",
			"options" => $footer_columns);
		
			
$options[] = array( "name" => "Footer Style",
			"desc" => "Select a footer design style.<br />(full, half, small)",
			"id" => $shortname."_footer_layout",
			"std" => "full_bottom",
			"type" => "images",
			"options" => array(
				'full_bottom' => $footerurl . 'footer-layout-1.png',
				'full' => $footerurl . 'footer-layout-2.png',
				'bottom' => $footerurl . 'footer-layout-3.png'
				));		
				
$options[] = array( "name" => "Scroll to Top Link",
			"desc" => "A scroll-to-top link is added to the footer by default. <em>Un-check this box to disable the link.</em>",
			"id" => $shortname."_scrolltoplink",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "Scroll to Top Label",
			"desc" => "Customize the text for the scroll-to-top link.",
			"id" => $shortname."_scrolltoplinktext",
			"std" => "top",
			"type" => "text");
				
//allows developer to add in new options to interface options page.				
$options = apply_filters('theme_option_interface_settings',$options);	


				
				


$options[] = array( "name" => "Forms",
			"type" => "heading");
			
$options[] = array( "name" => "Form Builder",
			"desc" => "A powerful form builder is included by default. <em>Un-check this box to disable the form builder.</em>",
			"id" => $shortname."_formbuilder",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "reCAPTCHA: Public Key",
			"desc" => "Enter your reCAPTCHA Public Key.<br><br>
			You can obtain your reCAPTCHA keys at: <a href=\"http://www.google.com/recaptcha\" target=\"_blank\">google.com/recaptcha</a><br><br><em>Simply leave this field blank if you won't be using this functionality.</em>",
			"id" => $shortname."_publickey",
			"std" => "",
			"type" => "text");			
			
$options[] = array( "name" => "reCAPTCHA: Private Key",
			"desc" => "Enter your reCAPTCHA Private Key.<br><br>
			You can obtain your reCAPTCHA keys at: <a href=\"http://www.google.com/recaptcha\" target=\"_blank\">google.com/recaptcha</a><br><br><em>Simply leave this field blank if you won't be using this functionality.</em>",
			"id" => $shortname."_privatekey",
			"std" => "",
			"type" => "text");
			
									
$options[] = array( "name" => "Required Text",
			"desc" => "Customize the text that will be displayed next to required fields.",
			"id" => $shortname."_contact_required",
			"std" => "(required)",
			"type" => "text");
			
$options[] = array( "name" => "Success Message",
			"desc" => "Customize the success message that will be displayed after the user submits the form.",
			"id" => $shortname."_contact_successmsg",
			"std" => "Thank you for messaging us. We will get back to you as soon as possible. Cheers!",
			"type" => "textarea");				
			
//allow developer to add in new options to forms.				
$options = apply_filters('theme_option_forms_settings',$options);	

			
			
			
			
$options[] = array( "name" => "Blog Settings",
			"type" => "heading");
			
$options[] = array( "name" => "Featured Images",
			"desc" => "Select the image frame style for featured images.",
			"id" => $shortname."_blog_image_frame",
			"std" => "modern",
			"type" => "images",
			"options" => array(
				'modern' => $framesurl . 'modern.png',
				'shadow' => $framesurl . 'shadow.png'
				));
			
$options[] = array( "name" => "Blog Page",
			"desc" => "Select your blog page from the dropdown list.",
			"id" => $shortname."_blogpage",
			"std" => "",
			"type" => "select",
			"options" => $of_pages);

$options[] = array( "name" => "Banner Text",
			"desc" => "This text is displayed in the banner area of the Blog page.",
			"id" => $shortname."_blogtitle",
			"std" => "Blog",
			"type" => "text");
			
$options[] = array( "name" => "Button Text",
			"desc" => "These buttons are displayed after each blog post excerpt.",
			"id" => $shortname."_blogbutton",
			"std" => "Continue Reading &rarr;",
			"type" => "text");
			
$options[] = array( "name" => "Drag-to-Share",
					"desc" => "Drag-to-share functionality is added to each blog post by default. <em>Un-check this box to disable drag-to-share.</em>",
					"id" => $shortname."_dragshare",
					"std" => "true",
					"type" => "checkbox");
			
$options[] = array( "name" => "\"Posted by\" Information",
			"desc" => "<em>Check this box</em> to disable the \"Posted by\" information located under each Blog Post Title.</em>",
			"id" => $shortname."_posted_by",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "Post Date and Comments",
			"desc" => "<em>Check this box</em> to disable the posted date and comments count located next to each Blog Post.</em>",
			"id" => $shortname."_post_date",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "About-the-Author",
			"desc" => "The author's bio is displayed at the end of each blog post by default. <em>Un-check this box to disable the bio.</em> (Author bio's can be set in the Wordpress user profile page. (<a href=\"profile.php\">Users > Your Profile</a>)",
			"id" => $shortname."_blogauthor",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "Related Posts",
			"desc" => "Related posts are displayed at the end of each blog post by default. <em>Un-check this box to disable the related posts.</em>",
			"id" => $shortname."_related_posts",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => "Related Posts Title",
			"desc" => "Enter the title that is displayed above the list of related posts. <em>Simply leave this field blank if you won't be using this functionality.</em>",
			"id" => $shortname."_related_posts_title",
			"std" => "Related Posts",
			"type" => "text");
			
$options[] = array( "name" => "Related Posts Count",
			"desc" => "Enter the amount of related posts you'd like to display. <em>Simply leave this field blank if you won't be using this functionality.</em>",
			"id" => $shortname."_related_posts_count",
			"std" => "5",
			"type" => "text");
			
			
$options[] = array( "name" => "Exclude Categories",
			"desc" => "Check off any post categories that you'd like to exclude from the blog.",
			"id" => $shortname."_blogexcludetest",
			"std" => "",
			"type" => "multicheck",
			"options" => $exclude_categories);
			
//allow developer to add in new options to blog settings.			
$options = apply_filters('theme_option_blog_settings',$options);				
			
			
			
			
			
			
$options[] = array( "name" => "Homepage Settings",
			"type" => "heading");

			
$options[] = array( "name" => "jQuery Slider Post Category",
			"desc" => "Select the category that will be used for generating the jQuery slides.",
			"id" => $shortname."_jcycle_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $of_categories);
			
$options[] = array( "name" => "JQuery Slider Pause Settings",
			"desc" => "Check this box if you would like the jQuery slider to pause when the user hovers over a slide.",
			"id" => $shortname."_jcycle_pause_hover",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "jQuery Homepage Slider Time",
			"desc" => "Enter the desired amount of time you would like to display each slide. (milliseconds)",
			"id" => $shortname."_jcycle_timeout",
			"std" => "8000",
			"type" => "text");
			
			
$options[] = array( "name" => "3D CU3ER - Slider ID Number",
			"desc" => "Enter the ID number of the 3D slider you would like to embed on the \"Homepage :: 3D\" page template.<br><br><em>Not sure where to find the slider ID number? <a href=\"http://themes.5-squared.com/support/cu3er-instructions.html\" target=\"_blank\">View these visual instructions.</a></em>",
			"id" => $shortname."_cu3er_slider_id",
			"std" => "1",
			"type" => "text");
			
			
//allow developer to add in new options to homepage settings.			
$options = apply_filters('theme_option_home_settings',$options);			
			
			
			
			
			
			
			
			
			$options[] = array( "name" => "Javascript Settings",
			"type" => "heading");				
			
$options[] = array( "name" => "Enable Testimonial Slider",
			"desc" => "Check this box to enable the testimonial slider.",
			"id" => $shortname."_testimonial_enable",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "Testimonial Slider Pause Settings",
			"desc" => "Check this box if you would like the testimonial slider to pause when the user hovers over a slide.",
			"id" => $shortname."_testimonial_pause_hover",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => "Testimonial Slider Time",
			"desc" => "Enter the desired amount of time you would like to display each testimonial. (milliseconds)",
			"id" => $shortname."_testimonial_timeout",
			"std" => "8000",
			"type" => "text");
		
		
//allow developer to add in new options to javascript settings.			
$options = apply_filters('theme_option_javascript_settings',$options);
			
			
			
			
			
			$options[] = array( "name" => "Additional Settings",
			"type" => "heading");
			
$options[] = array( "name" => "404 Page Banner Text",
			"desc" => "Set the page title that is displayed in the banner area of the 404 Error Page.",
			"id" => $shortname."_404title",
			"std" => "Page not Found",
			"type" => "text");
			
$options[] = array( "name" => "404 Message",
			"desc" => "Set the message that is displayed on the 404 Error Page.",
			"id" => $shortname."_404message",
			"std" => "Our Apologies, but the page you are looking for could not be found. Here are some links that you might find useful:
			<ul>
			<li><a href=\"http://www.\">Home</a></li>
			<li><a href=\"http://www.\">Sitemap</a></li>
			<li><a href=\"http://www.\">Contact Us</a></li>
			</ul>",
			"type" => "textarea");
			
$options[] = array( "name" => "Search Results Banner Text",
			"desc" => "Set the page title that is displayed in the banner area of the Search Results Page.",
			"id" => $shortname."_results_title",
			"std" => "Search Results",
			"type" => "text");
			
$options[] = array( "name" => "Search Results Fallback Message",
			"desc" => "Set the message that is displayed when a search comes back with no results.",
			"id" => $shortname."_results_fallback",
			"std" => "<p>Our Apologies, but your search did not return any results. Please try using a different search term.</p>",
			"type" => "textarea");
			
$options[] = array( "name" => "Sitemap: Column one title",
			"desc" => "This title gets displayed in the first column above the list of Pages.<br /><br /><em>Simply disregard this section if you won't be using the 'Sitemap 2' Page template.</em>",
			"id" => $shortname."_sitemap2_column1",
			"std" => "Pages",
			"type" => "text");
			
$options[] = array( "name" => "Sitemap: Column two title",
			"desc" => "This title gets displayed in the second column above the list of Posts.<br /><br /><em>Simply disregard this section if you won't be using the 'Sitemap 2' Page template.</em>",
			"id" => $shortname."_sitemap2_column2",
			"std" => "Posts",
			"type" => "text");
			
			
$options[] = array( "name" => "Sitemap: Column three text",
			"desc" => "This text gets displayed in the third column on the Sitemap page.<br /><br /><em>Simply disregard this section if you won't be using the 'Sitemap 2' Page template.</em>",
			"id" => $shortname."_sitemap2_column3",
			"std" => "<h2>Contact</h2>
    <p><strong>Email:</strong> <a href=\"mailto:you@yoursite.com\">you@yoursite.com</a><br />
	<strong>Mobile:</strong> 444-555-6666</p>",
			"type" => "textarea");
			
			
//allow developer to add in new options to Additional settings.			
$options = apply_filters('theme_option_additional_settings',$options);
			


		

update_option('of_template',$options); 					  
update_option('of_themename',$themename);   
update_option('of_shortname',$shortname);

}
}
?>