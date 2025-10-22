// Provider Dashboard JavaScript

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
    loadLocations();
    loadParkingRecords();
});

// Load locations
async function loadLocations() {
    try {
        const response = await fetch('../controllers/ProviderController.php?action=get_locations');
        const result = await response.json();
        
        if (result.success) {
            displayLocations(result.locations);
        } else {
            document.getElementById('locationsList').innerHTML = '<p>No locations found.</p>';
        }
    } catch (error) {
        console.error('Error loading locations:', error);
        document.getElementById('locationsList').innerHTML = '<p>Error loading locations.</p>';
    }
}

// Display locations
function displayLocations(locations) {
    const locationsList = document.getElementById('locationsList');
    
    if (locations.length === 0) {
        locationsList.innerHTML = '<p>No locations added yet.</p>';
        return;
    }
    
    let html = '<div class="location-grid">';
    locations.forEach(location => {
        const availableSlots = location.available_slots || 0;
        const occupiedSlots = location.no_of_slot - availableSlots;
        
        html += `
            <div class="location-item">
                <h3>${location.name}</h3>
                <p><strong>Address:</strong> ${location.address}</p>
                <p><strong>Total Slots:</strong> ${location.no_of_slot}</p>
                <p><strong>Available:</strong> ${availableSlots} | <strong>Occupied:</strong> ${occupiedSlots}</p>
                <div class="location-actions">
                    <button onclick="editLocation(${location.location_ID}, '${location.name}', ${location.no_of_slot}, '${location.address}')" class="edit-btn">Edit</button>
                    <button onclick="deleteLocation(${location.location_ID})" class="delete-btn">Delete</button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    locationsList.innerHTML = html;
}

// Load parking records
async function loadParkingRecords() {
    try {
        const response = await fetch('../controllers/ProviderController.php?action=get_records');
        const result = await response.json();
        
        if (result.success) {
            displayRecords(result.records);
        } else {
            document.getElementById('recordsList').innerHTML = '<p>No parking records found.</p>';
        }
    } catch (error) {
        console.error('Error loading records:', error);
        document.getElementById('recordsList').innerHTML = '<p>Error loading records.</p>';
    }
}

// Display parking records
function displayRecords(records) {
    const recordsList = document.getElementById('recordsList');
    
    if (records.length === 0) {
        recordsList.innerHTML = '<p>No parking records yet.</p>';
        return;
    }
    
    let html = '<table class="records-table"><thead><tr><th>Vehicle</th><th>Type</th><th>Start Time</th><th>End Time</th><th>Duration (hrs)</th><th>Fee</th><th>Status</th></tr></thead><tbody>';
    
    records.forEach(record => {
        const isActive = !record.end_time;
        html += `
            <tr class="${isActive ? 'active-record' : ''}">
                <td>${record.vehicle_number}</td>
                <td>${record.vehicle_type}</td>
                <td>${record.start_time}</td>
                <td>${record.end_time || 'Active'}</td>
                <td>${record.duration || '-'}</td>
                <td>Rs. ${record.total_fee || '0.00'}</td>
                <td>${isActive ? '<span class="status-active">Active</span>' : '<span class="status-completed">Completed</span>'}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    recordsList.innerHTML = html;
}

// Add location form handler
document.getElementById('addLocationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('../controllers/ProviderController.php?action=add_location', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('addLocationMessage', result.message, false);
            this.reset();
            loadLocations();
        } else {
            showMessage('addLocationMessage', result.message, true);
        }
    } catch (error) {
        showMessage('addLocationMessage', 'An error occurred. Please try again.', true);
        console.error('Error:', error);
    }
});

// Edit location function
function editLocation(id, name, slots, address) {
    const newName = prompt('Enter location name:', name);
    if (!newName) return;
    
    const newSlots = prompt('Enter number of slots:', slots);
    if (!newSlots) return;
    
    const newAddress = prompt('Enter address:', address);
    if (!newAddress) return;
    
    updateLocation(id, newName, newSlots, newAddress);
}

// Update location
async function updateLocation(id, name, slots, address) {
    const formData = new FormData();
    formData.append('location_id', id);
    formData.append('name', name);
    formData.append('no_of_slot', slots);
    formData.append('address', address);
    
    try {
        const response = await fetch('../controllers/ProviderController.php?action=update_location', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            loadLocations();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        console.error('Error:', error);
    }
}

// Delete location function
async function deleteLocation(id) {
    if (!confirm('Are you sure you want to delete this location?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('location_id', id);
    
    try {
        const response = await fetch('../controllers/ProviderController.php?action=delete_location', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            loadLocations();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        console.error('Error:', error);
    }
}
