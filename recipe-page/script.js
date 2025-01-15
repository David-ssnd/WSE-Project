document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector(".search-bar input");
    const clearIcon = document.querySelector(".clear-icon");

    clearIcon.addEventListener("click", () => {
        searchInput.value = "";
    });

    const starContainer = document.getElementById('stars');
    const ratingInput = document.getElementById('rating');
    const maxStars = 5;

        // Vytvor hviezdičky
        for (let i = 1; i <= maxStars; i++) {
            const star = document.createElement('span');
            star.classList.add('star');
            star.textContent = '★';
            star.dataset.value = i;

            // Pridaj hover efekt
            star.addEventListener('mouseover', () => {
                updateStars(i);
            });

            // Odstráň hover efekt pri odchode myši
            star.addEventListener('mouseout', () => {
                updateStars(ratingInput.value);
            });

            // Ulož hodnotenie pri kliknutí
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
});