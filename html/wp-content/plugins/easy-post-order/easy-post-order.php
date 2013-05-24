<?php
	/*
	Plugin Name: Easy Post Order
	Plugin URI: http://www.bluehornetstudios.com
	Description: Allows drag-and-drop ordering of posts
	Author: Young J. Yoon
	Version: 2.1
	Author URI: http://www.bluehornetstudios.com
	*/
?>
<?php
	global $epo_db_version;	
	$epo_db_version = "2.0";
	
	/* Install */
	function epo_install() {
		global $wpdb;
		global $epo_db_version;
		$table_name = $wpdb->prefix . "epo_order";
		
		$sql = "CREATE TABLE " . $table_name . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				post_id bigint(20) NOT NULL,
				epo_order mediumint(9) NOT NULL DEFAULT 0,
				epo_term_id mediumint(9) NOT NULL DEFAULT 0,
				PRIMARY KEY  (id),
				UNIQUE KEY upost (post_id, epo_term_id)
			);";
		if ($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") == $table_name) {
			$wpdb->query("ALTER TABLE ".$table_name." DROP INDEX post_id");
		}
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option('epo_db_version', $epo_db_version);
	}	
	register_activation_hook(__FILE__, 'epo_install');
	
	/* Update DB Structure for 2.0 */
	function epo_db_update() {
		global $epo_db_version;		
		if(get_site_option('epo_db_version') != $epo_db_version) {
			epo_install();
		}
	}
	add_action('plugins_loaded', 'epo_db_update');
	
	/* Initailaize Back-end */	
	function epo_admin_init() {
		wp_register_script( 'jquery-json', plugins_url('js/jquery.json.js', __FILE__) );
		wp_register_script( 'epo_script', plugins_url('js/epo.js', __FILE__) );
		wp_register_style( 'epo_css', plugins_url('css/epo.css', __FILE__) );
		
		$page_title = "Easy Post Order Configuration";
		$menu_title = "Easy Post Order";
		$capability = "publish_posts";
		$menu_slug = "epo_config_page";
		$function = "epo_config_page";
		$icon_url = "";
		$position = "";
		
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-json');
		wp_enqueue_script('epo_script');
		wp_enqueue_style('epo_css');
	}
	add_action('admin_menu', 'epo_admin_init');


	/* Back-end Interface */	
	function epo_config_page() {
		include('epo-sortable-queue-page.php');
	}
	
	/* Generate Post Type Options */
	function epo_generate_ptype_options() {
		global $wpdb;
		$arr = get_post_types(array('public' => true, 'capability_type' => 'post'),'names');
		$options = '<option value="0" selected>Select Post Type</option>';
		foreach($arr as $ptype) {
			$name = get_post_type_object($ptype)->labels->name;
			$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_type) as count FROM ".$wpdb->posts." WHERE post_type = '".$ptype."' AND post_status = 'publish' GROUP BY post_type"));
			if($count > 0) { $options .= '<option value="'.$ptype.'">'.$name.' ('.$count.')</option>'; }
		}
		echo $options;
	}
	
	/* Load Posts */
	function epo_fetch_type() {
		global $wpdb;
		$list = '';		
		if($_POST['ptype'] != '0') {
			$q = "SELECT po.*, pc.name as cat FROM (SELECT p.ID, p.post_title, m.epo_order FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->prefix."epo_order m ON p.ID = m.post_id WHERE p.post_status = 'publish' AND p.post_type = '".$_POST['ptype']."') po LEFT JOIN (SELECT tro.object_id, t.name FROM (SELECT tr.object_id, tt.term_id FROM ".$wpdb->prefix."term_relationships tr LEFT JOIN ".$wpdb->prefix."term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id GROUP BY tr.object_id) as tro LEFT JOIN ".$wpdb->prefix."terms t ON tro.term_id = t.term_id) as pc ON po.ID = pc.object_id ORDER BY po.epo_order";
			
			$res = $wpdb->get_results($q);
			foreach($res as $post) :
				$list .= '<li id="'.$post->ID.'">
				<span class="post-title">'.$post->post_title.'</span>
				<span class="post-cat">'.stripslashes($post->cat).'</span>
					</li>';
			endforeach;
		}		
		echo $list;
		die();
	}
	add_action('wp_ajax_epo_switch_type', 'epo_fetch_type');
	
	/* Load Categories */
	function epo_fetch_cats() {
		global $wpdb;
		$options = '';
		if($_POST['ptype'] != '0') {
			$q = "SELECT cat.name, cat.slug, cat.term_taxonomy_id, count(p.ID) as num FROM (SELECT ct.*, tr.object_id FROM (SELECT t.name, t.slug, tt.term_taxonomy_id FROM ".$wpdb->prefix."terms t JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id = tt.term_id WHERE tt.taxonomy = 'category') ct LEFT JOIN ".$wpdb->prefix."term_relationships tr ON ct.term_taxonomy_id = tr.term_taxonomy_id) cat LEFT JOIN ".$wpdb->posts." p ON cat.object_id = p.ID WHERE p.post_type = '".$_POST['ptype']."' AND p.post_status = 'publish' GROUP BY cat.slug";
			
			$res = $wpdb->get_results($q);
			$options = '<option value="0" selected>All</option>';
			foreach($res as $cat) {
				$options .= '<option value="'.$cat->term_taxonomy_id.'">'.$cat->name.' ('.$cat->num.')</option>';
			}
		}
		echo $options;
		die();		
	}
	add_action('wp_ajax_epo_populate_cats', 'epo_fetch_cats');
	
	/* Update posts according to the chosen categories */
	function epo_reload_posts() {
		global $wpdb;
		$list = '';
		$where = ($_POST['cat'] != 0) ? ' WHERE tt.term_taxonomy_id = '.$_POST['cat'] : '';
		$q = "SELECT pt.*, t.term_id, t.name as cat FROM (SELECT op.*, tr.term_taxonomy_id FROM (SELECT p.ID, p.post_title, m.epo_order FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->prefix."epo_order m ON p.ID = m.post_id WHERE p.post_status = 'publish' AND p.post_type = '".$_POST["ptype"]."') op LEFT JOIN ".$wpdb->prefix."term_relationships tr ON tr.object_id = op.ID) pt LEFT JOIN ".$wpdb->prefix."term_taxonomy tt ON pt.term_taxonomy_id = tt.term_taxonomy_id JOIN ".$wpdb->prefix."terms t ON tt.term_id = t.term_id ".$where." GROUP BY pt.ID ORDER BY pt.epo_order ASC";
		$res = $wpdb->get_results($q);
		foreach($res as $post) :
			$catNameArr = explode('(',$_POST['catName']);
			$list_cat = ($_POST['cat'] != 0) ? $catNameArr[0] : $post->cat;
			$list .= '<li id="'.$post->ID.'">
			<span class="post-title">'.$post->post_title.'</span>
			<span class="post-cat">'.stripslashes($list_cat).'</span>
				</li>';
		endforeach;
		echo $list;
		die();
	}
	add_action('wp_ajax_epo_switch_cats', 'epo_reload_posts');
	
	/* Save Custom Order */
	function epo_save_process() {
		global $wpdb;
		$array = json_decode(stripslashes($_POST["jsonArr"]));
		$q = "INSERT INTO ".$wpdb->prefix."epo_order (`post_id`,`epo_order`,`epo_term_id`) VALUES ('%d','%d','%d') ON DUPLICATE KEY UPDATE `epo_order` = '%d'";
		foreach($array as $order => $post_id) {
			$args = array( $post_id, $order, $_POST['cat'], $order );
			$wpdb->query( $wpdb->prepare($q, $args) );		
		}
		echo "Save Successful.";
		die();
	}
	add_action('wp_ajax_epo_save', 'epo_save_process');
	
	/* Query Hooks for flawless WP_Query */
	function set_use_epo_flag($query) {
		global $use_epo_flag, $use_epo_category;
		$use_epo_flag = ($query->query_vars['orderby'] == 'epo_custom');
		$use_epo_category = $query->query_vars['epo_cat'];
	}
	add_action('parse_query', 'set_use_epo_flag');
	
	function epo_query_join($args) {
		global $wpdb, $use_epo_flag, $use_epo_category;
		if($use_epo_flag) {
			$args .= " LEFT JOIN ".$wpdb->prefix."epo_order ON ".$wpdb->posts.".ID = ".$wpdb->prefix."epo_order.post_id ";
		}
		return $args;
	}
	add_filter('posts_join', 'epo_query_join');
	
	function epo_query_where($args) {
		global $wpdb, $use_epo_category;
		if($use_epo_category != '') {
			$args .= " AND ".$wpdb->prefix."epo_order.epo_term_id = '".$use_epo_category."'";
		}
		return $args;
	}
	add_filter('posts_where', 'epo_query_where');
	
	function epo_query_orderby($args) {
		global $wpdb, $use_epo_flag, $use_epo_category;
		if($use_epo_flag) {
			$args = str_replace($wpdb->posts.".post_date DESC",$wpdb->prefix."epo_order.epo_order ASC",$args);
		}
		$use_epo_flag = false;
		$use_epo_category = '';		
		return $args;
	}
	add_filter('posts_orderby', 'epo_query_orderby');
		
	/* Shortcodes */
	function epo_sc_func($atts) {
		global $wpdb;
		extract(shortcode_atts(array(
			'post_type' => 'post',
			'posts_per_page' => '10',
			'category' => '',
			'loop' => ''		
		), $atts));
		
		if($category != '') {
			$catQuery = "SELECT tt.term_taxonomy_id FROM ".$wpdb->prefix."terms t JOIN ".$wpdb->prefix."term_taxonomy tt ON t.term_id = tt.term_id WHERE t.slug = '".$category."'";
			$res = $wpdb->get_results($catQuery);
			$epo_cat_str = '&epo_cat='.$res[0]->term_taxonomy_id;
		} else { $epo_cat_str = '&epo_cat=0'; }
		
		$my_query = new WP_Query( "post_type=".$post_type.$epo_cat_str."&orderby=epo_custom&posts_per_page=".$posts_per_page );
		$loopname = ($loop == '') ? 'epo-loop.php' : get_stylesheet_directory().'/epo-loop-'.$loop.'.php';
		include($loopname);
	}
	add_shortcode('EPO', 'epo_sc_func');
?>