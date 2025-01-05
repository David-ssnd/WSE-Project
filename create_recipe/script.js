//search bar vymazanie textu - rovnake pre vsetky stranky
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
const uploadBtns = Array.from(document.getElementsByClassName('upload-btn'));
const fileInputs = Array.from(document.getElementsByClassName('create-recipe-file-input'));
const imagePreviews = Array.from(document.getElementsByClassName('image-preview'));

uploadBtns.forEach((uploadBtn, index) => {
  uploadBtn.addEventListener('click', () => {
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

function addStep() {
  const stepItem = document.createElement("div");
  stepItem.classList.add("step");

  const foodImage = document.createElement("div");
  foodImage.classList.add("food-image");

  const imgPreview = document.createElement("img");
  imgPreview.classList.add("image-preview");
  imgPreview.alt = "Image Preview";

  const uploadButton = document.createElement("button");
  uploadButton.classList.add("btn", "upload-btn");
  uploadButton.innerText = "Upload Image";

  const fileInput = document.createElement("input");
  fileInput.type = "file";
  fileInput.classList.add("create-recipe-file-input");
  fileInput.accept = "image/*";
  fileInput.style.display = "none";

  fileInput.addEventListener("change", function () {
    const file = fileInput.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        imgPreview.src = e.target.result;
        imgPreview.style.display = "block";
      };
      reader.readAsDataURL(file);
    }
  });

  uploadButton.onclick = function () {
    fileInput.click();
  };

  foodImage.appendChild(imgPreview);
  foodImage.appendChild(uploadButton);
  foodImage.appendChild(fileInput);

  const stepInfo = document.createElement("div");
  stepInfo.classList.add("step-info");

  const stepTitle = document.createElement("h3");
  stepTitle.innerText = `Step ${document.querySelectorAll('.step').length + 1}`;

  const stepDescription = document.createElement("textarea");
  stepDescription.classList.add("step-textarea");
  stepDescription.placeholder = "Step description...";

  stepInfo.appendChild(stepTitle);
  stepInfo.appendChild(stepDescription);

  const removeButton = document.createElement("button");
  removeButton.classList.add("btn", "remove-btn");
  removeButton.innerHTML = '<i class="fas fa-times"></i>';
  removeButton.onclick = function () {
    stepItem.remove();
  };

  stepItem.appendChild(foodImage);
  stepItem.appendChild(stepInfo);
  stepItem.appendChild(removeButton);

  const stepsList = document.getElementById("stepsList");
  stepsList.appendChild(stepItem);
}

function previewStepImage(event) {
  const file = event.target.files[0];
  const reader = new FileReader();
  const previewImg = event.target.closest('.step-item').querySelector('.step-img-preview');

  reader.onload = function(e) {
      previewImg.src = e.target.result;
      previewImg.style.display = 'block';
  }

  if (file) {
      reader.readAsDataURL(file);
  }
}

function removeStep(button) {
  const step = button.closest('.step');
  step.remove();
}