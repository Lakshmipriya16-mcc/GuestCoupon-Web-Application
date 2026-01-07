// Utility function to show error
function showError(element, message) {
    element.textContent = message;
    element.style.display = 'block';
}

// Utility function to hide error
function hideError(element) {
    element.style.display = 'none';
}

// Validate field with length constraints
function validateField(field, errorField, minLength, maxLength, fieldName) {
    const value = field.value.trim();
    if (value.length === 0) {
        showError(errorField, `${fieldName} cannot be empty.`);
        return false;
    } else if (value.length < minLength || value.length > maxLength) {
        showError(errorField, `${fieldName} must be between ${minLength} and ${maxLength} characters.`);
        return false;
    }
    hideError(errorField);
    return true;
}

// Validate contact number specifically
function validateContactNumber() {
    const contactNumberField = document.getElementById('contactnumber');
    const contactNumberError = document.getElementById('contactNumberError');
    const value = contactNumberField.value.trim();
    const phoneRegex = /^\d{10}$/;
    
    if (value.length === 0) {
        showError(contactNumberError, 'Contact Number cannot be empty.');
        return false;
    } else if (!phoneRegex.test(value)) {
        showError(contactNumberError, 'Contact Number must be 10 digits.');
        return false;
    }
    hideError(contactNumberError);
    return true;
}

// Validate email format
function validateEmail() {
    const emailField = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const value = emailField.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (value.length === 0) {
        showError(emailError, 'Email cannot be empty.');
        return false;
    } else if (!emailRegex.test(value)) {
        showError(emailError, 'Please enter a valid email address.');
        return false;
    }
    hideError(emailError);
    return true;
}

// Validate password strength
function validatePassword() {
    const passwordField = document.getElementById('password');
    const passwordError = document.getElementById('passwordError');
    const value = passwordField.value.trim();
    
    // Regex for at least 6 characters, including uppercase, lowercase, number, and special character
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,20}$/;

    if (value.length === 0) {
        showError(passwordError, 'Password cannot be empty.');
        return false;
    } else if (!passwordRegex.test(value)) {
        showError(passwordError, 'Password must be 6-20 characters long and include uppercase, lowercase, number, and special character.');
        return false;
    }
    hideError(passwordError);
    return true;
}

// Validate the form before submission
function validateForm() {
    let valid = true;

    // Validate each field
    valid &= validateField(document.getElementById('name'), document.getElementById('nameError'), 1, 50, 'Full Name');
    valid &= validateField(document.getElementById('employee_id'), document.getElementById('employeeIdError'), 1, 15, 'Employee ID');
    valid &= validateField(document.getElementById('designation'), document.getElementById('designationError'), 1, 50, 'Designation');
    valid &= validateField(document.getElementById('departmentdivision'), document.getElementById('departmentDivisionError'), 1, 50, 'Department/Division');
    valid &= validatePassword(); // Validate password
    valid &= validateField(document.getElementById('costcenterno'), document.getElementById('costCenterNoError'), 1, 15, 'Cost Center No');
    valid &= validateEmail(); // Validate email separately
    valid &= validateContactNumber();

    return valid;
}

// Display file name only when a file is selected
function displayFileName() {
    const fileInput = document.getElementById('profilePhoto');
    const fileName = document.getElementById('fileName');
    
    fileName.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file chosen';
}

// Form submission handling
document.getElementById('registration-form').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault(); // Prevent form submission if validation fails
    } else {
        // Show success message and redirect
        alert("Signup Successful!"); // Replace with UI notification if desired
        window.location.href = 'login.html'; // Redirect to login page
    }
});

// Event listener for file input change
document.getElementById('profilePhoto').addEventListener('change', displayFileName);