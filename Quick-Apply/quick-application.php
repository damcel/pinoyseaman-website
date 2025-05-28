<?php
session_start();

// Retrieve messages from session, then clear them
$errorMessages = $_SESSION['errors'] ?? [];
$successMessage = $_SESSION['success'] ?? '';

unset($_SESSION['errors'], $_SESSION['success']);

include 'includes/dbh.inc.php';  
include 'includes/header.php';

// Fetch job categories and job positions from the database
$query = "SELECT category, job FROM seaman_jobs ORDER BY category ASC, job ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<body>
    <?php if (!empty($errorMessages)): ?>
        <div id="errorPopup" class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup('errorPopup')">&times;</span>
                <h2>Error</h2>
                <p id="errorMessage"><?= implode("<br>", array_map('htmlspecialchars', $errorMessages)); ?></p>
                <button onclick="closePopup('errorPopup')">Close</button>
            </div>
        </div>
    <?php elseif (!empty($successMessage)): ?>
        <div id="successPopup" class="popup-overlay">
            <div class="popup-content">
                <span class="popup-close" onclick="closePopup('successPopup')">&times;</span>
                <h2>Success</h2>
                <p><?= htmlspecialchars($successMessage) ?></p>
                <button onclick="closePopup('successPopup')">Close</button>
            </div>
        </div>
    <?php endif; ?>

  <div class="main-content">
    <div class="company-content">

        <form action="includes/seaman_init_reg2.php" method="POST">
            <div class="form-container">
                <h3 class="index-form">Quick Registration</h3>

                <div class="form-group-container">
                    <label for="prefer_job">Desired Job Position</label>
                    <select name="prefer_job" id="prefer_job" class="js-job-select" style="width: 100%;">
                        <option value=""></option> <!-- Keeps the Select2 placeholder -->

                        <!-- Hardcoded options -->
                        <option value="Master">-Master</option>
                        <option value="Chief Mate">-Chief Mate</option>
                        <option value="2nd Officer">-2nd Officer</option>
                        <option value="3rd Officer">-3rd Officer</option>
                        <option value="4rth Officer">-4rth Officer</option>
                        <option value="Chief Engineer">-Chief Engineer</option>
                        <option value="2nd Engineer">-2nd Engineer</option>
                        <option value="3rd Engineer">-3rd Engineer</option>
                        <option value="4rth Engineer">-4rth Engineer</option>
                        <option value="Bosun">- Bosun</option>
                        <option value="Able Bodied Seaman">- Able Bodied Seaman</option>
                        <option value="Ordinary Seaman">- Ordinary Seaman</option>
                        <option value="Deck Cadet">- Deck Cadet</option>
                        <option value="Deck Fitter">- Deck Fitter</option>
                        <option value="Engine Fitter">- Engine Fitter</option>
                        <option value="Motorman">- Motorman</option>
                        <option value="Oiler">- Oiler</option>
                        <option value="Wiper">- Wiper</option>
                        <option value="Engine Cadet">- Engine Cadet</option>
                        <option value="Messman">- Messman</option>
                        <option value="Chief Cook">- Chief Cook</option>

                        <!-- Dynamic options from DB -->
                        <?php foreach ($jobs as $job): ?>
                            <option value="<?= htmlspecialchars($job['job']) ?>">
                                <?= htmlspecialchars($job['category']) . " - " . htmlspecialchars($job['job']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="col form-group-container">
                        <label>First name</label>
                        <input type="text" name="first_name" placeholder="Juan">
                    </div>
                    <div class="col form-group-container">
                        <label>Last name</label>
                        <input type="text" name="last_name" placeholder="Cruz">
                    </div>
                </div>

                <div class="form-group-container">
                    <label>Phone</label>
                    <input type="tel" name="phone" placeholder="11 digits">
                </div>

                <div class="form-row">
                    <div class="col form-group-container">
                        <label>Date of Birth</label>
                        <input type="date" name="birthday" value="2000-01-01">
                    </div>
                    <div class="col form-group-container">
                        <label>Gender</label>
                        <select name="gender">
                        <option>-Select Gender-</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-container">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="example@mail.com">
                </div>

                <div class="form-group-container form-btn">
                    <button class="btn-register" type="submit">Quick Apply</button>
                </div>
            </div>
        </form>
    </div>
  </div>
</body>
<script src="js/popup.js"></script>
<script>
    $(document).ready(function() {
    $('.js-job-select').select2({
        placeholder: "Select or type a position",
        tags: true,
        allowClear: true
    });

    // Add placeholder to search input inside Select2 dropdown
    $('.js-job-select').on('select2:open', function () {
        let searchField = $('.select2-container--open .select2-search__field');
        searchField.attr('placeholder', 'Enter your Job Position');
    });
    });
</script>