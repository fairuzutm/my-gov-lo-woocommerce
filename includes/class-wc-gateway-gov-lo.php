<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WC_Gateway_Gov_LO extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'gov_lo';
        $this->icon               = ''; 
        $this->has_fields         = true;
        $this->method_title       = 'Malaysia Gov Letter Order';
        $this->method_description = 'Benarkan pembayaran menggunakan Letter Order (LO) Kerajaan Malaysia dengan muat naik dokumen wajib.';

        $this->init_form_fields();
        $this->init_settings();

        $this->title       = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );

        // Simpan setting admin
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Aktifkan Pembayaran Gov LO',
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => 'Tajuk',
                'type'        => 'text',
                'description' => 'Tajuk yang dilihat oleh pengguna semasa checkout.',
                'default'     => 'Pesanan Kerajaan (LO)',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Penerangan',
                'type'        => 'textarea',
                'description' => 'Penerangan kaedah pembayaran.',
                'default'     => 'Sila muat naik dokumen LO rasmi anda dalam format PDF.',
            )
        );
    }

    // Paparan Field di Checkout (HTML)
    public function payment_fields() {
        if ( $this->description ) {
            echo wpautop( wp_kses_post( $this->description ) );
        }

        echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent; border:0; padding:0;">';

        // Input No LO
        woocommerce_form_field( 'gov_lo_number', array(
            'type'        => 'text',
            'class'       => array('form-row-wide'),
            'label'       => 'Nombor LO (Wajib)',
            'required'    => true,
            'placeholder' => 'Contoh: KKM/2024/001'
        ));

        // Input File Upload
        echo '<div class="form-row form-row-wide">
                <label>Muat Naik Dokumen LO (PDF sahaja) <span class="required">*</span></label>
                <input type="file" name="gov_lo_file" id="gov_lo_file" accept=".pdf" required />
                <small style="display:block; color: #666; margin-top:5px;">Maksimum saiz fail bergantung pada server anda.</small>
              </div>';

        echo '</fieldset>';
    }

    // Proses Pembayaran (Backend)
    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );

        // Tandakan sebagai On Hold
        $order->update_status( 'on-hold', __( 'Menunggu pengesahan dokumen LO.', 'wc-gov-lo' ) );
        wc_reduce_stock_levels( $order_id );
        WC()->cart->empty_cart();

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }
}