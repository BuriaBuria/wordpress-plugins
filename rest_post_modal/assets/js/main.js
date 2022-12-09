document.addEventListener("DOMContentLoaded", () => {
    document.getElementById('postModal').addEventListener('show.bs.modal', modalShowPost)
} )

function modalShowPost() {
    fetch(wpApiSettings.url, { headers: {
        'X-WP-Nonce': wpApiSettings.nonce
        }
    })
    .then((response) => {
        return response.json()
    })
        .then((post) => {
            console.log( post )
            document.getElementById('modalTitle').textContent = post.title
            document.getElementById('modalBody').innerHTML = post.content
        })
        .catch((e) => {
            document.getElementById('modalTitle').textContent = 'Error!'
            document.getElementById('modalBody').innerHTML = 'No data received from server.'
        });
}