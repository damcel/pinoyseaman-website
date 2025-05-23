<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "dbh.inc.php";

if (!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
    header("location: admin.php");
    exit;
}

// Fixed start date and current date as end date
$date_start = "2025-02-11";
$date_end = date("Y-m-d");

try {
    $query = "SELECT date, COUNT(*) as count FROM job_seeker WHERE date BETWEEN :date_start AND :date_end GROUP BY date ORDER BY date ASC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':date_start', $date_start);
    $stmt->bindParam(':date_end', $date_end);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($data)) {
        die("<h3 style='color: red;'>No data found for the selected date range.</h3>");
    }
} catch (PDOException $e) {
    die("<h3 style='color: red;'>Error executing query: " . $e->getMessage() . "</h3>");
}

// Prepare data for Chart.js
$dates = [];
$counts = [];
$totalRegistrations = 0;
$maxRegistrations = 0;
$minRegistrations = PHP_INT_MAX;

foreach ($data as $entry) {
    $dates[] = $entry['date'];
    $counts[] = $entry['count'];
    $totalRegistrations += $entry['count'];
    if ($entry['count'] > $maxRegistrations) {
        $maxRegistrations = $entry['count'];
    }
    if ($entry['count'] < $minRegistrations) {
        $minRegistrations = $entry['count'];
    }
}

$averageRegistrations = count($data) > 0 ? round($totalRegistrations / count($data), 2) : 0;

// CSV Export Function
if (isset($_POST['export_csv'])) {
    // Set CSV header
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="registrations.csv"');
    $output = fopen('php://output', 'w');
    
    // Add the header row
    fputcsv($output, ['Date', 'Number of Registrations']);
    
    // Add data rows
    foreach ($data as $entry) {
        fputcsv($output, [$entry['date'], $entry['count']]);
    }
    
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Graph of Total Newly Registered Seaman / Date</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js?ver=<?= time() ?>"></script>

<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
    }
    .chart-container {
        width: 80%;
        max-width: 800px;
        margin: auto;
        display: none; /* Initially hidden */
    }
    .summary {
        margin-top: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: inline-block;
        text-align: left;
    }
    .summary h2 {
        margin: 0 0 10px;
        font-size: 20px;
    }
    .summary p {
        margin: 5px 0;
        font-size: 16px;
    }
    .toggle-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .toggle-btn:hover {
        background-color: #0056b3;
    }
    table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid black;
    }
    th, td {
        padding: 10px;
        text-align: center;
    }
    th {
        background-color:rgb(54, 54, 54);
        color: white;
    }
    .sort-icon {
        margin-left: 5px;
        display: inline-block;
        transition: transform 0.2s ease-in-out;
    }
    .ascending::after {
        content: "▲";
    }
    .descending::after {
        content: "▼";
    }
    .export-btn {
        background-color: #28a745;
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        font-size: 16px;
        border-radius: 5px;
        display: inline-block;
        margin-left: 10px;
    }
    .export-btn:hover {
        background-color: #218838;
    }

</style>
</head>
<body>
<h1>Graph of Total Newly Registered Seaman / Date</h1>

<!-- Summary Section -->
<div class="summary">
    <h2>Summary (From <?= htmlspecialchars($date_start) ?> to <?= htmlspecialchars($date_end) ?>)</h2>
    <p><strong>Total Registered Seaman:</strong> <?= $totalRegistrations ?></p>
    <p><strong>Average Registrations per Day:</strong> <?= $averageRegistrations ?></p>
    <p><strong>Highest Daily Registration:</strong> <?= $maxRegistrations ?></p>
    <p><strong>Lowest Daily Registration:</strong> <?= $minRegistrations ?></p>
</div>

<!-- Export Button -->
<form method="post">
    <button type="submit" name="export_csv" class="toggle-btn">Export to CSV</button>
</form>

<!-- Toggle Button -->
<button class="toggle-btn" onclick="toggleChart()">Show Graph</button>

<!-- Chart -->
<div id="chartContainer" class="chart-container">
    <canvas id="seamanChart"></canvas>
</div>

<!-- Data Table -->
<table>
    <thead>
        <tr>
            <th onclick="sortTable(0, this)">Date (YYYY-MM-DD)<span class="sort-icon"></span></th>
            <th onclick="sortTable(1, this)">Number of Registrations <span class="sort-icon"></span></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $entry): ?>
        <tr>
            <td><?= htmlspecialchars($entry['date']) ?> (<?= date('D', strtotime($entry['date'])) ?>)</td>
            <td><?= htmlspecialchars($entry['count']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function toggleChart() {
        let chartContainer = document.getElementById("chartContainer");
        let toggleButton = document.querySelector(".toggle-btn");
        
        if (chartContainer.style.display === "none" || chartContainer.style.display === "") {
            chartContainer.style.display = "block";
            toggleButton.textContent = "Hide Graph";
        } else {
            chartContainer.style.display = "none";
            toggleButton.textContent = "Show Graph";
        }
    }

    // Initialize Chart
    const ctx = document.getElementById('seamanChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [{
                label: 'Total Registrations',
                data: <?= json_encode($counts) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `Registrations: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });

    function sortTable(columnIndex, header) {
        let table = document.querySelector("table tbody");
        let rows = Array.from(table.rows);
        let ascending = table.getAttribute("data-sort") !== columnIndex.toString();
        
        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText;
            let valB = b.cells[columnIndex].innerText;
    
            if (columnIndex === 0) { 
                return ascending ? new Date(valA) - new Date(valB) : new Date(valB) - new Date(valA);
            } else {
                return ascending ? valA - valB : valB - valA;
            }
        });
    
        table.innerHTML = "";
        rows.forEach(row => table.appendChild(row));
        table.setAttribute("data-sort", ascending ? columnIndex.toString() : "");
    
        // Remove sorting classes from all headers
        document.querySelectorAll(".sort-icon").forEach(icon => {
            icon.classList.remove("ascending", "descending");
        });
    
        // Add the correct class to the clicked header
        let icon = header.querySelector(".sort-icon");
        icon.classList.add(ascending ? "ascending" : "descending");
    }
</script>

</body>
</html>
