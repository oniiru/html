=== WCK - Custom Fields and Custom Post Types Creator === 

Contributors: reflectionmedia, madalin.ungureanu
Donate link: http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/
Tags: custom fields, custom field, wordpress custom fields, advanced custom fields, custom post type, custom post types, post types, repeater fields, repeater, repeatable, meta box, meta boxes, metabox, taxonomy, taxonomies, custom taxonomy, custom taxonomies, custom, custom fields creator, post meta, meta, get_post_meta, post creator, cck, content types, types

Requires at least: 3.1
Tested up to: 3.5.2
Stable tag: 1.0.2

A must have tool for creating custom fields, custom post types and taxonomies, fast and without any programming knowledge.


== Description ==

**WordPress Creation Kit** consists of three tools that can help you create and maintain custom post types, custom taxonomies and most importantly, custom fields and metaboxes for your posts, pages or CPT's.

**WCK Custom Fields Creator** offers an UI for setting up custom meta boxes for your posts, pages or custom post types. Uses standard custom fields to store data.

**WCK Custom Post Type Creator** facilitates creating custom post types by providing an UI for most of the arguments of register_post_type() function.

**WCK Taxonomy Creator** allows you to easily create and edit custom taxonomies for WordPress without any programming knowledge. It provides an UI for most of the arguments of register_taxonomy() function.

= Custom Fields =
* Custom fields types: wysiwyg editor, upload, text, textarea, select, checkbox, radio
* Easy to create custom fields for any post type.
* Support for **Repeater Fields** and **Repeater Groups**.
* Drag and Drop to sort the Repeater Fields.
* Support for all input fields: text, textarea, select, checkbox, radio.
* Image / File upload supported via the WordPress Media Uploader.
* Possibility to target only certain page-templates, target certain custom post types and even unique ID's.
* All data handling is done with ajax
* Data is saved as postmeta

= Custom Post Types and Taxonomy =
* Create and edit Custom Post Types from the Admin UI
* Advanced Labeling Options
* Attach built in or custom taxonomies to post types
* Create and edit Custom Taxonomies from the Admin UI
* Attach the taxonomies to built in or custom post types

= WCK PRO =
  The [PRO version](http://www.cozmoslabs.com/wordpress-creation-kit/) offers:
  
* Front-end Posting - form builder for content creation and editing
* Options Page Creator - create option pages for your theme or your plugin
* More field types: Datepicker, Country Select, User Select
* Premium Email Support for your project
  
 [See complete list of features](http://www.cozmoslabs.com/wordpress-creation-kit-sale-page/)

= Website =
http://www.cozmoslabs.com/wordpress-creation-kit/

= Announcement Post and Video =
http://www.cozmoslabs.com/3747-wordpress-creation-kit-a-sparkling-new-custom-field-taxonomy-and-post-type-creator/

= Documentation =
http://www.cozmoslabs.com/wordpress-creation-kit/custom-fields-creator/

= Bug Submission and Forum Support =
http://www.cozmoslabs.com/forums/forum/wordpresscreationkit/

== Installation ==

1. Upload the wordpress-creation-kit folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Then navigate to WCK => Custom Fields Creator tab and start creating your custom fields, or navigate to WCK => Post Type Creator tab and start creating your custom post types or navigate to WCK => Taxonomy Creator tab and start creating your taxonomies.

== Frequently Asked Questions ==

= How do I display my custom fields in the front end? =

Let's consider we have a meta box with the following arguments:
- Meta name: books
- Post Type: post
And we also have two fields defined:
- A text custom field with the Field Title: Book name
- And another text custom field with the Field Title: Author name

You will notice that slugs will automatically be created for the two text fields. For 'Book name' the slug will be 'book-name' and for 'Author name' the slug will be 'author-name'

Let's see what the code for displaying the meta box values in single.php of your theme would be:

`<?php $books = get_post_meta( $post->ID, 'books', true ); 
foreach( $books as $book){
	echo $book['book-name'] . '<br/>';
	echo $book['author-name'] . '<br/>';
}?>`

So as you can see the Meta Name 'books' is used as the $key parameter of the function get_post_meta() and the slugs of the text fields are used as keys for the resulting array. Basically CFC stores the entries as custom fields in a multidimensional array. In our case the array would be:

`<?php array( array( "book-name" => "The Hitchhiker's Guide To The Galaxy", "author-name" => "Douglas Adams" ),  array( "book-name" => "Ender's Game", "author-name" => "Orson Scott Card" ) );?>`

This is true even for single entries.

= How to query by post type in the front end? =

You can create new queries to display posts from a specific post type. This is done via the 'post_type' parameter to a WP_Query.

Example:

`<?php $args = array( 'post_type' => 'product', 'posts_per_page' => 10 );
$loop = new WP_Query( $args );
while ( $loop->have_posts() ) : $loop->the_post();
	the_title();
	echo '<div class="entry-content">';
	the_content();
	echo '</div>';
endwhile;?>`

This simply loops through the latest 10 product posts and displays the title and content of them. 

= How do I list the taxonomies in the front end? =

If you want to have a custom list in your theme, then you can pass the taxonomy name into the the_terms() function in the Loop, like so:

`<?php the_terms( $post->ID, 'people', 'People: ', ', ', ' ' ); ?>`

That displays the list of People attached to each post.

= How do I query by taxonomy in the frontend? =

Creating a taxonomy generally automatically creates a special query variable using WP_Query class, which we can use to retrieve posts based on. For example, to pull a list of posts that have 'Bob' as a 'person' taxomony in them, we will use:

`<?php $query = new WP_Query( array( 'person' => 'bob' ) ); ?>`

==Screenshots==
1. Creating custom post types and taxonomies
2. Creating custom fields and meta boxes
3. List of Meta boxes
4. Meta box with custom fields
5. Defined custom fields
6. Meta box arguments
7. Post Type Creator UI
8. Post Type Creator UI and listing
9. Taxonomy Creator UI
10. Taxonomy listing

== Changelog ==
= 1.0.2 =
* Fixed bug when arguments contained UTF8 characters ( like hebrew, chirilic... )
* Fixed Sortable field in Custom Fields Creator that wasn't clickable

= 1.0.1 =
* Fixed Menu Position argument for Custom Post Type Creator.
* Added filter for default_value.
* Fixed Template Select dropdown for Custom Fields Creator.
* Fixed a bug in Custom Fields Creator that prevented Options field in the process of creating custom fields from appearing.