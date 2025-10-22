// Payment Modal Handler
class PaymentModal {
    constructor() {
        this.modal = null;
        this.bookingData = null;
        this.init();
    }

    init() {
        // Create modal HTML
        this.createModal();
        
        // Attach event listeners
        this.attachEventListeners();
    }

    createModal() {
        const modalHTML = `
            <div id="paymentModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>💳 Payment Details</h2>
                        <span class="close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="payment-summary" id="paymentSummary">
                            <p><strong>Vehicle:</strong> <span id="summaryVehicle">-</span></p>
                            <p><strong>Location:</strong> <span id="summaryLocation">-</span></p>
                            <p><strong>Hourly Rate:</strong> Rs.<span id="summaryRate">-</span>/hr</p>
                        </div>
                        
                        <form id="paymentForm" class="payment-form">
                            <div class="form-group">
                                <label for="paymentType">Payment Method *</label>
                                <select id="paymentType" name="payment_type" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Debit Card">Debit Card</option>
                                    <option value="Mobile Payment">Mobile Payment</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>

                            <div class="form-group" id="cardholderGroup">
                                <label for="cardholderName">Cardholder Name *</label>
                                <input type="text" id="cardholderName" name="cardholder_name" placeholder="John Doe" required>
                            </div>

                            <div class="form-group" id="cardNumberGroup">
                                <label for="cardNumber">Card Number *</label>
                                <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>

                            <div class="card-row" id="cardDetailsRow">
                                <div class="form-group">
                                    <label for="expiryDate">Expiry Date *</label>
                                    <input type="text" id="expiryDate" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV *</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                                </div>
                            </div>

                            <div class="form-group" id="mobileNumberGroup" style="display: none;">
                                <label for="mobileNumber">Mobile Number *</label>
                                <input type="tel" id="mobileNumber" name="mobile_number" placeholder="0712345678">
                            </div>

                            <input type="hidden" name="vehicle_id" id="hiddenVehicleId">
                            <input type="hidden" name="location_id" id="hiddenLocationId">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" id="cancelPayment">Cancel</button>
                        <button type="button" class="btn-pay" id="confirmPayment">Confirm & Book</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('paymentModal');
    }

    attachEventListeners() {
        const closeBtn = this.modal.querySelector('.close');
        const cancelBtn = document.getElementById('cancelPayment');
        const confirmBtn = document.getElementById('confirmPayment');
        const paymentTypeSelect = document.getElementById('paymentType');
        const cardNumberInput = document.getElementById('cardNumber');
        const expiryInput = document.getElementById('expiryDate');

        // Close modal
        closeBtn.onclick = () => this.close();
        cancelBtn.onclick = () => this.close();
        
        // Click outside to close
        window.onclick = (event) => {
            if (event.target === this.modal) {
                this.close();
            }
        };

        // Payment type change
        paymentTypeSelect.onchange = (e) => this.handlePaymentTypeChange(e.target.value);

        // Card number formatting
        cardNumberInput.oninput = (e) => {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        };

        // Expiry date formatting
        expiryInput.oninput = (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        };

        // Confirm payment
        confirmBtn.onclick = () => this.confirmPayment();
    }

    handlePaymentTypeChange(paymentType) {
        const cardholderGroup = document.getElementById('cardholderGroup');
        const cardNumberGroup = document.getElementById('cardNumberGroup');
        const cardDetailsRow = document.getElementById('cardDetailsRow');
        const mobileNumberGroup = document.getElementById('mobileNumberGroup');
        
        const cardholderInput = document.getElementById('cardholderName');
        const cardNumberInput = document.getElementById('cardNumber');
        const expiryInput = document.getElementById('expiryDate');
        const cvvInput = document.getElementById('cvv');
        const mobileInput = document.getElementById('mobileNumber');

        if (paymentType === 'Credit Card' || paymentType === 'Debit Card') {
            // Show card fields
            cardholderGroup.style.display = 'flex';
            cardNumberGroup.style.display = 'flex';
            cardDetailsRow.style.display = 'grid';
            mobileNumberGroup.style.display = 'none';
            
            cardholderInput.required = true;
            cardNumberInput.required = true;
            expiryInput.required = true;
            cvvInput.required = true;
            mobileInput.required = false;
        } else if (paymentType === 'Mobile Payment') {
            // Show mobile field
            cardholderGroup.style.display = 'none';
            cardNumberGroup.style.display = 'none';
            cardDetailsRow.style.display = 'none';
            mobileNumberGroup.style.display = 'flex';
            
            cardholderInput.required = false;
            cardNumberInput.required = false;
            expiryInput.required = false;
            cvvInput.required = false;
            mobileInput.required = true;
        } else if (paymentType === 'Cash') {
            // Hide all payment fields
            cardholderGroup.style.display = 'none';
            cardNumberGroup.style.display = 'none';
            cardDetailsRow.style.display = 'none';
            mobileNumberGroup.style.display = 'none';
            
            cardholderInput.required = false;
            cardNumberInput.required = false;
            expiryInput.required = false;
            cvvInput.required = false;
            mobileInput.required = false;
        }
    }

    open(bookingData) {
        this.bookingData = bookingData;
        
        // Populate summary
        document.getElementById('summaryVehicle').textContent = bookingData.vehicleName;
        document.getElementById('summaryLocation').textContent = bookingData.locationName;
        document.getElementById('summaryRate').textContent = bookingData.hourlyRate;
        
        // Set hidden fields
        document.getElementById('hiddenVehicleId').value = bookingData.vehicleId;
        document.getElementById('hiddenLocationId').value = bookingData.locationId;
        
        // Reset form
        document.getElementById('paymentForm').reset();
        this.handlePaymentTypeChange('');
        
        // Show modal
        this.modal.style.display = 'block';
    }

    close() {
        this.modal.style.display = 'none';
        this.bookingData = null;
    }

    confirmPayment() {
        const form = document.getElementById('paymentForm');
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Disable button to prevent double submission
        const confirmBtn = document.getElementById('confirmPayment');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        // Create form data
        const formData = new FormData(form);
        formData.append('book_parking', '1');

        // Submit to controller
        fetch('../controllers/DriverController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .then(data => {
            if (data) {
                // If not redirected, reload the page
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Payment failed. Please try again.');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm & Book';
        });
    }
}

// Initialize payment modal when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.paymentModal = new PaymentModal();
});
