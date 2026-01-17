<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'add_meta_boxes', 'wc_gov_lo_add_admin_metabox' );

function wc_gov_lo_add_admin_metabox() {
    $screens = array( 'shop_order', 'woocommerce_page_wc-orders' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'gov_lo_verification_box',
            __( 'LO Document Verification', 'wc-gov-lo' ),
            'wc_gov_lo_render_admin_metabox',
            $screen,
            'side',
            'high'
        );
    }
}

function wc_gov_lo_render_admin_metabox( $post_or_order_object ) {
    $order = ( $post_or_order_object instanceof WC_Order ) ? $post_or_order_object : wc_get_order( $post_or_order_object->ID );
    if ( ! $order ) return;

    if ( $order->get_payment_method() !== 'gov_lo' ) {
        echo '<p style="color:#777;">This order does not use Gov LO.</p>';
        return;
    }

    $lo_number   = get_post_meta( $order->get_id(), '_gov_lo_number', true );
    $lo_file_url = get_post_meta( $order->get_id(), '_gov_lo_file_url', true );
    $order_date  = wc_format_datetime( $order->get_date_created() );

    ?>
    <div class="gov-lo-admin-box">
        <div class="admin-notice-box">
            <strong>⚠️ Mandatory Verification:</strong><br>
            <small>Ensure the PDF is valid before changing status to "Completed".</small>
        </div>

        <p><strong>LO Number:</strong><br>
        <input type="text" readonly value="<?php echo esc_attr($lo_number); ?>" class="widefat"></p>

        <p><strong>Date:</strong><br>
        <?php echo esc_html($order_date); ?></p>

        <p><strong>Document:</strong><br>
        <?php if ( $lo_file_url ) : ?>
            <a href="<?php echo esc_url( $lo_file_url ); ?>" target="_blank" class="button button-primary button-large" style="width:100%; text-align:center;">
                <span class="dashicons dashicons-pdf"></span> Open PDF
            </a>
        <?php else : ?>
            <span style="color:red;">No Document Found.</span>
        <?php endif; ?>
        </p>
    </div>
    <?php
}