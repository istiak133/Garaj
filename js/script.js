// Main JavaScript file for the car workshop website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initUserMenu();
    initServiceCards();
    initMechanicBooking();
    initFormValidation();
});

// User menu dropdown functionality
function initUserMenu() {
    const userMenu = document.querySelector('.user-menu');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (userMenu && dropdownMenu) {
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Service cards interaction
function initServiceCards() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            const serviceType = this.dataset.service;
            if (serviceType) {
                window.location.href = `service.php?type=${encodeURIComponent(serviceType)}`;
            }
        });
        
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Mechanic booking functionality
function initMechanicBooking() {
    const bookButtons = document.querySelectorAll('.book-mechanic');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const mechanicId = this.dataset.mechanicId;
            const serviceType = this.dataset.serviceType;
            
            // Check if user is logged in
            checkLoginStatus().then(isLoggedIn => {
                if (isLoggedIn) {
                    // Redirect to booking page
                    window.location.href = `booking.php?mechanic=${mechanicId}&service=${encodeURIComponent(serviceType)}`;
                } else {
                    // Show login prompt
                    showLoginPrompt();
                }
            });
        });
    });
}

// Check if user is logged in
async function checkLoginStatus() {
    try {
        const response = await fetch('api/check_login.php');
        const data = await response.json();
        return data.isLoggedIn;
    } catch (error) {
        console.error('Error checking login status:', error);
        return false;
    }
}

// Show login prompt modal
function showLoginPrompt() {
    const modal = document.createElement('div');
    modal.className = 'login-modal';
    modal.innerHTML = `
        <div class="login-modal-content">
            <div class="login-modal-header">
                <h3>Login Required</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="login-modal-body">
                <p>Please log in to book an appointment with our mechanics.</p>
                <div class="login-modal-actions">
                    <a href="login.php" class="btn">Login</a>
                    <a href="signup.php" class="btn btn-outline">Sign Up</a>
                </div>
            </div>
        </div>
    `;
    
    // Add modal styles
    const modalStyles = `
        .login-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }
        
        .login-modal-content {
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        
        .login-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .login-modal-header h3 {
            color: #ffffff;
            margin: 0;
        }
        
        .close-modal {
            background: none;
            border: none;
            color: #b0b0b0;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .close-modal:hover {
            color: #00d4ff;
        }
        
        .login-modal-body p {
            color: #b0b0b0;
            margin-bottom: 1.5rem;
        }
        
        .login-modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    
    // Add styles to head if not already added
    if (!document.querySelector('#modal-styles')) {
        const styleElement = document.createElement('style');
        styleElement.id = 'modal-styles';
        styleElement.textContent = modalStyles;
        document.head.appendChild(styleElement);
    }
    
    document.body.appendChild(modal);
    
    // Handle close modal
    const closeBtn = modal.querySelector('.close-modal');
    closeBtn.addEventListener('click', () => {
        modal.remove();
    });
    
    // Close on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Form validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

// Validate entire form
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Validate individual field
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name || field.id;
    let isValid = true;
    let errorMessage = '';
    
    // Clear previous errors
    clearFieldError(field);
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        errorMessage = `${getFieldLabel(field)} is required`;
        isValid = false;
    }
    
    // Email validation
    else if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            errorMessage = 'Please enter a valid email address';
            isValid = false;
        }
    }
    
    // Phone validation
    else if (fieldName === 'phone' && value) {
        const phoneRegex = /^01[3-9]\d{8}$/;
        if (!phoneRegex.test(value)) {
            errorMessage = 'Please enter a valid Bangladeshi phone number';
            isValid = false;
        }
    }
    
    // Password validation
    else if (field.type === 'password' && value) {
        if (value.length < 6) {
            errorMessage = 'Password must be at least 6 characters long';
            isValid = false;
        }
    }
    
    // Car license validation
    else if (fieldName === 'car_license_number' && value) {
        const licenseRegex = /^[A-Z]{2,3}-\d{4}$/;
        if (!licenseRegex.test(value)) {
            errorMessage = 'License format: DHA-1234';
            isValid = false;
        }
    }
    
    // Car engine validation
    else if (fieldName === 'car_engine_number' && value) {
        const engineRegex = /^[A-Z]{3}\d{6}$/;
        if (!engineRegex.test(value)) {
            errorMessage = 'Engine format: ENG123456';
            isValid = false;
        }
    }
    
    // Date validation (not in the past)
    else if (field.type === 'date' && value) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            errorMessage = 'Please select a future date';
            isValid = false;
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

// Show field error
function showFieldError(field, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.cssText = `
        color: #f87171;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    `;
    
    field.style.borderColor = '#f87171';
    
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        formGroup.appendChild(errorElement);
    }
}

// Clear field error
function clearFieldError(field) {
    field.style.borderColor = '';
    
    const formGroup = field.closest('.form-group');
    if (formGroup) {
        const errorElement = formGroup.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

// Get field label for error messages
function getFieldLabel(field) {
    const label = field.closest('.form-group')?.querySelector('label');
    if (label) {
        return label.textContent.replace(':', '');
    }
    
    return field.name || field.id || 'Field';
}

// Utility functions

// Show loading state
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="loading"></span> Loading...';
    element.disabled = true;
    
    return function hideLoading() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

// Show alert message
function showAlert(message, type = 'success') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }, 5000);
}

// Add slide animations
const slideAnimations = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;

// Add animations to head if not already added
if (!document.querySelector('#slide-animations')) {
    const styleElement = document.createElement('style');
    styleElement.id = 'slide-animations';
    styleElement.textContent = slideAnimations;
    document.head.appendChild(styleElement);
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Get next available date for a mechanic
async function getNextAvailableDate(mechanicId) {
    try {
        const response = await fetch(`api/get_next_available.php?mechanic_id=${mechanicId}`);
        const data = await response.json();
        return data.next_date;
    } catch (error) {
        console.error('Error getting next available date:', error);
        return null;
    }
}

// Smooth scroll to element
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Handle service type selection
function selectService(serviceType) {
    // Store selected service in sessionStorage
    sessionStorage.setItem('selectedService', serviceType);
    
    // Redirect to service page
    window.location.href = `service.php?type=${encodeURIComponent(serviceType)}`;
}

// Auto-hide alerts after showing
function autoHideAlert() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
}

// Initialize auto-hide for existing alerts
document.addEventListener('DOMContentLoaded', autoHideAlert);