// Modal
const modal = document.getElementById('foodModal');
const foodTitle = document.getElementById('foodTitle');
const foodIngredients = document.getElementById('foodIngredients');
const foodImage = document.getElementById('foodImage');

const foodItems = document.querySelectorAll('.food-item');

foodItems.forEach((item) => {
    item.addEventListener('click', () => {
        const title = item.querySelector('p').innerText;
        const ingredients = item.getAttribute('data-ingredients');
        const image = item.querySelector('img').src;

        foodImage.src = image;
        foodTitle.innerText = title;
        foodIngredients.innerText = `Ingredients: ${ingredients}`;

        modal.style.display = 'flex';
    });
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});
