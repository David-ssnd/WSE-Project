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