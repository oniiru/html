<?php
/*
Plugin Name: Dig Labs - Stripe Payments
Plugin URI: http://diglabs.com/stripe/
Description: This plugin allows the Stripe payment system to be easily integrated into Wordpress.
Author: Dig Labs
Version: 2.2.7
Author URI: http://diglabs.com/
*/

// Define variables

if ( ! defined( 'STRIPE_PAYMENTS_PLUGIN_BASENAME' ) )
	define( 'STRIPE_PAYMENTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'STRIPE_PAYMENTS_PLUGIN_NAME' ) )
	define( 'STRIPE_PAYMENTS_PLUGIN_NAME', trim( dirname( STRIPE_PAYMENTS_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'STRIPE_PAYMENTS_PLUGIN_DIR' ) )
	define( 'STRIPE_PAYMENTS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . STRIPE_PAYMENTS_PLUGIN_NAME );

if ( ! defined( 'STRIPE_PAYMENTS_PLUGIN_URL' ) )
	define( 'STRIPE_PAYMENTS_PLUGIN_URL', WP_PLUGIN_URL . '/' . STRIPE_PAYMENTS_PLUGIN_NAME );

if ( ! defined( 'STRIPE_PAYMENTS_PAYMENT_URL' ) )
	define( 'STRIPE_PAYMENTS_PAYMENT_URL', WP_PLUGIN_URL . '/payment' );

// Bootstrap this plugin
require_once STRIPE_PAYMENTS_PLUGIN_DIR . '/includes/initialize.php';

?>