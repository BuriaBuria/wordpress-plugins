document.querySelector( '.copymeta-init' ).addEventListener( 'click', initCopyMeta );
async function initCopyMeta() {
    document.querySelectorAll( '.copymeta-init , .copymeta-description' ).forEach( (e)=>e.remove() );
    updateInfo = document.createElement( 'div' );
    updateInfo.className = 'copymeta-update'
    updateInfo.innerHTML = '<div>Process started, please wait...</div>';
    document.querySelector( '.copymeta-header' ).after( updateInfo );
    let response = await fetch( RESTDATA.endpoint, {
        headers: {
            'X-WP-Nonce': RESTDATA.nonce
        }
    });
    let result = await response.text();
    resultInfo = document.createElement( 'div' );
    resultInfo.innerHTML = result;
    document.querySelector( '.copymeta-update' ).after( resultInfo );
}
