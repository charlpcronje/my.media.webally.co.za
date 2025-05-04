<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Admin Dashboard';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Get media count by type
try {
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) as videos,
            SUM(CASE WHEN type = 'audio' THEN 1 ELSE 0 END) as audio,
            SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as images
        FROM media
    ");
    $mediaCounts = $stmt->fetch();
} catch (PDOException $e) {
    logError('Error getting media counts: ' . $e->getMessage());
    $mediaCounts = [
        'total' => 0,
        'videos' => 0,
        'audio' => 0,
        'images' => 0
    ];
}

// Get recent events
try {
    $stmt = $conn->query("
        SELECT a.*, m.caption, m.type
        FROM analytics a
        JOIN media m ON a.media_id = m.id
        ORDER BY a.timestamp DESC
        LIMIT 10
    ");
    $recentEvents = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting recent events: ' . $e->getMessage());
    $recentEvents = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar">
                        <a href="#" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#testAppModal">
                            <i class="bi bi-bug"></i> Test All
                        </a>
                        <a href="upload.php" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Upload New Media
                        </a>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Media</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['total']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Videos</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['videos']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Audio</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['audio']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Images</h5>
                                <p class="card-text display-6"><?php echo $mediaCounts['images']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Media</th>
                                        <th>Event</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentEvents)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent activities</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentEvents as $event): ?>
                                        <tr>
                                            <td><?php echo date('M d, H:i', strtotime($event['timestamp'])); ?></td>
                                            <td><?php echo htmlspecialchars($event['user_name']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($event['caption']); ?>
                                                <span class="badge bg-secondary"><?php echo $event['type']; ?></span>
                                            </td>
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
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="analytics.php" class="btn btn-sm btn-outline-secondary">View All Activities</a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Manage Media</h5>
                                <p class="card-text">View, edit, or delete media files.</p>
                                <a href="media.php" class="btn btn-primary">Go to Media</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Manage Tags</h5>
                                <p class="card-text">Create or delete tags for media organization.</p>
                                <a href="tags.php" class="btn btn-primary">Go to Tags</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">View Analytics</h5>
                                <p class="card-text">See detailed user interaction statistics.</p>
                                <a href="analytics.php" class="btn btn-primary">Go to Analytics</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Test App Modal -->
    <div class="modal fade" id="testAppModal" tabindex="-1" aria-labelledby="testAppModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testAppModalLabel">Test Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex bg-light border-bottom p-2">
                        <div class="btn-group me-3">
                            <button class="btn btn-sm btn-outline-secondary" id="deviceDesktop">
                                <i class="bi bi-laptop"></i> Desktop
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="deviceTablet">
                                <i class="bi bi-tablet"></i> Tablet
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" id="deviceMobile">
                                <i class="bi bi-phone"></i> Mobile
                            </button>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" id="reloadIframe">
                            <i class="bi bi-arrow-clockwise"></i> Reload
                        </button>
                    </div>
                    <div class="iframe-container d-flex justify-content-center align-items-center bg-light p-3">
                        <iframe id="previewFrame" src="../../frontend/dist/index.html" style="width: 100%; height: 600px; border: 1px solid #dee2e6; transition: all 0.3s ease;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="../edit/index.php" class="btn btn-primary">
                        <i class="bi bi-code-slash"></i> Open Editor
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle responsive testing buttons
            const iframe = document.getElementById('previewFrame');
            
            // Device sizes
            const devices = {
                desktop: { width: '100%', height: '600px' },
                tablet: { width: '768px', height: '1024px' },
                mobile: { width: '375px', height: '812px' }
            };
            
            // Set desktop active by default
            document.getElementById('deviceDesktop').classList.add('active');
            
            // Desktop button
            document.getElementById('deviceDesktop').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.desktop.width;
                iframe.style.height = devices.desktop.height;
            });
            
            // Tablet button
            document.getElementById('deviceTablet').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.tablet.width;
                iframe.style.height = devices.tablet.height;
            });
            
            // Mobile button
            document.getElementById('deviceMobile').addEventListener('click', function() {
                resetButtons();
                this.classList.add('active');
                iframe.style.width = devices.mobile.width;
                iframe.style.height = devices.mobile.height;
            });
            
            // Reload button
            document.getElementById('reloadIframe').addEventListener('click', function() {
                iframe.src = iframe.src;
            });
            
            // Helper function to reset button states
            function resetButtons() {
                document.getElementById('deviceDesktop').classList.remove('active');
                document.getElementById('deviceTablet').classList.remove('active');
                document.getElementById('deviceMobile').classList.remove('active');
            }
        });
    </script>
    <script src="includes/dark-mode.js"></script>
</body>
</html>