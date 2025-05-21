<!------------------- Show user applicant profile ------------------------>
<section class="modal fade" id="applicant-profile-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Loading Spinner for Modal -->
            <div id="editJobLoadingSpinner" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:10;justify-content:center;align-items:center;">
                <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Applicant Information</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add this inside the modal-body -->
                <section class="modal-profile-card">
                    <div class="user-profile-header">
                        <div class="profile-pic">
                            <img src="images/default-profile.png" alt="">
                        </div>
                        <div class="user-profile-details">
                            <div class="name-rank">
                                <h3></h3>
                                <div class="rank-bg">
                                    <span><i class="fa-solid fa-id-badge"></i> <span class="rank-label"></span></span>
                                </div>
                            </div> 
                            <div class="employer-view-profile-details">
                                <div class="info-pair">
                                    <h5>Personal Details</h5>
                                    <p class="text-muted">Address:  <label class="address-label"></label></p>
                                    <p class="text-muted">Gender: <label class="gender-label"></label></p>
                                    <p class="text-muted">Date of Birth: <label class="bday-age-label"></label></p>
                                    <p class="text-muted">Marital Status: <label class="marital-status-label"></label></p>
                                    <p class="text-muted">Nationality: <label class="nationality-label"></label></p>
                                    <p class="text-muted">Religion: <label class="religion-label"></label></p>
                                    <p class="text-muted">Level of English: <label class="english-level-label"></label></p>
                                </div>
                                <div class="info-pair">
                                    <h5>Contact Details</h5>
                                    <p class="text-muted">Email:  <label class="email-label"></label></p>
                                    <p class="text-muted">Contact No.: <label class="contact-label"></label></p>
                                </div>
                            </div>
                        </div>
                    </div>               
                
                    <section class="experience-section">
                        <section class="box-container">  
                            <h2 class="header-info">Seafaring Experience</h2> 
                            <div class="experience-box">
                                <div>
                                    <div class="content-editIcon">
                                        <p class="experience-content">
                                            
                                        </p>
                                        <span class="download-wrapper">
                                            <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></i></a>
                                        </span>
                                    </div>
                            
                                    <!-- Styled uploaded file box -->
                                    <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                        <a href="uploads/Resume_JohnDoe.pdf" download class="text-decoration-none fw-medium text-dark">
                                        Resume_JohnDoe.pdf
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                
                        <section class="box-container">
                            <h2 class="header-info">Land-Based Work Experience</h2>
                            <div class="experience-box">
                                <div>
                                    <div class="content-editIcon">
                                        <p class="non-experience-content">
                                            
                                        </p>
                                        <span class="download-wrapper">
                                            <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></i></a>
                                        </span>
                                    </div>
                            
                                    <!-- Styled uploaded file box -->
                                    <div class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                        <a href="uploads/Resume_JohnDoe.pdf" download class="text-decoration-none fw-medium text-dark">
                                        Resume_JohnDoe.pdf
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </section>

                    <section class="education-section-modal">
                        <h2 class="header-info-modal">SEAFARER DOCUMENT</h2>
                        <div class="education-container">
                            <table class="table-content">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Number</th>
                                        <th>Country</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Document Name"><strong>Visa</strong></td>
                                        <td data-label="Number">1234-4567</td>
                                        <td data-label="Country">Philippines</td>
                                        <td data-label="Start Date">2020</td>
                                        <td data-label="End Date">2024</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>taengbinasateasdasda</span>
                                                <div class="download-wrapper">
                                                    <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Add another record -->
                                    <tr>
                                        <td data-label="Document Name"><strong>Passport</strong></td>
                                        <td data-label="Number">1234-5678</td>
                                        <td data-label="Country">Philippines</td>
                                        <td data-label="Start Date">2018</td>
                                        <td data-label="End Date">2020</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>marine_diploma.pdf</span>
                                                <div class="download-wrapper">
                                                    <a href="files/marine_diploma.pdf" download>
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Add another record -->
                                    <tr>
                                        <td data-label="Document Name"><strong>Seamans Book</strong></td>
                                        <td data-label="Number">1234-5678</td>
                                        <td data-label="Country">Philippines</td>
                                        <td data-label="Start Date">2018</td>
                                        <td data-label="End Date">2020</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>marine_diploma.pdf</span>
                                                <div class="download-wrapper">
                                                    <a href="files/marine_diploma.pdf" download>
                                                        <i class="fa-solid fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>  
                    
                    <section class="education-section-modal">
                        <h2 class="header-info-modal">TRAINING & CERTIFICATIONS</h2>
                        <div class="education-container">
                            <table class="table-content">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Number</th>
                                        <th>Country</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Document Name"><strong>First Aid Certification</strong></td>
                                        <td data-label="Number">1234-4567</td>
                                        <td data-label="Country">Philippines</td>
                                        <td data-label="Start Date">2020</td>
                                        <td data-label="End Date">2039</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>taengbinasateasdasda</span>
                                                <div class="download-wrapper">
                                                    <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
        
                    <section class="education-section-modal">
                        <h2 class="header-info-modal">EDUCATION</h2>
                        <div class="education-container">
                            <table class="table-content">
                                <thead>
                                    <tr>
                                        <th>School</th>
                                        <th>Field of Study</th>
                                        <th>Educational Level</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="School"><strong>University of Batangas</strong></td>
                                        <td data-label="Field of Study">Information Technology</td>
                                        <td data-label="Educational Level">Bachelor's Degree</td>
                                        <td data-label="Start Date">2020</td>
                                        <td data-label="End Date">2024</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>taengbinasateasdasda</span>
                                                <div class="download-wrapper">
                                                    <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>                
                            </table>          
                            </div>
                    </section>
                </section>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="downloadAllFiles()">Download All Files</button>
            </div>
        </div>
    </div>
</section>