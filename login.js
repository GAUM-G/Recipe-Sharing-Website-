document.getElementById("loginForm").onsubmit = function(event) {
    event.preventDefault(); // Default submit roko

    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "login.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            if (xhr.responseText.trim() === "success") {
                sessionStorage.setItem("loggedInUser", JSON.stringify({ id: username }));
                window.location.href = "homepage.html"; // Redirect to homepage
            } else {
                document.getElementById("passwordError").innerText = xhr.responseText; // Show error
            }
        }
    };

    xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
};