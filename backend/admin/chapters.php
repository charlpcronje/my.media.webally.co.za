<?php
// backend/admin/chapters.php
require_once('../config.php');
require_once('../models/ChaptersRepository.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Manage Chapters';

// Get database connection
$db = getDbConnection();
if (!$db) {
    die('Database connection failed');
}

// Initialize chapters repository
$chaptersRepo = new ChaptersRepository($db);

// Initialize variables
$success_message = '';
$error_message = '';
$mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;
$mediaData = null;
$chapters = [];

// Validate media ID
if (!$mediaId) {
    header('Location: media.php');
    exit;
}

// Get media data
try {
    $stmt = $db->prepare("
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
        
        // Get chapters for this media
        $chapters = $chaptersRepo->getByMediaId($mediaId);
    }
} catch (PDOException $e) {
    logError('Error getting media data: ' . $e->getMessage());
    $error_message = 'Failed to load media data';
}

// Handle chapter operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add chapter
    if (isset($_POST['action']) && $_POST['action'] === 'add_chapter') {
        $title = trim($_POST['title'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Validate required fields
        if (empty($title) || empty($startTime)) {
            $error_message = 'Title and start time are required';
        } else {
            // Convert time format (MM:SS) to seconds if needed
            if (strpos($startTime, ':') !== false) {
                $startTime = convertTimeToSeconds($startTime);
            }
            
            if (!empty($endTime) && strpos($endTime, ':') !== false) {
                $endTime = convertTimeToSeconds($endTime);
            } else if (empty($endTime)) {
                $endTime = null;
            }
            
            // Create chapter data array
            $chapterData = [
                'media_id' => $mediaId,
                'title' => $title,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'description' => $description
            ];
            
            // Add the chapter
            $chapter = $chaptersRepo->create($chapterData);
            
            if ($chapter) {
                $success_message = 'Chapter added successfully';
                // Refresh chapters list
                $chapters = $chaptersRepo->getByMediaId($mediaId);
            } else {
                $error_message = 'Failed to add chapter';
            }
        }
    }
    
    // Delete chapter
    if (isset($_POST['action']) && $_POST['action'] === 'delete_chapter') {
        $chapterId = $_POST['chapter_id'] ?? 0;
        
        if (empty($chapterId)) {
            $error_message = 'Invalid chapter ID';
        } else {
            $success = $chaptersRepo->delete($chapterId);
            
            if ($success) {
                $success_message = 'Chapter deleted successfully';
                // Refresh chapters list
                $chapters = $chaptersRepo->getByMediaId($mediaId);
            } else {
                $error_message = 'Failed to delete chapter';
            }
        }
    }
    
    // Update chapter
    if (isset($_POST['action']) && $_POST['action'] === 'update_chapter') {
        $chapterId = $_POST['chapter_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Validate required fields
        if (empty($chapterId) || empty($title) || empty($startTime)) {
            $error_message = 'Chapter ID, title, and start time are required';
        } else {
            // Convert time format (MM:SS) to seconds if needed
            if (strpos($startTime, ':') !== false) {
                $startTime = convertTimeToSeconds($startTime);
            }
            
            if (!empty($endTime) && strpos($endTime, ':') !== false) {
                $endTime = convertTimeToSeconds($endTime);
            } else if (empty($endTime)) {
                $endTime = null;
            }
            
            // Create chapter data array
            $chapterData = [
                'title' => $title,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'description' => $description
            ];
            
            // Update the chapter
            $chapter = $chaptersRepo->update($chapterId, $chapterData);
            
            if ($chapter) {
                $success_message = 'Chapter updated successfully';
                // Refresh chapters list
                $chapters = $chaptersRepo->getByMediaId($mediaId);
            } else {
                $error_message = 'Failed to update chapter';
            }
        }
    }
}

/**
 * Convert time string (HH:MM:SS or MM:SS) to seconds
 * @param string $timeString Time string
 * @return float Time in seconds
 */
function convertTimeToSeconds($timeString) {
    $parts = explode(':', $timeString);
    
    if (count($parts) === 3) {
        // HH:MM:SS
        return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
    } else if (count($parts) === 2) {
        // MM:SS
        return ($parts[0] * 60) + $parts[1];
    }
    
    return (float)$timeString;
}

/**
 * Format seconds to time string (MM:SS or HH:MM:SS)
 * @param float $seconds Time in seconds
 * @return string Formatted time
 */
function formatTimeString($seconds) {
    $seconds = (float)$seconds;
    
    if ($seconds >= 3600) {
        // Format as HH:MM:SS
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = floor($seconds % 60);
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    } else {
        // Format as MM:SS
        $minutes = floor($seconds / 60);
        $secs = floor($seconds % 60);
        
        return sprintf('%02d:%02d', $minutes, $secs);
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
        .chapter-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .chapter-card:hover {
            transform: translateY(-3px);
        }
        .media-info {
            position: sticky;
            top: 20px;
        }
        .chapters-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .chapter-timeline {
            height: 30px;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin: 30px 0;
        }
        .chapter-marker {
            position: absolute;
            top: -10px;
            width: 5px;
            height: 50px;
            background-color: #0d6efd;
            cursor: pointer;
        }
        .chapter-marker::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: -5px;
            width: 15px;
            height: 15px;
            background-color: #0d6efd;
            border-radius: 50%;
        }
        .chapter-marker-label {
            position: absolute;
            bottom: -35px;
            left: -20px;
            width: 50px;
            text-align: center;
            font-size: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                        <a href="edit-media.php?id=<?php echo $mediaId; ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Media Editor
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
                
                <div class="row">
                    <!-- Media Info -->
                    <div class="col-md-4">
                        <div class="card media-info">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Media Information</h5>
                            </div>
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($mediaData['caption']); ?></h4>
                                <p class="text-muted">
                                    <?php echo htmlspecialchars($mediaData['description']); ?>
                                </p>
                                
                                <div class="d-flex align-items-center mt-3">
                                    <span class="badge bg-primary me-2"><?php echo ucfirst($mediaData['type']); ?></span>
                                    <span class="text-muted small">
                                        Added: <?php echo date('M d, Y', strtotime($mediaData['created_at'])); ?>
                                    </span>
                                </div>
                                
                                <?php if (in_array($mediaData['type'], ['video', 'audio'])): ?>
                                <div class="mt-4">
                                    <h6>Preview</h6>
                                    <?php if ($mediaData['type'] === 'video'): ?>
                                    <video
                                        controls
                                        src="../uploads/<?php echo htmlspecialchars($mediaData['filename']); ?>"
                                        class="w-100 mt-2 rounded"
                                        id="mediaPreview"
                                    ></video>
                                    <?php elseif ($mediaData['type'] === 'audio'): ?>
                                    <audio
                                        controls
                                        src="../uploads/<?php echo htmlspecialchars($mediaData['filename']); ?>"
                                        class="w-100 mt-2"
                                        id="mediaPreview"
                                    ></audio>
                                    
                                    <?php if ($mediaData['thumbnail']): ?>
                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($mediaData['thumbnail']); ?>"
                                        alt="Audio thumbnail"
                                        class="mt-3 img-fluid rounded"
                                    />
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-warning mt-4">
                                    Chapters are only available for video and audio content.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chapters -->
                    <div class="col-md-8">
                        <?php if (in_array($mediaData['type'], ['video', 'audio'])): ?>
                        
                        <!-- Chapters Timeline -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Chapters Timeline</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($chapters)): ?>
                                <p class="text-muted">No chapters have been added yet.</p>
                                <?php else: ?>
                                <div class="chapter-timeline" id="chapterTimeline">
                                    <?php
                                    // Get the total duration (max end time or 1.5x the last start time if no end time)
                                    $totalDuration = 0;
                                    foreach ($chapters as $chapter) {
                                        if ($chapter['end_time'] && $chapter['end_time'] > $totalDuration) {
                                            $totalDuration = $chapter['end_time'];
                                        } else if ($chapter['start_time'] > $totalDuration) {
                                            $totalDuration = $chapter['start_time'] * 1.5;
                                        }
                                    }
                                    
                                    // Add a buffer to the total duration
                                    $totalDuration *= 1.1;
                                    
                                    // Create a marker for each chapter
                                    foreach ($chapters as $chapter):
                                        $position = ($chapter['start_time'] / $totalDuration) * 100;
                                    ?>
                                    <div 
                                        class="chapter-marker"
                                        style="left: <?php echo $position; ?>%;"
                                        data-chapter-id="<?php echo $chapter['id']; ?>"
                                        data-chapter-time="<?php echo $chapter['start_time']; ?>"
                                        title="<?php echo htmlspecialchars($chapter['title']) . ' - ' . formatTimeString($chapter['start_time']); ?>"
                                    >
                                        <div class="chapter-marker-label">
                                            <?php echo formatTimeString($chapter['start_time']); ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Add Chapter Form -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Add New Chapter</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" id="addChapterForm">
                                    <input type="hidden" name="action" value="add_chapter">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input type="text" class="form-control" id="start_time" name="start_time" 
                                                   placeholder="MM:SS" pattern="([0-9]+:)?[0-5]?[0-9]:[0-5][0-9]" required>
                                            <div class="form-text small">Format: MM:SS or HH:MM:SS</div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label for="end_time" class="form-label">End Time (Optional)</label>
                                            <input type="text" class="form-control" id="end_time" name="end_time" 
                                                   placeholder="MM:SS" pattern="([0-9]+:)?[0-5]?[0-9]:[0-5][0-9]">
                                            <div class="form-text small">Format: MM:SS or HH:MM:SS</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="description" class="form-label">Description (Optional)</label>
                                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                                        </div>
                                        
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Add Chapter
                                            </button>
                                            <button type="button" id="captureCurrentTime" class="btn btn-outline-secondary ms-2">
                                                <i class="bi bi-camera"></i> Capture Current Time
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Chapters List -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Chapters</h5>
                                <span class="badge bg-primary">
                                    <?php echo count($chapters); ?> chapters
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="chapters-list">
                                    <?php if (empty($chapters)): ?>
                                    <div class="p-4 text-center">
                                        <p class="text-muted mb-0">No chapters have been added yet.</p>
                                    </div>
                                    <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($chapters as $chapter): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <span class="badge bg-secondary me-2">
                                                        <?php echo formatTimeString($chapter['start_time']); ?>
                                                    </span>
                                                    <?php echo htmlspecialchars($chapter['title']); ?>
                                                </h6>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary preview-chapter" 
                                                            data-time="<?php echo $chapter['start_time']; ?>">
                                                        <i class="bi bi-play"></i> Preview
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-chapter" 
                                                            data-bs-toggle="modal" data-bs-target="#editChapterModal"
                                                            data-chapter-id="<?php echo $chapter['id']; ?>"
                                                            data-chapter-title="<?php echo htmlspecialchars($chapter['title']); ?>"
                                                            data-chapter-start="<?php echo formatTimeString($chapter['start_time']); ?>"
                                                            data-chapter-end="<?php echo $chapter['end_time'] ? formatTimeString($chapter['end_time']) : ''; ?>"
                                                            data-chapter-desc="<?php echo htmlspecialchars($chapter['description'] ?? ''); ?>">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-chapter" 
                                                            data-bs-toggle="modal" data-bs-target="#deleteChapterModal"
                                                            data-chapter-id="<?php echo $chapter['id']; ?>"
                                                            data-chapter-title="<?php echo htmlspecialchars($chapter['title']); ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <?php if ($chapter['description']): ?>
                                            <p class="text-muted small mt-2 mb-0">
                                                <?php echo htmlspecialchars($chapter['description']); ?>
                                            </p>
                                            <?php endif; ?>
                                            
                                            <?php if ($chapter['end_time']): ?>
                                            <div class="small text-muted mt-1">
                                                Duration: <?php echo formatTimeString($chapter['end_time'] - $chapter['start_time']); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <div class="alert alert-warning">
                            Chapters are only available for video and audio content. This media item is an image.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Edit Chapter Modal -->
                <div class="modal fade" id="editChapterModal" tabindex="-1" aria-labelledby="editChapterModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editChapterModalLabel">Edit Chapter</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="update_chapter">
                                    <input type="hidden" name="chapter_id" id="edit_chapter_id">
                                    
                                    <div class="mb-3">
                                        <label for="edit_title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="edit_title" name="title" required>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="edit_start_time" class="form-label">Start Time</label>
                                            <input type="text" class="form-control" id="edit_start_time" name="start_time" 
                                                   placeholder="MM:SS" pattern="([0-9]+:)?[0-5]?[0-9]:[0-5][0-9]" required>
                                            <div class="form-text small">Format: MM:SS or HH:MM:SS</div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="edit_end_time" class="form-label">End Time (Optional)</label>
                                            <input type="text" class="form-control" id="edit_end_time" name="end_time" 
                                                   placeholder="MM:SS" pattern="([0-9]+:)?[0-5]?[0-9]:[0-5][0-9]">
                                            <div class="form-text small">Format: MM:SS or HH:MM:SS</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_description" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Chapter Modal -->
                <div class="modal fade" id="deleteChapterModal" tabindex="-1" aria-labelledby="deleteChapterModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteChapterModalLabel">Delete Chapter</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete the chapter "<span id="delete_chapter_title"></span>"?</p>
                                <p class="text-danger">This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <form method="post">
                                    <input type="hidden" name="action" value="delete_chapter">
                                    <input type="hidden" name="chapter_id" id="delete_chapter_id">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete</button>
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
    <script src="includes/dark-mode.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mediaPreview = document.getElementById('mediaPreview');
            
            // Initialize modals
            const editChapterModal = document.getElementById('editChapterModal');
            if (editChapterModal) {
                editChapterModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const chapterId = button.getAttribute('data-chapter-id');
                    const chapterTitle = button.getAttribute('data-chapter-title');
                    const chapterStart = button.getAttribute('data-chapter-start');
                    const chapterEnd = button.getAttribute('data-chapter-end');
                    const chapterDesc = button.getAttribute('data-chapter-desc');
                    
                    document.getElementById('edit_chapter_id').value = chapterId;
                    document.getElementById('edit_title').value = chapterTitle;
                    document.getElementById('edit_start_time').value = chapterStart;
                    document.getElementById('edit_end_time').value = chapterEnd || '';
                    document.getElementById('edit_description').value = chapterDesc || '';
                });
            }
            
            const deleteChapterModal = document.getElementById('deleteChapterModal');
            if (deleteChapterModal) {
                deleteChapterModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const chapterId = button.getAttribute('data-chapter-id');
                    const chapterTitle = button.getAttribute('data-chapter-title');
                    
                    document.getElementById('delete_chapter_id').value = chapterId;
                    document.getElementById('delete_chapter_title').textContent = chapterTitle;
                });
            }
            
            // Handle chapter preview
            const previewButtons = document.querySelectorAll('.preview-chapter');
            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const time = parseFloat(this.getAttribute('data-time'));
                    if (mediaPreview) {
                        mediaPreview.currentTime = time;
                        mediaPreview.play();
                    }
                });
            });
            
            // Handle chapter markers click
            const chapterMarkers = document.querySelectorAll('.chapter-marker');
            chapterMarkers.forEach(marker => {
                marker.addEventListener('click', function() {
                    const time = parseFloat(this.getAttribute('data-chapter-time'));
                    if (mediaPreview) {
                        mediaPreview.currentTime = time;
                        mediaPreview.play();
                    }
                });
            });
            
            // Handle current time capture
            const captureButton = document.getElementById('captureCurrentTime');
            if (captureButton && mediaPreview) {
                captureButton.addEventListener('click', function() {
                    const currentTime = mediaPreview.currentTime;
                    const startTimeInput = document.getElementById('start_time');
                    
                    if (startTimeInput) {
                        // Format the time as MM:SS or HH:MM:SS
                        startTimeInput.value = formatTime(currentTime);
                    }
                });
            }
            
            // Format time helper function
            function formatTime(seconds) {
                seconds = Math.floor(seconds);
                
                if (seconds >= 3600) {
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const secs = seconds % 60;
                    
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                } else {
                    const minutes = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    
                    return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }
            }
        });
    </script>
</body>
</html>