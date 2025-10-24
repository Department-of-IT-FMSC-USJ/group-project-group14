function createParkingElements() {
    const container = document.querySelector('.animation-container');
    if (!container) return;

    // Create parking slots grid
    for (let i = 0; i < 5; i++) {
        for (let j = 0; j < 3; j++) {
            const slot = document.createElement('div');
            slot.className = 'parking-slot pulsing-slot';
            slot.style.left = `${j * 150 + 50}px`;
            slot.style.top = `${i * 220 + 50}px`;
            slot.style.animationDelay = `${(i + j) * 0.2}s`;
            container.appendChild(slot);
        }
    }

    // Create moving cars
    for (let i = 0; i < 3; i++) {
        const car = document.createElement('div');
        car.className = 'car moving-car';
        car.style.top = `${i * 250 + 150}px`;
        car.style.animationDelay = `${i * 5}s`;
        
        const headlight = document.createElement('div');
        headlight.className = 'headlight';
        car.appendChild(headlight);
        
        container.appendChild(car);
    }

    // Create traffic lights
    for (let i = 0; i < 2; i++) {
        const light = document.createElement('div');
        light.className = 'traffic-light';
        light.style.left = `${i * 300 + 100}px`;
        light.style.top = '50px';

        const colors = ['red', 'yellow', 'green'];
        colors.forEach(color => {
            const bulb = document.createElement('div');
            bulb.className = `light ${color} blinking-light`;
            bulb.style.animationDelay = `${Math.random() * 2}s`;
            light.appendChild(bulb);
        });

        container.appendChild(light);
    }

    // Create parking lines
    for (let i = 0; i < 10; i++) {
        const hLine = document.createElement('div');
        hLine.className = 'parking-line horizontal floating';
        hLine.style.left = `${i * 150}px`;
        hLine.style.top = `${Math.random() * 80 + 10}%`;
        hLine.style.animationDelay = `${i * 0.3}s`;
        container.appendChild(hLine);

        const vLine = document.createElement('div');
        vLine.className = 'parking-line vertical floating';
        vLine.style.left = `${i * 150 + 25}px`;
        vLine.style.top = `${Math.random() * 80 + 10}%`;
        vLine.style.animationDelay = `${i * 0.3}s`;
        container.appendChild(vLine);
    }
}

// Initialize animations based on page type
function initializePageAnimations() {
    const container = document.querySelector('.animation-container');
    if (!container) return;

    if (container.classList.contains('login-animation')) {
        // Login page specific initialization
        container.style.opacity = '0.8';
    } else if (container.classList.contains('driver-animation')) {
        // Driver dashboard specific initialization
        container.style.opacity = '0.9';
    } else if (container.classList.contains('provider-animation')) {
        // Provider dashboard specific initialization
        container.style.opacity = '0.85';
    } else if (container.classList.contains('bookings-animation')) {
        // Bookings page specific initialization
        container.style.opacity = '0.8';
    }

    createParkingElements();
}

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', initializePageAnimations);

// Adjust animations on scroll
window.addEventListener('scroll', () => {
    const container = document.querySelector('.animation-container');
    if (!container) return;
    
    const scrolled = window.pageYOffset;
    const rate = scrolled * 0.5;
    
    container.style.transform = `translate3d(0, ${rate}px, 0)`;
});