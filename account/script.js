const recipes = [
    {
        "nazov": "Pizza Margherita",
        "ingrediencie": ["cesto", "paradajky", "mozzarella", "bazalka", "olivový olej"],
        "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
    },
    {
        "nazov": "Spaghetti Carbonara",
        "ingrediencie": ["špagety", "slanina", "parmezán", "vajcia", "cesnak"],
        "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
    },
    {
        "nazov": "Hranolky",
        "ingrediencie": ["zemiaky", "soľ", "olej"],
        "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
    }
];

window.onload = function() {
    displayRecipes(recipes);
};

function displayRecipes(recipes) {
    const feed = document.querySelector('.recipes-feed');
    feed.innerHTML = '';

    recipes.forEach(recipe => {
        const foodItem = document.createElement('div');
        foodItem.classList.add('food-item');

        const foodImage = document.createElement('img');
        foodImage.src = recipe.foto;
        foodImage.alt = recipe.nazov;

        const foodName = document.createElement('p');
        foodName.textContent = recipe.nazov;

        foodItem.addEventListener('click', function() {
            openModal(recipe);
        });

        foodItem.appendChild(foodImage);
        foodItem.appendChild(foodName);
        feed.appendChild(foodItem);
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function openModal(recipe) {
    const modal = document.getElementById('foodModal');
    const foodTitle = document.getElementById('foodTitle');
    const foodImage = document.getElementById('foodImage');
    const foodIngredients = document.getElementById('foodIngredients');

    foodTitle.textContent = recipe.nazov;
    foodImage.src = recipe.foto;
    foodImage.alt = recipe.nazov;
    foodIngredients.textContent = "Ingrediencie: " + recipe.ingrediencie.join(', ');

    modal.style.display = 'flex';
}
