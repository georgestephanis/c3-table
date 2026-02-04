
const contactDialogTriggers = document.querySelectorAll( '.cta-button' );

contactDialogTriggers.forEach( ( btn ) => {
    btn.addEventListener( 'click', ( event ) => {
        event.preventDefault();
        window.contactDialog.showModal();
    } );
} );
