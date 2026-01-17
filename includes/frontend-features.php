<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 1. Enable Multipart Form Data
 */
add_action( 'woocommerce_checkout_init', 'wc_gov_lo_enable_checkout_upload' );
function wc_gov_lo_enable_checkout_upload() {
    echo '<script>jQuery(document).ready(function($){ $("form.checkout").attr("enctype", "multipart/form-data"); });</script>';
}

/**
 * 2. Checkout Validation
 */
add_action( 'woocommerce_checkout_process', 'wc_gov_lo_validate_checkout' );
function wc_gov_lo_validate_checkout() {
    if ( $_POST['payment_method'] != 'gov_lo' ) return;

    if ( empty( $_POST['gov_lo_number'] ) ) {
        wc_add_notice( __( 'Please enter the LO Number.', 'wc-gov-lo' ), 'error' );
    }

    if ( isset( $_FILES['gov_lo_file'] ) && ! empty( $_FILES['gov_lo_file']['name'] ) ) {
        $file_type = wp_check_filetype( $_FILES['gov_lo_file']['name'] );
        if ( $file_type['ext'] !== 'pdf' ) {
            wc_add_notice( __( 'Only PDF file format is allowed for the LO document.', 'wc-gov-lo' ), 'error' );
        }
    } else {
        wc_add_notice( __( 'Please upload the LO document.', 'wc-gov-lo' ), 'error' );
    }
}

/**
 * 3. Save Data to Order Meta
 */
add_action( 'woocommerce_checkout_update_order_meta', 'wc_gov_lo_save_data' );
function wc_gov_lo_save_data( $order_id ) {
    if ( $_POST['payment_method'] != 'gov_lo' ) return;

    if ( ! empty( $_POST['gov_lo_number'] ) ) {
        update_post_meta( $order_id, '_gov_lo_number', sanitize_text_field( $_POST['gov_lo_number'] ) );
    }

    if ( ! empty( $_FILES['gov_lo_file']['name'] ) ) {
        $uploaded_file = $_FILES['gov_lo_file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            update_post_meta( $order_id, '_gov_lo_file_url', $movefile['url'] );
            update_post_meta( $order_id, '_gov_lo_file_path', $movefile['file'] );
        }
    }
}

/**
 * 4. Display in My Account (Customer View)
 */
add_action( 'woocommerce_order_details_after_order_table', 'wc_gov_lo_display_my_account', 10, 1 );
function wc_gov_lo_display_my_account( $order ) {
    if ( $order->get_payment_method() !== 'gov_lo' ) return;

    $lo_number   = get_post_meta( $order->get_id(), '_gov_lo_number', true );
    $lo_file_url = get_post_meta( $order->get_id(), '_gov_lo_file_url', true );
    $order_date  = wc_format_datetime( $order->get_date_created() );
    $status      = wc_get_order_status_name( $order->get_status() );
    ?>
    <section class="gov-lo-my-account-box">
        <h2 class="woocommerce-column__title">Letter Order (LO) Details</h2>
        <table class="woocommerce-table shop_table">
            <tr><th>LO Status:</th><td><strong><?php echo esc_html( $status ); ?></strong></td></tr>
            <tr><th>Date:</th><td><?php echo esc_html( $order_date ); ?></td></tr>
            <tr><th>LO Number:</th><td><?php echo esc_html( $lo_number ); ?></td></tr>
            <tr><th>Document:</th><td>
                <?php if($lo_file_url): ?>
                    <a href="<?php echo esc_url($lo_file_url); ?>" target="_blank" class="button">View PDF</a>
                <?php else: echo 'None'; endif; ?>
            </td></tr>
        </table>
    </section>
    <?php
}