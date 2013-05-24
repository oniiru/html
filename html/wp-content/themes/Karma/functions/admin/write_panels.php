<?php
/* ADDS WRITE PANELS TO PAGES */
$new_meta_boxes = 
array(
"pagetitle" => array(
"name" => "_pagetitle",
"std" => "",
"title" => "<strong>Custom Banner Title:</strong><br />This text will override the default page title in the banner. <em style=\"color:#999;\">If you prefer to keep the default page title you can simply ignore this section.</em>",
"description" => ""),

"sc_port_count" => array(
"name" => "_sc_port_count",
"std" => "",
"title" => "<strong>Portfolio Count:</strong><br />Please enter the amount of portfolio items you'd like to display on each page. <em style=\"color:#999;\">If this is not a portfolio page you can simply ignore this section.</em>",
"description" => "")
);




/* ADDS WRITE PANELS TO POSTS */
$new_meta_boxes_2 =
array(
"portfull" => array(
"name"=>"_portimage_full",
"std"=>"",
"title"=>"Portfolio Full Size URL",
"title_desc"=>"(Image, Flash Video, Youtube Video, etc)",
"description"=>"<b>Samples:</b><br /><br />
<b>Image:</b>&nbsp;&nbsp; http://www.yourdomain.com/wp-content/uploads/project1.jpg<br />
<b>YouTube:</b>&nbsp;&nbsp; http://www.youtube.com/watch?v=VKS08be78os<br />
<b>Flash:</b>&nbsp;&nbsp; http://www.yourdomain.com/wp-content/uploads/design.swf?width=792&amp;height=294<br />
<b>Vimeo:</b>&nbsp;&nbsp; http://vimeo.com/8245346<br />
<b>iFrame:</b>&nbsp;&nbsp; http://www.apple.com?iframe=true&width=850&height=500<br />
"),


"portdesc" => array(
"name"=>"_portimage_desc",
"std"=>"",
"title"=>"Portfolio Description",
"title_desc"=>"",
"description"=>"<b>Note:</b> This description will be displayed in the JQuery pop-up."),


"jcycleurl" => array(
"name"=>"_jcycle_url",
"std"=>"",
"title"=>"Link This Image",
"title_desc"=>"",
"description"=>"Enter a URL if you wish to link this image.<br /><b>Sample:</b> &nbsp;http://www.yourdomain.com/about-us")
);




function new_meta_boxes() {
global $post, $new_meta_boxes;
foreach($new_meta_boxes as $meta_box) {
$meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);
if($meta_box_value == "")
$meta_box_value = $meta_box['std'];
echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
echo'<p>'.$meta_box['title'].'</p>';
echo'<input type="text" name="'.$meta_box['name'].'_value" value="'.$meta_box_value.'" style="width:50%;height:30px;"/><br />';
echo'<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p>';
}
}



function new_meta_boxes_2() {
global $post, $new_meta_boxes_2;
foreach($new_meta_boxes_2 as $meta_box) {
$meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);
if($meta_box_value == "")
$meta_box_value = $meta_box['std'];
echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
echo'<h3>'.$meta_box['title'].'&nbsp;&nbsp;<span style="font-weight:normal !important;"><em>'.$meta_box['title_desc'].'</em></span>'.'</h3>';
echo'<input type="text" name="'.$meta_box['name'].'_value" value="'.$meta_box_value.'" style="width:100%;height:30px;"/><br />';
echo'<p style="color: #21759B;"><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p><br /><br /><br />';
}
}







function create_meta_box() {

global $theme_name;
if ( function_exists('add_meta_box') ) {
add_meta_box( 'new-meta-boxes', 'Custom Settings', 'new_meta_boxes', 'page', 'normal', 'high' );
add_meta_box( 'new-meta-boxes', 'Custom Settings', 'new_meta_boxes_2', 'post', 'normal', 'high' );
}
}






function save_postdata( $post_id ) {



global $post, $new_meta_boxes, $new_meta_boxes_2;

if (!isset($_POST['post_type']))
{
//If post type not set, set it with null to prevent debug error
$_POST['post_type'] = null;
} 

if('post' == $_POST['post_type']){
          $new_meta_boxes = $new_meta_boxes_2;//just a little trick to use either array depending on the type of post.
}
foreach($new_meta_boxes as $meta_box) {

  	if (!isset($_POST[$meta_box['name'].'_noncename']))
	{
	//If nonce not posted, set it with null to prevent debug error.
	$_POST[$meta_box['name'].'_noncename'] = null;
	}  
	
// Verify
if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
return $post_id;
}




if ( 'page' == $_POST['post_type'] ) {
if ( !current_user_can( 'edit_page', $post_id ))
return $post_id;
} else {
if ( !current_user_can( 'edit_post', $post_id ))
return $post_id;
}



$data = $_POST[$meta_box['name'].'_value'];
if(get_post_meta($post_id, $meta_box['name'].'_value') == "")
add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
update_post_meta($post_id, $meta_box['name'].'_value', $data);
elseif($data == "")
delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
}
}



add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_postdata');
?>
<?php
/*CUSTOM PORTFOLIO CATEGORIES FUNCTIONS */
function add_portfolio_cat($post_id){
/*
*this function adds a list of the categories as a custom write pannel
*to the pages section in the admin area. 
*this will allow the user to specify different pages as portfolio pages.
*/

/* load categories even if empty */
	$catargs = array(
    'orderby'       => 'name',
    'order'         => 'ASC',
    'hide_empty'    => 0,
    'hierarchical'  => 1);
	
	$categories = get_categories( $catargs );
	
	global $post;
	echo "<br />";
	$n = get_post_meta($post->ID, '_multiple_portfolio_cat_id', true);
	?>
	<p>Please select the post category that will be used to populate the portfolio items. <em style="color:#999;">If this is not a portfolio page you can simply ignore this section.</em></p><br />
	<?php
	echo "<select name='multiple_portfolio_cat_id'>";
	echo "<option value=''>Select Category</option>";
	foreach($categories as $category){
		$id = $category->cat_ID;
		if($id == $n){
			$checked = 'selected="selected"';
		}else{
			$checked  = '';
		}
		echo "<option $checked value='{$category->cat_ID}'>{$category->name}</option>";
	}
	echo "</select><br /><br />";
}

function create_multiple_portfolio_pages(){
	add_meta_box( 'multiple-portfolio-pages', 'Portfolio Category', 'add_portfolio_cat', 'page', 'normal', 'high' );
}

function save_multiple_portfolio_options($post_id){
	if('page' == $_POST['post_type']){
		$value = $_POST['multiple_portfolio_cat_id'];
		$key = '_multiple_portfolio_cat_id';
		$already_there = get_post_meta($post_id, $key, true);

		if(!is_numeric($value) && $value != ''){
			wp_die('WRONG portfolio category value.');
		}

		if(get_post_meta($post_id, $key) == ''){
			add_post_meta($post_id, $key, $value, $true);
		}else if($value != get_post_meta($post_id, $key, true)){
			update_post_meta($post_id, $key, $value);
		}else if($value == ''){
			delete_post_meta($post_id, $key);
		}
	}
}

add_action('save_post', 'save_multiple_portfolio_options');
add_action('admin_menu','create_multiple_portfolio_pages');




//Start of new page side meta box

add_action('admin_init', 'truethemes_add_custom_box',1);
add_action('save_post', 'truethemes_save_postdata');

//add box to side column of page
function truethemes_add_custom_box(){
    add_meta_box(
        'truethemes_meta_box_id',
        __( 'Sub Navigation', 'truethemes' ), 
        'truethemes_inner_custom_box',
        'page','side','low'
    );
    
     add_meta_box(
        'truethemes_video_id',
        __( 'Featured Video', 'truethemes' ), 
        'truethemes_inner_custom_box_3',
        'post','side','low'
    );
    
     add_meta_box(
        'truethemes_featured_image_2',
        __( 'Featured Image (External Source)', 'truethemes' ), 
        'truethemes_inner_custom_box_4',
        'post','side','low'
    );
    
        

}

//page meta box
function truethemes_inner_custom_box(){

  //nonce
  wp_nonce_field( plugin_basename(__FILE__), 'truethemes_noncename' );
  
  //retrieve post meta value for check
  global $post;
  $post_id = $post->ID;
  $meta_value = get_post_meta($post_id,'truethemes_page_checkbox',true);

  //check box
  echo '<input type="checkbox" id="truethemes_page_checkbox" name="truethemes_page_checkbox" value="yes"';
 if($meta_value=='yes'){
  echo"checked='yes'";
 }else{
  echo"";
 }
  echo '/>';
  echo '<label for="truethemes_checkbox"> ';
  _e("Hide the sub navigation", 'truethemes' );
  echo '</label> ';
}



//post meta box
function truethemes_inner_custom_box_3(){

  //nonce
  wp_nonce_field( plugin_basename(__FILE__), 'truethemes_noncename' );
  
  //retrieve post meta value for check
  global $post;
  $post_id = $post->ID;
  $video_url = get_post_meta($post_id,'truethemes_video_url',true);

//video url input  
echo "<p><label>Video URL</label> ";
echo "<input type='text' id='truethemes_video_url' name='truethemes_video_url' value='$video_url' /></p>";
}

//post meta box
function truethemes_inner_custom_box_4(){

  //nonce
  wp_nonce_field( plugin_basename(__FILE__), 'truethemes_noncename' );
  
  //retrieve post meta value for check
  global $post;
  $post_id = $post->ID;
  $image_url = get_post_meta($post_id,'truethemes_external_image_url',true);
  
  if(!empty($image_url)){

		//show tim thumb image if there is setted image url.
		if(is_multisite()){
		//multisite timthumb request url - to tested online.
	
		$theme_name = get_current_theme();
	
		$image_src = get_site_url(1)."/wp-content/themes/$theme_name/functions/extended/timthumb/timthumb.php?src=$image_url&w=200";
		
		}else{
		//single site timthumb request url
	
		$image_src = get_template_directory_uri()."/functions/extended/timthumb/timthumb.php?src=$image_url&w=250";
	
		}

		echo "<img src='$image_src' alt=''/>";

	}

//video url input  
echo "<p><label>Image URL</label> ";
echo "<input type='text' id='truethemes_external_image_url' name='truethemes_external_image_url' value='$image_url' /></p>";
}


function truethemes_save_postdata($post_id){
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
      return $post_id;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  
  	if (!isset($_POST['truethemes_noncename']))
	{
	//If nonce not posted, set it with null to prevent debug error.
	$_POST['truethemes_noncename'] = null;
	}   

  if ( !wp_verify_nonce( $_POST['truethemes_noncename'], plugin_basename(__FILE__) ) )
      return $post_id;


	if (!isset($_POST['post_type']))
	{
	//If post_type not set, set it with null to prevent debug error.
	$_POST['post_type'] = null;
	} 
      
 	 if($_POST['post_type'] == 'page'){
 	 
 	 if (!isset($_POST['truethemes_page_checkbox']))
	{
	//If post_type not set, set it with null to prevent debug error.
	$_POST['truethemes_page_checkbox'] = null;
	}  	 

 	 $meta = $_POST['truethemes_page_checkbox'];
  	
  	update_post_meta($post_id,'truethemes_page_checkbox',$meta);
 	
  
  	}
  
  	if($_POST['post_type'] == 'post'){

  	$video_url = $_POST['truethemes_video_url'];
  	$image_url = $_POST['truethemes_external_image_url'];
  	  
  	update_post_meta($post_id,'truethemes_video_url',$video_url);
  	update_post_meta($post_id,'truethemes_external_image_url',$image_url);
  	  
	}

}
?>