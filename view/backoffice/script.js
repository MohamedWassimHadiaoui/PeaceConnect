
function saisie() {
    let name = document.getElementById("name").value.trim();
    let lastname = document.getElementById("lastname").value.trim();
    let email = document.getElementById("email").value.trim();
    let pass1 = document.getElementById("password").value;
    let pass2 = document.getElementById("password2").value;
    let cin = document.getElementById("cin").value.trim();
    let tel = document.getElementById("tel").value.trim();
    let gender = document.querySelector('input[name="gender"]:checked');

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

 
    if (pass1.length < 8) {
        errorBox.innerHTML = "Password must be at least 8 characters long.";
        return false;
    }
    if (!/^[A-Za-z0-9]+$/.test(pass1)) {
        errorBox.innerHTML = "Password must not contain special characters.";
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

    return true;
}
