document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".applicant-card");
    const viewAllButton = document.querySelector(".view-all button");

    let isExpanded = false;

    function showLimitedCards() {
    cards.forEach((card, index) => {
        card.style.display = index < 4 ? "flex" : "none";
    });
    viewAllButton.textContent = "View all →";
    isExpanded = false;
    }

    function showAllCards() {
    cards.forEach(card => {
        card.style.display = "flex";
    });
    viewAllButton.textContent = "View less ↑";
    isExpanded = true;
    }

    // Initial state
    showLimitedCards();

    viewAllButton.addEventListener("click", (e) => {
    e.preventDefault();
    isExpanded ? showLimitedCards() : showAllCards();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-job-btn");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const jobCode = this.getAttribute("data-job-code");

            if (!jobCode) {
                console.error("Job code is missing.");
                return;
            }

            // Fetch job details via AJAX
            fetch(`includes/get_job_details.php?job_code=${jobCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    // Populate the modal with job details
                    document.getElementById("editJobTitle").value = data.job_title;
                    document.getElementById("editRank").value = data.rank;
                    document.getElementById("editContractLength").value = data.contract;
                    document.getElementById("editVesselType").value = data.vessel;
                    document.getElementById("editJobRequirements").value = data.requirements;
                    document.getElementById("editJobDescription").value = data.job_description;
                    document.getElementById("editJobCode").value = data.code; // Hidden input for job ID

                    // Pre-select dropdown values
                    const jobTitleSelect = document.getElementById("editJobTitle");
                    const rankSelect = document.getElementById("editRank");
                    const vesselTypeSelect = document.getElementById("editVesselType");

                    Array.from(jobTitleSelect.options).forEach(option => {
                        if (option.value === data.job_title) {
                            option.selected = true;
                        }
                    });

                    Array.from(rankSelect.options).forEach(option => {
                        if (option.value === data.rank) {
                            option.selected = true;
                        }
                    });

                    Array.from(vesselTypeSelect.options).forEach(option => {
                        if (option.value === data.vessel) {
                            option.selected = true;
                        }
                    });
                })
                .catch(error => console.error("Error fetching job details:", error));
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {
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

    applicantCards.forEach(card => {
        card.addEventListener("click", function () {
            const jobSeekerId = this.getAttribute("data-job-seeker-id");

            // Show loading spinner
            let spinner = document.getElementById("applicantLoadingSpinner");
            if (!spinner) {
                spinner = document.createElement("div");
                spinner.id = "applicantLoadingSpinner";
                spinner.style.position = "fixed";
                spinner.style.top = "0";
                spinner.style.left = "0";
                spinner.style.width = "100vw";
                spinner.style.height = "100vh";
                spinner.style.background = "rgba(255,255,255,0.7)";
                spinner.style.zIndex = "2000";
                spinner.style.display = "flex";
                spinner.style.justifyContent = "center";
                spinner.style.alignItems = "center";
                spinner.innerHTML = `
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `;
                document.body.appendChild(spinner);
            } else {
                spinner.style.display = "flex";
            }

            // Fetch applicant details via AJAX
            fetch(`includes/get_applicant_details.php?job_seeker_id=${jobSeekerId}`)
                .then(response => response.json())
                .then(data => {

                    spinner.style.display = "none";

                    if (data.error) {
                        console.error(data.error);
                        alert(data.error);
                        return;
                    }

                    // Debugging
                    console.log("Data fetched:", data);

                    // Construct the full path for the user photo
                    const photoPath = data.user_photo 
                        ? `Uploads/Seaman/User-Photo/${data.user_photo}` 
                        : "Uploads/Seaman/User-Photo/Portrait_Placeholder.png";


                    // Populate the modal with applicant details
                    document.getElementById("applicantPhoto").src = photoPath;
                    document.getElementById("applicantName").textContent = data.name || "N/A";
                    document.getElementById("applicantRank").textContent = data.rank || "N/A";
                    document.getElementById("applicantPhone").textContent = data.applicant_cellphone || "N/A";
                    document.getElementById("applicantEmail").textContent = data.applicant_email || "N/A";
                    document.getElementById("applicantId").value = data.job_seeker_id || "N/A";
                    document.getElementById("applicantJobTitle").textContent = data.job_title || "N/A";
                    document.getElementById("applicantSeagoingExp").innerHTML = data.seagoing_work ? data.seagoing_work.replace(/\n/g, "<br>") : "N/A";
                    document.getElementById("applicantNonSeagoingExp").innerHTML = data.non_seagoing_work ? data.non_seagoing_work.replace(/\n/g, "<br>") : "N/A";

                    // Populate document links
                    const documents = data.documents || {};
                    document.querySelector(".document-item[data-doc='seaman_book'] a").href = documents.seaman_book 
                        ? `Uploads/Seaman/SBook/${documents.seaman_book}` 
                        : "#";
                    document.querySelector(".document-item[data-doc='passport'] a").href = documents.passport 
                        ? `Uploads/Seaman/Passport/${documents.passport}` 
                        : "#";
                    document.querySelector(".document-item[data-doc='competence'] a").href = documents.competence 
                        ? `Uploads/Seaman/Competence/${documents.competence}` 
                        : "#";
                    document.querySelector(".document-item[data-doc='merits'] a").href = documents.merits 
                        ? `Uploads/Seaman/Merits/${documents.merits}` 
                        : "#";

                    const visas = data.visas || [];
                    document.querySelector(".document-item[data-doc='visa'] a").href = visas.visa
                        ? `Uploads/Seaman/Visa/${visas.visa_url}` 
                        : "#";

                    const certificates = data.certificates || [];
                    document.querySelector(".document-item[data-doc='certificate'] a").href = certificates.certificate
                        ? `Uploads/Seaman/Certificate/${certificates.certificate_url}`
                        : "#";
                    
                    // Populate education table
                    const educationTableBody = document.querySelector(".education-container .table-content tbody");
                    educationTableBody.innerHTML = ""; // Clear existing rows

                    if (data.education && data.education.length > 0) {
                        data.education.forEach(edu => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td data-label="School">${edu.school_name || "N/A"}</td>
                                <td data-label="Field of Study">${edu.field_of_study || "N/A"}</td>
                                <td data-label="Educational Level">${edu.educ_level || "N/A"}</td>
                                <td data-label="Start Date">${edu.from_date || "N/A"}</td>
                                <td data-label="End Date">${edu.to_date || "N/A"}</td>
                                <td class="attachment-cell" data-label="Attachment">
                                    <div class="attachment-content">
                                        <span>${edu.attachment_url || "N/A"}</span>
                                        <div class="attachment-icons">
                                            <a href="Uploads/Seaman/Education/${edu.attachment_url}" download>
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            `;
                            educationTableBody.appendChild(row);
                        });
                    } else {
                        const noDataRow = document.createElement("tr");
                        noDataRow.innerHTML = `
                            <td colspan="6" class="text-center">No education records found.</td>
                        `;
                        educationTableBody.appendChild(noDataRow);
                    }

                })
                .catch(error => {
                    spinner.style.display = "none";
                    console.error("Error fetching applicant details:", error);
                });
        });
    });
});
