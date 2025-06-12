document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.querySelector(".search-bar input");
  const clearIcon = document.querySelector(".clear-icon");
  const profileImg = document.getElementById("profile-img");
  const descriptionTextarea = document.getElementById('recipe-description-input');
  const charCountDisplay = document.getElementById('description-char-count');

  document.querySelectorAll('.step').forEach(step => {
    const textarea = step.querySelector('textarea');
    const charCountDiv = step.querySelector('.step-char-count');
    const fileInput = step.querySelector('input[type="file"]');
    const imagePreview = step.querySelector('.image-preview');
    const uploadBtn = step.querySelector('.upload-btn');
    const foodImage = step.querySelector('.food-image');
  
    if (textarea && charCountDiv) {
      textarea.addEventListener('input', () => {
        const length = textarea.value.length;
        if (length > 300) {
          textarea.value = textarea.value.slice(0, 300);
        }
        charCountDiv.textContent = `${length} / 300`;
      });
  
      // Initialize current count
      const initLength = textarea.value.length;
      charCountDiv.textContent = `${initLength} / 300`;
    }
  
    if (fileInput && foodImage && imagePreview) {
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
  });

  descriptionTextarea.addEventListener('input', () => {
    let val = descriptionTextarea.value;
    if (val.length > 200) {
      val = val.slice(0, 200);
      descriptionTextarea.value = val;
    }
    charCountDisplay.textContent = `${val.length} / 200`;
  });

  fetch("http://localhost:8081/api/profile", { credentials: "include" })
    .then(res => {
      if (!res.ok) throw new Error("Failed to fetch profile");
      return res.json();
    })
    .then(data => {
      if (data.profile_picture) profileImg.src = data.profile_picture;
    })
    .catch(console.error);

  if (searchInput && clearIcon) {
    clearIcon.addEventListener("click", () => {
      searchInput.value = "";
      searchInput.focus();
    });

    searchInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        const query = searchInput.value.trim();
        if (query) {
          window.location.href = `/?query=${encodeURIComponent(query)}`;
        }
      }
    });
  }
});

// Table - add and remove ingredients
const table = document.querySelector(".fridge-table tbody");
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
        <button class="remove-btn" type="button"><i class="fas fa-times"></i></button>
      </td>
    `;
    table.insertBefore(newRow, table.lastElementChild);
  } else if (event.target.closest(".remove-btn")) {
    const row = event.target.closest("tr");
    if (row) row.remove();
  }
});

// Image input
const foodImages = Array.from(document.getElementsByClassName('food-image'));
const fileInputs = Array.from(document.getElementsByClassName('create-recipe-file-input'));
const imagePreviews = Array.from(document.getElementsByClassName('image-preview'));

foodImages.forEach((foodImage, index) => {
  foodImage.addEventListener('click', (event) => {
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
  const steps = document.querySelector('.recipe-steps');
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
      <h3>Step ${index}</h3>
      <textarea name="step-description[]" placeholder="Enter step description" maxlength="300"></textarea>
      <div class="step-char-count" style="font-size: 0.875rem; color: var(--secondary); margin-top: 0.25rem;">0 / 300</div>
    </div>
    <button class="btn remove-btn" type="button" onclick="removeStep(this)">
      <i class="fas fa-times"></i>
    </button>
  `;
  steps.appendChild(newStep);

  const textarea = newStep.querySelector('textarea');
  const charCountDiv = newStep.querySelector('.step-char-count');

  textarea.addEventListener('input', () => {
    if (textarea.value.length > 300) {
      textarea.value = textarea.value.slice(0, 300);
    }
    charCountDiv.textContent = `${textarea.value.length} / 300`;
  });

  const fileInput = newStep.querySelector('input[type="file"]');
  const imagePreview = newStep.querySelector('.image-preview');
  const foodImage = newStep.querySelector('.food-image');

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

// Validation for numbers
document.querySelectorAll('input[type="number"]').forEach(input => {
  input.addEventListener('input', () => {
    if (input.value < 1) {
      input.value = '';
    }
  });
});

// Utility
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

function toBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

async function submitRecipe(event) {
  event.preventDefault();

  const title = document.querySelector('.food-title').value.trim();
  const prepTime = parseInt(document.querySelector('#prep-time').value || 0);
  const cookTime = parseInt(document.querySelector('#cook-time').value || 0);
  const temperature = parseInt(document.querySelector('#temperature').value || 0);
  const servings = parseInt(document.querySelector('#servings').value || 0);

  const ingredients = Array.from(document.querySelectorAll('input[name="ingredients[]"]'))
    .map(input => input.value.trim());
  const amounts = Array.from(document.querySelectorAll('input[name="amounts[]"]'))
    .map(input => input.value.trim());

  const ingredientList = ingredients.map((ing, i) => ({
    ingredient: ing,
    amount: amounts[i] || ""
  }));

  const thumbnailInput = document.querySelector('.food-item .create-recipe-file-input');
  let thumbnailImage = null;
  if (thumbnailInput && thumbnailInput.files.length > 0) {
    thumbnailImage = await toBase64(thumbnailInput.files[0]);
  }

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
    description: document.getElementById('recipe-description-input').value.trim(),
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
    credentials: 'include',
    body: JSON.stringify(data)
  })
    .then(async res => {
      if (res.ok) {
        window.location.href = "/";
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
