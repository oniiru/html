<?php

class categoriesVideo {

	function categoriesVideo() {
		add_shortcode('videoGetCategories', array(&$this, 'get_categories'));
		wp_enqueue_style('categories-video-style', plugin_dir_url(__FILE__) . '/css/video-categories.css', false, '1.0.0', 'screen');
		wp_enqueue_style('categories-video-ui-dialog', plugin_dir_url(__FILE__) . '/css/ui-dialog.css', false, '1.0.0', 'screen');
		wp_enqueue_style('categories-video-style');
		wp_enqueue_style('categories-video-ui-dialog');
		add_action('admin_menu', array(&$this, 'createMetaAccessBox'));
		add_action('save_post', array(&$this, 'metaAccessBoxSave'));
	}

	function metaAccessBoxSave($post_id) {
		if (!empty($_POST['video_category_noncename']) && !wp_verify_nonce($_POST['video_category_noncename'], plugin_basename(__FILE__))) {
			return $post_id;
		}

		update_post_meta($post_id, 'include_video_field', $_POST['include_video_field']);
		update_post_meta($post_id, 'subtitle_field', $_POST['subtitle_field']);
		update_post_meta($post_id, 'new_content', $_POST['new_content']);
		update_post_meta($post_id, 'free_content', $_POST['free_content']);
		$expire_date = get_post_meta($post_id, 'expire_date', true) || '';
		if ($_POST['new_content'] == 'on') {
			if ($expire_date == '') {  
				add_post_meta($post_id, 'expire_date', date('F jS, Y', strtotime('+2 weeks')));
			}
		} else {
			if ($expire_date != '') {
				delete_post_meta($post_id, 'expire_date');
			}
		}
	}

	function createMetaAccessBox() {
		add_meta_box('new-meta-boxvideo', 'Video Manager Settings', array(&$this, 'metaAccessBoxVideo'), 'page', 'normal', 'high');
		add_submenu_page('options-general.php', 'Log the search result', 'Log the search result', 'administrator', 'log-search-result', array(&$this, 'log_search_result'));
	}

	function metaAccessBoxVideo($post) {
		$chkInclude = get_post_meta($post->ID, 'include_video_field', true);
		$subTitle = get_post_meta($post->ID, 'subtitle_field', true);
		$freeContent = get_post_meta($post->ID, 'free_content', true);
		$newContent = get_post_meta($post->ID, 'new_content', true);
		$expireDate = get_post_meta($post->ID, 'expire_date', true);
		if ($chkInclude == "on")
			$checked = 'checked="checked"';
		if ($freeContent== "on")
			$freeChecked = 'checked="checked"';
		if ($newContent == 'on' && strtotime($expireDate) >= strtotime('now')) {
			$newChecked = 'checked="checked"';
		} else {
			$expireDate = date('F jS, Y', strtotime('+2 weeks'));
		}

		echo '<input type="hidden" name="video_category_noncename" id="video_category_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
		echo '<p><input type="checkbox" id="inlude_new_field" name="include_video_field" ' . $checked . '/>&nbsp;&nbsp;';
		echo '<label for="inlude_new_field">Include in video category?</label></p>';
		echo '<p><label>Subtitle - To be included on video category page</label><br/>';
		echo '<input type="text" id="subtitle_field" name="subtitle_field" size="70" value="' . $subTitle . '"/></p>';
		echo '<p><input type="checkbox" id="free_content" name="free_content" ' . $freeChecked . ' />&nbsp;&nbsp;';
		echo '<label for="free_content">Free Content</label>';		
		echo '<p><input type="checkbox" id="new_content" name="new_content" ' . $newChecked . ' />&nbsp;&nbsp;';
		echo '<label for="new_content">New Content</label>&nbsp;&nbsp;&nbsp;&nbsp;<label>Expires : </label>' . $expireDate . '</p>';
	}

	function log_search_result() {
		$log_list_table = new Log_List_Table();
		?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<h2>The search Results of peoples</h2>
			<form method="post">
				<input type="hidden" name="page" value="log_search_list" />
				<?php
				$log_list_table->search_box('search', 'search_id');
				$log_list_table->prepare_items();
				$log_list_table->display();
				?>
			</form>
		</div>
		<?php
	}

	function record_log($search) {
		global $wpdb;

		$search = trim($search);

		$count = $wpdb->get_var('SELECT count FROM ' . VIDEO_CATEGORY_LOG_TABLE . ' WHERE search_content="' . $search . '"');
		if ($count) {
			$wpdb->update(
					VIDEO_CATEGORY_LOG_TABLE
					, array('count' => ($count + 1), 'date' => date('Y-m-d H:i:s'))
					, array('search_content' => $search)
					, array('%s', '%s')
					, array('%s')
			);
		} else {
			$wpdb->insert(
					VIDEO_CATEGORY_LOG_TABLE
					, array('search_content' => $search, 'date' => date('Y-m-d H:i:s'), 'count' => 1)
					, array('%s', '%s', '%d')
			);
		}
	}

	function get_categories($attr) {
		global $wpdb, $post;

		if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
			$search = $_GET['keyword'];
			$str = '';
			$page = 1;
			$this->record_log($search);
		} else {
			$page = get_query_var('page');
		}

		if (!$attr['limit'])
			$limit = 7;
		else
			$limit = $attr['limit'];

		if ($page)
			$start = ($page - 1) * $limit;
		else
			$start = 0;

		$sql_query = "SELECT posts.*
                FROM  $wpdb->posts AS posts
                INNER JOIN  $wpdb->postmeta AS meta1 ON posts.ID = meta1.post_ID
                WHERE
                posts.post_type = 'page' AND
                posts.post_status = 'publish' AND
                meta1.meta_key = 'include_video_field' AND
                meta1.meta_value = 'on'";

		if ($search != '') {
			$search_posts = $wpdb->get_results($sql_query);
			$search_post_ids = array();
			foreach ($search_posts as $search_post) {
				$content = $search_post->post_content;

				ob_start();
				echo apply_filters('the_content', $content);
				$content = ob_get_clean();
				$content = trim(strip_tags($content));

				if (strpos(strtolower($content), strtolower($search)) > -1) {
					array_push($search_post_ids, $search_post->ID);
				}
			}
			if ($search_post_ids) {
				$sql_query .= " AND (post_title like '%" . $search . "%' OR ID IN (" . implode(',', $search_post_ids) . '))';
			} else {
				$sql_query .= " AND post_title like '%" . $search . "%'";
			}
		}

		$sql_query.= " ORDER BY menu_order,ID asc";

		$content = '';
		$posts = $wpdb->get_results($sql_query . " LIMIT $start, $limit", object);
		$total_pages = count($wpdb->get_results($sql_query, ARRAY_A));

		$cP = 0;
		$varScripts = array();
		foreach ($posts as $post) {

			global $post;
			setup_postdata($post);
			$arID = 'item_' . $post->ID;
			$expired_date = get_post_meta($post->ID, 'expire_date', true);
			$free_content = get_post_meta($post->ID, 'free_content', true);
			if (($free_content == 'on') && (pmpro_hasMembershipLevel(array(0,1)))){
				$free_image = '<img class="freeimage" src="' . plugin_dir_url() . '/video-categories/images/freecontentribbon.png" />';
			} else {
				$free_image = '';
			}
			$new_content = get_post_meta($post->ID, 'new_content', true);
			if ($new_content == 'on' && strtotime($expired_date) >= strtotime('now')) {
				$new_image = '<img class="newimage" src="' . plugin_dir_url() . '/video-categories/images/newimage.png" />';
			} else {
				$new_image = '';
			}
			$varScripts[$arID] = get_permalink($post->ID);
			if (!$cP)
				$class = 'firstItem';
			else
				$class = '';

			$intro = get_post_meta($post->ID, 'subtitle_field', true);
			$src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
			$content .= '<div id="item_' . $post->ID . '" class="itempage ' . $class . '">
                    <div class="itemLeft">
                        <a href="' . get_permalink($post->ID) . '" title="' . get_the_title() . '">' . $new_image . $free_image . '<img width="275" height="150" src="' . $src[0] . '" /></a>
                    </div>
                    <div class="itemRight"><h2>' . get_the_title() . '</h2><p>' . $intro . '</p></div>
                </div>';
			$cP++;
		}
		wp_reset_postdata();
		$str.='<script type="text/javascript">jQuery(document).ready(function() {';
		$str.='jQuery(".itempage").each(function(){
jQuery(this).click(function(){
id=jQuery(this).attr("id");
switch(id){
';

		foreach ($varScripts as $key => $value) {
			$str.='case "' . $key . '":';
			$str.='jQuery(location).attr("href","' . $value . '");';
			$str.='break;';
		}
		$str.='	}
});
});
});
</script>
<div class="ion-css">
<div class="ion-css">
<div class="ion_tabs ui-tabs ui-widget ui-widget-content ui-corner-all">
<div id="incontent">
<div id="tophead">
<h2 class="categoryTitle">Training Categories</h2>
<div id="searchlib">
<form name="slib" action="" >
<input value="' . $search . '" id="ss" name="keyword"><input type="submit" name="submit" class=" searchbtn btn btn-info" value="search">
</form>
</div>
</div>';
		if (!is_user_logged_in()) {
			$str.='<div class="warringup"><h3>New to SolidWize? Click on any of the links below to try a few of our free videos.</h3><p>Ready to pull the trigger and become a SolidWorks badass? <a href="http://solidwize.com/pricing" class="btn btn-danger catsignupbtn" title="Sign Up">Sign Up Now</a> </p></div>';
		}
		if(pmpro_hasMembershipLevel('1')) {
			$str.='<div class="warringup"><h3>You now have unlimited access to the Intro and Parts section.</h3><p>Upgrade your account to take your learning to the next level. <a href="http://solidwize.com/pricing" class="btn btn-danger catupgradebtn" title="Sign Up">Upgrade Now</a> </p></div>';
			
		}

		$str.=$content . $this->paginationCats(get_permalink($post->ID), $total_pages, $page, $limit) . '</div>
</div>
</div>
</div>';
		return $str;
	}

	/* Pagination
	  $targetpage:url
	  $total_pages:total pages
	  $page:get from mothod GET,POST,
	 */

	function paginationCats($targetpage, $total_pages, $page, $limit) {

// How many adjacent pages should be shown on each side?
		$adjacents = 3;
		if ($page)
			$start = ($page - 1) * $limit;
//first item to display on this page
		else
			$start = 0;
//if no page var is given, set start to 0

		/* Setup page vars for display. */
		if ($page == 0)
			$page = 1;
//if no page var is given, default to 1.
		$prev = $page - 1;
//previous page is page - 1
		$next = $page + 1;
//next page is page + 1
		$lastpage = ceil($total_pages / $limit);
//lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;
//last page minus 1

		/*
		  Now we apply our rules and draw the pagination object.
		  We're actually saving the code to a variable in case we want to draw it more than once.
		 */

		$pagination = "";

		if ($lastpage > 1) {
			$pagination .= "<div class=\"pagination\">";

//previous button
			if ($page > 1)
				$pagination.= "<a href=\"$targetpage?page=$prev\">&laquo; previous</a>";
			else
				$pagination.= "<span class=\"disabled\">&laquo; previous</span>";

//pages
			if ($lastpage < 7 + ($adjacents * 2)) {
//not enough pages to bother breaking it up

				for ($counter = 1; $counter <= $lastpage; $counter++) {
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
				}
			}
			elseif ($lastpage > 5 + ($adjacents * 2)) {
//enough pages to hide some
//close to beginning; only hide later pages
				if ($page < 1 + ($adjacents * 2)) {
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";

						else
							$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
					}
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";
				}
//in middle; hide some front and some back
				elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {

					$pagination.= "<a href=\"$targetpage?page=1\">1</a>";

					$pagination.= "<a href=\"$targetpage?page=2\">2</a>";

					$pagination.= "...";

					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {

						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";

						else
							$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
					}
					$pagination.= "...";

					$pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";

					$pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";
				}
//close to end; only hide early pages

				else {
					$pagination.= "<a href=\"$targetpage?page=1\">1</a>";

					$pagination.= "<a href=\"$targetpage?page=2\">2</a>";
					$pagination.= "...";

					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";

						else
							$pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
					}
				}
			}

//next button
			if ($page < $counter - 1)
				$pagination.= "<a href=\"$targetpage?page=$next\">next &raquo;</a>";

			else
				$pagination.= "<span class=\"disabled\">next &raquo;</span>";

			$pagination.= "</div>\n";
		}
		return $pagination;
	}

}

new categoriesVideo();
?>