<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WC_Gateway_Gov_LO extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'gov_lo';
        $this->icon               = ''; 
        $this->has_fields         = true;
        $this->method_title       = 'Malaysian Gov Letter Order';
        $this->method_description = 'Allow payments using Malaysian Government Letter Order (LO) with mandatory document upload.';

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable Gov LO Payment',
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default'     => 'Government Letter Order (LO)',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'Payment method description that the customer will see on your checkout.',
                'default'     => 'Please upload your official Letter Order (LO) document in PDF format.',
            )
        );
    }

    public function payment_fields() {
        if ( $this->description ) {
            echo wpautop( wp_kses_post( $this->description ) );
        }

        echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent; border:0; padding:0;">';

        // LO Number Input
        woocommerce_form_field( 'gov_lo_number', array(
            'type'        => 'text',
            'class'       => array('form-row-wide'),
            'label'       => 'LO Number (Required)',
            'required'    => true,
            'placeholder' => 'Example: KKM/2026/001'
        ));

        // File Upload Input
        echo '<div class="form-row form-row-wide">
                <label>Upload LO Document (PDF Only) <span class="required">*</span></label>
                <input type="file" name="gov_lo_file" id="gov_lo_file" accept=".pdf" required />
                <small style="display:block; color: #666; margin-top:5px;">Maximum file size depends on your server settings.</small>
              </div>';

        echo '</fieldset>';
    }

    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );

        // Mark as On Hold
        $order->update_status( 'on-hold', __( 'Awaiting LO document verification.', 'wc-gov-lo' ) );
        wc_reduce_stock_levels( $order_id );
        WC()->cart->empty_cart();

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }
}