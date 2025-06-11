//search bar - clear icon
const searchInput = document.querySelector(".search-bar input");
const clearIcon = document.querySelector(".clear-icon");

clearIcon.addEventListener("click", () => {
    searchInput.value = "";
    displayRecipes(); // reset to all recipes
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

document.addEventListener("DOMContentLoaded", () => {
    const token = getCookie("token");

    if (token) {
        document.querySelector(".auth-buttons").style.display = "none";
        document.getElementById("profileIcon").style.display = "block";
    } else {
        document.querySelector(".auth-buttons").style.display = "flex";
        document.getElementById("profileIcon").style.display = "none";
    }

    document.querySelectorAll('.ingredient input').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Zá-žÁ-Ž ]/g, '');
        });
    });

    document.querySelectorAll('.amount input').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    let recipes = [];

    fetch('http://localhost:8081/api/recipes')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok ' + response);
            return response.json();
        })
        .then(data => {
            recipes = data;
            displayRecipes();
        })
        .catch(error => {
            console.error('There has been a problem with fetch operation:', error);
        });

    function displayRecipes() {
        const feed = document.querySelector(".recipes-feed");
        feed.innerHTML = "";

        recipes.forEach(recipe => {
            const foodItem = document.createElement("div");
            foodItem.classList.add("food-item");

            const foodImage = document.createElement("img");
            foodImage.src = recipe.thumbnail_image;
            foodImage.alt = recipe.title;

            const foodName = document.createElement("p");
            foodName.textContent = recipe.title;

            foodItem.appendChild(foodImage);
            foodItem.appendChild(foodName);

            feed.appendChild(foodItem);

            foodItem.addEventListener("click", function () {
                displayFoodModal(recipe);
            });
        });
    }

    function displayFilteredRecipes(filtered) {
        const feed = document.querySelector(".recipes-feed");
        feed.innerHTML = "";

        filtered.forEach(recipe => {
            const foodItem = document.createElement("div");
            foodItem.classList.add("food-item");

            const foodImage = document.createElement("img");
            foodImage.src = recipe.thumbnail_image;
            foodImage.alt = recipe.title;

            const foodName = document.createElement("p");
            foodName.textContent = recipe.title;

            foodItem.appendChild(foodImage);
            foodItem.appendChild(foodName);

            feed.appendChild(foodItem);

            foodItem.addEventListener("click", function () {
                displayFoodModal(recipe);
            });
        });
    }

    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    searchInput.addEventListener("input", debounce(async function () {
        const query = this.value.trim();

        if (!query) {
            displayRecipes();
            return;
        }

        try {
            const res = await fetch(`http://localhost:8081/api/search/recipes?query=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error("Failed to fetch search results");

            const filteredRecipes = await res.json();
            displayFilteredRecipes(filteredRecipes);
        } catch (err) {
            console.error("Search error:", err);
        }
    }, 300));

    async function isRecipeFavorited(recipeId) {
        try {
            const response = await fetch("http://localhost:8081/api/profile/favorites", {
                credentials: 'include'
            });
            if (!response.ok) return false;

            const favorites = await response.json();
            return favorites.some(fav => fav.id === recipeId);
        } catch {
            return false;
        }
    }

    async function toggleFavorite(recipeId, isFavoritedNow) {
        const method = isFavoritedNow ? 'DELETE' : 'POST';

        try {
            const response = await fetch(`http://localhost:8081/api/recipes/${recipeId}/favorite`, {
                method,
                credentials: 'include'
            });

            if (!response.ok) {
                const errData = await response.json();
                alert("Failed to update favorite: " + (errData.error || "Unknown error"));
                return false;
            }
            alert("success");
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
        const favoriteBtn = document.querySelector(".favoriteBtn i");
        const stepByStepBtn = document.querySelector(".instructionsBtn");

        let isFavorited = await isRecipeFavorited(recipe.id);
        updateFavoriteIcon();

        foodTitle.textContent = recipe.title;
        foodImage.src = recipe.thumbnail_image;
        foodIngredients.textContent = recipe.description;

        function updateFavoriteIcon() {
            favoriteBtn.classList.toggle("fas", isFavorited);
            favoriteBtn.classList.toggle("far", !isFavorited);
        }

        document.querySelector(".favoriteBtn").onclick = async () => {
            const success = await toggleFavorite(recipe.id, isFavorited);
            if (success) {
                isFavorited = !isFavorited;
                updateFavoriteIcon();
            }
        };

        stepByStepBtn.onclick = () => {
            window.location.href = `/recipe-page/?id=${recipe.id}`;
        };

        foodModal.style.display = "flex";
    }

    window.addEventListener('click', (event) => {
        const foodModal = document.getElementById("foodModal");
        if (event.target === foodModal) {
            foodModal.style.display = 'none';
        }
    });

    document.getElementById("createBtn").addEventListener("click", () => {
        const token = getCookie("token");

        if (token) {
            window.location.href = "/create_recipe/";
        } else {
            signupModal.style.display = "flex";
        }
    });
});

// // Table - add and remove ingredients - temporary unavailable
// const table = document.getElementsByClassName("fridge-table")[0].querySelector("tbody");

// table.addEventListener("click", (event) => {
//     if (event.target.classList.contains("fridge-add-btn")) {
//         const newRow = document.createElement("tr");
//         newRow.innerHTML = `
//             <td class="ingredient">
//                 <input type="text" placeholder="Ingredient">
//             </td>
//             <td class="amount">
//                 <input type="text" placeholder="Amount">
//             </td>
//             <td class="actions">
//                 <button class="remove-btn">
//                     <i class="fas fa-times"></i>
//                 </button>
//             </td>
//         `;
//         table.insertBefore(newRow, table.lastElementChild);
//     } else if (event.target.closest(".remove-btn")) {
//         const row = event.target.closest("tr");
//         if (row) {
//             row.remove();
//         }
//     }
// });

// Login and Signup Modals
const loginModal = document.getElementById("loginModal");
const signupModal = document.getElementById("signupModal");

const loginBtn = document.querySelector(".login-btn");
const signupBtn = document.querySelector(".signup-btn");

loginBtn.addEventListener("click", () => {
    loginModal.style.display = "flex";
});

signupBtn.addEventListener("click", () => {
    signupModal.style.display = "flex";
});

window.addEventListener("click", (e) => {
    if (e.target === loginModal) loginModal.style.display = "none";
    if (e.target === signupModal) signupModal.style.display = "none";
});

document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const usernameOrEmail = e.target.username_or_email.value.trim();
    const password = e.target.password.value;

    try {
        const response = await fetch("http://localhost:8081/api/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "include",
            body: JSON.stringify({
                username: usernameOrEmail,
                password: password
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            alert("Login failed: " + (errorData.error || "Unknown error"));
            return;
        }

        loginModal.style.display = "none";
        e.target.reset();
        window.location.reload();
    } catch (error) {
        alert("Network error: " + error.message);
    }
});

document.getElementById("signupForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = e.target.username.value.trim();
    const email = e.target.email.value.trim();
    const password1 = e.target.password.value;
    const password2 = e.target.confirm_password.value;

    if (password1 !== password2) {
        alert("Heslá sa musia zhodovať.");
        return;
    }

    try {
        const response = await fetch("http://localhost:8081/api/auth/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                username,
                email,
                password: password1
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            alert("Error: " + (errorData.error || "Registration failed"));
            return;
        }

        signupModal.style.display = "none";
        e.target.reset();
    } catch (error) {
        alert("Network error: " + error.message);
    }
});
