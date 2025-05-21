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
                                    <div id="seagoing-file-box" class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                        <a href="Uploads/Seaman/Seagoing/filename.file" download class="text-decoration-none fw-medium text-dark">
                                        File Name
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
                                    <div id="nonseagoing-file-box" class="uploaded-file-box border rounded p-3 mt-3 d-flex flex-column align-items-center justify-content-center text-center">
                                        <i class="fa-solid fa-file-lines text-primary mb-2" style="font-size: 24px;"></i>
                                        <a href="Uploads/Seaman/Land-Based-Exp/filename.file" download class="text-decoration-none fw-medium text-dark">
                                        File Name
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
                                        <td class="visa-num-label" data-label="Number"></td>
                                        <td class="visa-country-label" data-label="Country"></td>
                                        <td class="visa-issue-label" data-label="Start Date"></td>
                                        <td class="visa-valid-label" data-label="End Date"></td>
                                        <td class="visa-attachment-cell" data-label="Attachment">
                                            <div class="visa-attachment-content">
                                                <!-- <span>filename</span> -->
                                                <div class="download-wrapper">
                                                    <a href="Uploads/Seaman/Visa/" download>
                                                        <i class="fa-solid fa-download"></i> download
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td data-label="Document Name"><strong>Passport</strong></td>
                                        <td class="passport-num-label" data-label="Number"></td>
                                        <td class="passport-country-label" data-label="Country"></td>
                                        <td class="passport-issue-label" data-label="Start Date"></td>
                                        <td class="passport-valid-label" data-label="End Date"></td>
                                        <td class="passport-attachment-cell" data-label="Attachment">
                                            <div class="passport-attachment-content">
                                                <!-- <span>filename</span> -->
                                                <div class="download-wrapper">
                                                    <a href="Uploads/Seaman/Passport/" download>
                                                        <i class="fa-solid fa-download"></i> download
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Add another record -->
                                    <tr>
                                        <td data-label="Document Name"><strong>Seamans Book</strong></td>
                                        <td class="sbook-num-label" data-label="Number"></td>
                                        <td class="sbook-country-label" data-label="Country"></td>
                                        <td class="sbook-issue-label" data-label="Start Date"></td>
                                        <td class="sbook-valid-label" data-label="End Date"></td>
                                        <td class="sbook-attachment-cell" data-label="Attachment">
                                            <div class="sbook-attachment-content">
                                                <!-- <span>marine_diploma.pdf</span> -->
                                                <div class="download-wrapper">
                                                    <a href="Uploads/Seaman/SBook/" download>
                                                        <i class="fa-solid fa-download"></i> download
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
                                        <td data-label="Document Name"><strong class="certificate-name-label"></strong></td>
                                        <td class="certificate-num-label" data-label="Number"></td>
                                        <td class="certificate-country-label" data-label="Country"></td>
                                        <td class="certificate-start-label" data-label="Start Date"></td>
                                        <td class="certificate-end-label" data-label="End Date"></td>
                                        <td class="certificate-attachment-cell" data-label="Attachment">
                                            <div class="certificate-attachment-content">
                                                
                                                <div class="download-wrapper">
                                                    <a href="files/attachment-content" download><i class="fa-solid fa-download"></i> download</a>
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
                                        <td data-label="School"><strong class="school-name-label"></strong></td>
                                        <td class="school-field-label" data-label="Field of Study"></td>
                                        <td class="school-educlevel-label" data-label="Educational Level"></td>
                                        <td class="school-start-label" data-label="Start Date">2020</td>
                                        <td class="school-end-label" data-label="End Date">2024</td>
                                        <td class="school-attachment-cell" data-label="Attachment">
                                            <div class="school-attachment-content">
                                                
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