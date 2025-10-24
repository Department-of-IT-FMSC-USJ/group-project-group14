// DOM Elements
const myBookingsGrid = document.getElementById('myBookings');
const logoutBtn = document.getElementById('logoutBtn');

// Initialize the page
document.addEventListener('DOMContentLoaded', () => {
    loadMyBookings();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('currentUser');
        window.location.href = 'index.html';
    });
}

// Load user's bookings
function loadMyBookings() {
    const currentUser = localStorage.getItem('currentUser');
    const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
    const myBookings = bookings.filter(booking => booking.userId === currentUser);
    
    if (myBookings.length === 0) {
        myBookingsGrid.innerHTML = `
            <div class="no-bookings">
                <h3>No bookings found</h3>
                <p>You haven't made any parking space bookings yet.</p>
                <a href="driver-dashboard.html" class="button">Find Parking Space</a>
            </div>
        `;
        return;
    }

    displayBookings(myBookings);
}

// Display bookings
function displayBookings(bookings) {
    const allSpaces = JSON.parse(localStorage.getItem('parkingSpaces')) || [];
    
    myBookingsGrid.innerHTML = bookings.map(booking => {
        const space = allSpaces.find(s => s.id === booking.spaceId);
        if (!space) return '';
        
        return `
            <div class="booking-card">
                <h3>${space.location}</h3>
                <div class="booking-details">
                    <p><strong>Date:</strong> ${formatDate(booking.date)}</p>
                    <p><strong>Time:</strong> ${booking.time}</p>
                    <p><strong>Duration:</strong> ${booking.duration} hours</p>
                    <p><strong>Total Cost:</strong> $${(space.price * booking.duration).toFixed(2)}</p>
                </div>
                <div class="booking-status ${getStatusClass(booking)}">
                    ${getStatusText(booking)}
                </div>
                ${isUpcoming(booking) ? `
                    <div class="booking-actions">
                        <button onclick="cancelBooking(${booking.id})" class="cancel-btn">Cancel Booking</button>
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

// Format date for display
function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Get booking status class
function getStatusClass(booking) {
    const bookingDate = new Date(booking.date + ' ' + booking.time);
    const now = new Date();
    
    if (booking.cancelled) return 'cancelled';
    if (bookingDate < now) return 'completed';
    return 'upcoming';
}

// Get booking status text
function getStatusText(booking) {
    if (booking.cancelled) return 'Cancelled';
    if (isUpcoming(booking)) return 'Upcoming';
    return 'Completed';
}

// Check if booking is upcoming
function isUpcoming(booking) {
    const bookingDate = new Date(booking.date + ' ' + booking.time);
    const now = new Date();
    return bookingDate > now && !booking.cancelled;
}

// Cancel booking
function cancelBooking(bookingId) {
    if (!confirm('Are you sure you want to cancel this booking?')) return;

    const bookings = JSON.parse(localStorage.getItem('bookings')) || [];
    const updatedBookings = bookings.map(booking => {
        if (booking.id === bookingId) {
            return { ...booking, cancelled: true };
        }
        return booking;
    });

    localStorage.setItem('bookings', JSON.stringify(updatedBookings));
    loadMyBookings();
}