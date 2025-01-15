//import { recipesCreated } from './fakejson-created.js';
//import { recipesSaved } from './fakejson-saved.js';

// const recipesCreated = [
//     {
//         "nazov": "Pizza Margherita",
//         "ingrediencie": ["cesto", "paradajky", "mozzarella", "bazalka", "olivový olej"],
//         "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
//     },
//     {
//         "nazov": "Spaghetti Carbonara",
//         "ingrediencie": ["špagety", "slanina", "parmezán", "vajcia", "cesnak"],
//         "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
//     },
//     {
//         "nazov": "Hranolky",
//         "ingrediencie": ["zemiaky", "soľ", "olej"],
//         "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
//     },
//     {
//         "nazov": "Hranolky",
//         "ingrediencie": ["zemiaky", "soľ", "olej"],
//         "foto": "https://api.ketodiet.cz/data/files/de/349/ae1ce5956e8b2fa8463fea7ce9d4c1ae4bc/spagety-s-domacim-pestem-hlavni-foto.jpg"
//     }
// ];

// const recipesSaved = [
//     {
//         "nazov": "Pizza Margherita",
//         "ingrediencie": ["cesto", "paradajky", "mozzarella", "bazalka", "olivový olej"],
//         "foto": "https://kuchynalidla.sk/storage/app/uploads/public/5fa/c0e/2a5/5fac0e2a571e1460216977.jpg"
//     },
//     {
//         "nazov": "Spaghetti Carbonara",
//         "ingrediencie": ["špagety", "slanina", "parmezán", "vajcia", "cesnak"],
//         "foto": "https://kuchynalidla.sk/storage/app/uploads/public/5fa/c0e/2a5/5fac0e2a571e1460216977.jpg"
//     },
//     {
//         "nazov": "Hranolky",
//         "ingrediencie": ["zemiaky", "soľ", "olej"],
//         "foto": "https://kuchynalidla.sk/storage/app/uploads/public/5fa/c0e/2a5/5fac0e2a571e1460216977.jpg"
//     },
//     {
//         "nazov": "Hranolky",
//         "ingrediencie": ["zemiaky", "soľ", "olej"],
//         "foto": "https://kuchynalidla.sk/storage/app/uploads/public/5fa/c0e/2a5/5fac0e2a571e1460216977.jpg"
//     }
// ];

// window.onload = function() {
//     displayRecipes(recipesCreated);
// };

// Display loaded recipes
// function displayRecipes(recipes) {
//     const feed = document.querySelector('.recipes-feed');
//     feed.innerHTML = '';

//     recipes.forEach(recipe => {
//         const foodItem = document.createElement('div');
//         foodItem.classList.add('food-item');

//         const foodImage = document.createElement('img');
//         foodImage.src = recipe.foto;
//         foodImage.alt = recipe.nazov;

//         const foodName = document.createElement('p');
//         foodName.textContent = recipe.nazov;

//         foodItem.addEventListener('click', function() {
//             openModal(recipe);
//         });

//         foodItem.appendChild(foodImage);
//         foodItem.appendChild(foodName);
//         feed.appendChild(foodItem);
//     });

//     const modal = document.getElementById('foodModal');
//     window.addEventListener('click', function(event) {
//         if (event.target === modal) {
//             modal.style.display = 'none';
//         }
//     });
// }
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
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        recipesCreated = data;
        changeRecipes(1);
        
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

        function changeRecipes(number) {
            if(number == 1) {
                recipes = recipesCreated;
            }
            else {
                recipes = recipesSaved;
            }
            displayRecipes(recipes);
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
        
        displayRecipes();
    })
    .catch(error => {
        console.error('There has been a problem with fetch operation:', error);
    });
    
    fetch('saved.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            recipesSaved = data;
        })
        .catch(error => {
            console.error('There has been a problem with fetch operation:', error);
        });
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
        
        foodItem.appendChild(foodImage);
        foodItem.appendChild(foodName);
        
        feed.appendChild(foodItem);
        
        foodItem.addEventListener("click", function() {
            displayFoodModal(recipe);
        });
    });
}

function changeRecipes(number) {
    if(number == 1) {
        recipes = recipesCreated;
    }
    else {
        recipes = recipesSaved;
    }
    displayRecipes(recipes);
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

//Modal
// function openModal(recipe) {
    //     const modal = document.getElementById('foodModal');
    //     const foodTitle = document.getElementById('foodTitle');
    //     const foodImage = document.getElementById('foodImage');
    //     const foodIngredients = document.getElementById('foodIngredients');
    
    //     foodTitle.textContent = recipe.nazov;
    //     foodImage.src = recipe.foto;
    //     foodImage.alt = recipe.nazov;
    //     foodIngredients.textContent = "Ingrediencie: " + recipe.ingrediencie.join(', ');

//     modal.style.display = 'flex';
// }

//change profile image
function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const profileImage = document.getElementsByClassName('avatar-img');
        profileImage[0].src = e.target.result;

        const navbarImage = document.getElementsByClassName('profile-img');
        navbarImage[0].src = e.target.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");

    clearIcon.addEventListener("click", () => {
        searchInput.value = "";
    });
});