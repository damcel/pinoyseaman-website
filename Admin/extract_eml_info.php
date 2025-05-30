<?php
session_start();
include "./connect.php";

if(!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
  header("location: admin.php");
  exit;
} 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle clear EMLs request
if (isset($_POST['clear_emls'])) {
    $folder = __DIR__ . '/DH Quick Information EMLs 2025';
    $deletedFiles = 0;

    foreach (glob("$folder/*.eml") as $file) {
        if (unlink($file)) {
            $deletedFiles++;
        }
    }

    echo '<p style="color:red;">' . $deletedFiles . ' EML file(s) deleted.</p>';
}


// Handle ZIP file upload and extraction
if (isset($_FILES['zipFile']) && $_FILES['zipFile']['error'] === UPLOAD_ERR_OK) {
    $zipFile = $_FILES['zipFile']['tmp_name'];
    $targetDir = __DIR__ . '/DH Quick Information EMLs 2025';
    
    // Create directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $zip = new ZipArchive;
    if ($zip->open($zipFile)) {
        // Extract all files
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            // Only extract .eml files
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'eml') {
                $zip->extractTo($targetDir, $filename);
            }
        }
        $zip->close();
        echo '<p style="color:green;">ZIP file extracted successfully. Refresh the page to view extracted EMLs.</p>';
    } else {
        echo '<p style="color:red;">Failed to open the ZIP file.</p>';
    }
}

$emlFolder = __DIR__ . '/DH Quick Information EMLs 2025'; // Folder containing .eml files

// Fields to extract
$fields = [
    'Name', 'Date of Birth', 'Age', 'Rank / Position', 'Phone',
    'E-Mail', 'Nationality', 'Schengen Visa', 'Last Company',
    'Last Salary', 'Vessel Experience'
];

$rows = []; // Will store all extracted rows

// Scan .eml files
foreach (glob("$emlFolder/*.eml") as $filename) {
    $content = file_get_contents($filename);
    $lines = explode("\n", $content);
    $data = array_fill_keys($fields, '');

    foreach ($lines as $index => $line) {
        $line = trim($line);

        if (preg_match('/^Name\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Name'] = $matches[1];
        } elseif (preg_match('/^Date of Birth\s*:\s*([A-Za-z0-9 ,]+)\s*-\s*Age\s*:\s*(\d+)/i', $line, $matches)) {
            $data['Date of Birth'] = $matches[1];
            $data['Age'] = $matches[2];
        } elseif (preg_match('/^Rank\s*\/\s*Position\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Rank / Position'] = $matches[1];
        } elseif (preg_match('/^Phone\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Phone'] = $matches[1];
        } elseif (preg_match('/^E-Mail\s*:\s*(.+)$/i', $line, $matches)) {
            $data['E-Mail'] = $matches[1];
        } elseif (preg_match('/^Nationality\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Nationality'] = $matches[1];
        } elseif (preg_match('/^Schengen Visa\s*:\s*(.*)$/i', $line, $matches)) {
            $data['Schengen Visa'] = $matches[1];
        } elseif (preg_match('/^Last Company\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Last Company'] = $matches[1];
        } elseif (preg_match('/^Last Salary\s*:\s*(.+)$/i', $line, $matches)) {
            $data['Last Salary'] = $matches[1];
        } elseif (preg_match('/^Vessel Experience\s*:\s*$/i', $line)) {
            // Collect all lines after this that start with `-`
            $vesselLines = [];
            for ($i = $index + 1; $i < count($lines); $i++) {
                $vesselLine = trim($lines[$i]);
                if (preg_match('/^- (.+)/', $vesselLine, $vmatch)) {
                    $vesselLines[] = $vmatch[1];
                } else {
                    break;
                }
            }
            $data['Vessel Experience'] = implode(', ', $vesselLines);
        }
    }

    $rows[] = $data;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>EML Data Extract</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            caption-side: top;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .pagination, .limit-select {
            margin: 15px 0;
        }
        .pagination button {
            padding: 5px 10px;
            margin: 0 2px;
            cursor: pointer;
        }
        .limit-select select {
            padding: 5px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        #progress-container {
            width: 100%;
            background-color: #f1f1f1;
            margin-bottom: 20px;
            display: none;
        }
        #progress-bar {
            width: 0%;
            height: 30px;
            background-color: #4CAF50;
            text-align: center;
            line-height: 30px;
            color: white;
        }
        #progress-message {
            margin-bottom: 20px;
        }
        /* Add to your existing CSS */
        #zipUploadForm {
            margin: 20px 0;
            padding: 15px;
            background-color: #f0f8ff;
            border-radius: 5px;
        }

        #zipUploadForm input[type="file"] {
            margin-right: 10px;
        }

        #zipUploadStatus {
            margin-top: 10px;
            padding: 5px;
        }
    </style>
</head>
<body>

<h2>Extracted Data from EML Files</h2>

<h3>Upload ZIP File</h3>
<form id="zipUploadForm" enctype="multipart/form-data">
    <input type="file" name="zipFile" id="zipFile" accept=".zip" required>
    <button type="submit">Upload and Extract</button>
    <div id="zipUploadStatus"></div>
</form>

<!-- Clear EMLs Button -->
<form method="post" onsubmit="return confirm('Are you sure you want to delete all EML files?');">
    <button type="submit" name="clear_emls" style="background-color: red; color: white; padding: 8px 12px; margin-top: 10px;">
        Clear EMLs
    </button>
</form>


<!-- Progress bar container (initially hidden) -->
<div id="progress-container">
    <div id="progress-bar">0%</div>
</div>
<div id="progress-message"></div>
<div id="results"></div>

<?php if (count($rows) > 0): ?>
    <div class="limit-select">
        Show 
        <select id="rowLimit">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
        </select> entries
    </div>
    <form method="post" id="dataForm">

        <button type="submit" name="add_to_db" id="submitBtn">Add to Database</button>

        <table id="dataTable">
            <caption>Total Files Processed: <?= count($rows) ?></caption>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <?php foreach ($fields as $field): ?>
                        <th><?= htmlspecialchars($field) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $i => $row): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="row-checkbox" name="selected[<?= $i ?>]" value="1">
                            <?php foreach ($fields as $field): ?>
                                <input type="hidden" name="data[<?= $i ?>][<?= htmlspecialchars($field) ?>]" value="<?= htmlspecialchars($row[$field]) ?>">
                            <?php endforeach; ?>
                        </td>
                        <?php foreach ($fields as $field): ?>
                            <td><?= htmlspecialchars($row[$field]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </form>

    <div class="pagination" id="pagination"></div>
<?php else: ?>
    <p>No EML files found or no data extracted.</p>
<?php endif; ?>

<script>

    // Add this to your existing JavaScript
    document.getElementById('zipUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('zipFile');
        const statusDiv = document.getElementById('zipUploadStatus');
        const formData = new FormData();
        formData.append('zipFile', fileInput.files[0]);
        
        statusDiv.innerHTML = 'Uploading and extracting...';
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            statusDiv.innerHTML = text;
            // Refresh the page after successful extraction
            if (text.includes('successfully')) {
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch(error => {
            statusDiv.innerHTML = 'Error: ' + error.message;
        });
    });

    const table = document.getElementById('dataTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.rows);
    const pagination = document.getElementById('pagination');
    const rowLimitSelect = document.getElementById('rowLimit');
    let currentPage = 1;
    let rowsPerPage = 25;

    function renderTable() {
        let totalRows = rows.length;
        rows.forEach(row => row.style.display = 'none');

        let totalPages = Math.ceil(totalRows / rowsPerPage);
        if (rowLimitSelect.value === 'all') {
            rows.forEach(row => row.style.display = '');
            pagination.innerHTML = '';
            return;
        }

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        rows.slice(start, end).forEach(row => row.style.display = '');

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        pagination.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            if (i === currentPage) btn.disabled = true;
            btn.addEventListener('click', () => {
                currentPage = i;
                renderTable();
            });
            pagination.appendChild(btn);
        }
    }

    rowLimitSelect.addEventListener('change', () => {
        const val = rowLimitSelect.value;
        rowsPerPage = val === 'all' ? rows.length : parseInt(val);
        currentPage = 1;
        renderTable();
    });

    document.getElementById('selectAll').addEventListener('change', function() {
        // Get all visible checkboxes (only those in currently displayed rows)
        const visibleRows = Array.from(tbody.rows).filter(row => row.style.display !== 'none');
        const visibleCheckboxes = visibleRows.map(row => row.querySelector('.row-checkbox'));
        
        // Toggle all visible checkboxes
        visibleCheckboxes.forEach(cb => cb.checked = this.checked);
        
        // If unchecking, also uncheck the "Select All" checkbox
        if (!this.checked) {
            this.checked = false;
        }
    });

    // Initial render
    renderTable();
    

document.getElementById('dataForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const selectedCheckboxes = Array.from(document.querySelectorAll('.row-checkbox:checked'));
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one record.');
        return;
    }

    const progressContainer = document.getElementById('progress-container');
    const progressBar = document.getElementById('progress-bar');
    const progressMessage = document.getElementById('progress-message');
    const resultsDiv = document.getElementById('results');
    const submitBtn = document.getElementById('submitBtn');

    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
    progressMessage.textContent = 'Preparing data...';
    resultsDiv.innerHTML = '';
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';

    // Prepare all data first
    const selectedData = selectedCheckboxes.map(cb => {
        const row = cb.closest('tr');
        const inputs = row.querySelectorAll('input[type="hidden"]');
        const dataObj = {};
        inputs.forEach(input => {
            const match = input.name.match(/data\[[^\]]+\]\[([^\]]+)\]/);
            if (match && match[1]) {
                dataObj[match[1]] = input.value;
            }
        });
        return dataObj;
    });

    let processed = 0;
    let successCount = 0;
    let skipCount = 0;
    let errorCount = 0;
    const totalRecords = selectedData.length;

    function processNext() {
        if (processed >= totalRecords) {
            // Processing complete
            progressMessage.textContent = 'Processing complete!';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add to Database';
            
            // Display summary
            resultsDiv.innerHTML = `
                <div class="summary">
                    <h3>Processing Summary</h3>
                    <p>Total records processed: ${totalRecords}</p>
                    <p style="color:green;">Successfully inserted: ${successCount}</p>
                    <p style="color:orange;">Skipped (duplicate emails): ${skipCount}</p>
                    <p style="color:red;">Errors: ${errorCount}</p>
                </div>
            `;
            return;
        }

        const currentData = selectedData[processed];
        processed++;
        const progressPercent = Math.round((processed / totalRecords) * 100);
        progressBar.style.width = progressPercent + '%';
        progressBar.textContent = progressPercent + '%';
        progressMessage.textContent = `Processing record ${processed} of ${totalRecords}...`;

        fetch('insert_eml_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ data: currentData })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                successCount++;
            } else if (data.message === 'Duplicate email') {
                skipCount++;
            } else {
                errorCount++;
            }
            processNext();
        })
        .catch(error => {
            errorCount++;
            console.error('Error:', error);
            processNext();
        });
    }

    // Start processing
    processNext();
});


</script>

</body>
</html>