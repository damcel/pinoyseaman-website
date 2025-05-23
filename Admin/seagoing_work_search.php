<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "includes/dbh.inc.php";

if (!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
    header("location: admin.php");
    exit;
}

$seagoing_work = isset($_POST['seagoing_work']) ? trim($_POST['seagoing_work']) : '';
?>

<html>
<head>
    <title>Seagoing Work Search</title>
    <?php include "./meta.php"; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 70%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        td {
            color: black;
        }
        .back-link {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
			height: 70%;
            text-align: left;
            border-radius: 10px;
            position: relative;
            color: black;
			overflow-y: auto;
        }
        .close {
            color: red;
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
        }
        .name-link {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }
        .name-link:hover {
            color: darkblue;
        }
        .see-more {
            color: blue;
            cursor: pointer;
            text-decoration: underline;
        }
        .see-more:hover {
            color: darkblue;
        }
    </style>
<script>
    function toggleText(id) {
        let shortText = document.getElementById("short-" + id);
        let fullText = document.getElementById("full-" + id);
        let link = document.getElementById("link-" + id);
        
        if (fullText.style.display === "none") {
            fullText.style.display = "inline";
            shortText.style.display = "none";
            link.innerText = "See less";
        } else {
            fullText.style.display = "none";
            shortText.style.display = "inline";
            link.innerText = "See more";
        }
    }
</script>
</head>
<body>
    <h2>Job Seekers Matching Seagoing Work</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Preferred Job</th>
            <th>Seagoing Work</th>
        </tr>

        <?php
// ... (your existing PHP code)

if (!empty($seagoing_work)) {
    $query = "SELECT * FROM job_seeker WHERE seagoing_work LIKE ?";
    $stmt = $pdo->prepare($query);
    $searchTerm = "%$seagoing_work%";
    $stmt->execute([$searchTerm]);

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $middleInitial = !empty($row["middle_name"]) ? strtoupper(substr($row["middle_name"], 0, 1)) . '.' : '';
            $fullName = htmlspecialchars($row["first_name"] . " " . $middleInitial . " " . $row["last_name"]);
            $jobSeekerId = $row["id"];
            
            $fullText = nl2br(htmlspecialchars($row["seagoing_work"])); // Apply nl2br and htmlspecialchars
            $shortText = substr(strip_tags($fullText), 0, 50); // Strip tags for the short preview
            $isLongText = strlen($fullText) > 50;

            echo "<tr>";
            echo "<td><span class='name-link' onclick=\"document.getElementById('modal-".htmlspecialchars($jobSeekerId)."').style.display='block'\">$fullName</span></td>";
            echo "<td>" . htmlspecialchars($row["prefer_job"]) . "</td>";
            echo "<td>";
            echo "<span id='short-$jobSeekerId'>" . htmlspecialchars($shortText) . ($isLongText ? "..." : "") . "</span>";
            if ($isLongText) {
                echo "<span id='full-$jobSeekerId' style='display: none;'>$fullText</span> ";
                echo "<span id='link-$jobSeekerId' class='see-more' onclick='toggleText(\"".htmlspecialchars($jobSeekerId)."\")'>See more</span>";
            }
            echo "</td>";
            echo "</tr>";

            // Modal for each job seeker
            echo "
            <div id='modal-$jobSeekerId' class='modal'>
                <div class='modal-content'>
                    <span class='close' onclick=\"document.getElementById('modal-$jobSeekerId').style.display='none'\">&times;</span>
                    <h3>Job Seeker Profile</h3>
                    <p><strong>Name:</strong> $fullName</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($row["cellphone"]) . "</p>
                    <p><strong>Seagoing Work:</strong> " . nl2br(htmlspecialchars($row["seagoing_work"])) . "</p>
                    <p><strong>Birthday:</strong> " . htmlspecialchars($row["birthday"]) . "</p>
                    <p><strong>Gender:</strong> " . htmlspecialchars($row["gender"]) . "</p>
                    <p><strong>City:</strong> " . htmlspecialchars($row["city"]) . "</p>
                    <p><strong>Date Registered (YYYY-MM-DD):</strong> " . htmlspecialchars($row["date"]) . "</p>
                    <p><strong>Marital Status:</strong> " . htmlspecialchars($row["status"]) . "</p>
                    <p><strong>Passport Country:</strong> " . htmlspecialchars($row["passport_country"]) . "</p>
                    <p><strong>Passport No.:</strong> " . htmlspecialchars($row["passport_no"]) . "</p>
                    <p><strong>Passport Issued:</strong> " . htmlspecialchars($row["passport_issued"]) . "</p>
                    <p><strong>Passport Valid:</strong> " . htmlspecialchars($row["passport_valid"]) . "</p>
                    <p><strong>Seaman's Book Country:</strong> " . htmlspecialchars($row["sbook_country"]) . "</p>
                    <p><strong>Seaman's Book No.:</strong> " . htmlspecialchars($row["sbook_no"]) . "</p>
                    <p><strong>Seaman's Book Issued:</strong> " . htmlspecialchars($row["sbook_issued"]) . "</p>
                    <p><strong>Seaman's Book Valid:</strong> " . htmlspecialchars($row["sbook_valid"]) . "</p>
                    <p><strong>Competence:</strong> " . htmlspecialchars($row["competence"]) . "</p>
                    <p><strong>Certificates:</strong> " . htmlspecialchars($row["certificates"]) . "</p>
                    <p><strong>Merits:</strong> " . htmlspecialchars($row["merits"]) . "</p>
                    <p><strong>Educational Training:</strong> " . htmlspecialchars($row["educ_training"]) . "</p>
                    <p><strong>Non-Seagoing Work:</strong> " . htmlspecialchars($row["non_seagoing_work"]) . "</p>
                    <p><strong>Preferred Job:</strong> " . htmlspecialchars($row["prefer_job"]) . "</p>
                </div>
            </div>";
        }
    } else {
        echo "<tr><td colspan='2' style='color: red;'>No matching records found.</td></tr>";
    }
} else {
    echo "<tr><td colspan='2' style='color: red;'>Please enter a search term.</td></tr>";
}
?>
    </table>

    <br>
    <a href="admin_panel.php" class="back-link">Back to Admin Page</a>
</body>
</html>
