// Driver Dashboard JavaScript

// Display message function
function showMessage(elementId, message, isError = false) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
        messageElement.textContent = message;
        messageElement.style.color = isError ? '#ff4444' : '#4CAF50';
        messageElement.style.display = 'block';
        
        setTimeout(() => {
            messageElement.style.display = 'none';
        }, 5000);
    }
}

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadVehicles();
    loadLocations();
    loadBookings();
});

// Load vehicles
async function loadVehicles() {
    try {
        const response = await fetch('../controllers/DriverController.php?action=get_vehicles');
        const result = await response.json();
        
        if (result.success) {
            displayVehicles(result.vehicles);
            populateVehicleSelect(result.vehicles);
        } else {
            document.getElementById('vehiclesList').innerHTML = '<p>No vehicles found.</p>';
        }
    } catch (error) {
        console.error('Error loading vehicles:', error);
        document.getElementById('vehiclesList').innerHTML = '<p>Error loading vehicles.</p>';
    }
}

// Display vehicles
function displayVehicles(vehicles) {
    const vehiclesList = document.getElementById('vehiclesList');
    
    if (vehicles.length === 0) {
        vehiclesList.innerHTML = '<p>No vehicles added yet.</p>';
        return;
    }
    
    let html = '<div class="vehicle-grid">';
    vehicles.forEach(vehicle => {
        html += `
            <div class="vehicle-item">
                <h3>${vehicle.vehicle_number}</h3>
                <p>Type: ${vehicle.vehicle_type}</p>
            </div>
        `;
    });
    html += '</div>';
    
    vehiclesList.innerHTML = html;
}

// Populate vehicle select dropdown
function populateVehicleSelect(vehicles) {
    const vehicleSelect = document.getElementById('vehicleSelect');
    
    vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';
    
    vehicles.forEach(vehicle => {
        const option = document.createElement('option');
        option.value = vehicle.vehicle_ID;
        option.textContent = `${vehicle.vehicle_number} (${vehicle.vehicle_type})`;
        vehicleSelect.appendChild(option);
    });
}

// Load available locations
async function loadLocations() {
    try {
        const response = await fetch('../controllers/DriverController.php?action=get_dashboard');
        const result = await response.json();
        
        if (result.success && result.locations) {
            populateLocationSelect(result.locations);
        }
    } catch (error) {
        console.error('Error loading locations:', error);
    }
}

// Populate location select dropdown
function populateLocationSelect(locations) {
    const locationSelect = document.getElementById('locationSelect');
    
    locationSelect.innerHTML = '<option value="">Select Location</option>';
    
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location.location_ID;
        option.textContent = `${location.name} - ${location.address} (${location.no_of_slot} slots)`;
        locationSelect.appendChild(option);
    });
}

// Load bookings
async function loadBookings() {
    try {
        const response = await fetch('../controllers/DriverController.php?action=get_bookings');
        const result = await response.json();
        
        if (result.success) {
            displayBookings(result.bookings);
        } else {
            document.getElementById('bookingsList').innerHTML = '<p>No bookings found.</p>';
        }
    } catch (error) {
        console.error('Error loading bookings:', error);
        document.getElementById('bookingsList').innerHTML = '<p>Error loading bookings.</p>';
    }
}

// Display bookings
function displayBookings(bookings) {
    const bookingsList = document.getElementById('bookingsList');
    
    if (bookings.length === 0) {
        bookingsList.innerHTML = '<p>No bookings yet.</p>';
        return;
    }
    
    let html = '<table class="bookings-table"><thead><tr><th>Location</th><th>Vehicle</th><th>Start Time</th><th>End Time</th><th>Duration (hrs)</th><th>Fee</th><th>Action</th></tr></thead><tbody>';
    
    bookings.forEach(booking => {
        const isActive = !booking.end_time;
        html += `
            <tr class="${isActive ? 'active-booking' : ''}">
                <td>${booking.location_name}</td>
                <td>${booking.vehicle_number}</td>
                <td>${booking.start_time}</td>
                <td>${booking.end_time || 'Active'}</td>
                <td>${booking.duration || '-'}</td>
                <td>Rs. ${booking.total_fee || '0.00'}</td>
                <td>
                    ${isActive ? `<button onclick="endParking(${booking.record_ID})" class="end-btn">End Parking</button>` : '-'}
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    bookingsList.innerHTML = html;
}

// Add vehicle form handler
document.getElementById('addVehicleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../controllers/DriverController.php?action=add_vehicle', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('addVehicleMessage', result.message, false);
            this.reset();
            loadVehicles();
        } else {
            showMessage('addVehicleMessage', result.message, true);
        }
    } catch (error) {
        showMessage('addVehicleMessage', 'An error occurred. Please try again.', true);
        console.error('Error:', error);
    }
});

// Book parking form handler
document.getElementById('bookParkingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../controllers/DriverController.php?action=book_parking', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('bookParkingMessage', result.message, false);
            this.reset();
            loadBookings();
        } else {
            showMessage('bookParkingMessage', result.message, true);
        }
    } catch (error) {
        showMessage('bookParkingMessage', 'An error occurred. Please try again.', true);
        console.error('Error:', error);
    }
});

// End parking function
async function endParking(recordId) {
    if (!confirm('Are you sure you want to end this parking session?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('record_id', recordId);
    
    try {
        const response = await fetch('../controllers/DriverController.php?action=end_parking', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(`Parking ended!\nDuration: ${result.duration} hour(s)\nTotal Fee: Rs. ${result.total_fee}`);
            loadBookings();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        console.error('Error:', error);
    }
}
