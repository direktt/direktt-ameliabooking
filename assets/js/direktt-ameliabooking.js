jQuery( document ).ready( function ( $ ) { "use strict";
    const observer = new MutationObserver( function( mutations) {
        // Check if the labels exist yet
        const $lastNameLabel = $( 'label[for="customer.lastName"]' );
        const $emailLabel = $( 'label[for="customer.email"]' );
        const $phoneLabel = $( 'label[for="customer.phone"]' );
        
        if ( $lastNameLabel.length ) {
            $lastNameLabel.parent().parent().hide();
        }

        if ( $emailLabel.length ) {
            $emailLabel.parent().parent().hide();
        }
        
        if ( $phoneLabel.length ) {
            $phoneLabel.parent().parent().hide();
        }

        // Optional: if both are found and hidden, stop observing
        if ( $lastNameLabel.length && $emailLabel.length && $phoneLabel.length ) {
            observer.disconnect();
        }

        var display_name = direkttAmeliaBooking.displayName;
        $( 'input[name="given-name"]' ).val( display_name );
    });

    // Start observing the whole body for child additions
    observer.observe( document.body, {
        childList: true,
        subtree: true
    });

    
});