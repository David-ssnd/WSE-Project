import { recipes } from './fakejson.js';

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.ingredient input').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-Zá-žÁ-Ž ]/g, ''); // Povolené len písmená a medzery
        });
    });

    // Validácia pre pole Amount
    document.querySelectorAll('.amount input').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, ''); // Povolené len čísla
        });
    });



    // recipes are in fakejson.js

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

            foodItem.appendChild(foodImage);
            foodItem.appendChild(foodName);

            feed.appendChild(foodItem);

            foodItem.addEventListener("click", function() {
                displayFoodModal(recipe);
            });
        });
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
    }

    // Close modal
    window.addEventListener('click', (event) => {
        if (event.target === foodModal) {
            foodModal.style.display = 'none';
        }
    });
    jabl
    displayRecipes();

    // Table - fridge
    const table = document.getElementById("fridge-table").querySelector("tbody");

    table.addEventListener("click", (event) => {
        if (event.target.id === "fridge-add-btn") {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td class="ingredient">
                    <input type="text" placeholder="Ingredient">
                </td>
                <td class="amount">
                    <input type="text" placeholder="Amount">
                </td>
                <td class="actions">
                    <button class="remove-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            table.insertBefore(newRow, table.lastElementChild);
        }

        else if (event.target.classList.contains("remove-btn")) {
            event.target.closest("tr").remove();
        }
    });
});

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
    if (e.target === loginModal) {
        loginModal.style.display = "none";
    }
    if (e.target === signupModal) {
        signupModal.style.display = "none";
    }
});

document.getElementById("loginForm").addEventListener("submit", (e) => {
    e.preventDefault();
    alert("Login Successful!");
    loginModal.style.display = "none";
});

document.getElementById("signupForm").addEventListener("submit", (e) => {
    e.preventDefault();
    alert("Sign Up Successful!");
    signupModal.style.display = "none";
});