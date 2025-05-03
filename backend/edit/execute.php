<?php
// backend/edit/execute.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Page title
$pageTitle = 'Execute Commands';

// Initialize variables
$command = '';
$output = '';
$error = '';
$success = false;

// Define allowed commands for security
$allowed_commands = [
    // PHP commands
    'php -v' => 'Get PHP version',
    'php -m' => 'List loaded PHP modules',
    'php -i' => 'PHP information',
    
    // Node.js commands
    'node -v' => 'Get Node.js version',
    'npm -v' => 'Get npm version',
    
    // Frontend commands
    'npm run dev' => 'Start frontend development server',
    'npm run build' => 'Build frontend for production',
    'npm install' => 'Install frontend dependencies',
    
    // Backend commands
    'composer install' => 'Install backend dependencies',
    'composer update' => 'Update backend dependencies',
    
    // System commands
    'ls -la' => 'List files with details',
    'df -h' => 'Show disk usage',
    'free -m' => 'Show memory usage',
    'uname -a' => 'System information'
];

// Custom commands with directory context
$custom_commands = [
    'frontend:dev' => [
        'name' => 'Start Frontend Dev Server',
        'cmd' => 'cd ../../frontend && npm run dev',
        'description' => 'Start the Vite development server for the frontend'
    ],
    'frontend:build' => [
        'name' => 'Build Frontend',
        'cmd' => 'cd ../../frontend && npm run build',
        'description' => 'Build the frontend for production'
    ],
    'frontend:install' => [
        'name' => 'Install Frontend Dependencies',
        'cmd' => 'cd ../../frontend && npm install',
        'description' => 'Install all frontend dependencies'
    ],
    'backend:test' => [
        'name' => 'Test API Endpoints',
        'cmd' => 'curl -s http://localhost/media-share/backend/api/media.php',
        'description' => 'Test the media API endpoint'
    ]
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['command'])) {
        $command = trim($_POST['command']);
        
        // Check if it's a custom command
        if (isset($_POST['custom_command']) && isset($custom_commands[$_POST['custom_command']])) {
            $customCmd = $custom_commands[$_POST['custom_command']];
            $command = $customCmd['cmd'];
            $isCustom = true;
        } else {
            $isCustom = false;
        }
        
        // Validate command (for non-custom commands)
        $command_allowed = $isCustom;
        if (!$isCustom) {
            foreach ($allowed_commands as $allowed => $description) {
                if (strpos($command, $allowed) === 0) {
                    $command_allowed = true;
                    break;
                }
            }
        }
        
        if (!$command_allowed) {
            $error = 'This command is not allowed for security reasons.';
        } else {
            // Execute command
            $output_lines = [];
            $return_val = 0;
            
            // Execute with output capturing
            exec($command . ' 2>&1', $output_lines, $return_val);
            
            if ($return_val !== 0) {
                $error = "Command execution failed with code $return_val";
                $output = implode(PHP_EOL, $output_lines);
            } else {
                $success = true;
                $output = implode(PHP_EOL, $output_lines);
            }
        }
    } else {
        $error = 'No command specified';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Media Share Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            padding: 20px;
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 1rem;
            font-family: monospace;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        .cmd-success {
            color: #4caf50;
            font-weight: bold;
        }
        .cmd-error {
            color: #f44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $pageTitle; ?></h1>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Editor
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Execute Command</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="command" class="form-label">Command</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="command" name="command" 
                                        value="<?php echo htmlspecialchars($command); ?>" required>
                                    <button type="submit" class="btn btn-primary">Execute</button>
                                </div>
                                <div class="form-text">
                                    Only allowed commands can be executed for security reasons.
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Commands</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" id="quickCommandForm">
                            <input type="hidden" name="command" id="quickCommandInput" value="">
                            <input type="hidden" name="custom_command" id="customCommandInput" value="">
                            
                            <div class="row g-3">
                                <?php foreach ($allowed_commands as $cmd => $desc): ?>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-between w-100 cmd-btn" data-command="<?php echo htmlspecialchars($cmd); ?>">
                                        <span class="text-start"><?php echo htmlspecialchars($cmd); ?></span>
                                        <i class="bi bi-arrow-right-circle ms-2"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Custom Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($custom_commands as $key => $cmd): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($cmd['name']); ?></h6>
                                        <p class="card-text small text-muted"><?php echo htmlspecialchars($cmd['description']); ?></p>
                                        <button type="button" class="btn btn-primary btn-sm w-100 custom-cmd-btn" data-key="<?php echo htmlspecialchars($key); ?>">
                                            Execute
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Output</h5>
                        <?php if (!empty($output)): ?>
                        <button id="copyOutputBtn" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="terminal">
                            <?php if (!empty($command)): ?>
                            $ <span class="text-info"><?php echo htmlspecialchars($command); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                            <div class="cmd-success">Command executed successfully</div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                            <div class="cmd-error"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <?php if (!empty($output)): ?>
                            <pre><?php echo htmlspecialchars($output); ?></pre>
                            <?php elseif (empty($command)): ?>
                            <div class="text-muted">Select or enter a command to execute</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quick command buttons
            document.querySelectorAll('.cmd-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    document.getElementById('quickCommandInput').value = command;
                    document.getElementById('quickCommandForm').submit();
                });
            });
            
            // Custom command buttons
            document.querySelectorAll('.custom-cmd-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const key = this.getAttribute('data-key');
                    document.getElementById('customCommandInput').value = key;
                    document.getElementById('quickCommandForm').submit();
                });
            });
            
            // Copy output button
            const copyBtn = document.getElementById('copyOutputBtn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    const output = <?php echo json_encode($output); ?>;
                    navigator.clipboard.writeText(output).then(() => {
                        // Change button text temporarily
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check"></i> Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    });
                });
            }
        });
    </script>
</body>
</html>