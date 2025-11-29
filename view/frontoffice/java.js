function updateCountdown() {
const targetDate = new Date('2023-11-27T00:00:00');
    const now = new Date();
    const difference = targetDate - now;

    if (difference > 0) {
        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    } else {
        document.querySelector('.countdown').innerHTML = '<p>The sale has ended!</p>';
    }
}

setInterval(updateCountdown, 1000);
updateCountdown(); // Initial call

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

const nav = document.querySelector('nav ul');
const toggle = document.createElement('button');
toggle.textContent = 'Menu';
toggle.style.display = 'none';
toggle.addEventListener('click', () => {
    nav.classList.toggle('show');
});
document.querySelector('header .container').appendChild(toggle);

if (window.innerWidth <= 768) {
    toggle.style.display = 'block';
}
//bio
document.addEventListener("DOMContentLoaded", () => {
    const bioInput = document.getElementById("bio-input");
    const savedBio = localStorage.getItem("userBio");

    if (savedBio) {
        bioInput.value = savedBio;
    }

    document.getElementById("save-bio").addEventListener("click", () => {
        const newBio = bioInput.value.trim();
        localStorage.setItem("userBio", newBio);

        alert(" Your bio has been saved!");
    });
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


function submit() {
    let role = document.querySelector("input[name='role']:checked");
    let errorBox = document.getElementById("errorBox");

    if (!role) {
        errorBox.innerHTML = "Please select a role.";
        return;
    }

    errorBox.innerHTML = "";

    if (role.value === "admin") {
        window.location.href = "admin.html";
    } else {
        window.location.href = "form.html";
    }
}

