// Table - fridge
document.addEventListener("DOMContentLoaded", () => {
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

        if (event.target.classList.contains("remove-btn")) {
            event.target.closest("tr").remove();
        }
    });
});

// Food Modal
const foodModal = document.getElementById('foodModal');
const foodTitle = document.getElementById('foodTitle');
const foodIngredients = document.getElementById('foodIngredients');
const foodImage = document.getElementById('foodImage');
const instructionsBtn = document.getElementById('instructionsBtn');

const foodItems = document.querySelectorAll('.food-item');

foodItems.forEach((item) => {
    item.addEventListener('click', () => {
        const title = item.querySelector('p').innerText;
        const ingredients = item.getAttribute('data-ingredients');
        const image = item.querySelector('img').src;

        foodImage.src = image;
        foodTitle.innerText = title;
        foodIngredients.innerText = `Ingredients: ${ingredients}`;
        instructionsBtn.target = "../account/index.html";
        foodModal.style.display = 'flex';
    });
});

window.addEventListener('click', (event) => {
    if (event.target === foodModal) {
        foodModal.style.display = 'none';
    }
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