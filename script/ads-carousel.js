document.addEventListener("DOMContentLoaded", function () {
    const adsContainer = document.querySelector(".ads-container");
    const ads = document.querySelectorAll(".ads-card");
    const firstAdClone = ads[0].cloneNode(true); // Clone the first ad
    adsContainer.appendChild(firstAdClone); // Append the clone at the end

    let index = 0;
    const adWidth = ads[0].offsetWidth + 50; // Width of an ad + gap

    function slideAds() {
        index++;
        adsContainer.style.transition = "transform 0.5s ease-in-out"; // Smooth transition
        adsContainer.style.transform = `translateX(-${index * adWidth}px)`;

        if (index === ads.length) {
            setTimeout(() => {
                adsContainer.style.transition = "none"; // Remove transition
                adsContainer.style.transform = "translateX(0px)"; // Reset position
                index = 0; // Reset index
            }, 500); // Wait for animation to complete
        }
    }

    // Auto-slide every 2 seconds
    setInterval(slideAds, 2000);
});
