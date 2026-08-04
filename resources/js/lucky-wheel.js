document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('luckyWheelPopup');
    if (!popup) return;

    // Check session/localStorage to prevent annoying the user
    if (localStorage.getItem('lucky_wheel_played') === 'true') return;

    const delay = parseInt(popup.getAttribute('data-delay') || '5') * 1000;
    setTimeout(() => {
        if (localStorage.getItem('lucky_wheel_played') !== 'true') {
            popup.style.display = 'flex';
            setTimeout(() => popup.classList.add('show'), 50);
        }
    }, delay);

    // Close logic
    const closeBtn = document.getElementById('luckyWheelClose');
    closeBtn.addEventListener('click', () => {
        popup.classList.remove('show');
        setTimeout(() => popup.style.display = 'none', 400);
        localStorage.setItem('lucky_wheel_played', 'true');
    });

    // Canvas Initializations
    const canvas = document.getElementById('luckyWheelCanvas');
    const ctx = canvas.getContext('2d');
    const segmentsData = JSON.parse(canvas.getAttribute('data-segments') || '[]');
    const winnerCoupon = canvas.getAttribute('data-winner') || '';

    // If no segments from admin, use placeholders
    let segments = segmentsData.length > 0 ? segmentsData : ['10% OFF', '5% OFF', 'FREE GIFT', 'NO LUCK', '15% OFF', 'TRY AGAIN'];
    
    // Ensure winner is in the list
    if (winnerCoupon && !segments.includes(winnerCoupon)) {
        segments.push(winnerCoupon);
    }

    const numSegments = segments.length;
    const anglePerSegment = (2 * Math.PI) / numSegments;
    let currentRotation = 0;
    let isSpinning = false;

    // Draw the Wheel slices
    function drawWheel() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = centerX - 10;

        for (let i = 0; i < numSegments; i++) {
            const startAngle = i * anglePerSegment + currentRotation;
            const endAngle = startAngle + anglePerSegment;

            // Alternate colors
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = i % 2 === 0 ? '#248443' : '#e67e22'; // Brand Green & Orange
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Text Label
            ctx.save();
            ctx.translate(centerX, centerY);
            ctx.rotate(startAngle + anglePerSegment / 2);
            ctx.textAlign = 'right';
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 12px Plus Jakarta Sans, sans-serif';
            ctx.fillText(segments[i], radius - 15, 4);
            ctx.restore();
        }

        // Central peg decoration
        ctx.beginPath();
        ctx.arc(centerX, centerY, 20, 0, 2 * Math.PI);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#248443';
        ctx.stroke();
    }

    drawWheel();

    // Unlock spin button only after email submit
    const emailInput = document.getElementById('luckyWheelEmail');
    const submitBtn = document.getElementById('luckyWheelSubmitBtn');
    const spinBtn = document.getElementById('luckyWheelSpinBtn');
    const errorDiv = document.getElementById('luckyWheelError');

    // Spin button is disabled initially
    spinBtn.disabled = true;

    submitBtn.addEventListener('click', function () {
        const email = emailInput.value.trim();
        if (!email || !email.includes('@')) {
            errorDiv.style.display = 'block';
            return;
        }
        errorDiv.style.display = 'none';
        
        // Hide email box and unlock spin button with nice visuals
        document.querySelector('.lucky-wheel-form').style.display = 'none';
        spinBtn.disabled = false;
        spinBtn.style.animation = 'pulse 1s infinite alternate';
        
        // Auto trigger spin for seamless UX
        triggerSpin();
    });

    spinBtn.addEventListener('click', triggerSpin);

    function triggerSpin() {
        if (isSpinning) return;
        isSpinning = true;
        spinBtn.disabled = true;

        // Find the index of the winning coupon
        let targetIndex = segments.indexOf(winnerCoupon);
        if (targetIndex === -1) targetIndex = 0; // Fallback

        // Calculate the target rotation angle to land on targetIndex
        // The arrow is pointing at the top (angle = 3/2 * PI, or -PI/2)
        // We want the mid-point of targetIndex segment to land under the arrow.
        const arrowAngle = 3 * Math.PI / 2;
        
        // Calculate the target angle offset
        const targetSliceAngle = targetIndex * anglePerSegment + anglePerSegment / 2;
        const landingAngle = arrowAngle - targetSliceAngle;

        // Add 5 full rotations for smooth spinning
        const totalRotations = 5 * 2 * Math.PI;
        const targetRotation = totalRotations + landingAngle;

        const duration = 5000; // 5 seconds
        const startTime = performance.now();
        const startRotation = currentRotation % (2 * Math.PI);

        function animateSpin(timestamp) {
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Decelerating easeOut cubic function
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            currentRotation = startRotation + easeProgress * (targetRotation - startRotation);

            drawWheel();

            if (progress < 1) {
                requestAnimationFrame(animateSpin);
            } else {
                // Completed! Show winning screen
                setTimeout(showWinScreen, 500);
            }
        }

        requestAnimationFrame(animateSpin);
    }

    function showWinScreen() {
        // Close main popup
        popup.classList.remove('show');
        setTimeout(() => popup.style.display = 'none', 400);
        localStorage.setItem('lucky_wheel_played', 'true');

        // Open Winner popup
        const winModal = document.getElementById('luckyWheelWinModal');
        const codeDisplay = document.getElementById('luckyWheelWinCode');
        codeDisplay.textContent = winnerCoupon || 'ROHIDA10';
        
        winModal.style.display = 'flex';
        setTimeout(() => winModal.classList.add('show'), 50);

        // Confetti effect
        const confettiContainer = winModal.querySelector('.confetti-container');
        for (let i = 0; i < 40; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'absolute';
            confetti.style.width = '8px';
            confetti.style.height = '8px';
            confetti.style.backgroundColor = ['#248443', '#e67e22', '#3498db', '#f1c40f', '#e74c3c'][Math.floor(Math.random() * 5)];
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = Math.random() * 50 + '%';
            confetti.style.opacity = Math.random();
            confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
            confetti.style.borderRadius = '50%';
            confettiContainer.appendChild(confetti);
        }
    }

    // Win Modal Close button
    document.getElementById('luckyWheelWinClose').addEventListener('click', () => {
        const winModal = document.getElementById('luckyWheelWinModal');
        winModal.classList.remove('show');
        setTimeout(() => winModal.style.display = 'none', 400);
    });

    // Copy to clipboard functionality
    document.getElementById('luckyWheelCopyBtn').addEventListener('click', function () {
        const winCode = document.getElementById('luckyWheelWinCode').textContent;
        navigator.clipboard.writeText(winCode).then(() => {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
            this.classList.remove('btn-outline-success');
            this.classList.add('btn-success', 'text-white');
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('btn-success', 'text-white');
                this.classList.add('btn-outline-success');
            }, 2000);
        });
    });
});
