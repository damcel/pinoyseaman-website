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
                        <article class="applicant-profile-card">
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
});