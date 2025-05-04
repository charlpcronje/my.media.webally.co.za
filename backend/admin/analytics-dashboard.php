<?php
// backend/admin/analytics-dashboard.php
require_once('../config.php');
require_once('../models/AnalyticsRepository.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Analytics Dashboard';

// Get database connection
$db = getDbConnection();
if (!$db) {
    die('Database connection failed');
}

// Initialize analytics repository
$analyticsRepo = new AnalyticsRepository($db);

// Get filter parameters
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-d', strtotime('-30 days'));
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';
$mediaTypeFilter = isset($_GET['media_type']) ? $_GET['media_type'] : '';

// Set up filters array
$filters = [
    'date_from' => $dateFrom,
    'date_to' => $dateTo
];

if ($userFilter) {
    $filters['user_name'] = $userFilter;
}

if ($mediaTypeFilter) {
    $filters['media_type'] = $mediaTypeFilter;
}

// Get analytics data
$overallAnalytics = $analyticsRepo->getOverallAnalytics($filters);
$searchAnalytics = $analyticsRepo->getSearchAnalytics($filters);

// Get unique users for filter
try {
    $stmt = $db->query("SELECT DISTINCT user_name FROM analytics ORDER BY user_name");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    logError('Error getting users: ' . $e->getMessage());
    $users = [];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css">
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
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card-primary {
            border-left-color: var(--bs-primary);
        }
        .stat-card-success {
            border-left-color: var(--bs-success);
        }
        .stat-card-info {
            border-left-color: var(--bs-info);
        }
        .stat-card-warning {
            border-left-color: var(--bs-warning);
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
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
                
                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="text" class="form-control date-picker" id="date_from" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="text" class="form-control date-picker" id="date_to" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>">
                            </div>
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
                                <label for="media_type" class="form-label">Media Type</label>
                                <select name="media_type" id="media_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="video" <?php echo $mediaTypeFilter === 'video' ? 'selected' : ''; ?>>Video</option>
                                    <option value="audio" <?php echo $mediaTypeFilter === 'audio' ? 'selected' : ''; ?>>Audio</option>
                                    <option value="image" <?php echo $mediaTypeFilter === 'image' ? 'selected' : ''; ?>>Image</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="analytics-dashboard.php" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Key Metrics -->
                <div class="row mb-4">
                    <?php 
                    $totalViews = 0;
                    $totalPlays = 0;
                    $totalUsers = count($overallAnalytics['top_users']);
                    
                    // Sum up views and plays from event types
                    foreach ($overallAnalytics['event_types'] as $event) {
                        if ($event['event_type'] === 'view') {
                            $totalViews = $event['count'];
                        } else if ($event['event_type'] === 'play') {
                            $totalPlays = $event['count'];
                        }
                    }
                    
                    // Calculate average engagement per user
                    $avgEngagement = 0;
                    if ($totalUsers > 0) {
                        $totalEvents = array_sum(array_column($overallAnalytics['event_types'], 'count'));
                        $avgEngagement = round($totalEvents / $totalUsers);
                    }
                    ?>
                    <div class="col-md-3">
                        <div class="card stat-card stat-card-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Total Views</h6>
                                        <h3 class="mb-0"><?php echo number_format($totalViews); ?></h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-eye fs-3 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card stat-card-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Total Plays</h6>
                                        <h3 class="mb-0"><?php echo number_format($totalPlays); ?></h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-play-circle fs-3 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card stat-card-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Active Users</h6>
                                        <h3 class="mb-0"><?php echo number_format($totalUsers); ?></h3>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-people fs-3 text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card stat-card-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-1">Avg. Engagement</h6>
                                        <h3 class="mb-0"><?php echo number_format($avgEngagement); ?></h3>
                                        <small class="text-muted">events per user</small>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-bar-chart fs-3 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Event Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="eventDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Media Type Distribution</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="mediaTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Media & Users -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Top Media</h5>
                                <a href="analytics.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Type</th>
                                                <th>Views</th>
                                                <th>Unique Viewers</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($overallAnalytics['top_media'])): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-3">No data available</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach ($overallAnalytics['top_media'] as $media): ?>
                                                <tr>
                                                    <td>
                                                        <a href="edit-media.php?id=<?php echo $media['id']; ?>">
                                                            <?php echo htmlspecialchars($media['caption']); ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $media['type'] === 'video' ? 'success' : 
                                                                ($media['type'] === 'audio' ? 'info' : 'warning'); 
                                                        ?>">
                                                            <?php echo ucfirst($media['type']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo number_format($media['view_count']); ?></td>
                                                    <td><?php echo number_format($media['unique_viewers']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Top Users</h5>
                                <a href="analytics.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Media Viewed</th>
                                                <th>Actions</th>
                                                <th>View Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($overallAnalytics['top_users'])): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-3">No data available</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach ($overallAnalytics['top_users'] as $user): ?>
                                                <tr>
                                                    <td>
                                                        <a href="analytics.php?user=<?php echo urlencode($user['user_name']); ?>">
                                                            <?php echo htmlspecialchars($user['user_name']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo number_format($user['media_viewed']); ?></td>
                                                    <td><?php echo number_format($user['action_count']); ?></td>
                                                    <td>
                                                        <?php 
                                                        $viewTimeMinutes = round(($user['total_view_time'] ?? 0) / 60);
                                                        echo $viewTimeMinutes ? "{$viewTimeMinutes} min" : "N/A"; 
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search Analytics -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Top Searches</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Search Term</th>
                                                <th>Count</th>
                                                <th>Avg. Results</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($searchAnalytics['top_searches'])): ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3">No search data available</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach ($searchAnalytics['top_searches'] as $search): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($search['search_term']); ?></td>
                                                    <td><?php echo number_format($search['count']); ?></td>
                                                    <td><?php echo round($search['avg_results']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Zero Result Searches</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Search Term</th>
                                                <th>Count</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($searchAnalytics['zero_results'])): ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-3">No zero result searches</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach ($searchAnalytics['zero_results'] as $search): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($search['search_term']); ?></td>
                                                    <td><?php echo number_format($search['count']); ?></td>
                                                    <td>
                                                        <a href="tags.php?add=<?php echo urlencode($search['search_term']); ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            Add as Tag
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="includes/dark-mode.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr('.date-picker', {
                dateFormat: 'Y-m-d',
                maxDate: 'today'
            });
            
            // Chart colors
            const chartColors = {
                primary: '#0d6efd',
                success: '#198754',
                info: '#0dcaf0',
                warning: '#ffc107',
                danger: '#dc3545',
                secondary: '#6c757d',
                light: '#f8f9fa',
                dark: '#212529'
            };
            
            // Initialize charts
            
            // Event Distribution Chart
            const eventDistributionCtx = document.getElementById('eventDistributionChart').getContext('2d');
            const eventDistributionChart = new Chart(eventDistributionCtx, {
                type: 'pie',
                data: {
                    labels: <?php 
                        echo json_encode(array_column($overallAnalytics['event_types'], 'event_type')); 
                    ?>,
                    datasets: [{
                        data: <?php 
                            echo json_encode(array_column($overallAnalytics['event_types'], 'count')); 
                        ?>,
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.success,
                            chartColors.info,
                            chartColors.warning,
                            chartColors.danger,
                            chartColors.secondary
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // Media Type Chart
            const mediaTypeCtx = document.getElementById('mediaTypeChart').getContext('2d');
            const mediaTypeChart = new Chart(mediaTypeCtx, {
                type: 'bar',
                data: {
                    labels: <?php 
                        echo json_encode(array_column($overallAnalytics['media_types'], 'type')); 
                    ?>,
                    datasets: [{
                        label: 'Views',
                        data: <?php 
                            echo json_encode(array_column($overallAnalytics['media_types'], 'view_count')); 
                        ?>,
                        backgroundColor: chartColors.primary,
                        barPercentage: 0.5
                    },
                    {
                        label: 'Unique Viewers',
                        data: <?php 
                            echo json_encode(array_column($overallAnalytics['media_types'], 'unique_viewers')); 
                        ?>,
                        backgroundColor: chartColors.success,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>