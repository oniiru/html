<?php // Start Custom Login Logo
    function my_custom_login_logo() {
		$loginlogo = get_option('ka_loginlogo');
        echo '<style type="text/css">
            h1 a { background-image:url('.$loginlogo.') !important; }
        </style>';
    }
    add_action('login_head', 'my_custom_login_logo');
    
    

// Start Custom Login Logo URL
    function change_wp_login_url() {
    echo home_url();
    }
    function change_wp_login_title() {
    echo get_option('blogname');
    }
    add_filter('login_headerurl', 'change_wp_login_url');
    add_filter('login_headertitle', 'change_wp_login_title');
?>