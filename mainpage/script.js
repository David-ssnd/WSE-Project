// Modal
const modal = document.getElementById('foodModal');
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
        modal.style.display = 'flex';
    });
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});

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