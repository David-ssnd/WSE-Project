document.addEventListener("DOMContentLoaded", async () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");
    const profileImg = document.querySelector(".profile-icon img");

    fetch("http://localhost:8081/api/profile", { credentials: "include" })
    .then(res => {
        if (!res.ok) throw new Error("Profile fetch failed");
        return res.json();
    })
    .then(data => {
        if (data.profile_picture) {
        profileImg.src = data.profile_picture;
        }
    })
    .catch(console.error);

    if (clearIcon) {
        clearIcon.addEventListener("click", () => {
            searchInput.value = "";
        });
    }

    // --- Handle dynamic recipe loading ---
    const recipeId = getRecipeIdFromUrl();

    if (!recipeId) {
        alert("Recipe ID is missing from URL.");
        return;
    }

    try {
        const response = await fetch(`http://localhost:8081/api/recipes/${recipeId}`);
        const data = await response.json();

        if (data.error) {
            document.querySelector(".content-area").innerHTML = `<p>Error: ${data.error}</p>`;
        } else {
            console.log("Fetched recipe data:", data);
            populateRecipe(data.recipe || data); // adapt if `data` is the recipe object directly
        }
    } catch (error) {
        console.error("Failed to load recipe:", error);
        document.querySelector(".content-area").innerHTML = `<p>Failed to load recipe. Please try again later.</p>`;
    }
    // --- Star Rating UI ---
    const starContainer = document.getElementById('stars');
    const ratingInput = document.getElementById('rating');
    const maxStars = 5;

    for (let i = 1; i <= maxStars; i++) {
        const star = document.createElement('span');
        star.classList.add('star');
        star.textContent = '★';
        star.dataset.value = i;

        star.addEventListener('mouseover', () => updateStars(i));
        star.addEventListener('mouseout', () => updateStars(ratingInput.value));
        star.addEventListener('click', () => {
            ratingInput.value = i;
            updateStars(i);
        });

        starContainer.appendChild(star);
    }

    function updateStars(rating) {
        const stars = starContainer.querySelectorAll('.star');
        stars.forEach(star => {
            star.classList.remove('hover', 'selected');
            if (star.dataset.value <= rating) {
                star.classList.add(ratingInput.value == star.dataset.value ? 'selected' : 'hover');
            }
        });
    }

    function getRecipeIdFromUrl() {
        const params = new URLSearchParams(window.location.search);
        return params.get("id"); // URL format: ?id=RECIPE_UUID
    }

    function populateRecipe(recipe) {
        if (!recipe || typeof recipe !== "object") {
            console.error("Invalid recipe object:", recipe);
            document.querySelector(".content-area").innerHTML = "<p>Invalid recipe data.</p>";
            return;
        }
    
        // Title and image
        document.querySelector(".food-item.recipe-food-item p").textContent = recipe.title ?? "Untitled Recipe";
        document.querySelector(".food-item.recipe-food-item img").src = recipe.thumbnail_image ?? "../resources/default-food.jpg";
    
        // Ingredients list
        const ingredientsList = document.querySelector(".recipe-ingredients ul");
        ingredientsList.innerHTML = '';
        (recipe.ingredients || []).forEach(item => {
            const li = document.createElement("li");
            li.textContent = `${item.amount} ${item.ingredient}`;
            ingredientsList.appendChild(li);
        });
    
        // Recipe details
        document.getElementById("prep-time").textContent = recipe.prep_time ?? "N/A";
        document.getElementById("cook-time").textContent = recipe.cook_time ?? "N/A";
        document.getElementById("temperature").textContent = recipe.temperature ?? "N/A";
        document.getElementById("servings").textContent = recipe.servings ?? "N/A";
    
        // Steps
        const stepsContainer = document.querySelector(".recipe-steps");
        stepsContainer.innerHTML = '';
    
        const descriptions = recipe.step_descriptions || [];
        const images = recipe.step_images || [];
    
        for (let i = 0; i < Math.max(descriptions.length, images.length); i++) {
            const stepDiv = document.createElement("div");
            stepDiv.classList.add("step");
    
            const stepImage = document.createElement("img");
            stepImage.src = images[i] ?? "../resources/default-step.jpg";
            stepImage.alt = `Step ${i + 1}`;
            stepImage.classList.add("logo");
    
            const stepInfo = document.createElement("div");
            stepInfo.classList.add("step-info");
    
            const stepTitle = document.createElement("h3");
            stepTitle.textContent = `Step ${i + 1}`;
    
            const stepDescription = document.createElement("p");
            stepDescription.textContent = descriptions[i] ?? "No description provided.";
    
            stepInfo.appendChild(stepTitle);
            stepInfo.appendChild(stepDescription);
            stepDiv.appendChild(stepImage);
            stepDiv.appendChild(stepInfo);
            stepsContainer.appendChild(stepDiv);
        }
    
        // Ratings placeholder (your current data has none)
        const ratingsContainer = document.querySelector(".ratings");
        ratingsContainer.innerHTML = '<h2>Ratings</h2><p>No ratings yet.</p>';
    }
});
