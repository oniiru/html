<?php

// Required PHP files
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.settings.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.custom.form.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.form.helper.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.button.helper.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/class.email.helper.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/shortcodes.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/ajax-payment.php';
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/common/alt-api.php';

if( is_admin() ) {
    require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/admin/admin.php';
}

// WP Header Hook (add javascript and css to the page)
add_action('wp_head', 'stripe_payments_addHeaderCode', 0);
function stripe_payments_addHeaderCode() {  
    if (function_exists('wp_enqueue_script')) {
                    
        // include our CSS styles
        echo '<link type="text/css" rel="stylesheet" href="' . STRIPE_PAYMENTS_PLUGIN_URL . '/css/stripe.css" />' . "\n";
        
        // add our scripts and their dependencies
        wp_enqueue_script('jquery');
        
        // mark that we were here
        echo "\n<!--Stripe Payment Plugin Loaded-->\n";     
    }
}

// Hook into the template filter to inject our custom
//  templates for the payment and webhook URLs that
//  are configured on the admin page.
add_filter('template_include', 'stripe_custom_templates');
function stripe_custom_templates() {
    global $template;

    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 'https://' : 'http://';
    $the_url = $proto.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $the_url_lc = untrailingslashit( strtolower($the_url) );
    $request_parts = parse_url($the_url_lc);
    $request_path = $request_parts["path"];

    $settings = new StripeSettings();
    $web_hook_url = untrailingslashit( strtolower( $settings->getWebHookUrl() ) );
    
    if( $web_hook_url !=false )
    {
        $hook_parts = parse_url($web_hook_url);
        $hook_path = $hook_parts["path"];
        $site_url = site_url(null, 'https');
        $site_parts = parse_url($site_url);
        $site_path = $site_parts["path"];
        
        $request_path = str_replace($site_path, "", $request_path);
        $hook_path = str_replace($site_path, "", $hook_path);
                
        if( $request_path == $hook_path ) { 
            $template = STRIPE_PAYMENTS_PLUGIN_DIR.'/templates/webhook-template.php';
            return $template;
        }
    }
    return $template;
}

$stripe_payment_begin_callbacks = array();
$stripe_payment_end_callbacks = array();
function stripe_register_payment_begin_callback($callback){
    global $stripe_payment_begin_callbacks;
    $stripe_payment_begin_callbacks[] = $callback;
}
function stripe_register_payment_end_callback($callback){
    global $stripe_payment_end_callbacks;
    $stripe_payment_end_callbacks[] = $callback;
}


// Add the ability for the plugin to detect available updates.
//
$api_url = 'http://diglabs.com/api/plugin/';
$plugin_folder = 'diglabs-stripe-payments';
$plugin_file = 'diglabs-stripe-payments.php';

$dl_alt_api = new Dl_Plugin_Alt_Api( $api_url, $plugin_folder, $plugin_file );
add_filter( 'pre_set_site_transient_update_plugins', array( &$dl_alt_api, 'Check' ) );
add_filter( 'plugins_api', array( &$dl_alt_api, 'Info' ), 10, 3);


?>