jQuery(function($){
    $('form.checkout').on('checkout_place_order', function() {
        // Check if Gov LO payment method is selected
        if ($('#payment_method_gov_lo').is(':checked')) {
            
            var loNumber = $('#gov_lo_number').val();
            var loFile = $('#gov_lo_file').val();

            // 1. Basic Validation (Client Side)
            if(loNumber === '' || loFile === '') {
                alert('ATTENTION: Please enter the LO Number and upload the PDF Document before proceeding.');
                return false; // Stop checkout
            }

            // 2. Official Confirmation Pop-up
            var declaration = "CONFIRMATION & DECLARATION:\n\n" +
                              "I hereby confirm that the uploaded Letter Order (LO) document is genuine, valid, and currently in force.\n\n" +
                              "Click OK to proceed with the order.";
            
            if (confirm(declaration)) {
                return true; // Continue checkout
            } else {
                return false; // Cancel checkout
            }
        }
    });
});