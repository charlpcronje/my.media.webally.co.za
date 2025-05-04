// backend/edit/router.php
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Get requested action
$action = isset($_GET['action']) ? $_GET['action'] : '';
$response = ['success' => false];

// Handle file system operations
switch ($action) {
    case 'create-file':
        $response = createFile();
        break;
        
    case 'create-directory':
        $response = createDirectory();
        break;
        
    case 'rename':
        $response = renameItem();
        break;
        
    case 'delete':
        $response = deleteItem();
        break;
        
    case 'get-file-list':
        $response = getFileList();
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;

/**
 * Create a new file
 */
function createFile() {
    if (!isset($_POST['path']) || !isset($_POST['content'])) {
        return ['success' => false, 'message' => 'Missing required parameters'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    $content = $_POST['content'];
    
    // Validate file path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid file path. Cannot create files outside project directory.'];
    }
    
    // Create parent directories if they don't exist
    $dir = dirname($fullPath);
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create directory structure'];
        }
    }
    
    // Create file
    if (file_put_contents($fullPath, $content) === false) {
        return ['success' => false, 'message' => 'Failed to create file. Check file permissions.'];
    }
    
    return ['success' => true, 'message' => 'File created successfully'];
}

/**
 * Create a new directory
 */
function createDirectory() {
    if (!isset($_POST['path'])) {
        return ['success' => false, 'message' => 'Missing required parameter: path'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    
    // Validate directory path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid directory path. Cannot create directories outside project.'];
    }
    
    // Create directory
    if (!mkdir($fullPath, 0755, true)) {
        return ['success' => false, 'message' => 'Failed to create directory. Check permissions.'];
    }
    
    return ['success' => true, 'message' => 'Directory created successfully'];
}

/**
 * Rename a file or directory
 */
function renameItem() {
    if (!isset($_POST['old_path']) || !isset($_POST['new_path'])) {
        return ['success' => false, 'message' => 'Missing required parameters'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $oldPath = $_POST['old_path'];
    $newPath = $_POST['new_path'];
    
    // Validate paths
    $fullOldPath = $basePath . '/' . $oldPath;
    $fullNewPath = $basePath . '/' . $newPath;
    $realBasePath = realpath($basePath);
    
    if (strpos(realpath(dirname($fullOldPath)), $realBasePath) !== 0 ||
        strpos(realpath(dirname($fullNewPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid paths. Cannot rename items outside project directory.'];
    }
    
    // Perform rename
    if (!rename($fullOldPath, $fullNewPath)) {
        return ['success' => false, 'message' => 'Failed to rename item. Check permissions.'];
    }
    
    return ['success' => true, 'message' => 'Item renamed successfully'];
}

/**
 * Delete a file or directory
 */
function deleteItem() {
    if (!isset($_POST['path'])) {
        return ['success' => false, 'message' => 'Missing required parameter: path'];
    }
    
    $basePath = dirname(dirname(__FILE__));
    $path = $_POST['path'];
    
    // Validate path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    // Prevent deletion of sensitive files/directories
    $protectedItems = [
        'config.php',
        'admin',
        'api',
        'edit',
        'uploads'
    ];
    
    foreach ($protectedItems as $item) {
        if (stripos($path, $item) === 0 || $path === $item) {
            return ['success' => false, 'message' => 'Cannot delete protected files or directories'];
        }
    }
    
    if (strpos(realpath(dirname($fullPath)), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid path. Cannot delete items outside project directory.'];
    }
    
    // Delete file or directory
    if (is_file($fullPath)) {
        if (!unlink($fullPath)) {
            return ['success' => false, 'message' => 'Failed to delete file. Check permissions.'];
        }
    } else if (is_dir($fullPath)) {
        // Recursively delete directory
        if (!deleteDirectory($fullPath)) {
            return ['success' => false, 'message' => 'Failed to delete directory. Check permissions.'];
        }
    } else {
        return ['success' => false, 'message' => 'Item not found'];
    }
    
    return ['success' => true, 'message' => 'Item deleted successfully'];
}

/**
 * Recursively delete a directory
 */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!deleteDirectory($dir . '/' . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}

/**
 * Get file list for a specific directory
 */
function getFileList() {
    $basePath = dirname(dirname(__FILE__));
    $path = isset($_GET['path']) ? $_GET['path'] : '';
    
    // Validate path
    $fullPath = $basePath . '/' . $path;
    $realBasePath = realpath($basePath);
    
    if (!file_exists($fullPath) || !is_dir($fullPath)) {
        return ['success' => false, 'message' => 'Directory not found'];
    }
    
    if (strpos(realpath($fullPath), $realBasePath) !== 0) {
        return ['success' => false, 'message' => 'Invalid path. Cannot list files outside project directory.'];
    }
    
    // Get file list
    $files = scandir($fullPath);
    $result = [];
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = $fullPath . '/' . $file;
        $relativePath = $path ? $path . '/' . $file : $file;
        
        if (is_dir($filePath)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath
            ];
        } else {
            // Skip certain file types
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($extension, ['log', 'zip', 'gz', 'tar'])) {
                continue;
            }
            
            $result[] = [
                'type' => 'file',
                'name' => $file,
                'path' => $relativePath,
                'size' => filesize($filePath),
                'modified' => filemtime($filePath)
            ];
        }
    }
    
    return [
        'success' => true,
        'path' => $path,
        'items' => $result
    ];
}