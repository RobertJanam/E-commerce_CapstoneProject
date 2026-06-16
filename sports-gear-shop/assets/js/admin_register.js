document.getElementById("regForm").addEventListener("submit", function(e) {
    let fullname = document.getElementById("fullname").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirm_password").value;

    if (fullname === "" || email === "" || password === "") {
        e.preventDefault();
        alert("Inputs cannot be empty!");
        return;
    }
    if (password.length < 6) {
        e.preventDefault();
        alert("Password must be a minimum of 6 characters!");
        return;
    }
    if (password !== confirmPassword) {
        e.preventDefault();
        alert("Confirmation password must be the same as the original password!");
        return;
    }
});
