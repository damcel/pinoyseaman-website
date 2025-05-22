document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-job-btn");
    const spinner = document.getElementById("editJobLoadingSpinner");

    const applicantCards = document.querySelectorAll(".applicant-card");
    // Delete Job Button Handler
    const deleteBtn = document.getElementById('deleteJobBtn');
    const deleteInput = document.getElementById('deleteJobInput');
    const editForm = document.querySelector('#edit-recent-job form');

    if (deleteBtn && deleteInput && editForm) {
        deleteBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm("Are you sure you want to delete this job? This action cannot be undone.")) {
                deleteInput.value = "1";
                editForm.submit();
            }
        });
    }

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Show spinner
            if (spinner) spinner.style.display = "flex";

            const jobCode = this.getAttribute("data-job-code");
            if (!jobCode) {
                if (spinner) spinner.style.display = "none";
                console.error("Job code is missing.");
                return;
            }

            // Fetch job details via AJAX
            fetch(`includes/get_job_details.php?job_code=${encodeURIComponent(jobCode)}`)
                .then(response => response.json())
                .then(data => {
                    if (spinner) spinner.style.display = "none";

                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Populate the modal with job details
                    const jobTitleSelect = document.getElementById("editJobTitle");
                    const rankSelect = document.getElementById("editRank");
                    const vesselTypeSelect = document.getElementById("editVesselType");

                    // Set values for text/textarea/hidden fields
                    document.getElementById("editContractLength").value = data.contract || "";
                    document.getElementById("editJobRequirements").value = data.requirements || "";
                    document.getElementById("editJobDescription").value = data.job_description || "";
                    document.getElementById("editJobCode").value = data.code || "";

                    // Pre-select dropdown values (reset first)
                    if (jobTitleSelect) {
                        Array.from(jobTitleSelect.options).forEach(option => {
                            option.selected = (option.value === data.job_title);
                        });
                    }
                    if (rankSelect) {
                        Array.from(rankSelect.options).forEach(option => {
                            option.selected = (option.value === data.rank);
                        });
                    }
                    if (vesselTypeSelect) {
                        Array.from(vesselTypeSelect.options).forEach(option => {
                            option.selected = (option.value === data.vessel);
                        });
                    }
                })
                .catch(error => {
                    if (spinner) spinner.style.display = "none";
                    alert("Error fetching job details.");
                    console.error("Error fetching job details:", error);
                });
        });
    });
});