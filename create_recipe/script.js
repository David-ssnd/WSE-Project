function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const previewImg = document.getElementById('previewImg');
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';  // Zobrazenie náhľadu obrázka
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}

function addIngredient() {
    const ingredientList = document.getElementById('ingredientList');
    const newIngredient = document.createElement('div');
    newIngredient.classList.add('ingredient-item');

    newIngredient.innerHTML = `
        <input type="text" class="ingredient-name" placeholder="Enter ingredient">
        <div class="ingredient-actions">
            <button class="btn delete-button" onclick="deleteIngredient(this)">➖</button>
        </div>
    `;
    ingredientList.appendChild(newIngredient);
}

function deleteIngredient(button) {
    const ingredientItem = button.closest('.ingredient-item');
    ingredientItem.remove();
}

function addStep() {
    const stepItem = document.createElement("div");
    stepItem.classList.add("step-item");

    const stepTextarea = document.createElement("textarea");
    stepTextarea.classList.add("step-textarea");
    stepTextarea.placeholder = "Step description...";

    const stepActions = document.createElement("div");
    stepActions.classList.add("step-actions");

    // Tlačidlo pre upload obrázka
    const uploadButton = document.createElement("button");
    uploadButton.classList.add("btn");
    uploadButton.innerText = "📷";

    // Tlačidlo pre odstránenie kroku
    const deleteButton = document.createElement("button");
    deleteButton.classList.add("btn", "delete-step-button");
    deleteButton.innerText = "➖";
    deleteButton.onclick = function() {
        stepItem.remove();
    };

    // Vytvoríme input na nahrávanie obrázka (hidden)
    const fileInput = document.createElement("input");
    fileInput.type = "file";
    fileInput.accept = "image/*";
    fileInput.style.display = "none";
    fileInput.addEventListener("change", previewStepImage);

    // Náhľad obrázka
    const imgPreview = document.createElement("img");
    imgPreview.classList.add("step-img-preview");
    imgPreview.style.display = "none";  // Skrytý na začiatku

    // Pridanie tlačidla pre upload obrázka a inputu pre výber súboru
    uploadButton.onclick = function() {
        fileInput.click();  // Simulujeme kliknutie na input, aby sa otvoril dialóg na výber súboru
    };

    // Pridanie všetkých prvkov do kroku
    stepActions.appendChild(uploadButton);
    stepActions.appendChild(deleteButton);
    stepItem.appendChild(stepTextarea);
    stepItem.appendChild(stepActions);
    stepItem.appendChild(fileInput);
    stepItem.appendChild(imgPreview);

    // Pridanie nového kroku do zoznamu
    const stepsList = document.getElementById("stepsList");
    stepsList.appendChild(stepItem);
}

// Funkcia na náhľad obrázka
function previewStepImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    const previewImg = event.target.closest('.step-item').querySelector('.step-img-preview');

    reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';  // Zobrazenie obrázka
    }

    if (file) {
        reader.readAsDataURL(file);  // Čítanie obrázka ako dátového URL
    }
}


function deleteStep(button) {
    const stepItem = button.closest('.step-item');
    stepItem.remove();
}

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");

    clearIcon.addEventListener("click", () => {
        searchInput.value = "";
    });
});