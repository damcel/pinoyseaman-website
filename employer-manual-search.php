<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
     <link rel="stylesheet" href="css/dashboard.css">
    <title>Dashboard</title>

    <style>

.summary-table {
  table-layout: fixed;
  width: 100%;
}

.experience-cell p{
  width: 250px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.experience-cell {
  width: 250px;
  white-space: normal;
  word-wrap: break-word;
  vertical-align: top;
}

.view-applicant-btn {
  display: flex;
  justify-content: center;
  align-items: center;
}

.view-applicant-btn button {
  padding: 6px 12px;
  background-color: #007bff;
  border: none;
  color: white;
  border-radius: 4px;
  cursor: pointer;
}

.search-bar-container {
  position: relative;
  width: 100%;
  max-width: 400px;
  margin: 10px 0 20px 0;
}

#jobSearchInput {
  width: 100%;
  padding: 10px 40px 10px 15px;
  border: 1px solid #ccc;
  border-radius: 25px;
  font-size: 14px;
  outline: none;
  transition: 0.3s ease;
}

#jobSearchInput:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
}

.search-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #888;
  pointer-events: none;
}

#pagination-jobposted {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  gap: 12px;
  flex-wrap: wrap;
}

#pagination-jobposted .pagination-btn {
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  font-size: 16px;
  color: #333;
}

#pagination-jobposted .pagination-btn:hover {
  background-color: #f0f0f0;
  border-color: #999;
}

#pageNumbers-jobposted {
  display: flex;
  gap: 8px; /* controls spacing between page numbers */
  align-items: center;
  justify-content: center;
}

#pagination-jobposted .page-number {
  background-color: #ffffff;
  border: 1px solid #ccc;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
  font-size: 16px;
  color: #333;
}

#pagination-jobposted .page-number:hover {
  background-color: #f0f0f0;
  border-color: #999;
}

#pagination-jobposted .page-number.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}


/* Make table responsive on small screens */
@media (max-width: 768px) {

    .dashboard-job-container, .job-list-container{
        padding: 0;
        margin: 10px 0;
    }
  .table-responsive {
    overflow-x: auto;
  }

  .summary-table {
    border-collapse: collapse;
    width: 100%;
    display: block;
  }
  .summary-table thead {
    display: none;
  }

  .summary-table tbody,
  .summary-table tr,
  .summary-table td {
    display: block;
    width: 100%;
  }

  .summary-table tr {
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    background-color: #f9f9f9;
  }

  .summary-table td {
    text-align: left;
    padding: 8px 10px;
    border: none;
    font-size: 14px;
  }

  .summary-table td::before {
    content: attr(data-label);
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
    color: #333;
  }

  .experience-cell p {
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    max-height: 4.5em; /* limit to 3 lines */
  }

  .view-applicant-btn {
    justify-content: flex-start;
    margin-top: 10px;
  }

  .view-applicant-btn button {
    width: 100%;
  }
}


    </style>
 
</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="sidebar-header">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="pinoyseaman-logo/pinoyseaman-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                    </a>
                </div>
                <button onclick="toggleSidebar()" id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0B1C33">
                        <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
                    </svg>
                </button>
            </div>
            <ul class="ul-links">
                <div class="company-profile-card">
                  <img src="company-logo/scanmar_big.jpg" alt="company-logo">
                </div>
              <li>
                <a href="employer-dashboard.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-briefcase"></i><span>Dashboard</span>
                </a>
              </li>
              <li>
                <a href="employer-posting.php">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-user"></i><span>Job Post</span>
                </a>
              </li>
              <li class="separator">
                <a href="employer-manual-search.html">
                  <!-- SVG Icon -->
                  <i class="fa-regular fa-user"></i><span>Manual Search</span>
                </a>
              </li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-container">
        <section class="header-container">
            <!-- Your existing saved and profile dropdown (unchanged) -->
            <div class="saved-ctn">
              <a href="#" class="saved-btn">
                <i class="fa-solid fa-book-bookmark"></i>
              </a>
            </div>
            <div class="dropdown-container">
              <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
              <div class="dropdown" id="dropdownMenu">
                <a href="employer-settings.php" class="prfl">Settings</a>
                <a href="includes/logout.php">Logout</a>
              </div>
            </div>
        </section>

        <section class="job-list-container">
            <div class="job-search-container">                  
                <section class="dashboard-job-container">
                    <div class="display-job-posted">
                        <div class="project-summary">
                            <div class="search-bar-container">
                                <input type="text" id="jobSearchInput" placeholder="Search by Fullname or Rank..." />
                                <i class="fa fa-search search-icon"></i>
                            </div>
                    
                          <div class="table-responsive">
                            <table class="summary-table" id="projectTable">
                              <thead class="job-posted-header">
                                <tr>
                                  <th>Fullname</th>
                                  <th>Rank</th>
                                  <th>Sea Going Experience</th>
                                </tr>
                              </thead>
                              <tbody id="tableBody">
                                <tr class="job-posted">
                                    <td data-label="Fullname">Donny Pangilinan</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell"><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore inventore, impedit fugiat, dolores saepe sapiente quisquam nobis fuga tempore obcaecati laboriosam non iste est iusto libero ipsum! Asperiores, aspernatur fugit!</p></td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">Donny Pangilinan</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell"><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore inventore, impedit fugiat, dolores saepe sapiente quisquam nobis fuga tempore obcaecati laboriosam non iste est iusto libero ipsum! Asperiores, aspernatur fugit!</p></td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">Donny Pangilinan</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell"><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore inventore, impedit fugiat, dolores saepe sapiente quisquam nobis fuga tempore obcaecati laboriosam non iste est iusto libero ipsum! Asperiores, aspernatur fugit!</p></td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">Donny Pangilinan</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell"><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore inventore, impedit fugiat, dolores saepe sapiente quisquam nobis fuga tempore obcaecati laboriosam non iste est iusto libero ipsum! Asperiores, aspernatur fugit!</p></td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">Donny Pangilinan</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>

                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>

                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                                <tr class="job-posted">
                                    <td data-label="Fullname">daniel</td>
                                    <td data-label="Rank">Messman</td>
                                    <td data-label="Experience" class="experience-cell">
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime non et, temporibus ut delectus quae. Eveniet architecto, possimus earum excepturi temporibus laudantium neque doloremque numquam non molestiae! Accusantium, quam facilis!</p>
                                    </td>
                                    <td data-label="information" class="view-applicant-btn" data-bs-toggle="modal" data-bs-target="#applicant-profile-modal"><button>view</button></td>
                                </tr>
                              </tbody>
                            </table>
                            <div class="pagination-container" id="pagination-jobposted">
                                <button id="prevPage-jobposted" class="pagination-btn" style="display: none;">
                                    <i class="fa fa-arrow-left"></i>
                                </button>
                                <div id="pageNumbers-jobposted" class="pagination-numbers"></div>
                                <button id="nextPage-jobposted" class="pagination-btn">
                                    <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                          </div>
                        </div>
                      </div>                  
                </section>
            </div>
        </section>


        <!------------------- Show user applicant profile ------------------------>
    <section class="modal fade" id="applicant-profile-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
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
                                    <h3>Juan Dela Cruz</h3>
                                    <div class="rank-bg">
                                      <span><i class="fa-solid fa-id-badge"></i> Chief Engineer</span>
                                    </div>
                                </div> 
                                <div class="employer-view-profile-details">
                                    <div class="info-pair">
                                        <h5>Personal Details</h5>
                                        <p class="text-muted">Address:  <label>Lipa City</label></p>
                                        <p class="text-muted">Gender: <label>Male</label></p>
                                        <p class="text-muted">Date of Birth <label>Aug 26, 2000 (24 years old)</label></p>
                                        <p class="text-muted">Marital Status: <label>Single</label></p>
                                        <p class="text-muted">Nationality: <label>Filipino</label></p>
                                        <p class="text-muted">Religion: <label>Catholic</label></p>
                                        <p class="text-muted">Level of English: <label>Intermidiate</label></p>
                                    </div>
                                    <div class="info-pair">
                                        <h5>Last Employment</h5>
                                        <p class="text-muted">Rank:  <label>N/A</label></p>
                                        <p class="text-muted">Vessel: <label>Vessel type</label></p>
                                        <p class="text-muted">Duration: <label>N/A</label></p>
                                    </div>
                                </div>
                            </div>
                          
                            <button type="button" class="btn btn-secondary contact-btn" id="contactPopoverBtn">
                                Contact
                            </button>
                        </div>               
                    
                        <section class="experience-section">
                            <section class="box-container">  
                                <h2 class="header-info">Seafaring Experience</h2> 
                                <div class="experience-box">
                                    <div>
                                        <div class="content-editIcon">
                                            <p class="experience-content">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
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
                                            <p class="experience-content">
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
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
                                            <td data-label="Document Name"><strong>Certificate of Completion</strong></td>
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

    </main>       
          
    <script src="script/dashboard-drop-jobslist.js"></script>
    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <!-- Bootstrap JS with Popper (near the end of body) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('jobSearchInput').addEventListener('input', function () {
          const searchValue = this.value.toLowerCase();
          const rows = document.querySelectorAll('#tableBody .job-posted');
      
          rows.forEach(row => {
            const fullname = row.querySelector('[data-label="Fullname"]').textContent.toLowerCase();
            const rank = row.querySelector('[data-label="Rank"]').textContent.toLowerCase();
            if (fullname.includes(searchValue) || rank.includes(searchValue)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const rowsPerPage = 10;
  const table = document.getElementById("projectTable");
  const tbody = table.querySelector("#tableBody");
  const rows = tbody.querySelectorAll("tr.job-posted");
  const totalRows = rows.length;
  const totalPages = Math.ceil(totalRows / rowsPerPage);
  let currentPage = 1;

  const prevBtn = document.getElementById("prevPage-jobposted");
  const nextBtn = document.getElementById("nextPage-jobposted");
  const pageNumbersContainer = document.getElementById("pageNumbers-jobposted");

  function displayPage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
      row.style.display = (index >= start && index < end) ? "" : "none";
    });

    currentPage = page;
    updateButtons();
    renderPageNumbers();
  }

  function updateButtons() {
    prevBtn.style.display = currentPage > 1 ? "inline-flex" : "none";
    nextBtn.style.display = currentPage < totalPages ? "inline-flex" : "none";
  }

  function renderPageNumbers() {
    pageNumbersContainer.innerHTML = "";
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement("button");
      pageBtn.className = "page-number" + (i === currentPage ? " active" : "");
      pageBtn.textContent = i;
      pageBtn.addEventListener("click", () => displayPage(i));
      pageNumbersContainer.appendChild(pageBtn);
    }
  }

  prevBtn.addEventListener("click", function () {
    if (currentPage > 1) {
      displayPage(currentPage - 1);
    }
  });

  nextBtn.addEventListener("click", function () {
    if (currentPage < totalPages) {
      displayPage(currentPage + 1);
    }
  });

  displayPage(1); // initial render
});
</script>


</body>
</html>