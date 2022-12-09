let tabs = document.querySelectorAll('h2.nav-tab-wrapper a');
tabs.forEach( (tab) => {
    tab.classList.add('admin2pointer-events')
})

let sections = document.querySelectorAll('h2')
sections.forEach( (section) => {
    if( section.innerHTML === 'Status' || section.innerHTML === 'Cache Management' ){
        section.nextSibling.firstChild.classList.add('admin2pointer-events')
    }
} )


