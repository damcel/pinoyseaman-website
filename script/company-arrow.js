document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".company-track");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const items = document.querySelectorAll(".company-card");
    
    let currentIndex = 0;
    const visibleItems = 4; // Show 4 items at a time
    const totalItems = items.length;
    const cardWidth = items[0].offsetWidth + 40; // Card width + gap

    function updateButtons() {
        prevBtn.style.display = currentIndex === 0 ? "none" : "block";
        nextBtn.style.display = currentIndex >= totalItems - visibleItems ? "none" : "block";
    }

    nextBtn.addEventListener("click", () => {
        if (currentIndex < totalItems - visibleItems) {
            currentIndex++;
            track.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
            updateButtons();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (currentIndex > 0) {
            currentIndex--;
            track.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
            updateButtons();
        }
    });

    updateButtons(); // Initial button state
});
