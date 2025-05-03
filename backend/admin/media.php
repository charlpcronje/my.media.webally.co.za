<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Media Library';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle media operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete media
    if (isset($_POST['action']) && $_POST['action'] === 'delete_media') {
        $mediaId = $_POST['media_id'] ?? 0;
        
        if (empty($mediaId)) {
            $error_message = 'Invalid media ID';
        } else {
            try {
                // Get media info before deletion
                $stmt = $conn->prepare("SELECT filename, thumbnail FROM media WHERE id = ?");
                $stmt->execute([$mediaId]);
                $media = $stmt->fetch();
                
                if (!$media) {
                    $error_message = 'Media not found';
                } else {
                    // Start transaction
                    $conn->beginTransaction();
                    
                    // Delete from media_tags
                    $stmt = $conn->prepare("DELETE FROM media_tags WHERE media_id = ?");
                    $stmt->execute([$mediaId]);
                    
                    // Delete from analytics
                    $stmt = $conn->prepare("DELETE FROM analytics WHERE media_id = ?");
                    $stmt->execute([$mediaId]);
                    
                    // Delete from media
                    $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
                    $stmt->execute([$mediaId]);
                    
                    // Commit transaction
                    $conn->commit();
                    
                    // Delete files
                    if (file_exists(UPLOAD_PATH . '/' . $media['filename'])) {
                        unlink(UPLOAD_PATH . '/' . $media['filename']);
                    }
                    
                    if ($media['thumbnail'] && file_exists(UPLOAD_PATH . '/' . $media['thumbnail'])) {
                        unlink(UPLOAD_PATH . '/' . $media['thumbnail']);
                    }
                    
                    $success_message = 'Media deleted successfully';
                }
            } catch (PDOException $e) {
                // Rollback transaction on error
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                
                logError('Error deleting media: ' . $e->getMessage());
                $error_message = 'An error occurred while deleting the media';
            }
        }
    }
}

// Get filter parameters
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$tagFilter = isset($_GET['tag']) ? $_GET['tag'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$query = "
    SELECT m.*, GROUP_CONCAT(t.name) as tags
    FROM media m
    LEFT JOIN media_tags mt ON m.id = mt.media_id
    LEFT JOIN tags t ON mt.tag_id = t.id
";

$params = [];
$whereConditions = [];

if ($typeFilter) {
    $whereConditions[] = "m.type = ?";
    $params[] = $typeFilter;
}

if ($tagFilter) {
    $whereConditions[] = "t.name = ?";
    $params[] = $tagFilter;
}

if ($searchTerm) {
    $whereConditions[] = "(m.caption LIKE ? OR m.description LIKE ?)";
    $params[] = "%{$searchTerm}%";
    $params[] = "%{$searchTerm}%";
}

if ($whereConditions) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

$query .= " GROUP BY m.id ORDER BY m.created_at DESC";

// Get media items
try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $mediaItems = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting media: ' . $e->getMessage());
    $mediaItems = [];
}

// Get all available tags for filter
try {
    $stmt = $conn->query("
        SELECT name 
        FROM tags 
        ORDER BY name ASC
    ");
    $allTags = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting tags: ' . $e->getMessage());
    $allTags = [];
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
        .media-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .media-type-icon {
            width: 80px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 1.5rem;
        }
        .filter-tag {
            text-decoration: none;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            display: inline-block;
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
                        <a href="upload.php" class="btn btn-primary">
                            <i class="bi bi-cloud-upload"></i> Upload New Media
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
                
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Search & Filter</h5>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Search caption or description" 
                                           value="<?php echo htmlspecialchars($searchTerm); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="type" class="form-label">Media Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="video" <?php echo $typeFilter === 'video' ? 'selected' : ''; ?>>Videos</option>
                                    <option value="audio" <?php echo $typeFilter === 'audio' ? 'selected' : ''; ?>>Audio</option>
                                    <option value="image" <?php echo $typeFilter === 'image' ? 'selected' : ''; ?>>Images</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="tag" class="form-label">Tag</label>
                                <select class="form-select" id="tag" name="tag">
                                    <option value="">All Tags</option>
                                    <?php foreach ($allTags as $tag): ?>
                                    <option value="<?php echo htmlspecialchars($tag); ?>" <?php echo $tagFilter === $tag ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tag); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Apply Filters
                                </button>
                                <a href="media.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Media List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Media Library</h5>
                        <span class="badge bg-primary"><?php echo count($mediaItems); ?> items</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Preview</th>
                                        <th>Caption</th>
                                        <th>Type</th>
                                        <th>Tags</th>
                                        <th>Date Added</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mediaItems)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <?php if ($typeFilter || $tagFilter || $searchTerm): ?>
                                            No media items match your filters. <a href="media.php">Clear all filters</a>
                                            <?php else: ?>
                                            No media items found. <a href="upload.php">Upload your first media</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($mediaItems as $item): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                // Fix path to ensure files are found correctly
                                                $baseUrl = '../uploads';
                                                
                                                if ($item['type'] === 'image') {
                                                    echo '<img src="' . $baseUrl . '/' . $item['filename'] . '" alt="Preview" class="media-thumbnail">';
                                                } else if ($item['thumbnail']) {
                                                    // Check if the thumbnail path already includes 'thumbnails/'
                                                    $thumbnailPath = $item['thumbnail'];
                                                    // Make sure we don't duplicate the thumbnails/ directory in the path
                                                    if (strpos($thumbnailPath, 'thumbnails/') !== 0) {
                                                        $thumbnailPath = 'thumbnails/' . $thumbnailPath;
                                                    }
                                                    echo '<img src="' . $baseUrl . '/' . $thumbnailPath . '" alt="Thumbnail" class="media-thumbnail">';
                                                } else {
                                                    echo '<div class="media-type-icon">';
                                                    if ($item['type'] === 'video') {
                                                        echo '<i class="bi bi-film"></i>';
                                                    } else if ($item['type'] === 'audio') {
                                                        echo '<i class="bi bi-music-note-beamed"></i>';
                                                    } else {
                                                        echo '<i class="bi bi-file-earmark"></i>';
                                                    }
                                                    echo '</div>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['caption']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo mb_strimwidth(htmlspecialchars($item['description']), 0, 100, '...'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php 
                                                $typeClass = '';
                                                switch ($item['type']) {
                                                    case 'video': $typeClass = 'bg-success'; break;
                                                    case 'audio': $typeClass = 'bg-info'; break;
                                                    case 'image': $typeClass = 'bg-warning'; break;
                                                    default: $typeClass = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $typeClass; ?>">
                                                    <?php echo ucfirst($item['type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($item['tags']) {
                                                    $tags = explode(',', $item['tags']);
                                                    foreach ($tags as $tag) {
                                                        echo '<a href="media.php?tag=' . urlencode($tag) . '" class="badge bg-primary filter-tag">' . htmlspecialchars($tag) . '</a>';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted">No tags</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($item['created_at'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo $baseUrl . '/' . $item['filename']; ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="edit-media.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this media?');">
                                                        <input type="hidden" name="action" value="delete_media">
                                                        <input type="hidden" name="media_id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="includes/dark-mode.js"></script>
</body>
</html>