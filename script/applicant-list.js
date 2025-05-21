document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('searchApplicantsBtn').addEventListener('click', function () {
        const jobCode = document.getElementById('jobSelect').value;
        const rank = document.getElementById('rankSelect').value;
        const cardList = document.getElementById('applicantCardList');
        cardList.innerHTML = '<p>Loading...</p>';

        fetch(`includes/fetch_applicantList.php?job_code=${encodeURIComponent(jobCode)}&rank=${encodeURIComponent(rank)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    cardList.innerHTML = '<p class="text-muted">No applicants for this job yet.</p>';
                } else {
                    cardList.innerHTML = data.map(applicant => `
                        <article class="applicant-profile-card" data-applicant-id="${applicant.id}">
                            <div class="applicant-profile">
                                <div class="profile-information">
                                    <img src="${applicant.photo_path}" alt="applicant-profile-photo"
                                         style="width:60px;height:60px;object-fit:cover;" loading="lazy">
                                    <div class="important-details">
                                        <p>${applicant.name}</p>
                                        <p>Rank: ${applicant.rank}</p>
                                        <p>Seamans Validity: ${applicant.passport_valid}</p><p></p>
                                        <p>Passport Validity: ${applicant.sbook_valid}</p><p></p>
                                    </div>
                                </div>
                                <div>
                                    <button class="view-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal">View<i class="fa-solid fa-eye"></i></button>
                                </div>
                            </div>
                        </article>
                    `).join('');
                }
            })
            .catch(() => {
                cardList.innerHTML = '<p class="text-danger">Error loading applicants.</p>';
            });
    });

    document.getElementById('applicantCardList').addEventListener('click', function (e) {
        const btn = e.target.closest('.view-btn');
        if (btn) {
            const card = btn.closest('.applicant-profile-card');
            const applicantId = card.getAttribute('data-applicant-id');
            if (applicantId) {
                // Show the modal loading spinner overlay
                const spinner = document.getElementById('editJobLoadingSpinner');
                if (spinner) spinner.style.display = 'flex';

                // Optionally clear modal content fields here

                fetch(`includes/fetch_applicant_profile.php?id=${encodeURIComponent(applicantId)}`)
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
                    })
                    .catch(() => {
                        if (spinner) spinner.style.display = 'none';
                    });
            }
        }
    });

});