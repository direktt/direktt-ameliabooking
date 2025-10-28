jQuery( document ).ready( function ( $ ) { "use strict";
    const type = direkttAM.type;
    const display_name = direkttAmeliaBooking.displayName;
    const label = direkttAmeliaBooking.label;
    const observer = new MutationObserver( function( mutations ) {
        if ( type === '1' ) {
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

            const $givenName = $( 'input[name="given-name"]' );

            if ( $givenName.length) {
                $givenName.val( display_name );
                $( 'label[for="customer.firstName"]' ).text( label );

                $givenName.trigger( 'input' );
                $givenName.trigger( 'change' );
                $givenName[0].dispatchEvent( new Event( 'input', { bubbles: true } ) );
                $givenName[0].dispatchEvent( new Event( 'change', { bubbles: true } ) );
            }

            if ( $lastNameLabel.length && $emailLabel.length && $phoneLabel.length && $givenName.length ) {
                observer.disconnect();
            }
        } else if ( type === '2' ) {
            const $lastNameDiv = $( '.am-info-last-name' );
            const $emailDiv = $( '.am-info-email' );
            const $phoneDivChild = $( '.am-input-phone-wrapper' );
            
            if ( $lastNameDiv.length ) {
                $lastNameDiv.hide();
            }

            if ( $emailDiv.length ) {
                $emailDiv.hide();
            }
            
            if ( $phoneDivChild.length ) {
                $phoneDivChild.parent().parent().hide();
            }

            const $firstName = $( 'input[name="firstName"]' );
            const $lastName = $( 'input[name="lastName"]' );

            if ( $firstName.length && $lastName.length ) {
                $firstName.val( display_name );
                $firstName.parent().parent().parent().parent().parent().find( 'label' ).find( 'span' ).text( label );

                $firstName.trigger( 'input' );
                $firstName.trigger( 'change' );
                $firstName[0].dispatchEvent( new Event( 'input', { bubbles: true } ) );
                $firstName[0].dispatchEvent( new Event( 'change', { bubbles: true } ) );

                $lastName.val( direkttAM.user );

                $lastName.trigger( 'input' );
                $lastName.trigger( 'change' );
                $lastName[0].dispatchEvent( new Event( 'input', { bubbles: true } ) );
                $lastName[0].dispatchEvent( new Event( 'change', { bubbles: true } ) );
            }

            if ( $lastNameDiv.length && $emailDiv.length && $phoneDivChild.length && $firstName.length && $lastName.length ) {
                observer.disconnect();
            }
        }
    });

    observer.observe( document.body, {
        childList: true,
        subtree: true
    });
});