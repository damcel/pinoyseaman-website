document.addEventListener("DOMContentLoaded", function () {
    let companyCards = document.querySelectorAll(".company-card");
    let seeMoreButton = document.querySelector(".see-more");
    let searchInput = document.querySelector(".search-box input");
    let searchForm = document.querySelector(".search-box");
    let heading = document.querySelector(".company-search-main-container h2");

    // Hide all company cards beyond the first 8
    companyCards.forEach((card, index) => {
        if (index >= 8) {
            card.style.display = "none";
        }
    });

    // Hide the "See more" button if there are 8 or fewer companies
    if (companyCards.length <= 8) {
        seeMoreButton.style.display = "none";
    }

    // Add event listener to "See more" button
    seeMoreButton.addEventListener("click", function () {
        companyCards.forEach(card => card.style.display = "block"); // Show all cards
        seeMoreButton.style.display = "none"; // Hide the button after clicking
    });

    // Search functionality
    searchForm.addEventListener("submit", function (e) {
        e.preventDefault();
        let searchTerm = searchInput.value.toLowerCase().trim();
        let matchCount = 0;

        companyCards.forEach(card => {
            let companyName = card.querySelector(".company-name h3").textContent.toLowerCase();
            if (companyName.includes(searchTerm)) {
                card.style.display = "block";
                matchCount++;
            } else {
                card.style.display = "none";
            }
        });

        // Update heading to "Search Results"
        if (searchTerm !== "") {
            heading.textContent = "Search Results";
        } else {
            heading.textContent = "Explore Companies";
        }

        // Hide "See More" button when searching
        seeMoreButton.style.display = matchCount > 8 ? "block" : "none";
    });
});