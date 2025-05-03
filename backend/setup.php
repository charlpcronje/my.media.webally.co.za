// backend/setup.php
<?php
// Check if application is already installed
$configFile = __DIR__ . '/config.php';
$installed = false;

if (file_exists($configFile)) {
    // Check if config file contains database settings
    $configContent = file_get_contents($configFile);
    if (strpos($configContent, 'DB_HOST') !== false) {
        $installed = true;
    }
}

// Setup stage tracking
$stage = isset($_GET['stage']) ? $_GET['stage'] : 'database';
$error = '';
$success = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($stage) {
        case 'database':
            // Validate database connection and create database
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_port = $_POST['db_port'] ?? '3306';
            $db_user = $_POST['db_user'] ?? 'root';
            $db_pass = $_POST['db_pass'] ?? '';
            $db_name = $_POST['db_name'] ?? 'my_media';
            
            try {
                // Connect to database server (without specific database)
                $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
                $conn = new PDO($dsn, $db_user, $db_pass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create database if it doesn't exist
                $conn->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Select the new database
                $conn->exec("USE `{$db_name}`");
                
                // Create tables
                $sqlScript = file_get_contents(__DIR__ . '/schema.sql');
                $conn->exec($sqlScript);
                
                // Generate config file
                $configTemplate = file_get_contents(__DIR__ . '/config.template.php');
                $configContent = str_replace(
                    ['{{DB_HOST}}', '{{DB_PORT}}', '{{DB_NAME}}', '{{DB_USER}}', '{{DB_PASS}}'],
                    [$db_host, $db_port, $db_name, $db_user, $db_pass],
                    $configTemplate
                );
                
                // Write config file
                if (file_put_contents($configFile, $configContent) === false) {
                    throw new Exception('Failed to write config file. Please check file permissions.');
                }
                
                $success = 'Database setup completed successfully!';
                header('Location: setup.php?stage=admin&success=' . urlencode($success));
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            break;
            
        case 'admin':
            // Create admin user
            $admin_user = $_POST['admin_user'] ?? '';
            $admin_pass = $_POST['admin_pass'] ?? '';
            $admin_pass_confirm = $_POST['admin_pass_confirm'] ?? '';
            
            if (empty($admin_user) || empty($admin_pass)) {
                $error = 'All fields are required';
            } else if ($admin_pass !== $admin_pass_confirm) {
                $error = 'Passwords do not match';
            } else {
                try {
                    // Load config
                    require_once $configFile;
                    
                    // Connect to database
                    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                    $conn = new PDO($dsn, DB_USER, DB_PASS);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Check if admin user table exists
                    $stmt = $conn->query("SHOW TABLES LIKE 'admin_users'");
                    if ($stmt->rowCount() == 0) {
                        throw new Exception('Admin users table not found. Please restart setup.');
                    }
                    
                    // Hash password
                    $passwordHash = password_hash($admin_pass, PASSWORD_DEFAULT);
                    
                    // Insert admin user
                    $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                    $stmt->execute([$admin_user, $passwordHash]);
                    
                    // Create necessary directories if they don't exist
                    $directories = [
                        __DIR__ . '/uploads',
                        __DIR__ . '/uploads/thumbnails',
                        __DIR__ . '/uploads/temp',
                        __DIR__ . '/logs'
                    ];
                    
                    foreach ($directories as $dir) {
                        if (!file_exists($dir)) {
                            if (!mkdir($dir, 0755, true)) {
                                throw new Exception("Failed to create directory: $dir");
                            }
                        }
                    }
                    
                    $success = 'Setup completed successfully!';
                    header('Location: setup.php?stage=complete&success=' . urlencode($success));
                    exit;
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .setup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        .setup-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .setup-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .setup-steps::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 30px;
            right: 30px;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        .step {
            width: 30px;
            height: 30px;
            background-color: #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }
        .step.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .step.completed {
            background-color: #198754;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="setup-logo">
            <h1>Media Share Setup</h1>
            <p class="text-muted">Let's get your application up and running</p>
        </div>
        
        <div class="setup-steps">
            <div class="step <?php echo $stage == 'database' ? 'active' : ($stage == 'admin' || $stage == 'complete' ? 'completed' : ''); ?>">1</div>
            <div class="step <?php echo $stage == 'admin' ? 'active' : ($stage == 'complete' ? 'completed' : ''); ?>">2</div>
            <div class="step <?php echo $stage == 'complete' ? 'active' : ''; ?>">3</div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($installed && $stage != 'complete'): ?>
        <div class="alert alert-warning">
            Application appears to be already installed. Continuing with setup will overwrite existing configuration.
        </div>
        <?php endif; ?>
        
        <?php if ($stage == 'database'): ?>
        <!-- Database Configuration -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Database Configuration</h5>
            </div>
            <div class="card-body">
                <form method="post" action="setup.php?stage=database">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Database Host</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_port" class="form-label">Database Port</label>
                        <input type="text" class="form-control" id="db_port" name="db_port" value="3306" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_name" class="form-label">Database Name</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" value="my_media" required>
                        <div class="form-text">Database will be created if it doesn't exist.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_user" class="form-label">Database Username</label>
                        <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="db_pass" class="form-label">Database Password</label>
                        <input type="password" class="form-control" id="db_pass" name="db_pass" value="">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Next: Admin Setup</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php elseif ($stage == 'admin'): ?>
        <!-- Admin User Setup -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Admin User Setup</h5>
            </div>
            <div class="card-body">
                <form method="post" action="setup.php?stage=admin">
                    <div class="mb-3">
                        <label for="admin_user" class="form-label">Admin Username</label>
                        <input type="text" class="form-control" id="admin_user" name="admin_user" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_pass" class="form-label">Admin Password</label>
                        <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_pass_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="admin_pass_confirm" name="admin_pass_confirm" required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Complete Setup</button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php elseif ($stage == 'complete'): ?>
        <!-- Setup Complete -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Setup Complete</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h4 class="alert-heading">Congratulations!</h4>
                    <p>Your Media Share application has been successfully set up.</p>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <a href="admin/index.php" class="btn btn-primary">Go to Admin Dashboard</a>
                    <a href="edit/index.php" class="btn btn-outline-primary">Go to Editor</a>
                    <a href="../index.html" class="btn btn-outline-secondary">View Frontend</a>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>