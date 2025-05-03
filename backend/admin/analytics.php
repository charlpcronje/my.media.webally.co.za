<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Analytics';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Get filter values
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';
$eventFilter = isset($_GET['event']) ? $_GET['event'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$mediaFilter = isset($_GET['media']) ? $_GET['media'] : '';

// Base query
$query = "
    SELECT a.*, m.caption, m.type, m.filename
    FROM analytics a
    JOIN media m ON a.media_id = m.id
";

// Add filters
$whereConditions = [];
$params = [];

if ($userFilter) {
    $whereConditions[] = "a.user_name LIKE ?";
    $params[] = "%{$userFilter}%";
}

if ($eventFilter) {
    $whereConditions[] = "a.event_type = ?";
    $params[] = $eventFilter;
}

if ($dateFilter) {
    $whereConditions[] = "DATE(a.timestamp) = ?";
    $params[] = $dateFilter;
}

if ($mediaFilter) {
    $whereConditions[] = "(m.caption LIKE ? OR m.description LIKE ?)";
    $params[] = "%{$mediaFilter}%";
    $params[] = "%{$mediaFilter}%";
}

if ($whereConditions) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

$query .= " ORDER BY a.timestamp DESC LIMIT 100";

// Get analytics data
try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $analytics = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting analytics: ' . $e->getMessage());
    $analytics = [];
}

// Get unique users for filter
try {
    $stmt = $conn->query("SELECT DISTINCT user_name FROM analytics ORDER BY user_name");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting users: ' . $e->getMessage());
    $users = [];
}

// Get event types for filter
try {
    $stmt = $conn->query("SELECT DISTINCT event_type FROM analytics ORDER BY event_type");
    $eventTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting event types: ' . $e->getMessage());
    $eventTypes = [];
}

// Get dates for filter
try {
    $stmt = $conn->query("SELECT DISTINCT DATE(timestamp) as date FROM analytics ORDER BY date DESC");
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting dates: ' . $e->getMessage());
    $dates = [];
}

// Get media items for filter
try {
    $stmt = $conn->query("SELECT id, caption FROM media ORDER BY caption");
    $mediaItems = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting media items: ' . $e->getMessage());
    $mediaItems = [];
}

// Get summary stats
try {
    $stmt = $conn->query("
        SELECT 
            COUNT(*) as total_events,
            COUNT(DISTINCT user_name) as unique_users,
            COUNT(DISTINCT media_id) as media_accessed,
            COUNT(DISTINCT DATE(timestamp)) as active_days
        FROM analytics
    ");
    $stats = $stmt->fetch();
} catch (PDOException $e) {
    logError('Error getting summary stats: ' . $e->getMessage());
    $stats = [
        'total_events' => 0,
        'unique_users' => 0,
        'media_accessed' => 0,
        'active_days' => 0
    ];
}

// Get event type breakdown
try {
    $stmt = $conn->query("
        SELECT 
            event_type,
            COUNT(*) as count
        FROM analytics
        GROUP BY event_type
        ORDER BY count DESC
    ");
    $eventBreakdown = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting event breakdown: ' . $e->getMessage());
    $eventBreakdown = [];
}

// Get top media items
try {
    $stmt = $conn->query("
        SELECT 
            m.id,
            m.caption,
            m.type,
            COUNT(*) as view_count
        FROM analytics a
        JOIN media m ON a.media_id = m.id
        GROUP BY m.id
        ORDER BY view_count DESC
        LIMIT 5
    ");
    $topMedia = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting top media: ' . $e->getMessage());
    $topMedia = [];
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
        .event-badge {
            min-width: 70px;
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
                    <h1 class="h2">Analytics</h1>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Events</h5>
                                <p class="card-text display-6"><?php echo $stats['total_events']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Unique Users</h5>
                                <p class="card-text display-6"><?php echo $stats['unique_users']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Media Accessed</h5>
                                <p class="card-text display-6"><?php echo $stats['media_accessed']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Active Days</h5>
                                <p class="card-text display-6"><?php echo $stats['active_days']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="user" class="form-label">User</label>
                                <select name="user" id="user" class="form-select">
                                    <option value="">All Users</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user); ?>" <?php echo $userFilter === $user ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="event" class="form-label">Event Type</label>
                                <select name="event" id="event" class="form-select">
                                    <option value="">All Events</option>
                                    <?php foreach ($eventTypes as $event): ?>
                                    <option value="<?php echo htmlspecialchars($event); ?>" <?php echo $eventFilter === $event ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($event)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date" class="form-label">Date</label>
                                <select name="date" id="date" class="form-select">
                                    <option value="">All Dates</option>
                                    <?php foreach ($dates as $date): ?>
                                    <option value="<?php echo htmlspecialchars($date); ?>" <?php echo $dateFilter === $date ? 'selected' : ''; ?>>
                                        <?php echo date('M d, Y', strtotime($date)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="media" class="form-label">Media Search</label>
                                <input type="text" class="form-control" id="media" name="media" value="<?php echo htmlspecialchars($mediaFilter); ?>" placeholder="Search media...">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="analytics.php" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Analytics Table -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Activity Log</h5>
                        <span class="badge bg-secondary"><?php echo count($analytics); ?> events</span>
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
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($analytics)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No analytics data found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($analytics as $event): ?>
                                        <tr>
                                            <td><?php echo date('M d, H:i:s', strtotime($event['timestamp'])); ?></td>
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
                                                <span class="badge <?php echo $eventClass; ?> event-badge">
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
                                            <td>
                                                <small class="text-muted"><?php echo $event['ip_address']; ?></small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Event Breakdown -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Event Type Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Event Type</th>
                                                <th>Count</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $totalEvents = array_sum(array_column($eventBreakdown, 'count'));
                                            foreach ($eventBreakdown as $item): 
                                                $percentage = $totalEvents > 0 ? ($item['count'] / $totalEvents) * 100 : 0;
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                        $eventClass = '';
                                                        switch ($item['event_type']) {
                                                            case 'view': $eventClass = 'bg-primary'; break;
                                                            case 'play': $eventClass = 'bg-success'; break;
                                                            case 'pause': $eventClass = 'bg-warning'; break;
                                                            case 'seek': $eventClass = 'bg-info'; break;
                                                            case 'ended': $eventClass = 'bg-danger'; break;
                                                            case 'download': $eventClass = 'bg-secondary'; break;
                                                            default: $eventClass = 'bg-light text-dark';
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $eventClass; ?> event-badge">
                                                        <?php echo ucfirst($item['event_type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $item['count']; ?></td>
                                                <td>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar" role="progressbar" 
                                                            style="width: <?php echo $percentage; ?>%"
                                                            aria-valuenow="<?php echo $percentage; ?>" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo round($percentage, 1); ?>%
                                                    </small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Top Media Items</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Media</th>
                                                <th>Type</th>
                                                <th>Views</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topMedia as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['caption']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo $item['type']; ?></span></td>
                                                <td><?php echo $item['view_count']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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