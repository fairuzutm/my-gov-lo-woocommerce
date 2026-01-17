<?php
/**
 * Plugin Name: WooCommerce Malaysia Gov Letter Order Payment
 * Plugin URI: https://misolutions.my
 * Author URI: https://misolutions.my
 * Description: Adds Malaysian Government Letter Order (LO) payment method with PDF upload, confirmation pop-up, and admin verification.
 * Version: 1.2
 * Author: Fairuz Sulaiman
 * Text Domain: wc-gov-lo
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define Constants
define( 'WC_GOV_LO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_GOV_LO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// 1. Include required files
require_once WC_GOV_LO_PLUGIN_DIR . 'includes/admin-features.php';
require_once WC_GOV_LO_PLUGIN_DIR . 'includes/frontend-features.php';

// 2. Init Payment Gateway Class
add_action( 'plugins_loaded', 'init_wc_gov_lo_gateway_class' );

function init_wc_gov_lo_gateway_class() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;    
    require_once WC_GOV_LO_PLUGIN_DIR . 'includes/class-wc-gateway-gov-lo.php';
}

// 3. Register Gateway
add_filter( 'woocommerce_payment_gateways', 'add_wc_gov_lo_gateway' );
function add_wc_gov_lo_gateway( $methods ) {
    $methods[] = 'WC_Gateway_Gov_LO';
    return $methods;
}

// 4. Enqueue Frontend Scripts
add_action( 'wp_enqueue_scripts', 'wc_gov_lo_enqueue_scripts' );
function wc_gov_lo_enqueue_scripts() {
    if ( is_checkout() ) {
        wp_enqueue_script( 'wc-gov-lo-js', WC_GOV_LO_PLUGIN_URL . 'assets/js/checkout-script.js', array('jquery'), '1.0', true );
        wp_enqueue_style( 'wc-gov-lo-css', WC_GOV_LO_PLUGIN_URL . 'assets/css/style.css' );
    }
}

// 5. Enqueue Admin Styles
add_action( 'admin_enqueue_scripts', 'wc_gov_lo_admin_styles' );
function wc_gov_lo_admin_styles() {
    wp_enqueue_style( 'wc-gov-lo-admin-css', WC_GOV_LO_PLUGIN_URL . 'assets/css/style.css' );
}