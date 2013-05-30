<!doctype html>  

<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta charset="utf-8">
		<script src="//cdn.optimizely.com/js/175175161.js"></script>
		
		<title>
			<?php if ( !is_front_page() ) { echo wp_title( ' ', true, 'left' ); echo ' | '; }
			echo bloginfo( 'name' ); echo ' - '; bloginfo( 'description', 'display' );  ?> 
		</title>
				
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<!-- icons & favicons -->
		<!-- For iPhone 4 -->
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/library/images/icons/h/apple-touch-icon.png">
		<!-- For iPad 1-->
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/library/images/icons/m/apple-touch-icon.png">
		<!-- For iPhone 3G, iPod Touch and Android -->
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_template_directory_uri(); ?>/library/images/icons/l/apple-touch-icon-precomposed.png">
		<!-- For Nokia -->
		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/library/images/icons/l/apple-touch-icon.png">
		<!-- For everything else -->
		<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico">
				
		<!-- media-queries.js (fallback) -->
		<!--[if lt IE 9]>
			<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>			
		<![endif]-->

		<!-- html5.js -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
  		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

  		<link rel="stylesheet/less" type="text/css" href="<?php echo get_template_directory_uri(); ?>/less/bootstrap.less">
  		<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/css/jquery.bxslider.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,800,700,600,300' rel='stylesheet' type='text/css'>
		

		<!-- wordpress head functions -->
		<?php wp_head(); ?>
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/JS/jquery.bxslider.min.js"  type="text/javascript"></script>		
        <script src="http://a.vimeocdn.com/js/froogaloop2.min.js"></script>   
		
		
		<!-- end of wordpress head -->

		<!-- theme options from options panel -->
		<?php get_wpbs_theme_options(); ?>

		<?php 

			// check wp user level
			get_currentuserinfo(); 
			// store to use later
			global $user_level; 

			// get list of post names to use in 'typeahead' plugin for search bar
			if(of_get_option('search_bar', '1')) { // only do this if we're showing the search bar in the nav

				global $post;
				$tmp_post = $post;
				$get_num_posts = 40; // go back and get this many post titles
				$args = array( 'numberposts' => $get_num_posts );
				$myposts = get_posts( $args );
				$post_num = 0;

				global $typeahead_data;
				$typeahead_data = "[";

				foreach( $myposts as $post ) :	setup_postdata($post);
					$typeahead_data .= '"' . get_the_title() . '",';
				endforeach;

				$typeahedad_data = substr($typeahead_data, 0, strlen($typeahead_data) - 1);

				$typeahead_data .= "]";

				$post = $tmp_post;

			} // end if search bar is used

		?>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-28401694-1']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
<!-- start Mixpanel --><script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f);b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==
typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,
e,d])};b.__SV=1.2}})(document,window.mixpanel||[]);
mixpanel.init("48ebc33d8f538e16132f168d4b402d94");</script><!-- end Mixpanel -->
<!-- HitTail Code -->
<script type="text/javascript">
	(function(){ var ht = document.createElement('script');ht.async = true;
	  ht.type='text/javascript';ht.src = '//94870.hittail.com/mlt.js';
	  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ht, s);})();
</script>
	</head>
	
	<body <?php body_class(); ?>>
				
		<header role="banner">
		
			<div id="inner-header" class="clearfix">
				
				<div class="navbar navbar-fixed-top">
					<div class="navbar-inner">
						<div class="container-fluid nav-container">
							<nav role="navigation">
								<div class="solidlogo">
								<a href="<?php bloginfo('url'); ?>"><span>S</span>olid<span>W</span>ize</a>
								</div>
							
								<?php if (!is_user_logged_in()) { ?>
								<div class="signinbuttons">
									<div class="btn-group">
										<a href="#" class="btn btn-login btn-small dropdown-toggle" data-toggle="dropdown">Log In
									    <span class="caret"></span>
									  </a>
									  <ul class="dropdown-menu pull-right">
										  <?php dynamic_sidebar( 'nav-login' ); ?>										 
									   </ul>
									</div>
									<script>
									jQuery('.dropdown-menu').find('form').click(function (e) {
									    e.stopPropagation();
									  });
									  </script>
					  				<script>
					  				jQuery(document).ready(
					  					function(){
											jQuery(function() {
											    if ( document.location.href.indexOf('?action=login') > -1 ) {
													jQuery('.btn-group').addClass('open');														    
													}
											});
											
		
											});
												</script>
												
								  				
								
								<a href="<?php bloginfo('url'); ?>\pricing" class="btn btn-small btn-custom headersignupbtn">Sign Up</a>
								
								</div>
								<?php 	};
								?>		
								<?php if(pmpro_hasMembershipLevel('1')) 
								{ ?>  
							<div class="signinbuttons">
								<a href="<?php bloginfo('url'); ?>/pricing" class="btn btn-small btn-custom headerupgradebtn">Upgrade Account</a>
							</div>
							<?php } ?>
								<div class="nav-menusandsuch">
									<?php bones_main_nav2(); // Adjust using Menus in Wordpress Admin ?>
								</div>
								
								<div class="contactinfo">
									<p>Questions? Call: 877.688.7563 or <a href="#contactmodal" data-toggle="modal">email.</a>
								</div>
									
							

							</nav>
							
							<?php if(of_get_option('search_bar', '1')) {?>
							<form class="navbar-search pull-right" role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
								<input name="s" id="s" type="text" class="search-query" autocomplete="off" placeholder="<?php _e('Search','bonestheme'); ?>" data-provide="typeahead" data-items="4" data-source='<?php echo $typeahead_data; ?>'>
							</form>
							<?php } ?>
							
							
							
						</div>
					</div>
				</div>
			
			</div> <!-- end #inner-header -->
		
		
			<script>
			jQuery(document).ready(
				function(){
					jQuery('a[title="contactform"]').attr("data-toggle", "modal");
		

					});
						</script>
		
		</header> <!-- end header -->
							<div id="contactmodal" class="modal hide fade">
							  <div class="modal-header">
							    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							    <h3>Contact Us</h3>
							  </div>
							  <div class="modal-body">
								<p>  Have questions about SolidWize? Let us know what's on your mind and we'll be in touch shortly.<br><br>
								<span style="color:rgb(255, 92, 0);margin-top:5px">  Call Us: 877.688.7563<br>
								  Email:<a href="mailto:andrew@solidwize.com"> Andrew@SolidWize.com</a></span></p>
							    <p><iframe src="<?php bloginfo('url'); ?>/contact-form" 
frameborder="0" scrolling="auto" name="myInlineFrame" width="100%" height="245px">
</iframe>
</p>
							  </div>
							  <div class="modal-footer">
								  
							  </div>
							</div>
							
		<div class="container-fluid">
