document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".company-track");
    const nextBtn = document.getElementById("nextBtn");
    const prevBtn = document.getElementById("prevBtn");
    const items = document.querySelectorAll(".company-card");

    let currentIndex = 0;
    const totalItems = items.length;
    let visibleItems = calculateVisibleItems();
    let cardWidth = items[0].offsetWidth + 40;

    function calculateVisibleItems() {
        if (window.innerWidth <= 480) return 1;
        if (window.innerWidth <= 768) return 2;
        if (window.innerWidth <= 1024) return 3;
        return 4;
    }

    function updateSlider() {
        visibleItems = calculateVisibleItems();
        cardWidth = items[0].offsetWidth + 40;
        track.style.transform = `translateX(-${cardWidth * currentIndex}px)`;
        updateButtons();
    }

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

    window.addEventListener("resize", updateSlider);

    updateButtons();
});
