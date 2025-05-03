<?php
// backend/edit/file-actions.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Base paths
$basePath = dirname(dirname(__FILE__));
$relativeBasePath = '..';

// Page title
$pageTitle = 'File Operations';

// Initialize variables
$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create-file':
            if (empty($_POST['file_path'])) {
                $error_message = 'File path is required';
            } else {
                $filePath = $_POST['file_path'];
                $content = $_POST['file_content'] ?? '';
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $filePath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid file path. Cannot create files outside project directory.';
                } else {
                    // Create parent directories if they don't exist
                    $dir = dirname($fullPath);
                    if (!file_exists($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                            $error_message = 'Failed to create directory structure';
                            break;
                        }
                    }
                    
                    // Create file
                    if (file_put_contents($fullPath, $content) === false) {
                        $error_message = 'Failed to create file. Check file permissions.';
                    } else {
                        $success_message = 'File created successfully';
                    }
                }
            }
            break;
            
        case 'create-directory':
            if (empty($_POST['directory_path'])) {
                $error_message = 'Directory path is required';
            } else {
                $dirPath = $_POST['directory_path'];
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $dirPath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid directory path. Cannot create directories outside project.';
                } else {
                    // Create directory
                    if (!mkdir($fullPath, 0755, true)) {
                        $error_message = 'Failed to create directory. Check permissions.';
                    } else {
                        $success_message = 'Directory created successfully';
                    }
                }
            }
            break;
            
        case 'upload-file':
            if (!isset($_FILES['upload_file']) || $_FILES['upload_file']['error'] !== UPLOAD_ERR_OK) {
                $error_message = 'Please select a file to upload';
            } else if (empty($_POST['upload_path'])) {
                $error_message = 'Upload path is required';
            } else {
                $uploadPath = $_POST['upload_path'];
                $file = $_FILES['upload_file'];
                
                // Validate path is within our project
                $fullPath = $basePath . '/' . $uploadPath;
                if (strpos(realpath(dirname($fullPath)), realpath($basePath)) !== 0) {
                    $error_message = 'Invalid upload path. Cannot upload files outside project directory.';
                } else {
                    // Create parent directories if they don't exist
                    $dir = dirname($fullPath);
                    if (!file_exists($dir)) {
                        if (!mkdir($dir, 0755, true)) {
                            $error_message = 'Failed to create directory structure';
                            break;
                        }
                    }
                    
                    // Move uploaded file
                    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                        $error_message = 'Failed to upload file. Check file permissions.';
                    } else {
                        $success_message = 'File uploaded successfully';
                    }
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
    <title><?php echo $pageTitle; ?> - Media Share Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            padding: 20px;
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
        
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create File</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="create-file">
                            
                            <div class="mb-3">
                                <label for="file_path" class="form-label">File Path</label>
                                <input type="text" class="form-control" id="file_path" name="file_path" 
                                    placeholder="e.g., path/to/file.txt" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="file_content" class="form-label">File Content</label>
                                <textarea class="form-control" id="file_content" name="file_content" 
                                    rows="5"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Create File</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create Directory</h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="action" value="create-directory">
                            
                            <div class="mb-3">
                                <label for="directory_path" class="form-label">Directory Path</label>
                                <input type="text" class="form-control" id="directory_path" name="directory_path" 
                                    placeholder="e.g., path/to/directory" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Create Directory</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Upload File</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload-file">
                            
                            <div class="mb-3">
                                <label for="upload_file" class="form-label">Select File</label>
                                <input type="file" class="form-control" id="upload_file" name="upload_file" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="upload_path" class="form-label">Upload Path</label>
                                <input type="text" class="form-control" id="upload_path" name="upload_path" 
                                    placeholder="e.g., path/to/file.txt" required>
                                <div class="form-text">
                                    Relative to the backend directory
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Upload File</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">File Operations Help</h5>
                </div>
                <div class="card-body">
                    <h6>Notes:</h6>
                    <ul>
                        <li>All paths are relative to the <code>backend</code> directory.</li>
                        <li>To create files in the frontend directory, use <code>../frontend/path/to/file</code>.</li>
                        <li>For security reasons, you cannot create or modify files outside the project directory.</li>
                        <li>Some system directories like <code>logs</code>, <code>node_modules</code>, and <code>vendor</code> are restricted.</li>
                    </ul>
                    
                    <h6>Examples:</h6>
                    <ul>
                        <li>Create a PHP file: <code>api/new-endpoint.php</code></li>
                        <li>Create a directory: <code>uploads/custom</code></li>
                        <li>Create a frontend component: <code>../frontend/src/components/NewComponent.jsx</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>