document.addEventListener("DOMContentLoaded", function () {
    function updateAuthButtons() {
        const user = JSON.parse(sessionStorage.getItem("loggedInUser"));
        const authButtons = document.getElementById("authButtons");
        const sideMenu = document.getElementById("sideMenu");
        const addRecipeMenu = document.getElementById("addRecipeMenu");

        if (user) {
            authButtons.innerHTML = `
                <span class="user-display">${user.id}</span> 
                <button id="logoutBtn">Logout</button>
            `;

            document.getElementById("logoutBtn").addEventListener("click", function () {
                sessionStorage.removeItem("loggedInUser"); 
                window.location.href = "logout.php"; 
            });

            if (addRecipeMenu) {
                addRecipeMenu.style.display = "block"; 
            } else {
                const addRecipeLink = document.createElement("a");
                addRecipeLink.href = "addrecipe.html";
                addRecipeLink.id = "addRecipeMenu";
                addRecipeLink.textContent = "Add Recipe";
                sideMenu.appendChild(addRecipeLink);
            }
        } else {
            authButtons.innerHTML = `
                <button onclick="window.location.href='login.html'">Login</button>
                <button onclick="window.location.href='signup.html'">Signup</button>
            `;
            
            if (addRecipeMenu) {
                addRecipeMenu.style.display = "none";
            }
        }
    }

    function toggleMenu() {
        document.getElementById("sideMenu").classList.toggle("open");
    }

    function createRecipeCarousel(imagePath, videoPath, title) {
        return `
            <div class="carousel">
                <div class="carousel-container">
                    ${imagePath ? `
                    <div class="carousel-slide active">
                        <img src="${imagePath}" alt="${title}" class="carousel-img" onerror="this.onerror=null; this.src='default.jpg';">
                    </div>` : ""}

                    ${videoPath ? `
                    <div class="carousel-slide">
                        <video controls class="carousel-video" onerror="this.onerror=null; this.outerHTML='<p>Video not available</p>';">
                            <source src="${videoPath}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>` : ""}
                </div>

                ${(imagePath && videoPath) ? `
                <button class="prev">❮</button>
                <button class="next">❯</button>
                ` : ""}
            </div>
        `;
    }

    function searchRecipes() {
        const searchInput = document.querySelector(".search-bar").value.trim();
        const recipeList = document.getElementById("recipeList");

        if (searchInput.length === 0) {
            recipeList.innerHTML = "<p>Please enter a search term.</p>"; 
            return;
        }

        fetch(`search.php?query=${encodeURIComponent(searchInput)}`)
            .then(response => response.json())
            .then(data => {
                recipeList.innerHTML = "";

                if (data.length === 0) {
                    recipeList.innerHTML = "<p>No recipes found.</p>";
                    return;
                }

                data.forEach(recipe => {
                    const imagePath = recipe.image ? (recipe.image.startsWith("uploads/") ? recipe.image : `uploads/${recipe.image}`) : null;
                    const videoPath = recipe.video ? (recipe.video.startsWith("uploads/") ? recipe.video : `uploads/${recipe.video}`) : null;

                    console.log("Image Path:", imagePath);
                    console.log("Video Path:", videoPath);

                    const recipeItem = document.createElement("div");
                    recipeItem.classList.add("recipe-item");
                    recipeItem.innerHTML = `
                        <h2 class="recipe-title">${recipe.title}</h2>
                        ${createRecipeCarousel(imagePath, videoPath, recipe.title)}
                        <p><strong>Steps:</strong> ${recipe.steps}</p>
                        <p><strong>Benefits:</strong> ${recipe.benefits}</p>
                        <p><strong>Ingredients:</strong> ${recipe.contents || "Not provided"}</p>
                        <p class="uploaded-by"><strong>Uploaded by:</strong> ${recipe.uploaded_by || "Unknown"}</p>
                    `;
                    recipeList.appendChild(recipeItem);
                });

                document.querySelectorAll(".carousel").forEach(carousel => setupCarousel(carousel));
            })
            .catch(error => {
                console.error("Error fetching recipes:", error);
                recipeList.innerHTML = "<p>Error fetching recipes. Check console for details.</p>";
            });
    }

    function setupCarousel(carousel) {
        let currentIndex = 0;
        const slides = carousel.querySelectorAll(".carousel-slide");
        const totalSlides = slides.length;
        const prevButton = carousel.querySelector(".prev");
        const nextButton = carousel.querySelector(".next");

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? "block" : "none";
            });
        }

        if (totalSlides > 1) {
            prevButton.addEventListener("click", () => {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                showSlide(currentIndex);
            });

            nextButton.addEventListener("click", () => {
                currentIndex = (currentIndex + 1) % totalSlides;
                showSlide(currentIndex);
            });
        }

        showSlide(currentIndex);
    }

    // **Updated Recipe Fetching Code**
    function fetchRecipes(category = "") {
        let url = "fetch_random_recipes.php";
        if (category) {
            url += `?category=${encodeURIComponent(category)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                let container = document.getElementById('recipe-container');
                container.innerHTML = ""; // Purana data clear karo

                data.forEach(recipe => {
                    let recipeDiv = document.createElement('div');
                    recipeDiv.className = "recipe";

                    recipeDiv.innerHTML = `
                        <h2>${recipe.title}</h2>
                        <img src="${recipe.image}" alt="${recipe.title}" width="100%">
                        <p><strong>Steps:</strong> ${recipe.steps}</p>
                        <p><strong>Benefits:</strong> ${recipe.benefits}</p>
                        <p><strong>Contents:</strong> ${recipe.contents}</p>
                    `;

                    container.appendChild(recipeDiv);
                });
            })
            .catch(error => console.error('Error fetching recipes:', error));
    }

    // **Fetch Default 5 Random Recipes on Page Load**
    fetchRecipes();

    // **Fetch Category-Specific Recipes When a Category is Clicked**
    document.querySelectorAll(".category-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            let selectedCategory = this.getAttribute("data-category");
            fetchRecipes(selectedCategory);
        });
    });

    document.querySelector(".search-button").addEventListener("click", searchRecipes);
    document.querySelector(".search-bar").addEventListener("keyup", function (event) {
        if (event.key === "Enter") {
            searchRecipes();
        }
    });

    updateAuthButtons();
    window.toggleMenu = toggleMenu;
});
