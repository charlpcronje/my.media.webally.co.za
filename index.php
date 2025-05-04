
<?php
// index.php
// Check if application is installed
$configFile = __DIR__ . '/backend/config.php';
$installed = false;

if (file_exists($configFile)) {
    // Check if config file contains database settings
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'DB_HOST') !== false) {
        $installed = true;
    }
}

// Redirect based on installation status
if (!$installed) {
    // Application not installed, redirect to setup
    header('Location: backend/setup.php');
    exit;
} else {
    // Application installed, redirect to frontend
    header('Location: frontend/dist/index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .buttons {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-primary {
            background-color: #0d6efd;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Media Share Application</h1>
        
        <?php if ($installed): ?>
            <p>The application is installed and ready to use. Please choose where you want to go:</p>
            <div class="buttons">
                <a href="frontend/dist/index.html" class="btn btn-primary">Frontend</a>
                <a href="backend/admin/index.php" class="btn btn-secondary">Admin Dashboard</a>
            </div>
        <?php else: ?>
            <p>The application is not installed yet. Please run the setup wizard:</p>
            <div class="buttons">
                <a href="backend/setup.php" class="btn btn-primary">Run Setup</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>