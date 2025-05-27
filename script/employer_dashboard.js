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


// document.addEventListener("DOMContentLoaded", function () {
//     const editButtons = document.querySelectorAll(".edit-job-btn");

//     editButtons.forEach(button => {
//         button.addEventListener("click", function () {
//             const jobCode = this.getAttribute("data-job-code");

//             if (!jobCode) {
//                 console.error("Job code is missing.");
//                 return;
//             }

//             // Fetch job details via AJAX
//             fetch(`includes/get_job_details.php?job_code=${jobCode}`)
//                 .then(response => response.json())
//                 .then(data => {
//                     if (data.error) {
//                         console.error(data.error);
//                         return;
//                     }
//                     // Populate the modal with job details
//                     document.getElementById("editJobTitle").value = data.job_title;
//                     document.getElementById("editRank").value = data.rank;
//                     document.getElementById("editContractLength").value = data.contract;
//                     document.getElementById("editVesselType").value = data.vessel;
//                     document.getElementById("editJobRequirements").value = data.requirements;
//                     document.getElementById("editJobDescription").value = data.job_description;
//                     document.getElementById("editJobCode").value = data.code; // Hidden input for job ID

//                     // Pre-select dropdown values
//                     const jobTitleSelect = document.getElementById("editJobTitle");
//                     const rankSelect = document.getElementById("editRank");
//                     const vesselTypeSelect = document.getElementById("editVesselType");

//                     Array.from(jobTitleSelect.options).forEach(option => {
//                         if (option.value === data.job_title) {
//                             option.selected = true;
//                         }
//                     });

//                     Array.from(rankSelect.options).forEach(option => {
//                         if (option.value === data.rank) {
//                             option.selected = true;
//                         }
//                     });

//                     Array.from(vesselTypeSelect.options).forEach(option => {
//                         if (option.value === data.vessel) {
//                             option.selected = true;
//                         }
//                     });
//                 })
//                 .catch(error => console.error("Error fetching job details:", error));
//         });
//     });

// });

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
            // let spinner = document.getElementById("applicantLoadingSpinner");
            // if (!spinner) {
            //     spinner = document.createElement("div");
            //     spinner.id = "applicantLoadingSpinner";
            //     spinner.style.position = "fixed";
            //     spinner.style.top = "0";
            //     spinner.style.left = "0";
            //     spinner.style.width = "100vw";
            //     spinner.style.height = "100vh";
            //     spinner.style.background = "rgba(255,255,255,0.7)";
            //     spinner.style.zIndex = "2000";
            //     spinner.style.display = "flex";
            //     spinner.style.justifyContent = "center";
            //     spinner.style.alignItems = "center";
            //     spinner.innerHTML = `
            //         <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
            //             <span class="visually-hidden">Loading...</span>
            //         </div>
            //     `;
            //     document.body.appendChild(spinner);
            // } else {
            //     spinner.style.display = "flex";
            // }

            if (jobSeekerId) {
                // Mark as viewed (AJAX)
                // fetch('includes/mark_applicant_viewed.php', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                //     body: 'id=' + encodeURIComponent(jobSeekerId) +
                //         '&job_code=' + encodeURIComponent(document.getElementById('jobSelect').value)
                // });

                // Show the modal loading spinner overlay
                const spinner = document.getElementById('editJobLoadingSpinner');
                if (spinner) spinner.style.display = 'flex';

                // Optionally clear modal content fields here

                fetch(`includes/fetch_applicant_profile.php?id=${encodeURIComponent(jobSeekerId)}`)
                    .then(res => res.json())
                    .then(data => {
                        // Hide spinner when data is loaded
                        if (spinner) spinner.style.display = 'none';

                        // Name
                        if (data.first_name) {
                            const nameField = document.querySelector('#applicant-profile-modal .user-profile-header h3');
                            const middleInitial = data.middle_name ? data.middle_name.charAt(0).toUpperCase() + '.' : '';
                            const fullName = `${data.first_name} ${middleInitial} ${data.last_name}`;
                            if (nameField) nameField.textContent = fullName;
                        }

                        // User photo
                        const photoElem = document.querySelector('#applicant-profile-modal .profile-pic img');
                        if (photoElem) {
                            let photoPath = "Uploads/Seaman/User-Photo/Portrait-placeholder.png";
                            if (data.user_photo && data.user_photo !== "") {
                                photoPath = "Uploads/Seaman/User-Photo/" + encodeURIComponent(data.user_photo);
                            }
                            photoElem.src = photoPath;
                        }

                        // Address
                        const addressField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .address-label');
                        if (addressField) addressField.textContent = data.address || 'N/A';

                        // Gender
                        const genderField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .gender-label');
                        if (genderField) genderField.textContent = data.gender || 'N/A';

                        // Birthday + Age
                        const bdayField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .bday-age-label');
                        if (bdayField && data.birthday) {
                            const dateObj = new Date(data.birthday);
                            const options = { year: 'numeric', month: 'short', day: 'numeric' };
                            const formattedDate = dateObj.toLocaleDateString('en-US', options);
                            const today = new Date();
                            let age = today.getFullYear() - dateObj.getFullYear();
                            const m = today.getMonth() - dateObj.getMonth();
                            if (m < 0 || (m === 0 && today.getDate() < dateObj.getDate())) {
                                age--;
                            }
                            bdayField.textContent = `${formattedDate} (${age} years old)`;
                        } else if (bdayField) {
                            bdayField.textContent = 'N/A';
                        }

                        // Marital Status
                        const maritalField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .marital-status-label');
                        if (maritalField) maritalField.textContent = data.marital_status || 'N/A';

                        // Nationality
                        const nationalityField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .nationality-label');
                        if (nationalityField) nationalityField.textContent = data.nationality || 'N/A';

                        // Religion
                        const religionField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .religion-label');
                        if (religionField) religionField.textContent = data.religion || 'N/A';

                        // English Level
                        const englishField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .english-level-label');
                        if (englishField) englishField.textContent = data.english_level || 'N/A';

                        // Rank
                        const rankField = document.querySelector('#applicant-profile-modal .user-profile-header .rank-label');
                        if (rankField) rankField.textContent = data.rank || 'N/A';

                        // Email 
                        const emailField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .email-label');
                        if (emailField) emailField.textContent = data.email || 'N/A';

                        // Contact No. 
                        const contactField = document.querySelector('#applicant-profile-modal .user-profile-header .employer-view-profile-details .contact-label');
                        if (contactField) contactField.textContent = data.cellphone || 'N/A';

                        // Seafaring Exp 
                        const seafaringExpField = document.querySelector('#applicant-profile-modal .content-editIcon .experience-content');
                        if (seafaringExpField) {
                            if (data.seagoing_work) {
                                // Replace newlines with <br>
                                seafaringExpField.innerHTML = data.seagoing_work.replace(/\n/g, '<br>');
                            } else {
                                seafaringExpField.innerHTML = 'N/A';
                            }
                        }

                        // Seafaring Experience File Download
                        const seafaringFileBox = document.querySelector('#applicant-profile-modal #seagoing-file-box a');
                        if (seafaringFileBox) {
                            if (data.seagoing_doc_url && data.seagoing_doc_url !== "") {
                                // Set the download link and file name
                                seafaringFileBox.href = "Uploads/Seaman/Seagoing/" + encodeURIComponent(data.seagoing_doc_url);
                                seafaringFileBox.textContent = data.seagoing_doc_url;
                                seafaringFileBox.setAttribute('download', data.seagoing_doc_url);
                                seafaringFileBox.parentElement.style.display = '';
                            } else {
                                seafaringFileBox.href = "#";
                                seafaringFileBox.textContent = "No file uploaded";
                                seafaringFileBox.removeAttribute('download');
                                seafaringFileBox.parentElement.style.display = 'none';
                            }
                        }

                        // Non-Seafaring Exp 
                        const nonSeafaringExpField = document.querySelector('#applicant-profile-modal .content-editIcon .non-experience-content');
                        if (nonSeafaringExpField) {
                            if (data.non_seagoing_work) {
                                // Replace newlines with <br>
                                nonSeafaringExpField.innerHTML = data.non_seagoing_work.replace(/\n/g, '<br>');
                            } else {
                                nonSeafaringExpField.innerHTML = 'N/A';
                            }
                        }

                        // Non-Seafaring Experience File Download
                        const nonseafaringFileBox = document.querySelector('#applicant-profile-modal #nonseagoing-file-box a');
                        if (nonseafaringFileBox) {
                            if (data.landbased_doc_url && data.landbased_doc_url !== "") {
                                // Set the download link and file name
                                nonseafaringFileBox.href = "Uploads/Seaman/Land-Based-Exp/" + encodeURIComponent(data.landbased_doc_url);
                                nonseafaringFileBox.textContent = data.landbased_doc_url;
                                nonseafaringFileBox.setAttribute('download', data.landbased_doc_url);
                                nonseafaringFileBox.parentElement.style.display = '';
                            } else {
                                nonseafaringFileBox.href = "#";
                                nonseafaringFileBox.textContent = "No file uploaded";
                                nonseafaringFileBox.removeAttribute('download');
                                nonseafaringFileBox.parentElement.style.display = 'none';
                            }
                        }

                        // Passport No. 
                        const passportNumField = document.querySelector('#applicant-profile-modal .passport-num-label');
                        if (passportNumField) passportNumField.textContent = data.passport_no || 'N/A';

                        // Passport Country 
                        const passportCountryField = document.querySelector('#applicant-profile-modal .passport-country-label');
                        if (passportCountryField) passportCountryField.textContent = data.passport_country || 'N/A';

                        // Passport Issue Date
                        const passportIssueField = document.querySelector('#applicant-profile-modal .passport-issue-label');
                        if (passportIssueField) {
                            if (data.passport_issued && data.passport_issued !== '0000-00-00') {
                                const issueDate = new Date(data.passport_issued);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                passportIssueField.textContent = issueDate.toLocaleDateString('en-US', options);
                            } else {
                                passportIssueField.textContent = 'N/A';
                            }
                        }

                        // Passport Valid Date
                        const passportValidField = document.querySelector('#applicant-profile-modal .passport-valid-label');
                        if (passportValidField) {
                            if (data.passport_valid && data.passport_valid !== '0000-00-00') {
                                const validDate = new Date(data.passport_valid);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                passportValidField.textContent = validDate.toLocaleDateString('en-US', options);
                            } else {
                                passportValidField.textContent = 'N/A';
                            }
                        }

                        // Passport Attachment (file name and download)
                        const passportAttachmentCell = document.querySelector('#applicant-profile-modal .passport-attachment-cell .passport-attachment-content');
                        if (passportAttachmentCell) {
                            const fileNameSpan = passportAttachmentCell.querySelector('span');
                            const downloadLink = passportAttachmentCell.querySelector('a');
                            if (data.seaman_passport_url && data.seaman_passport_url !== "") {
                                
                                downloadLink.href = "Uploads/Seaman/Passport/" + encodeURIComponent(data.seaman_passport_url);
                                downloadLink.setAttribute('download', data.seaman_passport_url);
                                passportAttachmentCell.style.display = '';
                            } else {
                                
                                downloadLink.href = "#";
                                downloadLink.removeAttribute('download');
                                passportAttachmentCell.style.display = 'none';
                            }
                        }

                        // sbook No. 
                        const sbookNumField = document.querySelector('#applicant-profile-modal .sbook-num-label');
                        if (sbookNumField) sbookNumField.textContent = data.sbook_no || 'N/A';

                        // Visa No. 
                        const visaNumField = document.querySelector('#applicant-profile-modal .visa-num-label');
                        if (visaNumField) visaNumField.textContent = data.visa_no || 'N/A';

                        // sbook Country 
                        const sbookCountryField = document.querySelector('#applicant-profile-modal .sbook-country-label');
                        if (sbookCountryField) sbookCountryField.textContent = data.sbook_country || 'N/A';

                        // Visa Country 
                        const visaCountryField = document.querySelector('#applicant-profile-modal .visa-country-label');
                        if (visaCountryField) visaCountryField.textContent = data.visa_type_name || 'N/A';

                        // sbook Issue Date
                        const sbookIssueField = document.querySelector('#applicant-profile-modal .sbook-issue-label');
                        if (sbookIssueField) {
                            if (data.sbook_issued && data.sbook_issued !== '0000-00-00') {
                                const issueDate = new Date(data.sbook_issued);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                sbookIssueField.textContent = issueDate.toLocaleDateString('en-US', options);
                            } else {
                                sbookIssueField.textContent = 'N/A';
                            }
                        }

                        // visa Issue Date
                        const visaIssueField = document.querySelector('#applicant-profile-modal .visa-issue-label');
                        if (visaIssueField) {
                            if (data.visa_issued && data.visa_issued !== '0000-00-00') {
                                const issueDate = new Date(data.visa_issued);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                visaIssueField.textContent = issueDate.toLocaleDateString('en-US', options);
                            } else {
                                visaIssueField.textContent = 'N/A';
                            }
                        }

                        // sbook Valid Date
                        const sbookValidField = document.querySelector('#applicant-profile-modal .sbook-valid-label');
                        if (sbookValidField) {
                            if (data.sbook_valid && data.sbook_valid !== '0000-00-00') {
                                const validDate = new Date(data.sbook_valid);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                sbookValidField.textContent = validDate.toLocaleDateString('en-US', options);
                            } else {
                                sbookValidField.textContent = 'N/A';
                            }
                        }

                        // visa Valid Date
                        const visaValidField = document.querySelector('#applicant-profile-modal .visa-valid-label');
                        if (visaValidField) {
                            if (data.visa_valid && data.visa_valid !== '0000-00-00') {
                                const validDate = new Date(data.visa_valid);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                visaValidField.textContent = validDate.toLocaleDateString('en-US', options);
                            } else {
                                visaValidField.textContent = 'N/A';
                            }
                        }

                        // sbook Attachment (file name and download)
                        const sbookAttachmentCell = document.querySelector('#applicant-profile-modal .sbook-attachment-cell .sbook-attachment-content');
                        if (sbookAttachmentCell) {
                            const downloadLink = sbookAttachmentCell.querySelector('a');
                            if (data.sbook_url && data.sbook_url !== "") {
                                
                                downloadLink.href = "Uploads/Seaman/SBook/" + encodeURIComponent(data.sbook_url);
                                downloadLink.setAttribute('download', data.sbook_url);
                                sbookAttachmentCell.style.display = '';
                            } else {
                                
                                downloadLink.href = "#";
                                downloadLink.removeAttribute('download');
                                sbookAttachmentCell.style.display = 'none';
                            }
                        }

                        // visa Attachment (file name and download)
                        const visaAttachmentCell = document.querySelector('#applicant-profile-modal .visa-attachment-cell .visa-attachment-content');
                        if (visaAttachmentCell) {
                            const downloadLink = visaAttachmentCell.querySelector('a');
                            if (data.visa_url && data.visa_url !== "") {
                                
                                downloadLink.href = "Uploads/Seaman/Visa/" + encodeURIComponent(data.visa_url);
                                downloadLink.setAttribute('download', data.visa_url);
                                visaAttachmentCell.style.display = '';
                            } else {
                                
                                downloadLink.href = "#";
                                downloadLink.removeAttribute('download');
                                visaAttachmentCell.style.display = 'none';
                            }
                        }

                        // Certificate Name 
                        const certNameField = document.querySelector('#applicant-profile-modal .certificate-name-label');
                        if (certNameField) certNameField.textContent = data.cert_type_name || 'N/A';

                        // Certificate No. 
                        const certNumField = document.querySelector('#applicant-profile-modal .certificate-num-label');
                        if (certNumField) certNumField.textContent = data.cert_number || 'N/A';

                        // Certificate Country 
                        const certCountryField = document.querySelector('#applicant-profile-modal .certificate-country-label');
                        if (certCountryField) certCountryField.textContent = data.country || 'N/A';

                        // Certifcate Issue Date
                        const certIssueField = document.querySelector('#applicant-profile-modal .certificate-start-label');
                        if (certIssueField) {
                            if (data.start_date && data.start_date !== '0000-00-00') {
                                const issueDate = new Date(data.start_date);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                certIssueField.textContent = issueDate.toLocaleDateString('en-US', options);
                            } else {
                                certIssueField.textContent = 'N/A';
                            }
                        }

                        // Certificate Valid Date
                        const certValidField = document.querySelector('#applicant-profile-modal .certificate-end-label');
                        if (certValidField) {
                            if (data.end_date && data.end_date !== '0000-00-00') {
                                const validDate = new Date(data.end_date);
                                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                                certValidField.textContent = validDate.toLocaleDateString('en-US', options);
                            } else {
                                certValidField.textContent = 'N/A';
                            }
                        }

                        // Certificate Attachment (file name and download)
                        const certAttachmentCell = document.querySelector('#applicant-profile-modal .certificate-attachment-cell .certificate-attachment-content');
                        if (certAttachmentCell) {
                            const downloadLink = certAttachmentCell.querySelector('a');
                            if (data.file_path && data.file_path !== "") {
                                
                                downloadLink.href = "Uploads/Seaman/Certificate/" + encodeURIComponent(data.file_path);
                                downloadLink.setAttribute('download', data.file_path);
                                certAttachmentCell.style.display = '';
                            } else {
                                
                                downloadLink.href = "#";
                                downloadLink.removeAttribute('download');
                                certAttachmentCell.style.display = 'none';
                            }
                        }

                        // Dynamically populate EDUCATION table
                        const educationTableBody = document.querySelector('#applicant-profile-modal .education-section-modal:last-of-type .table-content tbody');
                        if (educationTableBody) {
                            educationTableBody.innerHTML = ''; // Clear previous rows

                            if (Array.isArray(data.educations) && data.educations.length > 0) {
                                data.educations.forEach(edu => {
                                    const fromDate = edu.from_date && edu.from_date !== '0000-00-00'
                                        ? new Date(edu.from_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                                        : 'N/A';
                                    const toDate = edu.to_date && edu.to_date !== '0000-00-00'
                                        ? new Date(edu.to_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                                        : 'N/A';
                                    const fileName = edu.attachment_url ? edu.attachment_url : 'No file uploaded';
                                    const fileLink = edu.attachment_url
                                        ? `<a href="Uploads/Seaman/Education/${encodeURIComponent(edu.attachment_url)}" download="${edu.attachment_url}"><i class="fa-solid fa-download"></i> download</a>`
                                        : 'No file uploaded';

                                    educationTableBody.innerHTML += `
                                        <tr>
                                            <td data-label="School"><strong>${edu.school_name || 'N/A'}</strong></td>
                                            <td class="school-field-label" data-label="Field of Study">${edu.field_of_study || 'N/A'}</td>
                                            <td class="school-educlevel-label" data-label="Educational Level">${edu.educ_level || 'N/A'}</td>
                                            <td class="school-start-label" data-label="Start Date">${fromDate}</td>
                                            <td class="school-end-label" data-label="End Date">${toDate}</td>
                                            <td class="school-attachment-cell" data-label="Attachment">
                                                <div class="school-attachment-content">
                                                    <div class="download-wrapper">
                                                        ${fileLink}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                });
                            } else {
                                educationTableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center">No education records found.</td>
                                    </tr>
                                `;
                            }
                        }

                        if (data.member_type && data.member_type.toLowerCase() === "free") {
                            document.querySelectorAll('#applicant-profile-modal a[download]').forEach(link => {
                                link.removeAttribute('download');
                                link.href = "#";
                                link.classList.add('disabled');
                                link.style.pointerEvents = 'none';
                                link.style.opacity = '0.5';
                                // Add indication if not already present
                                if (!link.parentNode.querySelector('.free-indication')) {
                                    const span = document.createElement('span');
                                    span.className = 'free-indication ms-2 text-danger';
                                    span.textContent = '(Upgrade to download)';
                                    link.parentNode.appendChild(span);
                                }
                            });
                        }

                    })
                    .catch(() => {
                        if (spinner) spinner.style.display = 'none';
                    });
            }

            // Fetch applicant details via AJAX
            // fetch(`includes/get_applicant_details.php?job_seeker_id=${jobSeekerId}`)
            //     .then(response => response.json())
            //     .then(data => {

            //         spinner.style.display = "none";

            //         if (data.error) {
            //             console.error(data.error);
            //             alert(data.error);
            //             return;
            //         }

            //         // Debugging
            //         console.log("Data fetched:", data);

            //         // Construct the full path for the user photo
            //         const photoPath = data.user_photo 
            //             ? `Uploads/Seaman/User-Photo/${data.user_photo}` 
            //             : "Uploads/Seaman/User-Photo/Portrait_Placeholder.png";


            //         // Populate the modal with applicant details
            //         document.getElementById("applicantPhoto").src = photoPath;
            //         document.getElementById("applicantName").textContent = data.name || "N/A";
            //         document.getElementById("applicantRank").textContent = data.rank || "N/A";
            //         document.getElementById("applicantPhone").textContent = data.applicant_cellphone || "N/A";
            //         document.getElementById("applicantEmail").textContent = data.applicant_email || "N/A";
            //         document.getElementById("jobSeekerId").value = data.job_seeker_id || "N/A";
            //         document.getElementById("applicantJobTitle").textContent = data.job_title || "N/A";
            //         document.getElementById("applicantSeagoingExp").innerHTML = data.seagoing_work ? data.seagoing_work.replace(/\n/g, "<br>") : "N/A";
            //         document.getElementById("applicantNonSeagoingExp").innerHTML = data.non_seagoing_work ? data.non_seagoing_work.replace(/\n/g, "<br>") : "N/A";

            //         // Populate document links
            //         const documents = data.documents || {};
            //         document.querySelector(".document-item[data-doc='seaman_book'] a").href = documents.seaman_book 
            //             ? `Uploads/Seaman/SBook/${documents.seaman_book}` 
            //             : "#";
            //         document.querySelector(".document-item[data-doc='passport'] a").href = documents.passport 
            //             ? `Uploads/Seaman/Passport/${documents.passport}` 
            //             : "#";
            //         document.querySelector(".document-item[data-doc='competence'] a").href = documents.competence 
            //             ? `Uploads/Seaman/Competence/${documents.competence}` 
            //             : "#";
            //         document.querySelector(".document-item[data-doc='merits'] a").href = documents.merits 
            //             ? `Uploads/Seaman/Merits/${documents.merits}` 
            //             : "#";

            //         const visas = data.visas || [];
            //         document.querySelector(".document-item[data-doc='visa'] a").href = visas.visa
            //             ? `Uploads/Seaman/Visa/${visas.visa_url}` 
            //             : "#";

            //         const certificates = data.certificates || [];
            //         document.querySelector(".document-item[data-doc='certificate'] a").href = certificates.certificate
            //             ? `Uploads/Seaman/Certificate/${certificates.certificate_url}`
            //             : "#";
                    
            //         // Populate education table
            //         const educationTableBody = document.querySelector(".education-container .table-content tbody");
            //         educationTableBody.innerHTML = ""; // Clear existing rows

            //         if (data.education && data.education.length > 0) {
            //             data.education.forEach(edu => {
            //                 const row = document.createElement("tr");
            //                 row.innerHTML = `
            //                     <td data-label="School">${edu.school_name || "N/A"}</td>
            //                     <td data-label="Field of Study">${edu.field_of_study || "N/A"}</td>
            //                     <td data-label="Educational Level">${edu.educ_level || "N/A"}</td>
            //                     <td data-label="Start Date">${edu.from_date || "N/A"}</td>
            //                     <td data-label="End Date">${edu.to_date || "N/A"}</td>
            //                     <td class="attachment-cell" data-label="Attachment">
            //                         <div class="attachment-content">
            //                             <span>${edu.attachment_url || "N/A"}</span>
            //                             <div class="attachment-icons">
            //                                 <a href="Uploads/Seaman/Education/${edu.attachment_url}" download>
            //                                     <i class="fa-solid fa-download"></i>
            //                                 </a>
            //                             </div>
            //                         </div>
            //                     </td>
            //                 `;
            //                 educationTableBody.appendChild(row);
            //             });
            //         } else {
            //             const noDataRow = document.createElement("tr");
            //             noDataRow.innerHTML = `
            //                 <td colspan="6" class="text-center">No education records found.</td>
            //             `;
            //             educationTableBody.appendChild(noDataRow);
            //         }

            //     })
            //     .catch(error => {
            //         spinner.style.display = "none";
            //         console.error("Error fetching applicant details:", error);
            //     });
        });
    });
});
