function updateProgress() {
    fetch('progress_bar.php')  // Make sure this path is correct
        .then(response => response.json())
        .then(data => {
            let progress = Math.round(data.progress);
            document.getElementById("progress-bar").style.width = progress + "%";
            document.getElementById("progress-text").innerText = progress + "% Completed";

            // Display missing fields
            const missingList = document.getElementById("missing-fields");
            missingList.innerHTML = ""; // Clear previous entries
            if (data.missing_fields.length > 0) {
                data.missing_fields.forEach((field, index) => {
                    const listItem = document.createElement("li");
                    listItem.textContent = `${index + 1}. ${formatFieldName(field)}`;
                    missingList.appendChild(listItem);
                });
            } else {
                missingList.innerHTML = "<li>All fields completed!</li>";
            }
        })
        .catch(error => console.error("Error fetching progress:", error));
}

// Function to format field names (replace underscores with spaces and capitalize first letter)
function formatFieldName(field) {
    return field.replace(/_/g, " ").replace(/\b\w/g, char => char.toUpperCase());
}

document.addEventListener("DOMContentLoaded", updateProgress);
