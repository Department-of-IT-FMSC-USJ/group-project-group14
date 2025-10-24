function initializeAnimations() {
    // Login page lines
    if (document.querySelector('.parking-lines-animation')) {
        for (let i = 0; i < 10; i++) {
            const line = document.createElement('div');
            line.className = 'parking-line animated-line';
            line.style.left = `${i * 200}px`;
            line.style.animationDelay = `${i * 0.5}s`;
            document.querySelector('.parking-lines-animation').appendChild(line);
        }
    }

    // Driver dashboard cars
    if (document.querySelector('.road-animation')) {
        for (let i = 0; i < 5; i++) {
            const car = document.createElement('div');
            car.className = 'car-icon animated-car';
            car.style.top = `${50 + i * 100}px`;
            car.style.animationDelay = `${i * 3}s`;
            document.querySelector('.road-animation').appendChild(car);

            const line = document.createElement('div');
            line.className = 'road-line';
            line.style.top = `${100 + i * 100}px`;
            document.querySelector('.road-animation').appendChild(line);
        }
    }

    // Provider dashboard grid
    if (document.querySelector('.parking-grid-animation')) {
        for (let i = 0; i < 5; i++) {
            for (let j = 0; j < 3; j++) {
                const cell = document.createElement('div');
                cell.className = 'grid-cell animated-grid';
                cell.style.left = `${j * 120 + 50}px`;
                cell.style.top = `${i * 170 + 50}px`;
                cell.style.animationDelay = `${(i + j) * 0.2}s`;
                document.querySelector('.parking-grid-animation').appendChild(cell);
            }
        }
    }

    // My bookings clocks
    if (document.querySelector('.booking-time-animation')) {
        for (let i = 0; i < 3; i++) {
            const clock = document.createElement('div');
            clock.className = 'clock';
            clock.style.left = `${150 + i * 200}px`;
            clock.style.top = `${100 + (i % 2) * 150}px`;

            const hourHand = document.createElement('div');
            hourHand.className = 'clock-hand hour-hand animated-hour';
            hourHand.style.animationDelay = `${-i * 5}s`;

            const minuteHand = document.createElement('div');
            minuteHand.className = 'clock-hand minute-hand animated-minute';
            minuteHand.style.animationDelay = `${-i * 0.5}s`;

            clock.appendChild(hourHand);
            clock.appendChild(minuteHand);
            document.querySelector('.booking-time-animation').appendChild(clock);
        }
    }
}

// Initialize animations when document is loaded
document.addEventListener('DOMContentLoaded', initializeAnimations);