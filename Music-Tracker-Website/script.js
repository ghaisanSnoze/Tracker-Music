document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 600);
            });
        }, 3000);
    }

    const searchInput = document.getElementById('searchTrack');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            const songs = document.querySelectorAll('.song-card');

            songs.forEach(song => {
                const title = song.querySelector('.song-info h5').innerText.toLowerCase();
                const artist = song.querySelector('.song-info p').innerText.toLowerCase();

                if (title.includes(term) || artist.includes(term)) {
                    song.style.display = 'flex';
                } else {
                    song.style.display = 'none';
                }
            });
        });
    }
});

window.addEventListener('load', function() {
    const loader = document.getElementById('loader-wrapper');
    if (loader) {
        const loadingDuration = 900;
        setTimeout(function() {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
        }, loadingDuration);
    }
});