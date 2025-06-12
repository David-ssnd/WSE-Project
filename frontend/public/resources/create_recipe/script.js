//search bar - clear icon
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-bar input");
  const clearIcon = document.querySelector(".clear-icon");
  const profileImg = document.getElementById("profile-img");
  const descriptionTextarea = document.getElementById('recipe-description-input');
  const charCountDisplay = document.getElementById('description-char-count');
  
  descriptionTextarea.addEventListener('input', () => {
    const currentLength = descriptionTextarea.value.length;
    charCountDisplay.textContent = `${currentLength} / 200`;

    if (descriptionTextarea.value.length > 200) {
      descriptionTextarea.value = descriptionTextarea.value.slice(0, 200);
    }
  });

  fetch("http://localhost:8081/api/profile", { credentials: "include" })
    .then(res => {
      if (!res.ok) throw new Error("Failed to fetch profile");
      return res.json();
    })
    .then(data => {
      if (data.profile_picture) {
        profileImg.src = data.profile_picture;
      }
    })
    .catch(console.error);

  if (!searchInput || !clearIcon) return;

  // Clear input when clear icon is clicked
  clearIcon.addEventListener("click", () => {
      searchInput.value = "";
      searchInput.focus();
  });

  // Handle Enter key for search
  searchInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
          const query = searchInput.value.trim();
          if (query) {
              window.location.href = `/?query=${encodeURIComponent(query)}`;
          }
      }
  });
});



// Table - add and remove ingredients
const table = document.getElementsByClassName("fridge-table")[0].querySelector("tbody");

table.addEventListener("click", (event) => {
    if (event.target.classList.contains("fridge-add-btn")) {
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td class="ingredient">
                <input type="text" name="ingredients[]" placeholder="Ingredient">
            </td>
            <td class="amount">
                <input type="text" name="amounts[]" placeholder="Amount">
            </td>
            <td class="actions">
                <button class="remove-btn" type="button">
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
          <button class="btn upload-btn" type="button">Upload Image</button>
          <input type="file" class="create-recipe-file-input" name="step-image[]" accept="image/*">
        </div>
        <div class="step-info">
          <h3>Step ` + index + `</h3>
          <textarea name="step-description[]" placeholder="Enter step description"></textarea>
        </div>
        <button class="btn remove-btn" type="button" onclick="removeStep(this)">
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

function parseJwt(token) {
  try {
      const base64Url = token.split('.')[1];
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(atob(base64).split('').map(c =>
          '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
      ).join(''));

      return JSON.parse(jsonPayload);
  } catch (e) {
      console.error("Invalid token", e);
      return null;
  }
}

function getTokenFromCookie(name = "token") {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? match[2] : null;
}

document.querySelectorAll('input[type="number"]').forEach(input => {
  input.addEventListener('input', () => {
      if (input.value < 1) {
          input.value = '';
      }
  });
});

async function submitRecipe(event) {
  event.preventDefault();

  const title = document.querySelector('.food-title').value.trim();
  const prepTime = parseInt(document.querySelector('#prep-time').value || 0);
  const cookTime = parseInt(document.querySelector('#cook-time').value || 0);
  const temperature = parseInt(document.querySelector('#temperature').value || 0);
  const servings = parseInt(document.querySelector('#servings').value || 0);

  // Ingredients
  const ingredients = Array.from(document.querySelectorAll('input[name="ingredients[]"]'))
      .map(input => input.value.trim());
  const amounts = Array.from(document.querySelectorAll('input[name="amounts[]"]'))
      .map(input => input.value.trim());

  const ingredientList = ingredients.map((ing, i) => ({
      ingredient: ing,
      amount: amounts[i] || ""
  }));

  // Main thumbnail image (the one in .food-item)
  const thumbnailInput = document.querySelector('.food-item .create-recipe-file-input');
  let thumbnailImage = null;
  if (thumbnailInput && thumbnailInput.files.length > 0) {
      thumbnailImage = await toBase64(thumbnailInput.files[0]);
  }

  // Steps
  const steps = Array.from(document.querySelectorAll('.step'));
  const stepDescriptions = [];
  const stepImages = [];

  for (let step of steps) {
      const description = step.querySelector('textarea')?.value.trim() || "";
      const fileInput = step.querySelector('input[type="file"]');
      const image = fileInput && fileInput.files.length > 0 ? await toBase64(fileInput.files[0]) : null;

      stepDescriptions.push(description);
      stepImages.push(image);
  }

  const data = {
      title,
      prep_time: prepTime,
      cook_time: cookTime,
      temperature,
      servings,
      thumbnail_image: thumbnailImage,
      ingredients: ingredientList,
      step_descriptions: stepDescriptions,
      step_images: stepImages
  };

  console.log("Sending recipe data:", data);

  fetch("http://localhost:8081/api/recipes", {
    method: "POST",
    headers: {
        "Content-Type": "application/json"
    },
    credentials: 'include', // This sends cookies with the request
    body: JSON.stringify(data)
})
.then(async res => {
  if (res.ok) {
      //window.location.href = "/";
  } else {
      const contentType = res.headers.get("content-type");
      if (contentType && contentType.includes("application/json")) {
          const err = await res.json();
          alert("Error: " + (err.message || "Failed to create recipe"));
      } else {
          const errText = await res.text();
          alert("Error: " + errText);
      }
  }
})
  .catch(err => {
      alert("Network error: " + err.message);
  });
}

function toBase64(file) {
  return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.onerror = reject;
      reader.readAsDataURL(file);
  });
}