<!------------------- Show user applicant profile ------------------------>
<section class="modal fade" id="applicantModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Applicant | Applied for - <span id="applicantJobTitle"></span></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Add this inside the modal-body -->
                <section class="modal-profile-card">
                    <div class="user-profile-header">
                        <div class="profile-pic">
                            <img src="" alt="Avatar" id="applicantPhoto">
                        </div>
                        <input type="text" id="applicantId" hidden>
                        <div class="user-profile-details">
                            <h3 id="applicantName">Name</h3>
                        
                            <div class="info-pair">
                                <p class="text-muted">Rank:</p>
                                <label id="applicantRank">N/A</label>
                            </div>

                            <div class="info-pair">
                                <p class="text-muted">Phone:</p>
                                <label id="applicantPhone">N/A</label>
                            </div>

                            <div class="info-pair">
                                <p class="text-muted">Email:</p>
                                <label id="applicantEmail">N/A</label>
                            </div>
                        
                            <span class="requirements-badge">
                                Complete Requirements <span class="info-icon">✔</span>
                            </span>
                        </div>
                        
                        <!-- <button type="button" class="btn btn-secondary contact-btn" id="contactPopoverBtn">
                            Contact
                        </button> -->
                    </div>
                
                    <section class="education-section">
                        <h2 class="header-info">Education</h2>
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
                                    <!-- <tr>
                                        <td data-label="School">University of Batangas</td>
                                        <td data-label="Field of Study">Information Technology</td>
                                        <td data-label="Educational Level">Bachelor's Degree</td>
                                        <td data-label="Start Date">2020</td>
                                        <td data-label="End Date">2024</td>
                                        <td class="attachment-cell" data-label="Attachment">
                                            <div class="attachment-content">
                                                <span>taengbinasateasdasda</span>
                                                <div class="attachment-icons">
                                                    <a href="files/attachment-content" download><i class="fa-solid fa-download"></i></i></a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr> -->
                                </tbody>                
                            </table>          
                            </div>
                    </section>               
                
                    <section class="experience-section">
                        <div>
                            <h5>Seafaring Experience</h5>
                            <p id="applicantSeagoingExp">Cadetship</p>
                            <!-- <small>Mar 2025 – Apr 2025</small> -->
                        </div>
                        <div>
                            <h5>Land-Based Work Experience</h5>
                            <p id="applicantNonSeagoingExp">Cadetship</p>
                            <!-- <small>Mar 2025 – Apr 2025</small> -->
                        </div>
                    </section>
                
                    <section class="documents-grid">
                        <div class="document-item" data-doc="seaman_book">
                            <strong>Seaman Book</strong><br>
                            <small>SeamanBook.pdf</small>
                            <span class="download-icon">
                            <a href="#" download>
                                <i class="fa-solid fa-download"></i>
                            </a>
                            </span>
                        </div>
                        <div class="document-item" data-doc="competence">
                            <strong>Competence</strong><br>
                            <small>Competence.pdf</small>
                            <span class="download-icon">
                                <a href="#" download>
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </span>
                        </div>
                        <div class="document-item" data-doc="visa">
                            <strong>Visas</strong><br>
                            <small>SeamanVisa.pdf</small>
                            <span class="download-icon">
                            <a href="#" download>
                                <i class="fa-solid fa-download"></i>
                            </a>
                            </span>
                        </div>
                        <div class="document-item" data-doc="certificate">
                            <strong>Certificate</strong><br>
                            <small>Certificate.pdf</small>
                            <span class="download-icon">
                            <a href="#" download>
                                <i class="fa-solid fa-download"></i>
                            </a>
                            </span>
                        </div>
                        <div class="document-item" data-doc="passport">
                            <strong>Seaman Passport</strong><br>
                            <small>SeamanPassport.pdf</small>
                            <span class="download-icon">
                                <a href="#" download>
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </span>
                        </div>
                        <div class="document-item" data-doc="merits">
                            <strong>Merits</strong><br>
                            <small>Merits.pdf</small>
                            <span class="download-icon">
                                <a href="#" download>
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </span>
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