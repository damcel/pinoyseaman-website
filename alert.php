<?php
$type = isset($_GET['type']) ? $_GET['type'] : 'info';
$message = isset($_GET['message']) ? $_GET['message'] : 'No message provided.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert</title>
    <style>
        /* Add your existing CSS styles here */
        .alert {
            padding: 15px;
            margin: 20px;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="alert <?php echo htmlspecialchars($type); ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <a href="../index.php">Go back to the homepage</a>
</body>
</html>