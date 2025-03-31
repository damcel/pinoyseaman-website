const toggleButton = document.getElementById('toggle-btn');
const sidebar = document.getElementById('sidebar');
const progressContainer = document.getElementById('progress-main-container');
const menuTitles = document.querySelectorAll('.menu-title');
const menuItems = document.querySelectorAll('#sidebar nav ul li a span'); // Select all menu text elements

// Toggle sidebar
function toggleSidebar() {
    sidebar.classList.toggle('close');
    toggleButton.classList.toggle('rotate');
    closeAllSubMenus();

    // Toggle progress container visibility
    if (progressContainer) {
        progressContainer.classList.toggle('hidden');
    }

    // Toggle visibility of MENU and ANALYTICS titles
    menuTitles.forEach(title => {
        title.classList.toggle('hidden');
    });

    // Hide text beside icons when sidebar is collapsed
    menuItems.forEach(item => {
        item.classList.toggle('hidden');
    });
}

// Close all submenus
function closeAllSubMenus() {
    Array.from(sidebar.getElementsByClassName('show')).forEach(ul => {
        ul.classList.remove('show');
        ul.previousElementSibling.classList.remove('rotate');
    });
}

// Bookmark toggle function
function toggleBookmark(button) {
    const icon = button.querySelector("i");
    icon.classList.toggle("saved");

    if (icon.classList.contains("saved")) {
        icon.classList.replace("fa-regular", "fa-solid"); // Change to solid icon
    } else {
        icon.classList.replace("fa-solid", "fa-regular"); // Change back to outline
    }
}

// Attach event listener to all bookmark buttons
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".bookmark-btn").forEach(button => {
        button.addEventListener("click", function () {
            toggleBookmark(this);
        });
    });

    // Active menu item script
    const links = document.querySelectorAll("aside nav ul li a");
    const currentPage = window.location.pathname.split("/").pop(); // Get current page filename
    const storedPage = localStorage.getItem("activeLink");

    // Set active class based on stored page OR current page
    links.forEach(link => {
        const linkPage = link.getAttribute("href").split("/").pop(); // Get filename from href

        if (linkPage === storedPage || linkPage === currentPage) {
            link.classList.add("active");
        }

        // Add click event to update the active class
        link.addEventListener("click", function () {
            // Remove 'active' class from all links
            links.forEach(l => l.classList.remove("active"));

            // Add 'active' class to the clicked link
            this.classList.add("active");

            // Store only the filename in localStorage
            localStorage.setItem("activeLink", linkPage);
        });
    });
});
