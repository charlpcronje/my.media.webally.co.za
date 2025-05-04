<?php
// backend/admin/login.php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Initialize variables
$error = '';
$username = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Only allow user 'charl' with password '4334.4334'
        if ($username === 'charl' && $password === '4334.4334') {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = 1; // hardcoded id for 'charl'
            $_SESSION['admin_username'] = $username;
            
            // Redirect to dashboard
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            height: 100vh;
        }
        
        .form-login {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        
        .form-login .form-floating:focus-within {
            z-index: 2;
        }
        
        .form-login input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .form-login input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body class="text-center">
    <main class="form-login">
        <form method="POST" action="login.php">
            <h1 class="h3 mb-3 fw-normal">Media Share Admin</h1>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" 
                       placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Password" required>
                <label for="password">Password</label>
            </div>
            
            <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-muted">&copy; <?php echo date('Y'); ?> Media Share</p>
        </form>
    </main>
</body>
</html>