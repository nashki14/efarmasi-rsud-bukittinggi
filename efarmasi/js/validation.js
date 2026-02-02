// Form Validation Functions
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const errorElement = field.parentNode.querySelector('.error-message');
    
    // Clear previous error
    field.classList.remove('error');
    if (errorElement) {
        errorElement.style.display = 'none';
    }
    
    // Check required field
    if (field.hasAttribute('required') && !value) {
        showError(field, 'Field ini wajib diisi');
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showError(field, 'Format email tidak valid');
            return false;
        }
    }
    
    // Password strength
    if (field.type === 'password' && value) {
        if (value.length < 8) {
            showError(field, 'Password minimal 8 karakter');
            return false;
        }
    }
    
    // Phone number validation
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[0-9+\-\s()]{10,}$/;
        if (!phoneRegex.test(value)) {
            showError(field, 'Format nomor telepon tidak valid');
            return false;
        }
    }
    
    // Date validation (not in future)
    if (field.type === 'date' && value) {
        const inputDate = new Date(value);
        const today = new Date();
        if (inputDate > today) {
            showError(field, 'Tanggal tidak boleh di masa depan');
            return false;
        }
    }
    
    return true;
}

function showError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.parentNode.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    });
});

// Password confirmation validation
function validatePasswordConfirmation(passwordField, confirmField) {
    if (passwordField.value !== confirmField.value) {
        showError(confirmField, 'Konfirmasi password tidak sesuai');
        return false;
    }
    return true;
}