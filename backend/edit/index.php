// backend/edit/index.php
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}

// Base paths
$basePath = dirname(dirname(__FILE__));
$relativeBasePath = '..';

// Initialize variables
$current_file = '';
$file_content = '';
$success_message = '';
$error_message = '';
$file_list = [];

// Handle file operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save file
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $filepath = $basePath . '/' . $_POST['filepath'];
        $content = $_POST['content'];
        
        // Validate path is within our project
        if (strpos(realpath($filepath), realpath($basePath)) !== 0) {
            $error_message = 'Invalid file path. Cannot save files outside project directory.';
        } else {
            if (file_put_contents($filepath, $content) !== false) {
                $success_message = 'File saved successfully.';
                $current_file = $_POST['filepath'];
                $file_content = $content;
            } else {
                $error_message = 'Failed to save file. Check file permissions.';
            }
        }
    }
    
    // Execute command
    if (isset($_POST['action']) && $_POST['action'] === 'execute') {
        $command = $_POST['command'];
        
        // Validate command for basic security
        $allowed_commands = [
            'npm run dev',
            'npm run build',
            'npm install',
            'composer install',
            'composer update',
            'php -v',
            'node -v',
            'npm -v'
        ];
        
        $command_safe = false;
        foreach ($allowed_commands as $allowed) {
            if (strpos($command, $allowed) === 0) {
                $command_safe = true;
                break;
            }
        }
        
        if (!$command_safe) {
            $error_message = 'Command not allowed for security reasons.';
        } else {
            $output = [];
            $return_var = 0;
            
            // Execute command in the appropriate directory
            if (strpos($command, 'npm') === 0 || strpos($command, 'node') === 0) {
                $cwd = dirname(dirname(dirname(__FILE__))) . '/frontend';
                chdir($cwd);
            }
            
            exec($command . ' 2>&1', $output, $return_var);
            
            $result = implode(PHP_EOL, $output);
            
            if ($return_var === 0) {
                $success_message = 'Command executed successfully: ' . $command;
            } else {
                $error_message = 'Command execution failed with error code ' . $return_var;
            }
            
            // Store command output in session for display
            $_SESSION['command_output'] = $result;
        }
    }
}

// Load file content
if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    $fullpath = $basePath . '/' . $filepath;
    
    // Validate path is within our project
    if (file_exists($fullpath) && strpos(realpath($fullpath), realpath($basePath)) === 0) {
        $current_file = $filepath;
        $file_content = file_get_contents($fullpath);
    } else {
        $error_message = 'Invalid or non-existent file.';
    }
}

// Function to scan directory recursively
function scanDirectoryRecursive($dir, $baseDir = '') {
    $result = [];
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        $relativePath = $baseDir ? $baseDir . '/' . $file : $file;
        
        if (is_dir($path)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath,
                'children' => scanDirectoryRecursive($path, $relativePath)
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
                'path' => $relativePath
            ];
        }
    }
    
    return $result;
}

// Get file list for navigator
$file_list = scanDirectoryRecursive($basePath, '');

// Get appropriate editor mode based on file extension
function getEditorMode($filepath) {
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    
    switch ($extension) {
        case 'php':
            return 'application/x-httpd-php';
        case 'js':
            return 'text/javascript';
        case 'jsx':
            return 'text/jsx';
        case 'css':
            return 'text/css';
        case 'html':
            return 'text/html';
        case 'json':
            return 'application/json';
        case 'sql':
            return 'text/x-sql';
        case 'md':
            return 'text/markdown';
        default:
            return 'text/plain';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Code Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .editor-header {
            flex: 0 0 auto;
        }
        .editor-body {
            flex: 1 1 auto;
            display: flex;
            overflow: hidden;
        }
        .file-explorer {
            flex: 0 0 250px;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
        }
        .editor-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .CodeMirror {
            height: 100%;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        .treeview ul {
            list-style: none;
            padding-left: 1.2rem;
        }
        .treeview > ul {
            padding-left: 0;
        }
        .treeview li {
            padding: 2px 0;
        }
        .treeview .folder {
            cursor: pointer;
        }
        .treeview .file {
            cursor: pointer;
        }
        .treeview .file:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 0.5rem;
            font-family: monospace;
            overflow-y: auto;
            height: 200px;
            display: none;
        }
        .terminal.show {
            display: block;
        }
        .preview-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .tabs {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .tab {
            display: inline-block;
            padding: 8px 15px;
            cursor: pointer;
            border-right: 1px solid #dee2e6;
        }
        .tab.active {
            background-color: #fff;
            border-bottom: 2px solid #0d6efd;
        }
        .tab-content {
            display: none;
            height: 100%;
        }
        .tab-content.active {
            display: block;
        }
        .resizable-panel {
            resize: vertical;
            overflow: auto;
            min-height: 200px;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Media Share Editor</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/index.php">
                                    <i class="bi bi-speedometer2"></i> Admin Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnSaveFile">
                                    <i class="bi bi-save"></i> Save File
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnToggleTerminal">
                                    <i class="bi bi-terminal"></i> Terminal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="btnPreview">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="editor-body">
            <div class="file-explorer">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">File Explorer</h6>
                    <button class="btn btn-sm btn-outline-secondary" id="btnRefreshFiles">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="treeview">
                    <ul>
                        <?php function renderFileTree($items) { ?>
                            <?php foreach ($items as $item): ?>
                                <?php if ($item['type'] === 'directory'): ?>
                                    <li>
                                        <div class="folder" data-path="<?php echo htmlspecialchars($item['path']); ?>">
                                            <i class="bi bi-folder"></i> <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                        <?php if (!empty($item['children'])): ?>
                                            <ul style="display: none;">
                                                <?php renderFileTree($item['children']); ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <div class="file" data-path="<?php echo htmlspecialchars($item['path']); ?>">
                                            <i class="bi bi-file-earmark-code"></i> <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php } ?>
                        <?php renderFileTree($file_list); ?>
                    </ul>
                </div>
            </div>
            
            <div class="editor-content">
                <div class="tabs">
                    <div class="tab active" data-tab="editor">Editor</div>
                    <div class="tab" data-tab="preview">Preview</div>
                    <div class="tab" data-tab="test">Test App</div>
                </div>
                
                <div class="tab-content active" id="editor-tab">
                    <?php if ($current_file): ?>
                        <div class="p-1 bg-light border-bottom">
                            <small class="text-muted">Editing: <?php echo htmlspecialchars($current_file); ?></small>
                        </div>
                        <div id="editor-container" style="height: calc(100% - 25px);"></div>
                    <?php else: ?>
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <i class="bi bi-file-earmark-code display-1 text-muted"></i>
                                <p class="mt-3">Select a file from the explorer to edit</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-content" id="preview-tab">
                    <div class="p-1 bg-light border-bottom d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Preview</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" id="btnReloadPreview">
                                <i class="bi bi-arrow-clockwise"></i> Reload
                            </button>
                        </div>
                    </div>
                    <iframe class="preview-iframe" id="preview-iframe"></iframe>
                </div>
                
                <div class="tab-content" id="test-tab">
                    <div class="p-1 bg-light border-bottom">
                        <small class="text-muted">Test Application</small>
                    </div>
                    <div class="container-fluid p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">Frontend Commands</div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" data-command="npm run dev">
                                                <i class="bi bi-play"></i> Start Development Server
                                            </button>
                                            <button class="btn btn-secondary" data-command="npm run build">
                                                <i class="bi bi-hammer"></i> Build for Production
                                            </button>
                                            <button class="btn btn-info" data-command="npm install">
                                                <i class="bi bi-box"></i> Install Dependencies
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">System Info</div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-secondary" data-command="php -v">
                                                <i class="bi bi-info-circle"></i> PHP Version
                                            </button>
                                            <button class="btn btn-outline-secondary" data-command="node -v">
                                                <i class="bi bi-info-circle"></i> Node.js Version
                                            </button>
                                            <button class="btn btn-outline-secondary" data-command="npm -v">
                                                <i class="bi bi-info-circle"></i> NPM Version
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Test Application</span>
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary" id="btnToggleSize">
                                                    <i class="bi bi-arrows-angle-expand"></i> Toggle Size
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <iframe src="../../" id="test-iframe" style="width: 100%; height: 500px; border: none;"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="terminal resizable-panel" id="terminal">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted">Terminal Output</span>
                <button class="btn btn-sm btn-close btn-close-white" id="btnCloseTerminal"></button>
            </div>
            <pre id="terminal-output"><?php echo isset($_SESSION['command_output']) ? htmlspecialchars($_SESSION['command_output']) : ''; ?></pre>
        </div>
    </div>
    
    <!-- Hidden forms for POST actions -->
    <form id="saveForm" method="post" style="display: none;">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="filepath" id="save_filepath" value="<?php echo htmlspecialchars($current_file); ?>">
        <textarea name="content" id="save_content"></textarea>
    </form>
    
    <form id="executeForm" method="post" style="display: none;">
        <input type="hidden" name="action" value="execute">
        <input type="hidden" name="command" id="execute_command">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
    
    <!-- CodeMirror modes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/jsx/jsx.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/markdown/markdown.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/sql/sql.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let editor = null;
            let currentFilePath = '<?php echo addslashes($current_file); ?>';
            
            // Initialize CodeMirror if a file is selected
            if (document.getElementById('editor-container')) {
                editor = CodeMirror(document.getElementById('editor-container'), {
                    value: <?php echo json_encode($file_content); ?>,
                    mode: '<?php echo $current_file ? getEditorMode($current_file) : 'text/plain'; ?>',
                    theme: 'dracula',
                    lineNumbers: true,
                    indentUnit: 4,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    lineWrapping: true,
                    tabSize: 4,
                    indentWithTabs: false,
                    extraKeys: {"Ctrl-Space": "autocomplete"}
                });
                
                // Auto-resize editor when window is resized
                window.addEventListener('resize', function() {
                    if (editor) {
                        editor.refresh();
                    }
                });
            }
            
            // File tree navigation
            document.querySelectorAll('.folder').forEach(folder => {
                folder.addEventListener('click', function() {
                    const parentLi = this.parentNode;
                    const subList = parentLi.querySelector('ul');
                    
                    if (subList) {
                        const isHidden = subList.style.display === 'none';
                        subList.style.display = isHidden ? 'block' : 'none';
                        
                        // Update folder icon
                        const icon = this.querySelector('i');
                        icon.className = isHidden ? 'bi bi-folder-open' : 'bi bi-folder';
                    }
                });
            });
            
            document.querySelectorAll('.file').forEach(file => {
                file.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-path');
                    window.location.href = `index.php?file=${encodeURIComponent(filePath)}`;
                });
            });
            
            // Save file
            document.getElementById('btnSaveFile').addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!currentFilePath || !editor) {
                    alert('No file is currently open for editing.');
                    return;
                }
                
                document.getElementById('save_filepath').value = currentFilePath;
                document.getElementById('save_content').value = editor.getValue();
                document.getElementById('saveForm').submit();
            });
            
            // Toggle terminal
            document.getElementById('btnToggleTerminal').addEventListener('click', function(e) {
                e.preventDefault();
                const terminal = document.getElementById('terminal');
                terminal.classList.toggle('show');
            });
            
            // Close terminal
            document.getElementById('btnCloseTerminal').addEventListener('click', function() {
                document.getElementById('terminal').classList.remove('show');
            });
            
            // Execute commands
            document.querySelectorAll('[data-command]').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    if (confirm(`Execute command: ${command}?`)) {
                        document.getElementById('execute_command').value = command;
                        document.getElementById('executeForm').submit();
                    }
                });
            });
            
            // Refresh file list
            document.getElementById('btnRefreshFiles').addEventListener('click', function() {
                window.location.reload();
            });
            
            // Tab navigation
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Update active tab
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                    
                    // Special handling for preview tab
                    if (tabId === 'preview' && currentFilePath) {
                        updatePreview();
                    }
                });
            });
            
            // Preview button
            document.getElementById('btnPreview').addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector('[data-tab="preview"]').click();
            });
            
            // Update preview
            function updatePreview() {
                const previewFrame = document.getElementById('preview-iframe');
                
                if (!currentFilePath) {
                    previewFrame.src = 'about:blank';
                    return;
                }
                
                // Only preview HTML, PHP and image files directly
                const extension = currentFilePath.split('.').pop().toLowerCase();
                if (['html', 'php', 'jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    previewFrame.src = `../${currentFilePath}`;
                } else {
                    previewFrame.src = 'about:blank';
                    previewFrame.onload = function() {
                        const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
                        doc.body.innerHTML = '<div style="padding: 20px; font-family: monospace;">' +
                            '<h3>Preview not available</h3>' +
                            '<p>Direct preview is only available for HTML, PHP and image files.</p>' +
                            '</div>';
                    };
                }
            }
            
            // Reload preview
            document.getElementById('btnReloadPreview').addEventListener('click', function() {
                updatePreview();
            });
            
            // Toggle iframe size
            document.getElementById('btnToggleSize').addEventListener('click', function() {
                const iframe = document.getElementById('test-iframe');
                if (iframe.style.height === '500px') {
                    iframe.style.height = '800px';
                } else {
                    iframe.style.height = '500px';
                }
            });
            
            // Auto-show terminal if there's output
            if (document.getElementById('terminal-output').textContent.trim()) {
                document.getElementById('terminal').classList.add('show');
            }
        });
    </script>
</body>
</html>success_message = 'Command executed successfully: ' . $command;
            } else {
                $error_message = 'Command execution failed with error code ' . $return_var;
            }
            
            // Store command output in session for display
            $_SESSION['command_output'] = $result;
        }
    }
}

// Load file content
if (isset($_GET['file'])) {
    $filepath = $_GET['file'];
    $fullpath = $basePath . '/' . $filepath;
    
    // Validate path is within our project
    if (file_exists($fullpath) && strpos(realpath($fullpath), realpath($basePath)) === 0) {
        $current_file = $filepath;
        $file_content = file_get_contents($fullpath);
    } else {
        $error_message = 'Invalid or non-existent file.';
    }
}

// Function to scan directory recursively
function scanDirectoryRecursive($dir, $baseDir = '') {
    $result = [];
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        $relativePath = $baseDir ? $baseDir . '/' . $file : $file;
        
        if (is_dir($path)) {
            // Skip node_modules and vendor directories
            if ($file === 'node_modules' || $file === 'vendor' || $file === 'logs') {
                continue;
            }
            
            $result[] = [
                'type' => 'directory',
                'name' => $file,
                'path' => $relativePath,
                'children' => scanDirectoryRecursive($path, $relativePath)
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
                'path' => $relativePath
            ];
        }
    }
    
    return $result;
}

// Get file list for navigator
$file_list = scanDirectoryRecursive($basePath, '');

// Get appropriate editor mode based on file extension
function getEditorMode($filepath) {
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    
    switch ($extension) {
        case 'php':
            return 'application/x-httpd-php';
        case 'js':
            return 'text/javascript';
        case 'jsx':
            return 'text/jsx';
        case 'css':
            return 'text/css';
        case 'html':
            return 'text/html';
        case 'json':
            return 'application/json';
        case 'sql':
            return 'text/x-sql';
        case 'md':
            return 'text/markdown';
        default:
            return 'text/plain';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Share - Code Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/dracula.min.css">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .editor-header {
            flex: 0 0 auto;
        }
        .editor-body {
            flex: 1 1 auto;
            display: flex;
            overflow: hidden;
        }
        .file-explorer {
            flex: 0 0 250px;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            padding: 1rem;
        }
        .editor-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .CodeMirror {
            height: 100%;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        .treeview ul {
            list-style: none;
            padding-left: 1.2rem;
        }
        .treeview > ul {
            padding-left: 0;
        }
        .treeview li {
            padding: 2px 0;
        }
        .treeview .folder {
            cursor: pointer;
        }
        .treeview .file {
            cursor: pointer;
        }
        .treeview .file:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .terminal {
            background-color: #1e1e1e;
            color: #f8f8f8;
            padding: 0.5rem;
            font-family: monospace;
            overflow-y: auto;
            height: 200px;
            display: