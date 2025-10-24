// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        // Add fade-out after 5 seconds
        setTimeout(function() {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            
            // Remove from DOM after animation
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });
});
