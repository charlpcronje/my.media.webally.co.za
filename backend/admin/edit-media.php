<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Edit Media';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Initialize variables
$success_message = '';
$error_message = '';
$mediaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mediaData = null;
$mediaTags = [];

// Validate media ID
if (!$mediaId) {
    header('Location: media.php');
    exit;
}

// Get all available tags
try {
    $stmt = $conn->query("SELECT name FROM tags ORDER BY name ASC");
    $allTags = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting tags: ' . $e->getMessage());
    $allTags = [];
}

// Get media data
try {
    $stmt = $conn->prepare("
        SELECT m.*, GROUP_CONCAT(t.name) as tags
        FROM media m
        LEFT JOIN media_tags mt ON m.id = mt.media_id
        LEFT JOIN tags t ON mt.tag_id = t.id
        WHERE m.id = ?
        GROUP BY m.id
    ");
    $stmt->execute([$mediaId]);
    $mediaData = $stmt->fetch();
    
    if (!$mediaData) {
        $error_message = 'Media not found';
    } else {
        // Parse tags
        if ($mediaData['tags']) {
            $mediaTags = explode(',', $mediaData['tags']);
        }
    }
} catch (PDOException $e) {
    logError('Error getting media data: ' . $e->getMessage());
    $error_message = 'Failed to load media data';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_media') {
        $caption = trim($_POST['caption'] ?? '');
        $description = $_POST['description'] ?? '';
        $selectedTags = isset($_POST['tags']) ? $_POST['tags'] : [];
        
        if (empty($caption)) {
            $error_message = 'Caption is required';
        } else {
            try {
                // Start transaction
                $conn->beginTransaction();
                
                // Update media record
                $stmt = $conn->prepare("UPDATE media SET caption = ?, description = ? WHERE id = ?");
                $stmt->execute([$caption, $description, $mediaId]);
                
                // Handle media file replacement if provided
                if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
                    $mediaFile = $_FILES['media_file'];
                    $fileExt = strtolower(pathinfo($mediaFile['name'], PATHINFO_EXTENSION));
                    
                    // Check file type based on media type
                    $validFileType = false;
                    if ($mediaData['type'] === 'video' && in_array($fileExt, ALLOWED_VIDEO_TYPES)) {
                        $validFileType = true;
                    } else if ($mediaData['type'] === 'audio' && in_array($fileExt, ALLOWED_AUDIO_TYPES)) {
                        $validFileType = true;
                    } else if ($mediaData['type'] === 'image' && in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
                        $validFileType = true;
                    }
                    
                    if (!$validFileType) {
                        throw new Exception('Invalid file type for ' . $mediaData['type'] . ' media');
                    }
                    
                    // Check file size
                    if ($mediaFile['size'] > MAX_FILE_SIZE) {
                        throw new Exception('File is too large (max ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)');
                    }
                    
                    // Generate filename and path
                    $newFilename = uniqid() . '.' . $fileExt;
                    $newFilePath = UPLOAD_PATH . '/' . $newFilename;
                    
                    // Move file
                    if (!move_uploaded_file($mediaFile['tmp_name'], $newFilePath)) {
                        throw new Exception('Failed to save media file');
                    }
                    
                    // Delete old file if exists
                    if (file_exists(UPLOAD_PATH . '/' . $mediaData['filename'])) {
                        unlink(UPLOAD_PATH . '/' . $mediaData['filename']);
                    }
                    
                    // Update filename in database
                    $stmt = $conn->prepare("UPDATE media SET filename = ? WHERE id = ?");
                    $stmt->execute([$newFilename, $mediaId]);
                    
                    // Update media data
                    $mediaData['filename'] = $newFilename;
                }
                
                // Handle thumbnail replacement if provided
                if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                    $thumbFile = $_FILES['thumbnail'];
                    $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
                    
                    // Check file type
                    if (!in_array($thumbExt, ALLOWED_IMAGE_TYPES)) {
                        throw new Exception('Invalid thumbnail file type');
                    }
                    
                    // Generate filename and path
                    $thumbnailFilename = uniqid() . '_thumb.' . $thumbExt;
                    $thumbnailPath = THUMBNAIL_PATH . '/' . $thumbnailFilename;
                    
                    // Move file
                    if (!move_uploaded_file($thumbFile['tmp_name'], $thumbnailPath)) {
                        throw new Exception('Failed to save thumbnail file');
                    }
                    
                    // Delete old thumbnail if exists
                    if ($mediaData['thumbnail'] && file_exists(UPLOAD_PATH . '/' . $mediaData['thumbnail'])) {
                        unlink(UPLOAD_PATH . '/' . $mediaData['thumbnail']);
                    }
                    
                    // Update thumbnail in database
                    $stmt = $conn->prepare("UPDATE media SET thumbnail = ? WHERE id = ?");
                    $stmt->execute(['thumbnails/' . $thumbnailFilename, $mediaId]);
                    
                    // Update media data
                    $mediaData['thumbnail'] = 'thumbnails/' . $thumbnailFilename;
                }
                
                // Handle tags
                // First, remove all existing tags
                $stmt = $conn->prepare("DELETE FROM media_tags WHERE media_id = ?");
                $stmt->execute([$mediaId]);
                
                // Then add selected tags
                if (!empty($selectedTags)) {
                    foreach ($selectedTags as $tagName) {
                        $tagName = trim($tagName);
                        if (empty($tagName)) continue;
                        
                        // Get or create tag
                        $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
                        $stmt->execute([$tagName]);
                        $tag = $stmt->fetch();
                        
                        if (!$tag) {
                            // Create new tag
                            $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
                            $stmt->execute([$tagName]);
                            $tagId = $conn->lastInsertId();
                        } else {
                            $tagId = $tag['id'];
                        }
                        
                        // Link tag to media
                        $stmt = $conn->prepare("INSERT INTO media_tags (media_id, tag_id) VALUES (?, ?)");
                        $stmt->execute([$mediaId, $tagId]);
                    }
                }
                
                // Commit transaction
                $conn->commit();
                
                // Update media data for display
                $mediaData['caption'] = $caption;
                $mediaData['description'] = $description;
                $mediaTags = $selectedTags;
                
                $success_message = 'Media updated successfully';
            } catch (PDOException $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                logError('Error updating media: ' . $e->getMessage());
                $error_message = 'Database error occurred while updating media';
            } catch (Exception $e) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                logError('Error: ' . $e->getMessage());
                $error_message = $e->getMessage();
            }
        }
    }
}

// Get view count
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count
        FROM analytics
        WHERE media_id = ? AND event_type = 'view'
    ");
    $stmt->execute([$mediaId]);
    $viewCount = $stmt->fetchColumn();
} catch (PDOException $e) {
    logError('Error getting view count: ' . $e->getMessage());
    $viewCount = 0;
}

// Get play count (for video and audio)
$playCount = 0;
if ($mediaData && ($mediaData['type'] === 'video' || $mediaData['type'] === 'audio')) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count
            FROM analytics
            WHERE media_id = ? AND event_type = 'play'
        ");
        $stmt->execute([$mediaId]);
        $playCount = $stmt->fetchColumn();
    } catch (PDOException $e) {
        logError('Error getting play count: ' . $e->getMessage());
    }
}

// Function to get file size in human readable format
function formatFileSize($path) {
    if (!file_exists($path)) return 'N/A';
    
    $bytes = filesize($path);
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css">
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
        .media-preview {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 0.375rem;
        }
        .thumbnail-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.375rem;
            cursor: pointer;
        }
        .stat-badge {
            padding: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            height: 100%;
        }
        .stat-badge i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-badge .count {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .stat-badge .label {
            font-size: 0.875rem;
            opacity: 0.8;
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
                    <div>
                        <a href="media.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Media Library
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
                
                <?php if (!$mediaData): ?>
                <div class="alert alert-warning">
                    Media not found. <a href="media.php" class="alert-link">Return to Media Library</a>
                </div>
                <?php else: ?>
                
                <div class="row mb-4">
                    <!-- Media Preview Column -->
                    <div class="col-md-5 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Media Preview</h5>
                                <span class="badge bg-primary"><?php echo ucfirst($mediaData['type']); ?></span>
                            </div>
                            <div class="card-body text-center">
                                <?php 
                                $baseUrl = '../uploads';
                                
                                if ($mediaData['type'] === 'image'): ?>
                                    <img src="<?php echo $baseUrl . '/' . $mediaData['filename']; ?>" 
                                         alt="<?php echo htmlspecialchars($mediaData['caption']); ?>" 
                                         class="media-preview">
                                <?php elseif ($mediaData['type'] === 'video'): ?>
                                    <video controls class="media-preview">
                                        <source src="<?php echo $baseUrl . '/' . $mediaData['filename']; ?>" 
                                                type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php elseif ($mediaData['type'] === 'audio'): ?>
                                    <?php if ($mediaData['thumbnail']): ?>
                                    <?php 
                                    // Check if the thumbnail path already includes 'thumbnails/'
                                    $thumbnailPath = $mediaData['thumbnail'];
                                    // Make sure we don't duplicate the thumbnails/ directory in the path
                                    if (strpos($thumbnailPath, 'thumbnails/') !== 0) {
                                        $thumbnailPath = 'thumbnails/' . $thumbnailPath;
                                    }
                                    ?>
                                    <img src="<?php echo $baseUrl . '/' . $thumbnailPath; ?>" 
                                         alt="Audio thumbnail" class="mb-3 media-preview">
                                    <?php endif; ?>
                                    <audio controls class="w-100">
                                        <source src="<?php echo $baseUrl . '/' . $mediaData['filename']; ?>" 
                                                type="audio/mpeg">
                                        Your browser does not support the audio tag.
                                    </audio>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="stat-badge bg-primary text-white">
                                            <i class="bi bi-eye"></i>
                                            <div class="count"><?php echo $viewCount; ?></div>
                                            <div class="label">Views</div>
                                        </div>
                                    </div>
                                    <?php if ($mediaData['type'] === 'video' || $mediaData['type'] === 'audio'): ?>
                                    <div class="col-6">
                                        <div class="stat-badge bg-success text-white">
                                            <i class="bi bi-play-circle"></i>
                                            <div class="count"><?php echo $playCount; ?></div>
                                            <div class="label">Plays</div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Form Column -->
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Edit Details</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_media">
                                    
                                    <div class="mb-3">
                                        <label for="caption" class="form-label">Caption</label>
                                        <input type="text" class="form-control" id="caption" name="caption" 
                                               value="<?php echo htmlspecialchars($mediaData['caption']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="4"><?php echo htmlspecialchars($mediaData['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="tags" class="form-label">Tags</label>
                                        <select id="tags" name="tags[]" multiple class="form-select" placeholder="Select or create tags...">
                                            <?php foreach ($allTags as $tag): ?>
                                            <option value="<?php echo htmlspecialchars($tag); ?>" 
                                                    <?php echo in_array($tag, $mediaTags) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tag); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Media Type</label>
                                        <input type="text" class="form-control" value="<?php echo ucfirst($mediaData['type']); ?>" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="media_file" class="form-label">Replace Media File</label>
                                        <input type="file" class="form-control" id="media_file" name="media_file">
                                        <div class="form-text">
                                            Upload a new file to replace the current one. Leave empty to keep the current file.<br>
                                            Allowed file types for <?php echo $mediaData['type']; ?>: 
                                            <?php 
                                            if ($mediaData['type'] === 'video') {
                                                echo implode(', ', ALLOWED_VIDEO_TYPES);
                                            } else if ($mediaData['type'] === 'audio') {
                                                echo implode(', ', ALLOWED_AUDIO_TYPES);
                                            } else if ($mediaData['type'] === 'image') {
                                                echo implode(', ', ALLOWED_IMAGE_TYPES);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">File Information</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Filename:</strong> <?php echo htmlspecialchars($mediaData['filename']); ?></p>
                                                <p class="mb-1"><strong>Size:</strong> <?php echo formatFileSize(UPLOAD_PATH . '/' . $mediaData['filename']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Uploaded:</strong> <?php echo date('M d, Y', strtotime($mediaData['created_at'])); ?></p>
                                                <p class="mb-1"><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($mediaData['updated_at'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if ($mediaData['type'] !== 'image'): ?>
                                    <div class="mb-3">
                                        <label for="thumbnail" class="form-label">Thumbnail</label>
                                        <?php if ($mediaData['thumbnail']): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo $baseUrl . '/' . $mediaData['thumbnail']; ?>" 
                                                 alt="Thumbnail" class="thumbnail-preview" id="currentThumbnail">
                                        </div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                        <div class="form-text">
                                            Upload a new thumbnail to replace the existing one. Leave empty to keep the current thumbnail.
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save"></i> Save Changes
                                        </button>
                                        <div>
                                            <a href="<?php echo $baseUrl . '/' . $mediaData['filename']; ?>" download class="btn btn-outline-success">
                                                <i class="bi bi-download"></i> Download File
                                            </a>
                                            <a href="#" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteMediaModal">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Media Analytics Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Analytics Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get recent analytics for this media
                        try {
                            $stmt = $conn->prepare("
                                SELECT a.*, m.caption
                                FROM analytics a
                                JOIN media m ON a.media_id = m.id
                                WHERE a.media_id = ?
                                ORDER BY a.timestamp DESC
                                LIMIT 10
                            ");
                            $stmt->execute([$mediaId]);
                            $recentAnalytics = $stmt->fetchAll();
                        } catch (PDOException $e) {
                            logError('Error getting recent analytics: ' . $e->getMessage());
                            $recentAnalytics = [];
                        }
                        
                        if (empty($recentAnalytics)): 
                        ?>
                        <div class="alert alert-info mb-0">
                            No analytics data available for this media yet.
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Event</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentAnalytics as $event): ?>
                                    <tr>
                                        <td><?php echo date('M d, H:i', strtotime($event['timestamp'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['user_name']); ?></td>
                                        <td>
                                            <?php
                                                $eventClass = '';
                                                switch ($event['event_type']) {
                                                    case 'view': $eventClass = 'bg-primary'; break;
                                                    case 'play': $eventClass = 'bg-success'; break;
                                                    case 'pause': $eventClass = 'bg-warning'; break;
                                                    case 'seek': $eventClass = 'bg-info'; break;
                                                    case 'ended': $eventClass = 'bg-danger'; break;
                                                    case 'download': $eventClass = 'bg-secondary'; break;
                                                    default: $eventClass = 'bg-light text-dark';
                                                }
                                            ?>
                                            <span class="badge <?php echo $eventClass; ?>">
                                                <?php echo ucfirst($event['event_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($event['percentage'] !== null): ?>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar" role="progressbar" 
                                                    style="width: <?php echo $event['percentage']; ?>%"
                                                    aria-valuenow="<?php echo $event['percentage']; ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo round($event['percentage'], 1); ?>%
                                            </small>
                                            <?php else: ?>
                                            <small class="text-muted">N/A</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <a href="analytics.php?media_id=<?php echo $mediaId; ?>" class="btn btn-sm btn-primary">View All Analytics</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteMediaModal" tabindex="-1" aria-labelledby="deleteMediaModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteMediaModalLabel">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this media? This action cannot be undone.</p>
                                <p><strong>Caption:</strong> <?php echo htmlspecialchars($mediaData['caption']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form method="post" action="media.php">
                                    <input type="hidden" name="action" value="delete_media">
                                    <input type="hidden" name="media_id" value="<?php echo $mediaId; ?>">
                                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php endif; ?>
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
            
            // Thumbnail preview click handler
            const currentThumbnail = document.getElementById('currentThumbnail');
            if (currentThumbnail) {
                currentThumbnail.addEventListener('click', function() {
                    window.open(this.src, '_blank');
                });
            }
            
            // Image preview functionality for thumbnail input
            const thumbnailInput = document.getElementById('thumbnail');
            if (thumbnailInput) {
                thumbnailInput.addEventListener('change', function(e) {
                    if (e.target.files && e.target.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            if (currentThumbnail) {
                                currentThumbnail.src = e.target.result;
                            } else {
                                // Create new thumbnail preview if none exists
                                const container = thumbnailInput.parentElement;
                                const preview = document.createElement('div');
                                preview.className = 'mb-2 mt-2';
                                preview.innerHTML = `<img src="${e.target.result}" alt="New Thumbnail" class="thumbnail-preview">`;
                                container.insertBefore(preview, thumbnailInput);
                            }
                        }
                        
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            }
        });
    </script>
</body>
</html>