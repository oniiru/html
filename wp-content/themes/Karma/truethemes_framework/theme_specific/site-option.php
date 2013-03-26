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
$GLOBALS['template_path'] = TRUETHEMES_FRAMEWORK;


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
$url =  get_template_directory_uri() . '/truethemes_framework/admin/images/color-schemes/';
$footerurl =  get_template_directory_uri() . '/truethemes_framework/admin/images/footer-layouts/';
$fonturl =  get_template_directory_uri() . '/truethemes_framework/admin/images/fonts/';
$framesurl =  get_template_directory_uri() . '/truethemes_framework/admin/images/image-frames/';
$logourl =  get_template_directory_uri() . '/truethemes_framework/admin/images/logo-builder/';
$recaptcha_themes = get_template_directory_uri() . '/truethemes_framework/admin/images/recaptcha-themes/';//since version 2.6


//Access the WordPress Categories via an Array
$exclude_categories = array();  
$exclude_categories_obj = get_categories('hide_empty=0');
foreach ($exclude_categories_obj as $exclude_cat) {
$exclude_categories[$exclude_cat->cat_ID] = $exclude_cat->cat_name;}










/*-----------------------------------------------------------------------------------*/
/* Create Site Options Array */
/*-----------------------------------------------------------------------------------*/
$options = array();
			
			
			$options[] = array( "name" => __('General Settings','truethemes_localize'),
			"type" => "heading");
			

$options[] = array( "name" => __('Website Logo','truethemes_localize'),
			"desc" => __('Upload a custom logo for your Website.','truethemes_localize'),
			"id" => $shortname."_sitelogo",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => __('Login Screen Logo','truethemes_localize'),
			"desc" => __('Upload a custom logo for your Wordpress login screen.','truethemes_localize'),
			"id" => $shortname."_loginlogo",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => __('Favicon','truethemes_localize'),
			"desc" => __('Upload a 16px x 16px image that will represent your website\'s favicon.<br /><br /><em>To ensure cross-browser compatibility, we recommend converting the favicon into .ico format before uploading. (<a href="http://www.favicon.cc/">www.favicon.cc</a>)</em>','truethemes_localize'),
			"id" => $shortname."_favicon",
			"std" => "",
			"type" => "upload");
			
$options[] = array( "name" => __('Logo Builder - Select an Icon','truethemes_localize'),
			"desc" => __('Select an icon to be used for your logo.<br><br><em>note: you should only select an icon if you won\'t be uploading a custom logo.</em>','truethemes_localize'),
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
				
$options[] = array( "name" => __('Logo Builder - Text','truethemes_localize'),
			"desc" => __('Enter the text to be used for your logo.<br><br><em>note: you should only enter logo text if you won\'t be uploading a custom logo.</em>','truethemes_localize'),
			"id" => $shortname."_logo_text",
			"std" => "",
			"type" => "text");
			
$options[] = array( "name" => __('Hide Meta Boxes','truethemes_localize'),
			"desc" => __('This functionality hides meta boxes in the Dashboard to help Wordpress feel more like a CMS. This includes: Comments, Discussion, Trackbacks, Custom Fields, Author, and Slug. <em>Un-check this box to disable this functionality.</em>','truethemes_localize'),
			"id" => $shortname."_hidemetabox",
			"std" => "true",
			"type" => "checkbox");
			
									   
$options[] = array( "name" => __('Tracking Code','truethemes_localize'),
			"desc" => __('Paste Google Analytics (or other) tracking code here.','truethemes_localize'),
			"id" => $shortname."_google_analytics",
			"std" => "",
			"type" => "textarea");
			
			
$options[] = array( "name" => __('SEO Module','truethemes_localize'),
			"desc" => __('A Search Engine Optimization Module is included. <em>Please check this box to enable this Module. Please remove any SEO plugins before enabling this module, so as to prevent any possible SEO conflicts.</em>','truethemes_localize'),
			"id" => $shortname."_seo_module",
			"std" => "false",
			"type" => "checkbox");
						
			
//filter to allow developer to add new options to general settings.			
$options = apply_filters('theme_option_general_settings',$options);			
			
			
			
			
			
$options[] = array( "name" => __('Styling Options','truethemes_localize'),
			"type" => "heading");
		
$options[] = array( "name" => __('Website Color Scheme','truethemes_localize'),
			"desc" => __('Select the primary color scheme for your website.','truethemes_localize'),
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
$options[] = array( "name" => __('Secondary Color Scheme','truethemes_localize'),
			"desc" => __('Select a secondary color scheme only if you wish to override the default secondary color.','truethemes_localize'),
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
									
$options[] = array( "name" => __('Custom CSS','truethemes_localize'),
			"desc" => __('Use this area to add custom CSS to your website.','truethemes_localize'),
			"id" => $shortname."_custom_css",
			"std" => "",
			"type" => "textarea");
			
			
			
//filter to allow developer to add in new options for styling options.			
$options = apply_filters('theme_option_styling_settings',$options);			
			
			
			
			
			
$options[] = array( "name" => __('Typography','truethemes_localize'),
			"type" => "heading");
			
			
$options[] = array( "name" => __('Google Web Fonts','truethemes_localize'),
			"desc" => __('Select a font face to be used for your website\'s headings.<br><br>Font names (from left to right):<br>- (none)<br>- Droid Sans<br>- Cabin<br>- Cantarell<br>- Cuprum<br>- Oswald<br>- Neuton<br>- Oritron<br>- Arvo<br>- Kreon<br>- Indie Flower<br>- Josefin Sans','truethemes_localize'),
			"id" => $shortname."_google_font",
			"std" => "nofont",
			"type" => "images",
			"options" => array(
				'nofont' => $fonturl . 'no-font.png',
				'Droid+Sans' => $fonturl . '1-droid-sans.png',
				'Cabin' => $fonturl . '2-cabin.png',
				'Cantarell' => $fonturl . '3-cantarell.png',
				'Cuprum' => $fonturl . '4-cuprum.png',
				'Oswald' => $fonturl . '5-oswald.png',
				'Neuton' => $fonturl . '6-neuton.png',
				'Orbitron' => $fonturl . '7-orbitron.png',
				'Arvo' => $fonturl . '8-arvo.png',
				'Kreon' => $fonturl . '9-kreon.png',
				'Indie+Flower' => $fonturl . '10-indie-flower.png',
				'Josefin Sans' => $fonturl . '11-josefin-sans.png'
				));
				
				
$options[] = array( "name" => __('Custom Google Web Font','truethemes_localize'),
			"desc" => __('Enter a custom font name If you prefer to use a font that\'s not listed above.<br><br>Here is the complete list of available <a href=\"http://www.google.com/webfonts\" target=\"_blank\">Google Web Fonts</a>.','truethemes_localize'),
			"id" => $shortname."_custom_google_font",
			"std" => "",
			"type" => "text");
			
			
//allow developer to add in new options to typography settings.			
$options = apply_filters('theme_option_typography_settings',$options);	


			
			
			
			
$options[] = array( "name" => __('Interface Options','truethemes_localize'),
			"type" => "heading");
			
$options[] = array( "name" => __('Toolbar','truethemes_localize'),
			"desc" => __('A toolbar is displayed above the main navigation by default. <em>Un-check this box to disable the toolbar.</em>','truethemes_localize'),
			"id" => $shortname."_toolbar",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Dropdown Navigation','truethemes_localize'),
			"desc" => __('<em>Check this box</em> to disable the dropdown navigation.','truethemes_localize'),
			"id" => $shortname."_dropdown",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Navigation Descriptions','truethemes_localize'),
			"desc" => __('<em>Check this box</em> to disable the navigation descriptions.','truethemes_localize'),
			"id" => $shortname."_nav_description",
			"std" => "false",
			"type" => "checkbox");


$options[] = array( "name" => __('Utility Panel','truethemes_localize'),
			"desc" => __('The utility panel is displayed above the main content area of all interior pages. This panel holds the page title, breadcrumbs and search bar. <em>Un-check this box to disable the utility panel and all of it\'s contents.</em>','truethemes_localize'),
			"id" => $shortname."_tools_panel",
			"std" => "true",
			"type" => "checkbox");
			
			
$options[] = array( "name" => __('Search Bar','truethemes_localize'),
			"desc" => __('A search bar is displayed by default. <em>Un-check this box to disable the search bar.</em>','truethemes_localize'),
			"id" => $shortname."_searchbar",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Search Bar Text','truethemes_localize'),
			"desc" => __('Customize the text that is displayed in the search bar.','truethemes_localize'),
			"id" => $shortname."_searchbartext",
			"std" => "Search",
			"type" => "text");
			
$options[] = array( "name" => __('Breadcrumbs','truethemes_localize'),
			"desc" => __('Breadcrumbs are displayed by default. <em>Un-check this box to disable the breadcrumbs.</em>','truethemes_localize'),
			"id" => $shortname."_crumbs",
			"std" => "true",
			"type" => "checkbox");
			

$options[] = array( "name" => __('Breadcrumbs - Home Link','truethemes_localize'),
			"desc" => __('Customize the text for the homepage link in the breadcrumbs.','truethemes_localize'),
			"id" => $shortname."_breadcrumbs_home_text",
			"std" => "Home",
			"type" => "text");			
			
			
			
$options[] = array( "name" => __('Footer Columns','truethemes_localize'),
			"desc" => __('Select the number of columns you would like to display in the footer.','truethemes_localize'),
			"id" => $shortname."_footer_columns",
			"std" => "4",
			"type" => "select",
			"options" => $footer_columns);
		
			
$options[] = array( "name" => __('Footer Style','truethemes_localize'),
			"desc" => __('Select a footer design style.<br />(full, half, small)','truethemes_localize'),
			"id" => $shortname."_footer_layout",
			"std" => "full_bottom",
			"type" => "images",
			"options" => array(
				'full_bottom' => $footerurl . 'footer-layout-1.png',
				'full' => $footerurl . 'footer-layout-2.png',
				'bottom' => $footerurl . 'footer-layout-3.png'
				));		
				
$options[] = array( "name" => __('Scroll to Top Link','truethemes_localize'),
			"desc" => __('A scroll-to-top link is added to the footer by default. <em>Un-check this box to disable the link.</em>','truethemes_localize'),
			"id" => $shortname."_scrolltoplink",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Scroll to Top Label','truethemes_localize'),
			"desc" => __('Customize the text for the scroll-to-top link.','truethemes_localize'),
			"id" => $shortname."_scrolltoplinktext",
			"std" => "top",
			"type" => "text");
				
//allows developer to add in new options to interface options page.				
$options = apply_filters('theme_option_interface_settings',$options);	


				
				


$options[] = array( "name" => __('Forms','truethemes_localize'),
			"type" => "heading");
			
$options[] = array( "name" => __('Form Builder','truethemes_localize'),
			"desc" => __('A powerful form builder is included by default. <em>Un-check this box to disable the form builder.</em>','truethemes_localize'),
			"id" => $shortname."_formbuilder",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('reCAPTCHA: Public Key','truethemes_localize'),
			"desc" => __('Enter your reCAPTCHA Public Key.<br><br>
			You can obtain your reCAPTCHA keys at: <a href="http://www.google.com/recaptcha" target="_blank">google.com/recaptcha</a><br><br><em>Simply leave this field blank if you won\'t be using this functionality.</em>','truethemes_localize'),
			"id" => $shortname."_publickey",
			"std" => "",
			"type" => "text");			
			
$options[] = array( "name" => __('reCAPTCHA: Private Key','truethemes_localize'),
			"desc" => __('Enter your reCAPTCHA Private Key.<br><br>
			You can obtain your reCAPTCHA keys at: <a href="http://www.google.com/recaptcha" target="_blank">google.com/recaptcha</a><br><br><em>Simply leave this field blank if you won\'t be using this functionality.</em>','truethemes_localize'),
			"id" => $shortname."_privatekey",
			"std" => "",
			"type" => "text");
			

//added since version 2.6
$options[] = array( "name" => __('reCAPTCHA Theme - Select a theme','truethemes_localize'),
			"desc" => __('Select a reCAPTCHA theme.</em>','truethemes_localize'),
			"id" => $shortname."_recaptcha_theme",
			"std" => "default_theme",
			"type" => "images",
			"options" => array(
				'default_theme' => $recaptcha_themes . 'red.jpg',
				'white_theme' => $recaptcha_themes . 'white.jpg',
				'black_theme' => $recaptcha_themes . 'black.jpg',
				'clean_theme' => $recaptcha_themes . 'clean.jpg',
				));


//added since version 2.6
$options[] = array( "name" => __('reCAPTCHA Theme - customization','truethemes_localize'),
			"desc" => __('(For Advance User Only)<br/><br/>This setting overwrites the above reCAPTCHA theme selection. <br/><br/>You can customize the look and feel of reCAPTCHA, by entering your custom javascript code here. Please read <a href="http://code.google.com/intl/pt-PT/apis/recaptcha/docs/customization.html" target="_blank">reCAPTCHA developer documentation</a> for details.<br/><br/><u><strong>Important Notes:</strong></u><br/>Please change the javascript codes from google documentation to use <strong>double quotes</strong> for all javascript variables, and not single quotes.','truethemes_localize'),
			"id" => $shortname."_recaptcha_custom",
			"std" => "",
			"type" => "textarea");
			
			
							
									
$options[] = array( "name" => __('Required Text','truethemes_localize'),
			"desc" => __('Customize the text that will be displayed next to required fields.','truethemes_localize'),
			"id" => $shortname."_contact_required",
			"std" => "(required)",
			"type" => "text");
			
$options[] = array( "name" => __('Success Message','truethemes_localize'),
			"desc" => __('Customize the success message that will be displayed after the user submits the form.','truethemes_localize'),
			"id" => $shortname."_contact_successmsg",
			"std" => "Thank you for messaging us. We will get back to you as soon as possible. Cheers!",
			"type" => "textarea");
			
			$options[] = array( "name" => __('Submit Button - Text','truethemes_localize'),
			"desc" => __('Customize the text that will be displayed on submit button','truethemes_localize'),
			"id" => $shortname."_submit_button_text",
			"std" => "SUBMIT",
			"type" => "text");				
			
//allow developer to add in new options to forms.				
$options = apply_filters('theme_option_forms_settings',$options);	






		$options[] = array( "name" => __('Utility Pages','truethemes_localize'),
			"type" => "heading");
			
$options[] = array( "name" => __('404 Page Banner Text','truethemes_localize'),
			"desc" => __('Set the page title that is displayed in the banner area of the 404 Error Page.','truethemes_localize'),
			"id" => $shortname."_404title",
			"std" => "Page not Found",
			"type" => "text");
			
$options[] = array( "name" => __('404 Message','truethemes_localize'),
			"desc" => __('Set the message that is displayed on the 404 Error Page.','truethemes_localize'),
			"id" => $shortname."_404message",
			"std" => "Our Apologies, but the page you are looking for could not be found. Here are some links that you might find useful:
			<ul>
			<li><a href=\"http://www.\">Home</a></li>
			<li><a href=\"http://www.\">Sitemap</a></li>
			<li><a href=\"http://www.\">Contact Us</a></li>
			</ul>",
			"type" => "textarea");
			
$options[] = array( "name" => __('Search Results Banner Text','truethemes_localize'),
			"desc" => __('Set the page title that is displayed in the banner area of the Search Results Page.','truethemes_localize'),
			"id" => $shortname."_results_title",
			"std" => "Search Results",
			"type" => "text");
			
$options[] = array( "name" => __('Search Results Fallback Message','truethemes_localize'),
			"desc" => __('Set the message that is displayed when a search comes back with no results.','truethemes_localize'),
			"id" => $shortname."_results_fallback",
			"std" => "<p>Our Apologies, but your search did not return any results. Please try using a different search term.</p>",
			"type" => "textarea");
			
$options[] = array( "name" => __('Sitemap: Column one title','truethemes_localize'),
			"desc" => __('This title gets displayed in the first column above the list of Pages.<br /><br /><em>Simply disregard this section if you won\'t be using the "Sitemap 2" Page template.</em>','truethemes_localize'),
			"id" => $shortname."_sitemap2_column1",
			"std" => "Pages",
			"type" => "text");
			
$options[] = array( "name" => __('Sitemap: Column two title','truethemes_localize'),
			"desc" => __('This title gets displayed in the second column above the list of Posts.<br /><br /><em>Simply disregard this section if you won\'t be using the "Sitemap 2" Page template.</em>','truethemes_localize'),
			"id" => $shortname."_sitemap2_column2",
			"std" => "Posts",
			"type" => "text");
			
			
$options[] = array( "name" => __('Sitemap: Column three text','truethemes_localize'),
			"desc" => __('This text gets displayed in the third column on the Sitemap page.<br /><br /><em>Simply disregard this section if you won\'t be using the "Sitemap 2" Page template.</em>','truethemes_localize'),
			"id" => $shortname."_sitemap2_column3",
			"std" => "<h2>Contact</h2>
    <p><strong>Email:</strong> <a href=\"mailto:you@yoursite.com\">you@yoursite.com</a><br />
	<strong>Mobile:</strong> 444-555-6666</p>",
			"type" => "textarea");
			
//allow developer to add in new options to forms.				
$options = apply_filters('theme_option_forms_settings',$options);			
			
			
			
$options[] = array( "name" => __('Blog Settings','truethemes_localize'),
			"type" => "heading");
			
$options[] = array( "name" => __('Featured Images','truethemes_localize'),
			"desc" => __('Select the image frame style for featured images.','truethemes_localize'),
			"id" => $shortname."_blog_image_frame",
			"std" => "modern",
			"type" => "images",
			"options" => array(
				'modern' => $framesurl . 'modern.png',
				'shadow' => $framesurl . 'shadow.png'
				));
			
$options[] = array( "name" => __('Blog Page','truethemes_localize'),
			"desc" => __('Select your blog page from the dropdown list.','truethemes_localize'),
			"id" => $shortname."_blogpage",
			"std" => "",
			"type" => "select",
			"options" => $of_pages);

$options[] = array( "name" => __('Banner Text','truethemes_localize'),
			"desc" => __('This text is displayed in the banner area of the Blog page.','truethemes_localize'),
			"id" => $shortname."_blogtitle",
			"std" => "Blog",
			"type" => "text");
			
$options[] = array( "name" => __('Button Text','truethemes_localize'),
			"desc" => __('These buttons are displayed after each blog post excerpt.','truethemes_localize'),
			"id" => $shortname."_blogbutton",
			"std" => "Continue Reading &rarr;",
			"type" => "text");
			
$options[] = array( "name" => __('Drag-to-Share','truethemes_localize'),
					"desc" => __('Drag-to-share functionality is added to each blog post by default. <em>Un-check this box to disable drag-to-share.</em>','truethemes_localize'),
					"id" => $shortname."_dragshare",
					"std" => "true",
					"type" => "checkbox");
			
$options[] = array( "name" => __('"Posted by" Information','truethemes_localize'),
			"desc" => __('<em>Check this box</em> to disable the "Posted by" information located under each Blog Post Title.</em>','truethemes_localize'),
			"id" => $shortname."_posted_by",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Post Date and Comments','truethemes_localize'),
			"desc" => __('<em>Check this box</em> to disable the posted date and comments count located next to each Blog Post.</em>','truethemes_localize'),
			"id" => $shortname."_post_date",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('About-the-Author','truethemes_localize'),
			"desc" => __('The author\'s bio is displayed at the end of each blog post by default. <em>Un-check this box to disable the bio.</em> (Author bio\s can be set in the Wordpress user profile page. (<a href=\"profile.php\">Users > Your Profile</a>)','truethemes_localize'),
			"id" => $shortname."_blogauthor",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Related Posts','truethemes_localize'),
			"desc" => __('Related posts are displayed at the end of each blog post by default. <em>Un-check this box to disable the related posts.</em>','truethemes_localize'),
			"id" => $shortname."_related_posts",
			"std" => "true",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Related Posts Title','truethemes_localize'),
			"desc" => __('Enter the title that is displayed above the list of related posts. <em>Simply leave this field blank if you won\'t be using this functionality.</em>','truethemes_localize'),
			"id" => $shortname."_related_posts_title",
			"std" => "Related Posts",
			"type" => "text");
			
$options[] = array( "name" => __('Related Posts Count','truethemes_localize'),
			"desc" => __('Enter the amount of related posts you\'d like to display. <em>Simply leave this field blank if you won\'t be using this functionality.</em>','truethemes_localize'),
			"id" => $shortname."_related_posts_count",
			"std" => "5",
			"type" => "text");
			
			
$options[] = array( "name" => __('Exclude Categories','truethemes_localize'),
			"desc" => __('Check off any post categories that you\'d like to exclude from the blog.','truethemes_localize'),
			"id" => $shortname."_blogexcludetest",
			"std" => "",
			"type" => "multicheck",
			"options" => $exclude_categories);

$options[] = array( "name" => __('Post Comments','truethemes_localize'),
			"desc" => __('Post comments are enabled by default. <em>Un-check this box to completely disable comments on all blog posts.</em>','truethemes_localize'),
			"id" => $shortname."_post_comments",
			"std" => "true",
			"type" => "checkbox");			
			
			
			
//allow developer to add in new options to blog settings.			
$options = apply_filters('theme_option_blog_settings',$options);				
			
			
			
			
			
			
$options[] = array( "name" => __('Homepage Settings','truethemes_localize'),
			"type" => "heading");

			
$options[] = array( "name" => __('jQuery Slider Post Category','truethemes_localize'),
			"desc" => __('Select the category that will be used for generating the jQuery slides.','truethemes_localize'),
			"id" => $shortname."_jcycle_category",
			"std" => "Select a category:",
			"type" => "select",
			"options" => $of_categories);
			
$options[] = array( "name" => __('JQuery Slider Pause Settings','truethemes_localize'),
			"desc" => __('Check this box if you would like the jQuery slider to pause when the user hovers over a slide.','truethemes_localize'),
			"id" => $shortname."_jcycle_pause_hover",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('jQuery Homepage Slider Time','truethemes_localize'),
			"desc" => __('Enter the desired amount of time you would like to display each slide. (milliseconds)','truethemes_localize'),
			"id" => $shortname."_jcycle_timeout",
			"std" => "8000",
			"type" => "text");
			
			
$options[] = array( "name" => __('3D CU3ER - Slider ID Number','truethemes_localize'),
			"desc" => __('Enter the ID number of the 3D slider you would like to embed on the "Homepage :: 3D" page template.<br><br><em>Not sure where to find the slider ID number? <a href="http://themes.5-squared.com/support/cu3er-instructions.html" target="_blank">View these visual instructions.</a></em>','truethemes_localize'),
			"id" => $shortname."_cu3er_slider_id",
			"std" => "1",
			"type" => "text");
			
			
//allow developer to add in new options to homepage settings.			
$options = apply_filters('theme_option_home_settings',$options);			
			
			
			
			
			
			
			
			
			$options[] = array( "name" => __('Javascript Settings','truethemes_localize'),
			"type" => "heading");				
			
$options[] = array( "name" => __('Enable Testimonial Slider','truethemes_localize'),
			"desc" => __('Check this box to enable the testimonial slider.','truethemes_localize'),
			"id" => $shortname."_testimonial_enable",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Testimonial Slider Pause Settings','truethemes_localize'),
			"desc" => __('Check this box if you would like the testimonial slider to pause when the user hovers over a slide.','truethemes_localize'),
			"id" => $shortname."_testimonial_pause_hover",
			"std" => "false",
			"type" => "checkbox");
			
$options[] = array( "name" => __('Testimonial Slider Time','truethemes_localize'),
			"desc" => __('Enter the desired amount of time you would like to display each testimonial. (milliseconds)','truethemes_localize'),
			"id" => $shortname."_testimonial_timeout",
			"std" => "8000",
			"type" => "text");
		
		
//allow developer to add in new options to javascript settings.			
$options = apply_filters('theme_option_javascript_settings',$options);
			
			
			
			
			
			
			$options[] = array( "name" => __('Advanced Options','truethemes_localize'),
			"type" => "heading");
			
$options[] = array( "name" =>  __('Attention','truethemes_localize'),
					"desc" => "",
					"id" => $shortname."_custom_info_text",
					"std" => __('This section is intended for advanced users who wish to make significant design changes to the default theme. If you do not wish to make these types of changes you can simply ignore this entire section.','truethemes_localize'),
					"type" => "info");
					
/* $options[] = array( "name" =>  __('Background Color &rarr; Main Content Area",
					"desc" => __('Select a background color for the main content area.",
					"id" => $shortname."_main_content_background_color",
					"std" => "",
					"type" => "color"); */
					

$options[] = array( "name" =>  __('Font Color &rarr; Custom Logo','truethemes_localize'),
					"desc" => __('Select a font color for the custom logo.','truethemes_localize'),
					"id" => $shortname."_custom_logo_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; Main Menu','truethemes_localize'),
					"desc" => __('Select a font color for the main menu items.','truethemes_localize'),
					"id" => $shortname."_main_menu_font_color",
					"std" => "",
					"type" => "color");
					
					$options[] = array( "name" =>  __('Font Color &rarr; Main Content','truethemes_localize'),
					"desc" => __('Select a font color for the main content area.','truethemes_localize'),
					"id" => $shortname."_main_content_font_color",
					"std" => "",
					"type" => "color");

$options[] = array( "name" =>  __('Font Color &rarr; Footer Content','truethemes_localize'),
					"desc" => __('Select a font color for the footer content area.','truethemes_localize'),
					"id" => $shortname."_footer_content_font_color",
					"std" => "",
					"type" => "color");
					
					
$options[] = array( "name" =>  __('Font Color &rarr; Links','truethemes_localize'),
					"desc" => __('Select a font color for links.','truethemes_localize'),
					"id" => $shortname."_link_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; Link:Hover','truethemes_localize'),
					"desc" => __('Select a font color for links on hover.','truethemes_localize'),
					"id" => $shortname."_link_hover_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; Side Navigation','truethemes_localize'),
					"desc" => __('Select a font color for the side navigation items.','truethemes_localize'),
					"id" => $shortname."_side_menu_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; H1 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h1&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h1_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; H2 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h2&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h2_font_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" =>  __('Font Color &rarr; H3 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h3&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h3_font_color",
					"std" => "",
					"type" => "color");					

$options[] = array( "name" =>  __('Font Color &rarr; H4 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h4&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h4_font_color",
					"std" => "",
					"type" => "color");

$options[] = array( "name" =>  __('Font Color &rarr; H5 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h5&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h5_font_color",
					"std" => "",
					"type" => "color");


$options[] = array( "name" =>  __('Font Color &rarr; H6 Headings','truethemes_localize'),
					"desc" => __('Select a font color for all &lt;h6&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h6_font_color",
					"std" => "",
					"type" => "color");					
					

										


//start of font-size selectors.

//auto generate font size array from 9px to 50px.
//change numbers to increase or decrease sizes.
$font_sizes = array();
for($size = 9; $size < 51; $size ++){
$font_sizes[] = $size."px";
}

array_unshift($font_sizes,"--select--");										
					
$options[] = array( "name" => __('Font Size &rarr; Custom Logo','truethemes_localize'),
			"desc" => __('Select a font size for the custom logo.','truethemes_localize'),
			"id" => $shortname."_custom_logo_font_size",
			"std" => "--select--",
			"type" => "select",
			"options" => $font_sizes);	
			
$options[] = array( "name" => __('Font Size &rarr; Main Menu','truethemes_localize'),
			"desc" => __('Select a font size for the main menu items.','truethemes_localize'),
			"id" => $shortname."_main_menu_font_size",
			"std" => "--select--",
			"type" => "select",
			"options" => $font_sizes);
			

$options[] = array( "name" => __('Font Size &rarr; Main Content','truethemes_localize'),
			"desc" => __('Select a font size for the main content area.','truethemes_localize'),
			"id" => $shortname."_main_content_font_size",
			"std" => "--select--",
			"type" => "select",
			"options" => $font_sizes);
			
$options[] = array( "name" => __('Font Size &rarr; Side Navigation','truethemes_localize'),
			"desc" => __('Select a font size for side navigation items. headings.','truethemes_localize'),
			"id" => $shortname."_side_menu_font_size",
			"std" => "--select--",
			"type" => "select",
			"options" => $font_sizes);

$options[] = array( "name" =>  __('Font Size &rarr; H1 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h1&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h1_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);				

$options[] = array( "name" =>  __('Font Size &rarr; H2 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h2&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h2_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);	
					
$options[] = array( "name" =>  __('Font Size &rarr; H3 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h3&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h3_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);			

$options[] = array( "name" =>  __('Font Size &rarr; H4 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h4&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h4_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);	

$options[] = array( "name" =>  __('Font Size &rarr; H5 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h5&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h5_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);	


$options[] = array( "name" =>  __('Font Size &rarr; H6 Headings','truethemes_localize'),
					"desc" => __('Select a font size for all &lt;h6&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h6_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);
					
$options[] = array( "name" =>  __('Font Size &rarr; Footer Content','truethemes_localize'),
					"desc" => __('Select a font size for the footer content area. headings.','truethemes_localize'),
					"id" => $shortname."_footer_content_font_size",
					"std" => "--select--",
					"type" => "select",
					"options" => $font_sizes);



//array of all custom font types.
$font_types = array(
				'nofont',
				'Arial',
				'Arial Black',
				'Courier New',
				'Georgia',
				'Helvetica',
				'Impact',
				'Lucida Console',
				'Lucida Sans Unicode',
				'Tahoma',
				'Times New Roman',
				'Verdana',
				'MS Sans Serif',
				'Droid Sans',
				'Cabin',
				'Cantarell',
				'Cuprum',
				'Oswald',
				'Neuton',
				'Orbitron',
				'Arvo',
				'Kreon',
				'Indie Flower',
				'Josefin Sans'
				);
										
					
$options[] = array( "name" => __('Font Face &rarr; Custom Logo Text','truethemes_localize'),
			"desc" => __('Select a font face for your custom logo text.','truethemes_localize'),
			"id" => $shortname."_custom_logo_font",
			"std" => "nofont",
			"type" => "select",
			"options" => $font_types);											


$options[] = array( "name" => __('Font Face &rarr; Main Content','truethemes_localize'),
			"desc" => __('Select a font face for the main content area.','truethemes_localize'),
			"id" => $shortname."_main_content_font",
			"std" => "nofont",
			"type" => "select",
			"options" => $font_types);


$options[] = array( "name" => __('Font Face &rarr; Main Nenu','truethemes_localize'),
			"desc" => __('Select a font face for the main menu items.','truethemes_localize'),
			"id" => $shortname."_main_navigation_font",
			"std" => "nofont",
			"type" => "select",
			"options" => $font_types);

$options[] = array( "name" => __('Font Face &rarr; Side Navigation','truethemes_localize'),
			"desc" => __('Select a font face for the side navigation items.','truethemes_localize'),
			"id" => $shortname."_sidebar_menu_font",
			"std" => "nofont",
			"type" => "select",
			"options" => $font_types);


$options[] = array( "name" =>  __('Font Face &rarr; H1 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h1&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h1_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);			

$options[] = array( "name" =>  __('Font Face &rarr; H2 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h2&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h2_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);
					

$options[] = array( "name" =>  __('Font Face &rarr; H3 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h3&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h3_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);


$options[] = array( "name" =>  __('Font Face &rarr; H4 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h4&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h4_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);


$options[] = array( "name" =>  __('Font Face &rarr; H5 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h5&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h5_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);


				
$options[] = array( "name" =>  __('Font Face &rarr; H6 Headings','truethemes_localize'),
					"desc" => __('Select a font face for all &lt;h6&gt; headings.','truethemes_localize'),
					"id" => $shortname."_h6_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);	
				
$options[] = array( "name" =>  __('Font Face &rarr; Footer Content','truethemes_localize'),
					"desc" => __('Select a font face for the footer content area.','truethemes_localize'),
					"id" => $shortname."_footer_content_font",
					"std" => "nofont",
					"type" => "select",
					"options" => $font_types);
													
									
//allow developer to add in new options to Additional settings.			
$options = apply_filters('theme_option_additional_settings',$options);
			


		

update_option('of_template',$options); 					  
update_option('of_themename',$themename);   
update_option('of_shortname',$shortname);

}
}
?>