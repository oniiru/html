<?php
/* MODIFY EXCERPT LENGTH */
function wp_new_excerpt($text)
{
	if ($text == '')
	{
		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]>', $text);
		$text = strip_tags($text);
		$text = nl2br($text);
		$excerpt_length = apply_filters('excerpt_length', 80);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '...');
			$text = implode(' ', $words);
		}
	}
	return $text;
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wp_new_excerpt');






/* HIDE UNWANTED PROFILE FIELDS */
add_filter('user_contactmethods','hide_profile_fields',10,1);

function hide_profile_fields( $contactmethods ) {
unset($contactmethods['aim']);
unset($contactmethods['jabber']);
unset($contactmethods['yim']);
return $contactmethods;
}








/* RETRIEVE EXCLUDED BLOG CATEGORIES */
function B_getExcludedCats(){
	global $wpdb;
	$excluded = '';
	
	$cats = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '%ka_blogexcludetest_%'");
	foreach($cats as $cat){
		if($cat->option_value == "true"){
			$exploded = explode("_",$cat->option_name);
			$excluded .= "-{$exploded[2]}, ";
		}
	}
	
	return rtrim(trim($excluded), ',');
}



function positive_exlcude_cats(){
	global $wpdb;
	$pos_excluded = '';
	
	$cats = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE '%ka_blogexcludetest_%'");
	foreach($cats as $cat){
		if($cat->option_value == "true"){
			$exploded_pos = explode("_",$cat->option_name);
			$pos_excluded .= "{$exploded_pos[2]},";
		}
	}
	
	return rtrim(trim($pos_excluded), ',');
}






/* HIDE CATEGORIES FROM THE LOOP */

function wploop_exclude($query) {
$exclude = B_getExcludedCats();
if ($query->is_feed || $query->is_search || $query->is_archive || $query->is_home) {
$query->set('cat',''.$exclude.'');
}
return $query;
}
add_filter('pre_get_posts','wploop_exclude');


function wpfeed_exclude($query) {
$excludefeed = B_getExcludedCats();
if ($query->is_feed) {
$query->set('cat',''.$excludefeed.'');
}
return $query;
}
add_filter('pre_get_posts','wpfeed_exclude');


















/* CUSTOM ARCHIVES PARAMETERS 

MODIFIED BY TrueThemes, ORIGINAL PLUGIN:


Plugin Name: Archives for a category 
Plugin URI: http://kwebble.com/blog/2007_08_15/archives_for_a_category
Description: Adds a cat parameter to wp_get_archives() to limit the posts used to generate the archive links to one or more categories.   
Author: Rob Schlüter
Author URI: http://kwebble.com/
Version: 1.4a

Copyright
=========
Copyright 2007, 2008, 2009 Rob Schlüter. All rights reserved.

Licensing terms
===============
- You may use, change and redistribute this software provided the copyright notice above is included. 
- This software is provided without warranty, you use it at your own risk. 
*/
function kwebble_getarchives_where_for_category($where, $args){
	global $kwebble_getarchives_data, $wpdb;

	if (isset($args['cat'])){
		// Preserve the category for later use.
		$kwebble_getarchives_data['cat'] = $args['cat'];

		// Split 'cat' parameter in categories to include and exclude.
		$allCategories = explode(',', $args['cat']);

		// Element 0 = included, 1 = excluded.
		$categories = array(array(), array());
		foreach ($allCategories as $cat) {
			if (strpos($cat, ' ') !== FALSE) {
				// Multi category selection.
			}
			$idx = $cat < 0 ? 1 : 0;
			$categories[$idx][] = abs($cat);
		}

		$includedCatgories = implode(',', $categories[0]);
		$excludedCatgories = implode(',', $categories[1]);

		// Add SQL to perform selection.
		if (get_bloginfo('version') < 2.3){
			$where .= " AND $wpdb->posts.ID IN (SELECT DISTINCT ID FROM $wpdb->posts JOIN $wpdb->post2cat post2cat ON post2cat.post_id=ID";

			if (!empty($includedCatgories)) {
				$where .= " AND post2cat.category_id IN ($includedCatgories)";
			}
			if (!empty($excludedCatgories)) {
				$where .= " AND post2cat.category_id NOT IN ($excludedCatgories)";
			}

			$where .= ')';
		} else{
			$where .= ' AND ' . $wpdb->prefix . 'posts.ID IN (SELECT DISTINCT ID FROM ' . $wpdb->prefix . 'posts'
					. ' JOIN ' . $wpdb->prefix . 'term_relationships term_relationships ON term_relationships.object_id = ' . $wpdb->prefix . 'posts.ID'
					. ' JOIN ' . $wpdb->prefix . 'term_taxonomy term_taxonomy ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id'
					. ' WHERE term_taxonomy.taxonomy = \'category\'';
			if (!empty($includedCatgories)) {
				$where .= " AND term_taxonomy.term_id IN ($includedCatgories)";
			}
			if (!empty($excludedCatgories)) {
				$where .= " AND term_taxonomy.term_id NOT IN ($excludedCatgories)";
			}

			$where .= ')';
		}
	}

	return $where;
}

 /* Changes the archive link to include the categories from the 'cat' parameter.
 */
function kwebble_archive_link_for_category($url){
	global $kwebble_getarchives_data;

	if (isset($kwebble_getarchives_data['cat'])){
		$url .= strpos($url, '?') === false ? '?' : '&';
		$url .= 'cat=' . $kwebble_getarchives_data['cat'];

		// Remove cat parameter so it's not automatically used in all following archive lists.
		unset($kwebble_getarchives_data['cat']);
	}

	return $url;
}

/*
 * Add the filters.
 */

// Prevent error if executed outside WordPress.
if (function_exists('add_filter')){
	// Constants for form field and options.
	define('KWEBBLE_OPTION_DISABLE_CANONICAL_URLS', 'kwebble_disable_canonical_urls');
	define('KWEBBLE_GETARCHIVES_FORM_CANONICAL_URLS', 'kwebble_disable_canonical_urls');
	define('KWEBBLE_ENABLED', '');
	define('KWEBBLE_DISABLED', 'Y');

	add_filter('getarchives_where', 'kwebble_getarchives_where_for_category', 10, 2);

	add_filter('year_link', 'kwebble_archive_link_for_category');
	add_filter('month_link', 'kwebble_archive_link_for_category');
	add_filter('day_link', 'kwebble_archive_link_for_category');

	// Disable canonical URLs if the option is set.
	if (get_option(KWEBBLE_OPTION_DISABLE_CANONICAL_URLS) == KWEBBLE_DISABLED){
		remove_filter('template_redirect', 'redirect_canonical');
	}
}






?>