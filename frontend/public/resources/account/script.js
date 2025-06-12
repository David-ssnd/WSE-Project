let recipesCreated = [];
let recipesSaved = [];
let recipes = [];

// Fetch created recipes from cookie-authenticated route
async function fetchCreatedRecipes() {
    const response = await fetch("http://localhost:8081/api/recipes/created", {
        credentials: 'include'
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

async function isRecipeFavorited(recipeId) {
    try {
        const res = await fetch("http://localhost:8081/api/profile/favorites", {
            credentials: 'include'
        });
        if (!res.ok) return false;
        const favorites = await res.json();
        return favorites.some(fav => fav.id === recipeId);
    } catch {
        return false;
    }
}

async function toggleFavorite(recipeId, isFavoritedNow) {
    const method = isFavoritedNow ? 'DELETE' : 'POST';
    try {
        const res = await fetch(`http://localhost:8081/api/recipes/${recipeId}/favorite`, {
            method,
            credentials: 'include'
        });
        if (!res.ok) {
            const errData = await res.json();
            alert("Failed to update favorite: " + (errData.error || "Unknown error"));
            return false;
        }
        return true;
    } catch (error) {
        console.error("Network error:", error);
        return false;
    }
}

async function displayFoodModal(recipe) {
    const foodModal = document.getElementById("foodModal");
    const foodTitle = document.getElementById("foodTitle");
    const foodImage = document.getElementById("foodImage");
    const foodIngredients = document.getElementById("foodIngredients");
    const favoriteBtnIcon = document.querySelector(".favoriteBtn i");
    const stepByStepBtn = document.querySelector(".instructionsBtn");

    let isFavorited = await isRecipeFavorited(recipe.id);

    function updateFavoriteIcon() {
        favoriteBtnIcon.classList.toggle("fas", isFavorited);
        favoriteBtnIcon.classList.toggle("far", !isFavorited);
    }
    updateFavoriteIcon();

    foodTitle.textContent = recipe.title;
    foodImage.src = recipe.thumbnail_image || "../resources/noimage.png";
    foodIngredients.textContent = recipe.description || "No description available";

    document.querySelector(".favoriteBtn").onclick = async () => {
        const success = await toggleFavorite(recipe.id, isFavorited);
        if (!success) return;

        isFavorited = !isFavorited;
        updateFavoriteIcon();

        if (!isFavorited) {
            const index = recipesSaved.findIndex(r => r.id === recipe.id);
            if (index !== -1) recipesSaved.splice(index, 1);
            if (recipes === recipesSaved) {
                displayRecipes();
                foodModal.style.display = "none";
            }
        } else {
            if (!recipesSaved.some(r => r.id === recipe.id)) {
                recipesSaved.push(recipe);
            }
            if (recipes === recipesSaved) {
                displayRecipes();
            }
        }
    };

    stepByStepBtn.onclick = () => {
        window.location.href = `/recipe-page/?id=${recipe.id}`;
    };

    foodModal.style.display = "flex";
}

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

function changeRecipes(type) {
    recipes = (type === 1) ? recipesCreated : recipesSaved;
    displayRecipes();
}

function setupModalClose() {
    const foodModal = document.getElementById("foodModal");
    window.addEventListener("click", (event) => {
        if (event.target === foodModal) {
            foodModal.style.display = "none";
        }
    });
}

async function uploadProfilePicture(file) {
    const formData = new FormData();
    formData.append('profile_picture', file);

    try {
        const response = await fetch('http://localhost:8081/api/profile', {
            method: 'PATCH',
            body: formData,
            credentials: 'include'
        });

        const result = await response.json();
        if (!response.ok) {
            console.error("Failed to upload avatar:", result.error);
        } else {
            console.log("Avatar updated");
        }
    } catch (error) {
        console.error("Upload failed:", error);
    }
}

function previewImage(event) {
    const input = event.target;
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = async function (e) {
        const base64Image = e.target.result;

        const response = await fetch('http://localhost:8081/api/profile', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                profile_picture: base64Image
            })
        });

        const result = await response.json();
        if (response.ok) {
            // Update avatar image src immediately with the new image
            const avatarImg = document.querySelector(".avatar-img");
            avatarImg.src = base64Image;
        } else {
            console.error("Failed to update avatar:", result.error);
        }
    };

    reader.readAsDataURL(file);
}

document.addEventListener("DOMContentLoaded", async () => {

    document.querySelector("#profile-picture-input")?.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;
    
        const previewUrl = URL.createObjectURL(file);
        const avatarImg = document.querySelector(".avatar-img");
        avatarImg.src = previewUrl;
    });

    try {
        const searchInput = document.querySelector(".search-bar input");
        const clearIcon = document.querySelector(".clear-icon");
        clearIcon.addEventListener("click", () => searchInput.value = "");

        const user = await fetchUserProfile();
        updateUserProfileUI(user);

        const avatarImg = document.querySelector(".avatar-img");
        if (user.profile_picture) {
            avatarImg.src = user.profile_picture.startsWith("data:")
                ? user.profile_picture
                : "data:image/png;base64," + user.profile_picture;
        } else {
            avatarImg.src = "../resources/avatar.png";
        }

        const navbarProfileImg = document.getElementById("navbar-profile-img");
        if (navbarProfileImg) {
            if (user.profile_picture) {
                navbarProfileImg.src = user.profile_picture.startsWith("data:")
                    ? user.profile_picture
                    : "data:image/png;base64," + user.profile_picture;
            } else {
                navbarProfileImg.src = "../resources/avatar.png";
            }
        }

        try {
            recipesCreated = await fetchCreatedRecipes();
        } catch (err) {
            console.error("Failed to fetch created recipes:", err);
            recipesCreated = [];
        }

        try {
            recipesSaved = await fetchSavedRecipes();
        } catch (err) {
            console.warn("Saved recipes not available:", err);
            recipesSaved = [];
        }

        changeRecipes(1);
        setupModalClose();
    } catch (error) {
        console.error("Initialization error:", error);
    }
});

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
