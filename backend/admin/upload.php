<?php
// backend/admin/upload.php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Upload Media';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Initialize variables
$success = false;
$error = '';
$message = '';

// Get all tags for dropdown
try {
    $stmt = $conn->query("SELECT name FROM tags ORDER BY name ASC");
    $allTags = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting tags: ' . $e->getMessage());
    $allTags = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $error = 'Please select a file to upload';
        } else {
            $mediaFile = $_FILES['media_file'];
            $caption = trim($_POST['caption'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            // Validate caption
            if (empty($caption)) {
                $error = 'Please enter a caption for the media';
            } 
            // Validate file upload
            else if ($mediaFile['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload error: ' . $mediaFile['error'];
            }
            // Validate file size
            else if ($mediaFile['size'] > MAX_FILE_SIZE) {
                $error = 'File is too large (max ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)';
            } else {
                // Get file extension and validate media type
                $fileExt = strtolower(pathinfo($mediaFile['name'], PATHINFO_EXTENSION));
                
                if (in_array($fileExt, ALLOWED_VIDEO_TYPES)) {
                    $mediaType = 'video';
                } else if (in_array($fileExt, ALLOWED_AUDIO_TYPES)) {
                    $mediaType = 'audio';
                } else if (in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
                    $mediaType = 'image';
                } else {
                    $error = 'Invalid file type. Allowed types: ' . 
                             implode(', ', array_merge(ALLOWED_VIDEO_TYPES, ALLOWED_AUDIO_TYPES, ALLOWED_IMAGE_TYPES));
                }
                
                if (empty($error)) {
                    // Generate unique filename
                    $filename = uniqid() . '.' . $fileExt;
                    $uploadPath = UPLOAD_PATH . '/' . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($mediaFile['tmp_name'], $uploadPath)) {
                        // Process thumbnail if provided
                        $thumbnail = null;
                        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                            $thumbnailFile = $_FILES['thumbnail'];
                            $thumbExt = strtolower(pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION));
                            
                            if (!in_array($thumbExt, ALLOWED_IMAGE_TYPES)) {
                                throw new Exception('Invalid thumbnail file type');
                            }
                            
                            $thumbnailFilename = uniqid() . '_thumb.' . $thumbExt;
                            $thumbnailPath = THUMBNAIL_PATH . '/' . $thumbnailFilename;
                            
                            if (move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {
                                $thumbnail = 'thumbnails/' . $thumbnailFilename;
                            }
                        }
                        
                        // Start transaction
                        $conn->beginTransaction();
                        
                        // Insert media record
                        $sql = "INSERT INTO media (filename, thumbnail, type, caption, description) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$filename, $thumbnail, $mediaType, $caption, $description]);
                        
                        $mediaId = $conn->lastInsertId();
                        
                        // Process tags
                        $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
                        if (!empty($tags)) {
                            foreach ($tags as $tagName) {
                                // Get or create tag
                                $sql = "INSERT IGNORE INTO tags (name) VALUES (?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$tagName]);
                                
                                // Get tag ID
                                $sql = "SELECT id FROM tags WHERE name = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$tagName]);
                                $tagId = $stmt->fetch()['id'];
                                
                                // Link tag to media
                                $sql = "INSERT INTO media_tags (media_id, tag_id) VALUES (?, ?)";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute([$mediaId, $tagId]);
                            }
                        }
                        
                        // Commit transaction
                        $conn->commit();
                        
                        $success = true;
                        $message = 'Media uploaded successfully!';
                    } else {
                        $error = 'Failed to save uploaded file';
                    }
                }
            }
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        logError('Upload error: ' . $e->getMessage());
        $error = 'An error occurred during upload: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Media Share Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/dark-mode.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .nav-link {
            font-weight: 500;
            color: #333;
        }
        .nav-link.active {
            color: #2470dc;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $pageTitle; ?></h1>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="media_file" class="form-label">Media File</label>
                                <input type="file" class="form-control" id="media_file" name="media_file" required>
                                <div class="form-text">
                                    Allowed file types: 
                                    <?php echo implode(', ', array_merge(ALLOWED_VIDEO_TYPES, ALLOWED_AUDIO_TYPES, ALLOWED_IMAGE_TYPES)); ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label">Thumbnail (Optional)</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                <div class="form-text">
                                    For videos and audio files, you can upload a custom thumbnail image.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="caption" class="form-label">Caption</label>
                                <input type="text" class="form-control" id="caption" name="caption" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <select id="tags" name="tags[]" multiple class="form-select" placeholder="Select or create tags...">
                                    <?php foreach ($allTags as $tag): ?>
                                    <option value="<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Upload Media</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="includes/dark-mode.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tag selector with ability to create new tags
            new TomSelect('#tags', {
                plugins: ['remove_button'],
                create: true,
                createOnBlur: true
            });
        });
    </script>
</body>
</html>