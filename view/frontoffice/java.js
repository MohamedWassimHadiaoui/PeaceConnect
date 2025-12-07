// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});


// Password Strength Checker
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];

    // Length check
    if (password.length >= 8) {
        strength++;
    } else {
        feedback.push("at least 8 characters");
    }

    // Uppercase letter check
    if (/[A-Z]/.test(password)) {
        strength++;
    } else {
        feedback.push("uppercase letter");
    }

    // Lowercase letter check
    if (/[a-z]/.test(password)) {
        strength++;
    } else {
        feedback.push("lowercase letter");
    }

    // Number check
    if (/[0-9]/.test(password)) {
        strength++;
    } else {
        feedback.push("number");
    }

    // Special character check
    if (/[^A-Za-z0-9]/.test(password)) {
        strength++;
    } else {
        feedback.push("special character");
    }

    return { strength, feedback };
}

function updatePasswordStrengthIndicator() {
    const passwordInput = document.getElementById("password");
    if (!passwordInput) return;

    const password = passwordInput.value;
    const segment1 = document.getElementById("strength-segment-1");
    const segment2 = document.getElementById("strength-segment-2");
    const segment3 = document.getElementById("strength-segment-3");
    const strengthText = document.getElementById("password-strength-text");

    if (!segment1 || !segment2 || !segment3 || !strengthText) return;

    // Reset all segments
    segment1.className = "strength-segment";
    segment2.className = "strength-segment";
    segment3.className = "strength-segment";

    if (password.length === 0) {
        strengthText.textContent = "";
        return;
    }

    const { strength, feedback } = checkPasswordStrength(password);

    if (strength <= 2) {
        // Weak - Red
        segment1.className = "strength-segment weak";
        strengthText.textContent = "Weak password. Add: " + feedback.slice(0, 2).join(", ");
        strengthText.style.color = "#f44336";
    } else if (strength === 3) {
        // Medium - Yellow
        segment1.className = "strength-segment weak";
        segment2.className = "strength-segment medium";
        strengthText.textContent = "Medium password. Add: " + feedback.slice(0, 1).join(", ");
        strengthText.style.color = "#ff9800";
    } else {
        // Strong - Green
        segment1.className = "strength-segment weak";
        segment2.className = "strength-segment medium";
        segment3.className = "strength-segment strong";
        strengthText.textContent = "Strong password!";
        strengthText.style.color = "#4caf50";
    }
}

// Initialize password strength indicator on page load
document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById("password");
    if (passwordInput) {
        passwordInput.addEventListener("input", updatePasswordStrengthIndicator);
        passwordInput.addEventListener("keyup", updatePasswordStrengthIndicator);
    }
});

function saisie() {
    let name = document.getElementById("name").value.trim();
    let lastname = document.getElementById("lastname").value.trim();
    let email = document.getElementById("email").value.trim();
    let pass1 = document.getElementById("password").value;
    let pass2 = document.getElementById("password2").value;
    let cin = document.getElementById("cin").value.trim();
    let tel = document.getElementById("tel").value.trim();
    let gender = document.querySelector('input[name="gender"]:checked');
    let captcha = document.getElementById("captcha");

    let errorBox = document.getElementById("errorBox");
    errorBox.innerHTML = "";

    if (name === "" || lastname === "") {
        errorBox.innerHTML = "Name and last name cannot be empty.";
        return false;
    }
    if (!/^[A-Z][a-zA-Z]*$/.test(name)) {
        errorBox.innerHTML = "Name must start with a capital letter and contain only letters.";
        return false;
    }
    if (!/^[A-Z][a-zA-Z]*$/.test(lastname)) {
        errorBox.innerHTML = "Lastname must start with a capital letter and contain only letters.";
        return false;
    }

    let emailFormat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailFormat.test(email)) {
        errorBox.innerHTML = "Email format is invalid.";
        return false;
    }

    // Enhanced password validation with complexity requirements
    const passwordStrength = checkPasswordStrength(pass1);
    if (pass1.length < 8) {
        errorBox.innerHTML = "Password must be at least 8 characters long.";
        return false;
    }
    if (passwordStrength.strength < 3) {
        errorBox.innerHTML = "Password is too weak. Please include uppercase, lowercase, numbers, and special characters.";
        return false;
    }
    if (pass1 !== pass2) {
        errorBox.innerHTML = "Passwords do not match.";
        return false;
    }

    if (!gender) {
        errorBox.innerHTML = "Please select your gender.";
        return false;
    }

    if (!/^\d{8}$/.test(cin)) {
        errorBox.innerHTML = "CIN must contain exactly 8 digits.";
        return false;
    }
    if (!/^\d{8}$/.test(tel)) {
        errorBox.innerHTML = "Telephone must contain exactly 8 digits.";
        return false;
    }

    // CAPTCHA validation (basic check - server will verify the actual answer)
    if (captcha && (!captcha.value || isNaN(captcha.value) || parseInt(captcha.value) < 0)) {
        errorBox.innerHTML = "Please solve the CAPTCHA math problem correctly.";
        return false;
    }

    return true;
}

