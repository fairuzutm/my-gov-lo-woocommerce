jQuery(function($){
    $('form.checkout').on('checkout_place_order', function() {
        // Hanya jalan jika payment method Gov LO dipilih
        if ($('#payment_method_gov_lo').is(':checked')) {
            
            var loNumber = $('#gov_lo_number').val();
            var loFile = $('#gov_lo_file').val();

            // 1. Validasi Asas (Client Side)
            if(loNumber === '' || loFile === '') {
                alert('PERHATIAN: Sila masukkan No LO dan Muat Naik Dokumen PDF sebelum meneruskan.');
                return false; // Stop checkout
            }

            // 2. Pop-up Pengesahan Rasmi
            var declaration = "PENGESAHAN & DEKLARASI:\n\n" +
                              "Saya dengan ini mengesahkan bahawa dokumen Letter Order (LO) yang dimuat naik adalah tulen, sah, dan masih berkuat kuasa.\n\n" +
                              "Tekan OK untuk teruskan tempahan.";
            
            if (confirm(declaration)) {
                return true; // Teruskan checkout
            } else {
                return false; // Batal checkout
            }
        }
    });
});