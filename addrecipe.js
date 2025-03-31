document.getElementById("recipeForm").addEventListener("submit", function (event) {
    event.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("addrecipe.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        window.location.href = "homepage.html";
    })
    .catch(error => console.error("Error:", error));
});