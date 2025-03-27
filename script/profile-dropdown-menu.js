const dpBtn = document.getElementById("dpBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");

    // Toggle dropdown visibility when clicking "DP" button
    dpBtn.addEventListener("click", function(event) {
        dropdownMenu.classList.toggle("show");
        event.stopPropagation(); // Prevent closing when clicking the button
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
        if (!dpBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });