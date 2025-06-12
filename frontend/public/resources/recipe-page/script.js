document.addEventListener("DOMContentLoaded", async () => {
  function getRecipeIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id");
  }

  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }

  function parseJwt(token) {
    try {
      const base64Url = token.split('.')[1];
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
          .join('')
      );
      return JSON.parse(jsonPayload);
    } catch {
      return {};
    }
  }

  const starContainer = document.getElementById('stars');
  const ratingInput = document.getElementById('rating');
  const maxStars = 5;

  if (starContainer && ratingInput) {
    function updateStars(rating) {
      const stars = starContainer.querySelectorAll('.star');
      stars.forEach(star => {
        star.classList.remove('hover', 'selected');
        const starValue = Number(star.dataset.value);
        if (ratingInput.value && starValue <= ratingInput.value) {
          star.classList.add('selected');
        } else if (starValue <= rating) {
          star.classList.add('hover');
        }
      });
    }

    for (let i = 1; i <= maxStars; i++) {
      const star = document.createElement('span');
      star.classList.add('star');
      star.textContent = '★';
      star.dataset.value = i;
      star.addEventListener('mouseover', () => updateStars(i));
      star.addEventListener('mouseout', () => updateStars(0));
      star.addEventListener('click', () => {
        ratingInput.value = i;
        updateStars(i);
      });
      starContainer.appendChild(star);
    }
  }

  const recipeId = getRecipeIdFromUrl();
  if (!recipeId) return;

  const token = getCookie("token");
  const currentUser = token ? parseJwt(token).username : null;

  async function loadReviews() {
    try {
      const res = await fetch(`http://localhost:8081/api/recipes/${recipeId}/reviews`);
      if (!res.ok) throw new Error('Failed to fetch reviews');
      const data = await res.json();

      const ratingsContainer = document.querySelector(".ratings");
      if (!ratingsContainer) return;
      ratingsContainer.innerHTML = '<h2>Ratings & Comments</h2>';

      if (data.length === 0) {
        ratingsContainer.appendChild(document.createElement("p")).textContent = "No reviews yet.";
        return;
      }

      const avgRating = (data.reduce((acc, r) => acc + r.rating, 0) / data.length).toFixed(1);
      const avgRatingEl = document.createElement("p");
      avgRatingEl.textContent = `Average rating: ${avgRating} (${data.length} review${data.length > 1 ? 's' : ''})`;
      ratingsContainer.appendChild(avgRatingEl);

      data.forEach(review => {
        const reviewEl = document.createElement("div");
        reviewEl.classList.add("review-item");
        reviewEl.innerHTML = `
          <strong>${review.user ?? 'Anonymous'}</strong> 
          <span>${"★".repeat(review.rating)}${"☆".repeat(maxStars - review.rating)}</span>
          <p>${review.comment ?? ''}</p>
        `;

        if (currentUser && review.user === currentUser) {
          const delBtn = document.createElement("button");
          delBtn.textContent = "Delete";
          delBtn.addEventListener("click", async () => {
            try {
              const delRes = await fetch(`http://localhost:8081/api/reviews/${review.id}`, {
                method: "DELETE",
                headers: {
                  "Authorization": `Bearer ${token}`
                }
              });
              if (!delRes.ok) throw new Error("Delete failed");
              loadReviews();
            } catch (e) {
              alert(e.message);
            }
          });
          reviewEl.appendChild(delBtn);
        }
        ratingsContainer.appendChild(reviewEl);
      });

      const userRatingContainer = document.querySelector(".user-rating");
      if (userRatingContainer && currentUser) {
        const myReview = data.find(r => r.user === currentUser);
        if (myReview) {
          ratingInput.value = myReview.rating;
          updateStars(myReview.rating);
          userRatingContainer.innerHTML = `
            <h3>Your Review</h3>
            <p>${"★".repeat(myReview.rating)}${"☆".repeat(maxStars - myReview.rating)}</p>
            <p>${myReview.comment ?? ''}</p>
          `;
        } else {
          userRatingContainer.innerHTML = "<p>You have not submitted a review yet.</p>";
          ratingInput.value = "";
          updateStars(0);
        }
      }
    } catch {
      const ratingsContainer = document.querySelector(".ratings");
      if (ratingsContainer) ratingsContainer.innerHTML = "<p>Failed to load reviews.</p>";
    }
  }

  loadReviews();

  const ratingForm = document.querySelector(".add-rating form");
  const commentInput = document.getElementById("comment");

  if (ratingForm && ratingInput && commentInput) {
    ratingForm.addEventListener("submit", async e => {
      e.preventDefault();
      const ratingValue = Number(ratingInput.value);
      const comment = commentInput.value.trim();
      if (!ratingValue) {
        alert("Select a rating");
        return;
      }
      if (!token) {
        alert("Login required");
        return;
      }
      try {
        const res = await fetch(`http://localhost:8081/api/recipes/${recipeId}/review`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
          },
          body: JSON.stringify({ rating: ratingValue, comment })
        });
        if (!res.ok) throw new Error("Submit failed");
        ratingInput.value = "";
        commentInput.value = "";
        updateStars(0);
        loadReviews();
      } catch (err) {
        alert(err.message);
      }
    });
  }
});
