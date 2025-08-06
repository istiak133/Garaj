// Main JavaScript file for Car Workshop

// Form validation utilities
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^01[3-9]\d{8}$/;
    return re.test(phone);
}

// Show loading spinner
function showLoading(buttonId) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.innerHTML = '<div class="loading"></div> Processing...';
        button.disabled = true;
    }
}

// Hide loading spinner
function hideLoading(buttonId, originalText) {
    const button = document.getElementById(buttonId);
    if (button) {
        button.innerHTML = originalText;
        button.disabled = false;
    }
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});

// Form submission with loading state
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                const originalText = submitButton.innerHTML;
                showLoading(submitButton.id || 'submitBtn');
                
                // Restore button after form submission
                setTimeout(function() {
                    hideLoading(submitButton.id || 'submitBtn', originalText);
                }, 3000);
            }
        });
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Phone number formatting
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    input.value = value;
}

// Real-time form validation
function showFieldError(field, message) {
    hideFieldError(field); // Remove existing error
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function setupFormValidation() {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value && !validateEmail(this.value)) {
                this.style.borderColor = '#e74c3c';
                showFieldError(this, 'Please enter a valid email address');
            } else {
                this.style.borderColor = '#e1e1e1';
                hideFieldError(this);
            }
        });
    });
    
    // Phone validation
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            formatPhoneNumber(this);
        });
        
        input.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                this.style.borderColor = '#e74c3c';
                showFieldError(this, 'Please enter a valid phone number (01XXXXXXXXX)');
            } else {
                this.style.borderColor = '#e1e1e1';
                hideFieldError(this);
            }
        });
    });
}

// Initialize form validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupFormValidation();
});

// Utility function for AJAX requests
function makeAjaxRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            callback(null, xhr.responseText);
        } else {
            callback(new Error(`Request failed with status ${xhr.status}`), null);
        }
    };
    
    xhr.onerror = function() {
        callback(new Error('Network error occurred'), null);
    };
    
    xhr.send(data);
}