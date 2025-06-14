<?php
session_start();
include 'connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all employers with member_type 'y'
$sql = "SELECT company_code, company, phone, email, secret, member_type FROM employer WHERE verify = 'y' ORDER BY date_registered DESC";
$result = mysqli_query($link, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company_code'], $_POST['plan_duration'], $_POST['duration_count'])) {
    $company_code = mysqli_real_escape_string($link, $_POST['company_code']);
    $billing_type = mysqli_real_escape_string($link, $_POST['plan_duration']);
    $duration = (int) $_POST['duration_count'];

    $interval = $billing_type === 'monthly' ? '1 MONTH' : '1 YEAR';
    $today = date('Y-m-d');

    $interval = $billing_type === 'monthly' ? 'month' : ($billing_type === 'yearly' ? 'year' : '');
    $current_due_date = date('Y-m-d'); // Start from today

    for ($i = 0; $i < $duration; $i++) {
        $status = $i === 0 ? 'paid' : 'unpaid';

        $insert = "INSERT INTO billing (company_code, billing_type, duration, billing_cycle, status, due_date)
                VALUES ('$company_code', '$billing_type', '$duration', '".($i+1)."', '$status', '$current_due_date')";
        mysqli_query($link, $insert);

        $current_due_date = date('Y-m-d', strtotime("+1 $interval", strtotime($current_due_date)));
    }

    // Update member_type to PREMIUM
    $update = "UPDATE employer SET member_type = 'PREMIUM' WHERE company_code = '$company_code'";
    mysqli_query($link, $update);

    echo "<script>window.location.href = window.location.href;</script>"; // Refresh to reflect changes
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/admin-panel-employer.css">
    <link rel="icon" href="../Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>

</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="admin-sidebar">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="../pinoyseaman-logo/admin-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
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
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr data-company-code="<?php echo htmlspecialchars($row['company_code']); ?>">
                            <td data-label="Company"><?php echo htmlspecialchars($row['company']); ?></td>
                            <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td data-label="password"><?php echo htmlspecialchars($row['secret']); ?></td>
                            <td data-label="Premium-duration" class="duration-td d-flex align-items-center justify-content-between">
                                <h6 class="mb-0"><?php echo htmlspecialchars($row['member_type']); ?></h6>
                                <button 
                                    class="btn btn-sm custom-edit-btn ms-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#premiumPlanModal" 
                                    title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                            <td><button class="delete-btn">Delete</button></td>
                        </tr>
                    <?php endwhile; ?>
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
                            <input type="hidden" name="company_code" id="companyCodeInput">

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
document.addEventListener("DOMContentLoaded", () => {
    const table = document.querySelector(".table-content tbody");
    const rows = Array.from(table.querySelectorAll("tr"));
    const rowsPerPage = 10;
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / rowsPerPage);

    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? "" : "none";
        });

        renderPagination();
    }

    function renderPagination() {
        pagination.innerHTML = "";

        const createButton = (label, page) => {
            const btn = document.createElement("button");
            btn.textContent = label;
            btn.className = "btn btn-sm btn-outline-primary mx-1";
            if (page === currentPage) {
                btn.classList.add("active");
            }
            btn.addEventListener("click", () => showPage(page));
            return btn;
        };

        const addEllipsis = () => {
            const span = document.createElement("span");
            span.textContent = "...";
            span.className = "mx-1 text-muted";
            pagination.appendChild(span);
        };

        // First Page
        if (currentPage > 2) {
            pagination.appendChild(createButton("1", 1));
            if (currentPage > 3) addEllipsis();
        }

        // Middle Pages
        for (let i = currentPage - 1; i <= currentPage + 1; i++) {
            if (i > 0 && i <= totalPages) {
                pagination.appendChild(createButton(i, i));
            }
        }

        // Last Page
        if (currentPage < totalPages - 1) {
            if (currentPage < totalPages - 2) addEllipsis();
            pagination.appendChild(createButton(totalPages, totalPages));
        }
    }

    // Initial page load
    showPage(currentPage);
});
</script>

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
<script>
    document.querySelectorAll('.custom-edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const tr = this.closest('tr');
            const companyCode = tr.getAttribute('data-company-code');
            document.getElementById('companyCodeInput').value = companyCode;
        });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"></script>
      
</body>
</html>