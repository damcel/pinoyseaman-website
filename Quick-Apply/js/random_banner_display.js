const firstBannerFolder = 'banner_rotate/';  // Folder containing the images I added this
let firstBannerImages = [];

// Fetch image filenames from the server
fetch('includes/banner_rotate.php')
    .then(response => response.json())
    .then(images => {
        firstBannerImages = images;

        // Start the banner rotation only after fetching the images
        changeBanners();
        setInterval(changeBanners, 120000); // Update banners every 2 minutes (120,000 ms)
    })
    .catch(error => console.error('Error fetching images:', error));

// Function to change the banners dynamically
function changeBanners() {
    if (firstBannerImages.length === 0) return;  // No images available

    // Randomly select an image and corresponding company code
    const selectedBanner = firstBannerImages[Math.floor(Math.random() * firstBannerImages.length)];

    // Update the `src` and `alt` of the image inside the `.first-banner` div
    const firstBannerImageElement = document.querySelector('.first-banner img');
    if (firstBannerImageElement) {
        firstBannerImageElement.src = firstBannerFolder + selectedBanner.image;
        firstBannerImageElement.alt = selectedBanner.image;  // Dynamically set the alt text to the image file name

        // Add click event to redirect to the company page
        firstBannerImageElement.addEventListener('click', function() {
            window.location.href = 'display_company.php?id=' + selectedBanner.companyCode;
        });
    }
}
