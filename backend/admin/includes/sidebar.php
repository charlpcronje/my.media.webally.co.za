<!-- backend/admin/includes/sidebar.php -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>" href="media.php">
                    <i class="bi bi-file-earmark-play"></i>
                    Media Library
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : ''; ?>" href="upload.php">
                    <i class="bi bi-cloud-upload"></i>
                    Upload Media
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'active' : ''; ?>" href="tags.php">
                    <i class="bi bi-tags"></i>
                    Manage Tags
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                    <i class="bi bi-graph-up"></i>
                    Analytics
                </a>
            </li>
        </ul>
    </div>
</nav>