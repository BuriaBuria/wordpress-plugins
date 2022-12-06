let watchTrigger = new MutationObserver(displayPatch);
let trigger = document.querySelector('.abc-booking-selection');
watchTrigger.observe(trigger, {childList:true});

function displayPatch() {
    let target = document.querySelector('.abc-column');
    let targetContent = target.innerHTML;
    let end = targetContent.search('<input');
    let start = targetContent.search('room:') + 11;
    if( -1 !== start && -1 !== end ) {
        let price = targetContent.substring(start, end).trim();
        target.insertAdjacentHTML('beforeend', '<br><b>Price with discount:</b> ' + Math.round( price * 0.7 ) );
    }
}



