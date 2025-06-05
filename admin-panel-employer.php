<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>
    <style>

        .header-ctn{
            background-color: #FEFEFE;
            display: flex;
            justify-content: space-between; 
            padding: 15px 60px 15px 60px;
            align-items: center;
            position: relative;
            border-bottom: 1px solid #DAE3F8;
        }

        .admin-panel-ctn{
            background-color: #fefefe;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            height: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .span-style span{
            font-size: 14px;
        }

        .admin-sidebar {
        height: 60px;
        display: flex;
        align-items: center;
        padding: 5px;
        box-sizing: border-box;
        padding-bottom: 10px;
        margin-bottom: 10px;
        }

        .table-content th{
            background-color: gray;
            color: white;
            font-weight: 600;
            font-size: 16px;
            border-top: 1px solid #ccc; 
            border-left: 1px solid #ccc;
            border-right: 1px solid #ccc;
        }
            .table-content td {
            padding: 10px; /* Reduced padding to avoid large white space */
            text-align: left;
            border: 1px solid #ccc; /* Cell borders */
            word-break: break-word; /* Better than word-wrap */
            }

            .table-content td {
            font-size: 15px;
            font-weight: 400;
            }

            .delete-btn {
  background-color: #e74c3c; /* Red color */
  color: white;
  border: none;
  padding: 8px 16px;
  font-size: 14px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.delete-btn:hover {
  background-color: #c0392b;
  transform: scale(1.05);
}

.delete-btn:active {
  background-color: #a93226;
  transform: scale(0.98);
}

.pagination-controls {
  display: flex;
  justify-content: center;
  margin-top: 15px;
  gap: 8px;
}

.pagination-controls button {
  padding: 6px 12px;
  border: 1px solid #ccc;
  background-color: #f0f0f0;
  cursor: pointer;
  border-radius: 4px;
  font-size: 14px;
}

.pagination-controls button.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination-controls button:hover {
  background-color: #dcdcdc;
}

.search-wrapper {
            position: relative;
            width: 300px;
            margin-bottom: 10px;
        }

        #searchInput {
            padding: 8px 32px 8px 32px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
            font-size: 16px;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            max-height: 250px;
            overflow-y: auto;
            background: linear-gradient(to bottom, #ffffff, #f9f9f9);
            border: 1px solid #ccc;
            border-radius: 0 0 5px 5px;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .dropdown-list li {
            padding: 12px 16px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .dropdown-list li:hover {
            background-color: #e6f0ff;
        }

        h6{
            margin: 0;
        }


        .duration-td {
            display: flex;
            gap: 15px;
            border: none;
            padding: 0; /* Optional: match rest of the table styling */
        }
        .duration-td h6,
        .duration-td .btn {
            padding: 10px; /* Reapply spacing inside if needed */
        }

        .btn-primary{
            padding: 5px;
            margin: 0;
            align-items: center;
        }
    </style>
</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="admin-sidebar">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="pinoyseaman-logo/admin-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                    </a>
                </div>
                <button onclick="toggleSidebar()" id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0B1C33">
                        <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
                    </svg>
                </button>
            </div>
            <ul class="ul-links">
              <li class="menu-title">MENU</li>
              <li class="span-style">
                <a href="admin-panel-verification.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-user-check"></i><span>EMPLOYER VERIFICATION</span>
                </a>
              </li>
              <li  class="separator">
                <a href="admin-panel-seafarer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users"></i><span>SEAFARER</span>
                </a>
              </li>
              <li>
                <a href="admin-panel-employer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users-line"></i><span>EMPLOYER</span>
                </a>
              </li>
              <li class="separator">
                <a href="admin-panel-statistics.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-chart-simple"></i><span>STATISTICS</span>
                </a>
              </li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-container">
        <section class="header-ctn">
            <div class="search-ctn">
                <div class="search-ctn">
                    <div class="search-wrapper">
                      <i class="fa fa-search search-icon"></i>
                      <input type="text" id="searchInput" placeholder="Search by email, Company, or rank..." />
                      <ul id="searchResults" class="dropdown-list"></ul>
                    </div>
                </div>
            </div>
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="job-list-container">
            <div class="education-container">
                <table class="table-content">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Member Type</th>
                            <th>Account Deletion</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>

                        <tr>
                            <td data-label="Company">Luis Drum Studio</td>
                            <td data-label="Phone">09452733164</td>
                            <td data-label="Email">luisbelga@gmail.com</td>
                            <td data-label="password">Tambay</td>
                            <td data-label="Premium-duration" class="duration-td">
                                <h6>3 months</h6>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#premiumPlanModal"><i class="fa-solid fa-pen-to-square"></i></button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>
                    </tbody>                
                </table>    
                <div id="pagination" class="pagination-controls"></div>
            </div>      
        </section>

        <section class="modal fade" id="premiumPlanModal" tabindex="-1" aria-labelledby="premiumPlanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="premiumPlanModalLabel">Premium Plan Duration Settings</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <!-- Billing Type -->
                            <div class="mb-4">
                                <label for="planDuration" class="form-label fw-semibold">Billing Type</label>
                                <select class="form-select border-primary shadow-sm" id="planDuration" name="plan_duration" required>
                                    <option disabled selected value="">Select billing type</option>
                                    <option value="monthly">Per Month</option>
                                    <option value="yearly">Per Year</option>
                                </select>
                                <div class="form-text">Choose whether the plan bills monthly or yearly.</div>
                            </div>

                            <!-- Duration Count -->
                            <div class="mb-4">
                                <label for="durationCount" class="form-label fw-semibold">Number of Billing Cycles</label>
                                <select class="form-select border-primary shadow-sm" id="durationCount" name="duration_count" required>
                                    <option disabled selected value="">Select duration</option>
                                </select>
                                <div class="form-text">This depends on the billing type you selected above.</div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary w-100 py-2">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>


    </main>

    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script>
        const rowsPerPage =8;
        const table = document.querySelector(".table-content tbody");
        const rows = Array.from(table.querySelectorAll("tr"));
        const paginationContainer = document.getElementById("pagination");
      
        let currentPage = 1;
        const pageCount = Math.ceil(rows.length / rowsPerPage);
      
        function displayRows(page) {
          const start = (page - 1) * rowsPerPage;
          const end = start + rowsPerPage;
      
          rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? "" : "none";
          });
      
          currentPage = page;
          updatePaginationUI();
        }
      
        function updatePaginationUI() {
          paginationContainer.innerHTML = "";
      
          // ← Previous Arrow (only if currentPage > 1)
          if (currentPage > 1) {
            const prevBtn = document.createElement("button");
            prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
            prevBtn.addEventListener("click", () => displayRows(currentPage - 1));
            paginationContainer.appendChild(prevBtn);
          }
      
          // Page Number Buttons
          for (let i = 1; i <= pageCount; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === currentPage) btn.classList.add("active");
      
            btn.addEventListener("click", () => displayRows(i));
            paginationContainer.appendChild(btn);
          }
      
          // → Next Arrow (only if not on last page)
          if (currentPage < pageCount) {
            const nextBtn = document.createElement("button");
            nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            nextBtn.addEventListener("click", () => displayRows(currentPage + 1));
            paginationContainer.appendChild(nextBtn);
          }
        }
      
        // Initialize on load
        displayRows(1);
      </script>
      <script>
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");
    const allRows = document.querySelectorAll(".table-content tbody tr");

    searchInput.addEventListener("input", function () {
        const term = this.value.toLowerCase();
        searchResults.innerHTML = "";
        searchResults.style.display = "none";

        if (term.length === 0) {
            allRows.forEach(row => row.style.display = "");
            return;
        }

        let matchFound = false;

        allRows.forEach(row => {
            const email = row.querySelector("td[data-label='Email']").textContent.toLowerCase();
            const company = row.querySelector("td[data-label='Company']").textContent.toLowerCase();

            const isMatch = email.includes(term) || company.includes(term);

            row.style.display = isMatch ? "" : "none";

            if (isMatch) {
                matchFound = true;
                const name = `${company}, ${email}`;
                const li = document.createElement("li");
                li.textContent = name;
                li.addEventListener("click", () => {
                    searchInput.value = name;
                    searchResults.style.display = "none";
                });
                searchResults.appendChild(li);
            }
        });

        if (matchFound) {
            searchResults.style.display = "block";
        }
    });
</script>

<script>
    const planDuration = document.getElementById('planDuration');
    const durationCount = document.getElementById('durationCount');

    planDuration.addEventListener('change', function () {
        const type = this.value; // 'monthly' or 'yearly'
        const label = type === 'monthly' ? 'Month' : 'Year';
        
        // Clear existing options
        durationCount.innerHTML = '<option disabled selected value="">Select duration</option>';

        // Populate new options
        for (let i = 1; i <= 12; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `${i} ${label}${i > 1 ? 's' : ''}`;
            durationCount.appendChild(option);
        }
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
      
</body>
</html>