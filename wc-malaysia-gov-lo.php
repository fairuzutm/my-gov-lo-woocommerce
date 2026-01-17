<?php
/**
 * Plugin Name: WooCommerce Malaysia Gov Letter Order Payment
 * Plugin URI: https://misolutions.my/
 * Author URI: https://misolutions.my/
 * Description: Kaedah pembayaran LO Kerajaan dengan muat naik PDF, pop-up pengesahan dan semakan admin.
 * Version: 1.1
 * Author: Fairuz Sulaiman
 * Text Domain: wc-gov-lo
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define Constants untuk Path
define( 'WC_GOV_LO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_GOV_LO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// 1. Muat turun fail-fail sokongan (Includes)
require_once WC_GOV_LO_PLUGIN_DIR . 'includes/admin-features.php';
require_once WC_GOV_LO_PLUGIN_DIR . 'includes/frontend-features.php';

// 2. Init Payment Gateway Class apabila plugins loaded
add_action( 'plugins_loaded', 'init_wc_gov_lo_gateway_class' );

function init_wc_gov_lo_gateway_class() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
    
    // Panggil fail Class Gateway
    require_once WC_GOV_LO_PLUGIN_DIR . 'includes/class-wc-gateway-gov-lo.php';
}

// 3. Daftar Gateway ke WooCommerce
add_filter( 'woocommerce_payment_gateways', 'add_wc_gov_lo_gateway' );
function add_wc_gov_lo_gateway( $methods ) {
    $methods[] = 'WC_Gateway_Gov_LO';
    return $methods;
}

// 4. Load Scripts (JS/CSS) untuk Frontend
add_action( 'wp_enqueue_scripts', 'wc_gov_lo_enqueue_scripts' );
function wc_gov_lo_enqueue_scripts() {
    if ( is_checkout() ) {
        // Load JS untuk Pop-up
        wp_enqueue_script( 'wc-gov-lo-js', WC_GOV_LO_PLUGIN_URL . 'assets/js/checkout-script.js', array('jquery'), '1.0', true );
        // Load CSS (Optional)
        wp_enqueue_style( 'wc-gov-lo-css', WC_GOV_LO_PLUGIN_URL . 'assets/css/style.css' );
    }
}

// 5. Load CSS untuk Admin Dashboard (Supaya kotak semakan nampak cantik)
add_action( 'admin_enqueue_scripts', 'wc_gov_lo_admin_styles' );
function wc_gov_lo_admin_styles() {
    wp_enqueue_style( 'wc-gov-lo-admin-css', WC_GOV_LO_PLUGIN_URL . 'assets/css/style.css' );
}