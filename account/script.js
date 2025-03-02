var recipesCreated;
var recipesSaved;
var recipes;

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");
    
    clearIcon.addEventListener("click", () => {
        searchInput.value = "";
    });

    fetch('created.json')
        .then(response => response.json())
        .then(data => {
            recipesCreated = data;
            changeRecipes(1);
        })
        .catch(error => console.error('Error loading created recipes:', error));

    fetch('saved.json')
        .then(response => response.json())
        .then(data => {
            recipesSaved = data;
        })
        .catch(error => console.error('Error loading saved recipes:', error));
});

function displayRecipes() {
    const feed = document.querySelector(".recipes-feed");
    feed.innerHTML = "";

    recipes.forEach(recipe => {
        const foodItem = document.createElement("div");
        foodItem.classList.add("food-item");

        const foodImage = document.createElement("img");
        foodImage.src = recipe.foto;
        foodImage.alt = recipe.nazov;

        const foodName = document.createElement("p");
        foodName.textContent = recipe.nazov;
        
        const prepTime = document.createElement("p");
        prepTime.classList.add("prep-time");
        const clockIcon = document.createElement("img");
        clockIcon.src = "../resources/prepTime-clock.svg";
        clockIcon.alt = "Clock Icon";

        prepTime.textContent = recipe.cas;
        prepTime.prepend(clockIcon);

        foodItem.appendChild(foodImage);
        foodItem.appendChild(foodName);
        foodItem.appendChild(prepTime);

        feed.appendChild(foodItem);

        foodItem.addEventListener("click", function() {
            displayFoodModal(recipe);
        });
    });
}

function changeRecipes(number) {
    recipes = number === 1 ? recipesCreated : recipesSaved;
    displayRecipes();
    updateDeleteButtonVisibility();
}

function displayFoodModal(recipe) {
    const foodModal = document.getElementById("foodModal");
    const foodTitle = document.getElementById("foodTitle");
    const foodImage = document.getElementById("foodImage");
    const foodIngredients = document.getElementById("foodIngredients");

    foodTitle.textContent = recipe.nazov;
    foodImage.src = recipe.foto;
    foodIngredients.textContent = `Ingredients: ${recipe.ingrediencie.join(", ")}`;

    foodModal.style.display = "flex";
    updateDeleteButtonVisibility();
}

// Zatvorenie modalu kliknutím mimo neho
window.addEventListener('click', (event) => {
    const foodModal = document.getElementById("foodModal");
    if (event.target === foodModal) {
        foodModal.style.display = 'none';
    }
});

// Logika na zobrazenie alebo skrytie tlačidla odstránenia
function shouldShowDeleteButton() {
    return JSON.stringify(recipes) !== JSON.stringify(recipesSaved);
}

function updateDeleteButtonVisibility() {
    const deleteButton = document.querySelector(".deleteBtn");
    if (deleteButton) {
        deleteButton.style.display = shouldShowDeleteButton() ? "block" : "none";
    }
}

// Funkcia na odstránenie receptu
function deleteRecipe() {
    if (confirm("Are you sure you want to delete this recipe?")) {
        alert("Recipe deleted!");
        // Tu môžeš doplniť kód na skutočné odstránenie receptu
    }
}

// Zmena profilového obrázka
function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        document.querySelector('.avatar-img').src = e.target.result;
        document.querySelector('.profile-img').src = e.target.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

// Dropdown menu pre profil
function toggleDropdown() {
    const dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

// Zatvorenie dropdown menu kliknutím mimo
document.addEventListener("click", function(event) {
    const dropdown = document.getElementById("profileDropdown");
    const profileIcon = document.querySelector(".profile-icon");
    if (!profileIcon.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.style.display = "none";
    }
});
