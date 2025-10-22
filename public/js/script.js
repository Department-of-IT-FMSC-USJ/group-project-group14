// Modern loading animation functions
function showLoader() {
    const loader = document.querySelector('.modern-loader-container');
    if (loader) {
        loader.classList.add('active');
    }
}

function hideLoader() {
    const loader = document.querySelector('.modern-loader-container');
    if (loader) {
        loader.classList.remove('active');
    }
}

// Wait for the page to load
window.addEventListener('load', () => {
    const optionsSection = document.getElementById('optionsSection');
    if (optionsSection) {
        showLoader();
        // Show loading animation for 2 seconds
        setTimeout(() => {
            hideLoader();
            optionsSection.style.display = 'block';
        }, 2000);
    }
});

// Function to show different forms
function showForm(formType) {
    // Hide all forms first
    const forms = [
        'optionsSection',
        'driverLogin',
        'driverRegister',
        'providerLogin',
        'providerRegister'
    ];
    
    forms.forEach(form => {
        const element = document.getElementById(form);
        if (element) {
            element.style.display = 'none';
        }
    });

    // Show the selected form
    let formId = formType;
    if (formType === 'driver' || formType === 'provider') {
        formId = formType + 'Login';
    }
    
    const targetForm = document.getElementById(formId);
    if (targetForm) {
        targetForm.style.display = 'block';
    }
}

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

// Driver Login Form Handler
const driverLoginForm = document.getElementById('driverLoginForm');
if (driverLoginForm) {
    driverLoginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('controllers/AuthController.php?action=login_driver', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('driverLoginMessage', result.message, false);
                setTimeout(() => {
                    window.location.href = 'views/' + result.redirect;
                }, 1000);
            } else {
                showMessage('driverLoginMessage', result.message, true);
            }
        } catch (error) {
            showMessage('driverLoginMessage', 'An error occurred. Please try again.', true);
            console.error('Error:', error);
        }
    });
}

// Driver Register Form Handler
const driverRegisterForm = document.getElementById('driverRegisterForm');
if (driverRegisterForm) {
    driverRegisterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('controllers/AuthController.php?action=register_driver', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('driverRegisterMessage', result.message, false);
                setTimeout(() => {
                    showForm('driverLogin');
                }, 1500);
            } else {
                showMessage('driverRegisterMessage', result.message, true);
            }
        } catch (error) {
            showMessage('driverRegisterMessage', 'An error occurred. Please try again.', true);
            console.error('Error:', error);
        }
    });
}

// Provider Login Form Handler
const providerLoginForm = document.getElementById('providerLoginForm');
if (providerLoginForm) {
    providerLoginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('controllers/AuthController.php?action=login_provider', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('providerLoginMessage', result.message, false);
                setTimeout(() => {
                    window.location.href = 'views/' + result.redirect;
                }, 1000);
            } else {
                showMessage('providerLoginMessage', result.message, true);
            }
        } catch (error) {
            showMessage('providerLoginMessage', 'An error occurred. Please try again.', true);
            console.error('Error:', error);
        }
    });
}

// Provider Register Form Handler
const providerRegisterForm = document.getElementById('providerRegisterForm');
if (providerRegisterForm) {
    providerRegisterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('controllers/AuthController.php?action=register_provider', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage('providerRegisterMessage', result.message, false);
                setTimeout(() => {
                    showForm('providerLogin');
                }, 1500);
            } else {
                showMessage('providerRegisterMessage', result.message, true);
            }
        } catch (error) {
            showMessage('providerRegisterMessage', 'An error occurred. Please try again.', true);
            console.error('Error:', error);
        }
    });
}
