let recipesCreated = [];
let recipesSaved = [];
let recipes = [];

// Fetch created recipes from cookie-authenticated route
async function fetchCreatedRecipes() {
    const response = await fetch("http://localhost:8081/api/recipes/created", {
        credentials: 'include' // sends the token cookie
    });
    if (!response.ok) throw new Error("Failed to fetch created recipes.");
    return await response.json();
}

// Fetch saved recipes from cookie-authenticated route
async function fetchSavedRecipes() {
    const response = await fetch("http://localhost:8081/api/profile/favorites", {
        credentials: 'include'
    });
    if (!response.ok) throw new Error("Failed to fetch saved recipes.");
    return await response.json();
}

// Display modal
function displayFoodModal(recipe) {
    const foodModal = document.getElementById("foodModal");
    const foodTitle = document.getElementById("foodTitle");
    const foodImage = document.getElementById("foodImage");
    const foodIngredients = document.getElementById("foodIngredients");
    const stepByStepBtn = document.querySelector(".instructionsBtn");

    foodTitle.textContent = recipe.title;
    foodImage.src = recipe.thumbnail_image || "../resources/noimage.png";

    let ingredients = [];
    try {
        ingredients = typeof recipe.ingredients === 'string'
            ? JSON.parse(recipe.ingredients)
            : recipe.ingredients || [];
    } catch (e) {
        console.error("Failed to parse ingredients:", e);
    }

    const ingredientList = ingredients
        .map(i => `${i.amount} ${i.ingredient}`)
        .join(", ");
    foodIngredients.textContent = `Ingredients: ${ingredientList}`;

    // ✅ Link button dynamically using recipe ID
    stepByStepBtn.onclick = () => {
        window.location.href = `/recipe-page/?id=${recipe.id}`;
    };

    foodModal.style.display = "flex";
}


// Display recipe cards
function displayRecipes() {
    const feed = document.querySelector(".recipes-feed");
    feed.innerHTML = "";

    recipes.forEach(recipe => {
        const foodItem = document.createElement("div");
        foodItem.classList.add("food-item");

        const foodImage = document.createElement("img");
        foodImage.src = recipe.thumbnail_image || "../resources/noimage.png";
        foodImage.alt = recipe.title;

        const foodName = document.createElement("p");
        foodName.textContent = recipe.title;

        foodItem.appendChild(foodImage);
        foodItem.appendChild(foodName);

        foodItem.addEventListener("click", () => displayFoodModal(recipe));
        feed.appendChild(foodItem);
    });
}

// Handle recipe switch
function changeRecipes(type) {
    recipes = (type === 1) ? recipesCreated : recipesSaved;
    displayRecipes();
}

// Modal close behavior
function setupModalClose() {
    const foodModal = document.getElementById("foodModal");
    window.addEventListener("click", (event) => {
        if (event.target === foodModal) {
            foodModal.style.display = "none";
        }
    });
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", async () => {

    try {
        const user = await fetchUserProfile();
        updateUserProfileUI(user);
    } catch (err) {
        console.error("Error loading user profile:", err);
    }

    async function fetchUserProfile() {
        const response = await fetch("http://localhost:8081/api/profile", {
            credentials: 'include'
        });
    
        if (!response.ok) {
            throw new Error("Failed to fetch user profile");
        }
    
        return await response.json();
    }
    
    function updateUserProfileUI(user) {
        const profileNameEl = document.getElementById("profile-nickname");
        const profileIdEl = document.getElementById("profile-id");
    
        profileNameEl.textContent = user.username || "Unknown";
        profileIdEl.textContent = `@${user.username || "unknown"}`;
    }

    try {
        // Clear search input logic
        const searchInput = document.querySelector(".search-bar input");
        const clearIcon = document.querySelector(".clear-icon");
        clearIcon.addEventListener("click", () => searchInput.value = "");

        // Fetch created recipes
        try {
            recipesCreated = await fetchCreatedRecipes();
        } catch (err) {
            console.error("Failed to fetch created recipes:", err);
            recipesCreated = [];
        }

        // Fetch saved recipes
        try {
            recipesSaved = await fetchSavedRecipes(); // This might fail
        } catch (err) {
            console.warn("Saved recipes not available:", err);
            recipesSaved = [];
        }

        // Display created recipes by default
        changeRecipes(1);

        // Modal close behavior
        setupModalClose();
    } catch (error) {
        console.error("Initialization error:", error);
    }
});