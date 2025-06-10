//search bar - clear icon
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");

    clearIcon.addEventListener("click", () => {
        searchInput.value = "";
    });
});

// Table - add and remove ingredients
const table = document.getElementsByClassName("fridge-table")[0].querySelector("tbody");

table.addEventListener("click", (event) => {
    if (event.target.classList.contains("fridge-add-btn")) {
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
    else if (event.target.closest(".remove-btn")) {
        const row = event.target.closest("tr");
        if (row) {
            row.remove();
        }
    }
});


// add image
const foodImages = Array.from(document.getElementsByClassName('food-image'));
const fileInputs = Array.from(document.getElementsByClassName('create-recipe-file-input'));
const imagePreviews = Array.from(document.getElementsByClassName('image-preview'));

foodImages.forEach((foodImage, index) => {
  foodImage.addEventListener('click', () => {
    if (event.target === imagePreviews[index]) return;
    if (fileInputs[index]) fileInputs[index].click();
  });
});

fileInputs.forEach((fileInput, index) => {
  fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (imagePreviews[index]) {
          imagePreviews[index].src = e.target.result;
          imagePreviews[index].style.display = 'block';
        }
      };
      reader.readAsDataURL(file);
    }
  });
});

// Add and remove steps
function addStep() {
    const index = document.getElementsByClassName('step').length + 1;
    const steps = document.getElementsByClassName('recipe-steps')[0];
    const newStep = document.createElement('div');
    newStep.classList.add('step');
    newStep.innerHTML = `
        <div class="food-image">
          <div class="step-img-container">
            <img class="image-preview" alt="Image Preview">
          </div>
          <button class="btn upload-btn">Upload Image</button>
          <input type="file" class="create-recipe-file-input" accept="image/*">
        </div>
        <div class="step-info">
          <h3>Step ` + index + `</h3>
          <textarea placeholder="Enter step description"></textarea>
        </div>
        <button class="btn remove-btn" onclick="removeStep(this)">
        <i class="fas fa-times"></i>
    `;
    steps.appendChild(newStep);

    // Add event listeners for new step
    const foodImage = newStep.querySelector('.food-image');
    const imagePreview = newStep.querySelector('.image-preview');
    const fileInput = newStep.querySelector('.create-recipe-file-input');
    const uploadBtn = newStep.querySelector('.upload-btn');

    foodImage.addEventListener('click', (event) => {
        if (event.target === imagePreview) return;
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}

function removeStep(button) {
    const step = button.closest('.step');
    if (step) {
      step.remove();
      reNumerate();
    }
}

function reNumerate() {
  const steps = document.getElementsByClassName('step');
  for (let i = 0; i < steps.length; i++) {
      steps[i].querySelector('h3').innerText = 'Step ' + (i + 1);
  }
}

document.querySelectorAll('input[type="number"]').forEach(input => {
  input.addEventListener('input', () => {
      if (input.value < 1) {
          input.value = '';
      }
  });
});

async function submitRecipe() {
    const title = document.querySelector('.food-title').value;
    const cookTime = parseInt(document.querySelector('#cook-time').value || 0);
    const instructions = Array.from(document.querySelectorAll('.recipe-steps textarea'))
        .map((textarea, i) => `Step ${i + 1}: ${textarea.value}`)
        .join('\n');

    const fileInput = document.querySelector('.create-recipe-file-input');
    let thumbnailImage = null;
    if (fileInput && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        thumbnailImage = await toBase64(file);
    }

    const data = {
        user_id: 1, // TODO: Dynamicky získať prihláseného usera
        title,
        cook_time: cookTime,
        instructions,
        thumbnail_image: thumbnailImage
    };

    fetch("/recipe/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => {
        if (res.ok) {
            alert("Recipe created!");
            window.location.href = "/"; // alebo redirect na detail
        } else {
            return res.json().then(err => {
                alert("Error: " + (err.message || "Unknown"));
            });
        }
    })
    .catch(err => alert("Network error: " + err));
}

function toBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}