document.getElementById("loginForm").addEventListener("submit", function(e) {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;

    // Local Browser Intervention Verification Popups
    if (email === "" || password === "") {
        e.preventDefault();
        alert("Inputs cannot be blank!");
    }
});
