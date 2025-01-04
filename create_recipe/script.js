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
