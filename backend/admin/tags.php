<?php
require_once('../config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Page title
$pageTitle = 'Manage Tags';

// Get database connection
$conn = getDbConnection();
if (!$conn) {
    die('Database connection failed');
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle tag operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new tag
    if (isset($_POST['action']) && $_POST['action'] === 'add_tag') {
        $tagName = trim($_POST['tag_name'] ?? '');
        
        if (empty($tagName)) {
            $error_message = 'Tag name cannot be empty';
        } else {
            try {
                // Check if tag already exists
                $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
                $stmt->execute([$tagName]);
                
                if ($stmt->rowCount() > 0) {
                    $error_message = 'Tag already exists';
                } else {
                    // Insert new tag
                    $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
                    $stmt->execute([$tagName]);
                    
                    $success_message = 'Tag added successfully';
                }
            } catch (PDOException $e) {
                logError('Error adding tag: ' . $e->getMessage());
                $error_message = 'An error occurred while adding the tag';
            }
        }
    }
    
    // Delete tag
    if (isset($_POST['action']) && $_POST['action'] === 'delete_tag') {
        $tagId = $_POST['tag_id'] ?? 0;
        
        if (empty($tagId)) {
            $error_message = 'Invalid tag ID';
        } else {
            try {
                // Start transaction
                $conn->beginTransaction();
                
                // Delete tag associations
                $stmt = $conn->prepare("DELETE FROM media_tags WHERE tag_id = ?");
                $stmt->execute([$tagId]);
                
                // Delete tag
                $stmt = $conn->prepare("DELETE FROM tags WHERE id = ?");
                $stmt->execute([$tagId]);
                
                // Commit transaction
                $conn->commit();
                
                $success_message = 'Tag deleted successfully';
            } catch (PDOException $e) {
                // Rollback transaction on error
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                
                logError('Error deleting tag: ' . $e->getMessage());
                $error_message = 'An error occurred while deleting the tag';
            }
        }
    }
}

// Get all tags with usage count
try {
    $stmt = $conn->query("
        SELECT t.id, t.name, COUNT(mt.media_id) as count
        FROM tags t
        LEFT JOIN media_tags mt ON t.id = mt.tag_id
        GROUP BY t.id
        ORDER BY t.name ASC
    ");
    $tags = $stmt->fetchAll();
} catch (PDOException $e) {
    logError('Error getting tags: ' . $e->getMessage());
    $tags = [];
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
        .tag-badge {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
        }
        .tag-count {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 50rem;
            padding: 0.15rem 0.5rem;
            margin-left: 0.5rem;
            font-size: 0.8rem;
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
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Add New Tag</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <input type="hidden" name="action" value="add_tag">
                                    
                                    <div class="mb-3">
                                        <label for="tag_name" class="form-label">Tag Name</label>
                                        <input type="text" class="form-control" id="tag_name" name="tag_name" 
                                            placeholder="Enter tag name" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Add Tag
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Tag Cloud</h5>
                                <span class="badge bg-secondary"><?php echo count($tags); ?> tags total</span>
                            </div>
                            <div class="card-body">
                                <?php if (empty($tags)): ?>
                                <div class="alert alert-info mb-0">
                                    No tags found. Create your first tag using the form on the left.
                                </div>
                                <?php else: ?>
                                <div>
                                    <?php foreach ($tags as $tag): ?>
                                    <span class="tag-badge bg-primary text-white">
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                        <span class="tag-count"><?php echo $tag['count']; ?></span>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Manage Tags</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tag Name</th>
                                        <th>Media Count</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tags)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No tags found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($tags as $tag): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($tag['name']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($tag['count'] > 0): ?>
                                                <a href="media.php?tag=<?php echo urlencode($tag['name']); ?>" class="text-decoration-none">
                                                    <?php echo $tag['count']; ?> media items
                                                </a>
                                                <?php else: ?>
                                                <span class="text-muted">0 media items</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-muted">-</span>
                                            </td>
                                            <td>
                                                <?php if ($tag['count'] == 0): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                                    <input type="hidden" name="action" value="delete_tag">
                                                    <input type="hidden" name="tag_id" value="<?php echo $tag['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-danger" disabled title="Can't delete tag with associated media">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                                <?php endif; ?>
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