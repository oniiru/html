<?php
/* ---------------------------------------
SOCIAL MEDIA WIDGET
--------------------------------------- */
class SocialMediaWidget extends WP_Widget {

	function SocialMediaWidget() {
		$widget_ops = array('classname' => 'social_widget', 'description' => __('Link to your RSS feed and social media accounts.'));
		$this->WP_Widget('social_networks', __('CUSTOM - Social Networks'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$title_link = strip_tags($instance['title_link']);		
    if (!empty( $title_link) ) { $title_page = get_post($title_link); }
		
		$networks['RSS'] = $instance['rss'];
		$networks['Twitter'] = $instance['twitter'];
		$networks['Facebook'] = $instance['facebook'];
		$networks['Flickr'] = $instance['flickr'];
		$networks['YouTube'] = $instance['youtube'];
		$networks['LinkedIn'] = $instance['linkedin'];
		$networks['FourSquare'] = $instance['foursquare'];
		$networks['Delicious'] = $instance['delicious'];
		$networks['Digg'] = $instance['digg'];

		$display = $instance['display'];
		

		echo $before_widget;
		echo $before_title;
		if (!empty( $title_link) ) { echo "<a href=\"" . get_permalink($title_page->ID) . "\">"; } 
		  if (empty( $title) ) { echo $title_page->post_title;}
		  else { echo $title; }
		if (!empty( $title_link) ) { echo "</a>"; } 
		?>
		<?php echo $after_title; ?>
		



<ul class="social_icons">
<?php if (empty($networks['RSS'])) : ?>
<li><a href="<?php bloginfo('rss2_url'); ?>" onclick="window.open(this.href);return false;" class="rss">rss</a></li>
<?php else : ?>
<li><a href="<?php echo $networks['RSS'] ?>" onclick="window.open(this.href);return false;" class="rss">rss</a></li>
<?php endif; ?>	
<?php foreach(array("Twitter", "Facebook", "Flickr", "YouTube", "LinkedIn", "FourSquare", "Delicious", "Digg") as $network) : ?>
<?php if (!empty($networks[$network])) : ?>
<li><a href="<?php echo $networks[$network] ?>" class="<?php echo strtolower($network); ?>" onclick="window.open(this.href);return false;"><?php echo $network; ?></a></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
<br />


		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_link'] = $new_instance['title_link'];
		$instance['rss'] = $new_instance['rss'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['flickr'] = $new_instance['flickr'];
		$instance['youtube'] = $new_instance['youtube'];
		$instance['linkedin'] = $new_instance['linkedin'];
		$instance['foursquare'] = $new_instance['foursquare'];
		$instance['delicious'] = $new_instance['delicious'];
		$instance['digg'] = $new_instance['digg'];
		$instance['display'] = $new_instance['display'];

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'title_link' => '' ) );
		$title = strip_tags($instance['title']);
		$title_link = strip_tags($instance['title_link']);		
		$rss = $instance['rss'];
		$twitter = $instance['twitter'];		
		$facebook = $instance['facebook'];	
		$flickr = $instance['flickr'];	
		$youtube = $instance['youtube'];
		$linkedin = $instance['linkedin'];
		$foursquare = $instance['foursquare'];
		$delicious = $instance['delicious'];
		$digg = $instance['digg'];
		$display = $instance['display'];		


		$text = format_to_edit($instance['text']);
?>
		<p style="color:#999;"><em>Enter the full URL to each of your social media accounts. Simply leave the field blank if you wish not to display that social media service.</em></p><br />

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

    	<p><label for="<?php echo $this->get_field_id('title_link'); ?>"><?php _e('Title link:'); ?></label>     
    	<?php wp_dropdown_pages(array('selected' => $title_link, 'name' => $this->get_field_name('title_link'), 'show_option_none' => __('None'), 'sort_column'=> 'menu_order, post_title'));?>
   		 </p>
		
		<p><label for="<?php echo $this->get_field_id('rss'); ?>"><?php _e('RSS URL: (leave empty for default feed)'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" type="text" value="<?php echo esc_attr($rss); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" /></p>


		<p><label for="<?php echo $this->get_field_id('flickr'); ?>"><?php _e('Flickr URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('flickr'); ?>" name="<?php echo $this->get_field_name('flickr'); ?>" type="text" value="<?php echo esc_attr($flickr); ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('Youtube URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo esc_attr($youtube); ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('LinkedIn URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="text" value="<?php echo esc_attr($linkedin); ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('foursquare'); ?>"><?php _e('FourSquare URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('foursquare'); ?>" name="<?php echo $this->get_field_name('foursquare'); ?>" type="text" value="<?php echo esc_attr($foursquare); ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('delicious'); ?>"><?php _e('Delicious URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('delicious'); ?>" name="<?php echo $this->get_field_name('delicious'); ?>" type="text" value="<?php echo esc_attr($delicious); ?>" /></p>
        
        <p><label for="<?php echo $this->get_field_id('digg'); ?>"><?php _e('Digg URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('digg'); ?>" name="<?php echo $this->get_field_name('digg'); ?>" type="text" value="<?php echo esc_attr($digg); ?>" /></p>

    
<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("SocialMediaWidget");'));

















/* ---------------------------------------
RECENT POSTS WIDGET
--------------------------------------- */
class show_recent extends WP_Widget {
	function show_recent() {
		$widget_ops = array('classname' => 'show_recent', 'description' => __('Show your recent posts.'));
		$this->WP_Widget('show_recent', __('CUSTOM - Recent Posts'), $widget_ops);
	}

	function widget($args, $instance){
		extract($args);

		//$options = get_option('custom_recent');
		$title = $instance['title'];
		$posts = $instance['posts'];

		//GET the posts
		global $post;
		$exclude = B_getExcludedCats();
		$myposts = get_posts('numberposts='.$posts.'&offset=0&category='.$exclude);
		
		echo $before_widget . $before_title . $title . $after_title;


		//SHOW the posts
		foreach($myposts as $post){
			setup_postdata($post);
//added strip_tags to solve a problem with code being displayed improperly.
			?>
				<div class="footer_post">
				<h4><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h4>
				<p><a href="<?php the_permalink() ?>"><?php echo substr(strip_tags($post->post_content), 0, 125); ?>...</a></p>
                </div><!-- end footer_post -->
			<?php
		}
		echo $after_widget;
	}

	function update($newInstance, $oldInstance){
		$instance = $oldInstance;
		$instance['title'] = strip_tags($newInstance['title']);
		$instance['posts'] = $newInstance['posts'];

		return $instance;
	}

	function form($instance){
		echo '<p><label for="'.$this->get_field_id('title').'">' . __('Title:') . '</label><input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$instance['title'].'" /></p>';

		echo '<p><label for="'.$this->get_field_id('posts').'">' . __('Number of Posts:', 'widgets') . '</label><input class="widefat" id="'.$this->get_field_id('posts').'" name="'.$this->get_field_name('posts').'" type="text" value="'.$instance['posts'].'" /></p>';

		echo '<input type="hidden" id="custom_recent" name="custom_recent" value="1" />';
	}
}

add_action('widgets_init', create_function('', 'return register_widget("show_recent");'));













/* ---------------------------------------
CATEGORIES WIDGET
--------------------------------------- */
class ka_custom_cats extends WP_Widget {

	function ka_custom_cats() {
		$widget_ops = array( 'classname' => 'widget_categories', 'description' => __( "A list or dropdown of categories" ) );
		$this->WP_Widget('categories', __('CUSTOM - Categories'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Categories' ) : $instance['title'], $instance, $this->id_base);
		$c = $instance['count'] ? '1' : '0';
		$h = $instance['hierarchical'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
			
			// Bring in excluded categories from options panel
			$pos_excluded = positive_exlcude_cats();
			$pos_cats = $pos_excluded;
			$cat_args = array('orderby' => 'name', 'exclude' => $pos_cats, 'title_li' => __( '' ), 'show_count' => $c, 'hierarchical' => $h);
			

		if ( $d ) {
			$cat_args['show_option_none'] = _e('Select Category');
			wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo home_url(); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		wp_list_categories(apply_filters('widget_categories_args', $cat_args));
?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Show as dropdown' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy' ); ?></label></p>
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("ka_custom_cats");'));



















/* ---------------------------------------
ARCHIVES WIDGET
--------------------------------------- */
class ka_custom_archives extends WP_Widget {

	function ka_custom_archives() {
		$widget_ops = array('classname' => 'widget_archive', 'description' => __( 'A monthly archive of your site&#8217;s posts') );
		$this->WP_Widget('archives', __('CUSTOM - Archives'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$c = $instance['count'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Archives') : $instance['title'], $instance, $this->id_base);
		$neg_excluded = B_getExcludedCats();
		$neg_cats = $neg_excluded;
		
		

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		if ( $d ) {
?>
		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo esc_attr(__('Select Month')); ?></option> <?php wp_get_archives(apply_filters('widget_archives_dropdown_args', array('type' => 'monthly', 'format' => 'option', 'show_post_count' => $c, 'cat' => $neg_cats))); ?> </select>
<?php
		} else {
?>
		<ul>
		<?php wp_get_archives(apply_filters('widget_archives_args', array('type' => 'monthly', 'show_post_count' => $c, 'cat' => $neg_cats))); ?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$title = strip_tags($instance['title']);
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as a drop down'); ?></label>
		</p>
<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("ka_custom_archives");'));
?>