document.addEventListener("DOMContentLoaded", async () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");

    if (clearIcon) {
        clearIcon.addEventListener("click", () => {
            searchInput.value = "";
        });
    }

    //const recipeData = await fetch('recipe.json').then(response => response.json());

    //populateRecipe(recipeData.recipe);

    const starContainer = document.getElementById('stars');
    const ratingInput = document.getElementById('rating');
    const maxStars = 5;

    for (let i = 1; i <= maxStars; i++) {
        const star = document.createElement('span');
        star.classList.add('star');
        star.textContent = '★';
        star.dataset.value = i;

        star.addEventListener('mouseover', () => {
            updateStars(i);
        });

        star.addEventListener('mouseout', () => {
            updateStars(ratingInput.value);
        });

        star.addEventListener('click', () => {
            ratingInput.value = i;
            updateStars(i);
        });

        starContainer.appendChild(star);
    }

    function updateStars(rating) {
        const stars = starContainer.querySelectorAll('.star');
        stars.forEach(star => {
            star.classList.remove('hover', 'selected');
            if (star.dataset.value <= rating) {
                star.classList.add(ratingInput.value == star.dataset.value ? 'selected' : 'hover');
            }
        });
    }

    function populateRecipe(recipe) {

        document.querySelector(".food-item.recipe-food-item p").textContent = recipe.name;

        document.querySelector(".food-item.recipe-food-item img").src = recipe.image;

        const ingredientsList = document.querySelector(".recipe-ingredients ul");
        ingredientsList.innerHTML = '';
        recipe.ingredients.forEach(ingredient => {
            const li = document.createElement("li");
            li.textContent = ingredient;
            ingredientsList.appendChild(li);
        });

        document.getElementById("prep-time").textContent = recipe.details.preparation_time;
        document.getElementById("cook-time").textContent = recipe.details.cooking_time;
        document.getElementById("temperature").textContent = recipe.details.cooking_temperature;
        document.getElementById("servings").textContent = recipe.details.servings;

        // Populate recipe steps
        const stepsContainer = document.querySelector(".recipe-steps");
        stepsContainer.innerHTML = '';
        recipe.steps.forEach(step => {
            const stepDiv = document.createElement("div");
            stepDiv.classList.add("step");

            const stepImage = document.createElement("img");
            stepImage.src = step.image;
            stepImage.alt = `Step ${step.step_number}`;
            stepImage.classList.add("logo");

            const stepInfo = document.createElement("div");
            stepInfo.classList.add("step-info");

            const stepTitle = document.createElement("h3");
            stepTitle.textContent = `Step ${step.step_number}`;

            const stepDescription = document.createElement("p");
            stepDescription.textContent = step.description;

            stepInfo.appendChild(stepTitle);
            stepInfo.appendChild(stepDescription);
            stepDiv.appendChild(stepImage);
            stepDiv.appendChild(stepInfo);
            stepsContainer.appendChild(stepDiv);
        });

        const ratingsContainer = document.querySelector(".ratings");
        ratingsContainer.innerHTML = '<h2>Ratings</h2>';
        recipe.ratings.forEach(rating => {
            const ratingDiv = document.createElement("div");
            ratingDiv.classList.add("rating");

            const ratingHeader = document.createElement("div");
            ratingHeader.classList.add("rating-header");

            const avatar = document.createElement("img");
            avatar.src = "../resources/avatar.png";
            avatar.alt = "Avatar";
            avatar.classList.add("rating-avatar");

            const name = document.createElement("h3");
            name.textContent = rating.name;

            const stars = document.createElement("p");
            stars.textContent = "⭐".repeat(rating.stars);

            const comment = document.createElement("p");
            comment.classList.add("rating-comment");
            comment.textContent = rating.comment;

            const date = document.createElement("p");
            date.classList.add("rating-date");
            date.textContent = rating.date;

            ratingHeader.appendChild(avatar);
            ratingHeader.appendChild(name);
            ratingHeader.appendChild(stars);
            ratingDiv.appendChild(ratingHeader);
            ratingDiv.appendChild(comment);
            ratingDiv.appendChild(date);
            ratingsContainer.appendChild(ratingDiv);
        });
    }
});
/*
document.addEventListener("DOMContentLoaded", () => {
    const recipeId = getRecipeIdFromUrl();

    if (!recipeId) {
        alert("Recipe ID is missing from URL.");
        return;
    }

    fetch(`../../api/recipe/detail/${recipeId}`)  // uprav ak máš inú cestu
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.querySelector(".content-area").innerHTML = `<p>Error: ${data.error}</p>`;
                return;
            }

            populateRecipeData(data);
        })
        .catch(error => {
            console.error("Failed to load recipe:", error);
        });
});

function getRecipeIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id"); // napr. ?id=RECIPE_UUID
}

function populateRecipeData(recipe) {
    document.querySelector(".recipe-info .food-item img").src = recipe.thumbnail_image || "../resources/default-food.jpg";
    document.querySelector(".recipe-info .food-item p").textContent = recipe.description || "";

    // Napln detaily
    document.getElementById("prep-time").textContent = recipe.prep_time || "N/A";
    document.getElementById("cook-time").textContent = recipe.cook_time || "N/A";
    document.getElementById("temperature").textContent = recipe.temperature || "N/A";
    document.getElementById("servings").textContent = recipe.servings || "N/A";

    // Inštrukcie (kroky)
    const stepsDiv = document.querySelector(".recipe-steps");
    if (recipe.instructions) {
        const steps = recipe.instructions.split('\n');
        steps.forEach((step, index) => {
            const stepElem = document.createElement("p");
            stepElem.textContent = `${index + 1}. ${step}`;
            stepsDiv.appendChild(stepElem);
        });
    }
}*/
