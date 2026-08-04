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
