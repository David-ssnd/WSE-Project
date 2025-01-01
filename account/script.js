// Príklad dát
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

// Po načítaní stránky vykonaj zobrazenie receptov
window.onload = function() {
    displayRecipes(recipes);
};

function displayRecipes(recipes) {
    const profileContent = document.querySelector('.profile-content');
    profileContent.innerHTML = ''; // Vymaže predchádzajúci obsah

    recipes.forEach(recipe => {
        const foodItem = document.createElement('div');
        foodItem.classList.add('food-item');

        const foodImage = document.createElement('img');
        foodImage.src = recipe.foto;
        foodImage.alt = recipe.nazov;

        const foodName = document.createElement('p');
        foodName.textContent = recipe.nazov;

        // Pridanie kliknutia na recept, ktoré otvorí popup
        foodItem.addEventListener('click', function() {
            openModal(recipe);
        });

        foodItem.appendChild(foodImage);
        foodItem.appendChild(foodName);
        profileContent.appendChild(foodItem);
    });
}

function openModal(recipe) {
    const modal = document.getElementById('foodModal');
    const foodTitle = document.getElementById('foodTitle');
    const foodImage = document.getElementById('foodImage');
    const foodIngredients = document.getElementById('foodIngredients');

    // Nastav obsah modálu
    foodTitle.textContent = recipe.nazov;
    foodImage.src = recipe.foto;
    foodImage.alt = recipe.nazov;
    foodIngredients.textContent = "Ingrediencie: " + recipe.ingrediencie.join(', ');

    // Zobrazenie modálu
    modal.style.display = 'block';

    // Zatvorenie modálu pri kliknutí na krížik
    const closeModal = document.querySelector('.auth-close-btn');
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Zatvorenie modálu pri kliknutí mimo okna
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
