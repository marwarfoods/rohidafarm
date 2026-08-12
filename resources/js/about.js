/* About Us Page Custom Animation Scripts */
document.addEventListener('DOMContentLoaded', function () {
    const timelineItems = document.querySelectorAll('.timeline-item');
    if (timelineItems.length === 0) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.25
    };

    const timelineObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const dot = entry.target.querySelector('.timeline-dot');
                if (dot) {
                    dot.style.backgroundColor = 'var(--primary-green, #248443)';
                    dot.style.borderColor = '#fff';
                    dot.style.transform = 'scale(1.3)';
                }
            } else {
                const dot = entry.target.querySelector('.timeline-dot');
                if (dot) {
                    dot.style.backgroundColor = '#fff';
                    dot.style.borderColor = 'var(--primary-green, #248443)';
                    dot.style.transform = 'scale(1)';
                }
            }
        });
    }, observerOptions);

    timelineItems.forEach(item => {
        timelineObserver.observe(item);
    });
});

/* About Us — "Making Of" Video Play Button */
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('aboutFeatureVideo');
    const playBtn = document.getElementById('aboutVideoPlayBtn');
    if (!video || !playBtn) return;

    playBtn.addEventListener('click', function () {
        video.controls = true;
        video.play();
        playBtn.classList.add('is-playing');
    });

    video.addEventListener('pause', function () {
        if (!video.ended) return;
        playBtn.classList.remove('is-playing');
    });

    video.addEventListener('ended', function () {
        playBtn.classList.remove('is-playing');
    });
});
