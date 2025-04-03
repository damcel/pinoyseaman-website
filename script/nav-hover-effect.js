document.addEventListener("DOMContentLoaded", function () {
    // Main navigation links
    const mainNavLinks = document.querySelectorAll(".nav-links a");
    // Mini navigation links
    const miniNavLinks = document.querySelectorAll(".job-nav-links a");

    // Function to activate a link
    function activateLink(link) {
        // Remove active class from all links
        mainNavLinks.forEach(nav => nav.classList.remove("active"));
        miniNavLinks.forEach(nav => nav.classList.remove("active"));
        
        // Add active class to clicked link
        link.classList.add("active");

        // Store the active link href in localStorage for persistent state
        localStorage.setItem("activeNav", link.getAttribute("href"));
    }

    // Attach click event listener to main navigation links
    mainNavLinks.forEach(link => {
        link.addEventListener("click", function () {
            activateLink(this);
        });
    });

    // Attach click event listener to mini navigation links
    miniNavLinks.forEach(link => {
        link.addEventListener("click", function () {
            activateLink(this);
        });
    });

    // Handle logo click (main navigation logo)
    const logoLink = document.querySelector('.logo-link');
    if (logoLink) {
        logoLink.addEventListener("click", function () {
            activateLink(this); // Apply active state to logo as well
        });
    }

    // Handle the burger icon click to toggle the navigation menu and buttons
    const burgerMenu = document.querySelector(".burger-menu");
    const navLinks = document.querySelector(".nav-links");
    const buttons = document.querySelector(".buttons");

    burgerMenu.addEventListener("click", function () {
        navLinks.classList.toggle("active"); // Toggle visibility of nav-links
        buttons.classList.toggle("active");  // Toggle visibility of buttons
    });

    // Load the last active link from localStorage (on page load)
    const activePage = localStorage.getItem("activeNav");
    if (activePage) {
        // Check both main and mini nav for active link
        mainNavLinks.forEach(link => {
            if (link.getAttribute("href") === activePage) {
                link.classList.add("active"); // Keep it active after reload
            }
        });
        miniNavLinks.forEach(link => {
            if (link.getAttribute("href") === activePage) {
                link.classList.add("active"); // Keep it active after reload
            }
        });
    }
});
