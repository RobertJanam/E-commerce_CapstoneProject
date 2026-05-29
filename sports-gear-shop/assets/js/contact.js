document.getElementById("contactForm").addEventListener("submit", function(e) {
    let name = document.getElementById("fullname").value.trim();
    let email = document.getElementById("email").value.trim();
    let message = document.getElementById("message").value.trim();

    if (name === "" || email === "" || message === "") {
        e.preventDefault();
        alert("Attention: All core contact form components must be completed!");
    }
});