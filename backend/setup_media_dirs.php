// backend/setup_media_dirs.php
<?php
/**
 * Setup script to create and configure media directories
 */
require_once __DIR__ . '/config.php';

// Define paths
$mediaBaseDir = dirname(__FILE__, 2) . '/media';
$thumbnailDir = $mediaBaseDir . '/thumbnails';
$oldUploadsDir = UPLOAD_PATH;
$oldThumbnailDir = THUMBNAIL_PATH;

echo "Setting up media directories...\n";

// Create base media directory if it doesn't exist
if (!file_exists($mediaBaseDir)) {
    if (mkdir($mediaBaseDir, 0755, true)) {
        echo "✅ Created media directory at: $mediaBaseDir\n";
    } else {
        echo "❌ Failed to create media directory at: $mediaBaseDir\n";
        exit(1);
    }
} else {
    echo "✓ Media directory already exists at: $mediaBaseDir\n";
}

// Create thumbnails directory if it doesn't exist
if (!file_exists($thumbnailDir)) {
    if (mkdir($thumbnailDir, 0755, true)) {
        echo "✅ Created thumbnails directory at: $thumbnailDir\n";
    } else {
        echo "❌ Failed to create thumbnails directory at: $thumbnailDir\n";
        exit(1);
    }
} else {
    echo "✓ Thumbnails directory already exists at: $thumbnailDir\n";
}

// Check if running on Unix-like system
$isUnix = (DIRECTORY_SEPARATOR === '/');

// Set proper permissions
if ($isUnix) {
    // Set recursive permissions
    system("chmod -R 755 $mediaBaseDir");
    echo "✅ Set permissions on media directory\n";
    
    // Check for common web server users based on distribution
    // Rocky Linux / RHEL / CentOS typically use 'apache'
    $webServerUser = 'apache';
    
    // Check if the apache user exists
    if (function_exists('posix_getpwnam') && posix_getpwnam($webServerUser)) {
        echo "Using web server user: $webServerUser\n";
        system("chown -R $webServerUser:$webServerUser $mediaBaseDir");
        echo "✅ Set ownership to $webServerUser\n";
    } else {
        // Try to determine from process list (useful if not running as root)
        $processUser = trim(shell_exec("ps -ef | grep -E '(httpd|apache2|nginx)' | grep -v root | head -n1 | awk '{print $1}'"));
        
        if (!empty($processUser)) {
            echo "Detected web server user: $processUser\n";
            system("chown -R $processUser:$processUser $mediaBaseDir");
            echo "✅ Set ownership to $processUser\n";
        } else {
            echo "⚠️ Could not determine web server user. You may need to manually set permissions.\n";
            echo "   Typical commands:\n";
            echo "   - sudo chown -R apache:apache $mediaBaseDir  # for Rocky Linux/CentOS\n";
            echo "   - sudo chown -R nginx:nginx $mediaBaseDir    # if using Nginx\n";
        }
    }
} else {
    echo "⚠️ Running on Windows system. Please ensure your web server has write access to the media directories.\n";
}

// Migrate existing files if needed
if (file_exists($oldUploadsDir) && is_dir($oldUploadsDir)) {
    echo "Do you want to migrate existing files from $oldUploadsDir to $mediaBaseDir? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    
    if (strtolower($line) === 'y') {
        echo "Starting migration...\n";
        
        // Migrate files from uploads directory
        $migratedCount = 0;
        $failedCount = 0;
        
        // Get all files in the uploads directory (non-recursive)
        $files = scandir($oldUploadsDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'thumbnails' || is_dir("$oldUploadsDir/$file")) {
                continue;
            }
            
            $sourcePath = "$oldUploadsDir/$file";
            $targetPath = "$mediaBaseDir/$file";
            
            if (copy($sourcePath, $targetPath)) {
                echo "Copied: $file\n";
                $migratedCount++;
            } else {
                echo "Failed to copy: $file\n";
                $failedCount++;
            }
        }
        
        // Migrate files from thumbnails directory
        if (file_exists($oldThumbnailDir) && is_dir($oldThumbnailDir)) {
            $thumbnailFiles = scandir($oldThumbnailDir);
            foreach ($thumbnailFiles as $file) {
                if ($file === '.' || $file === '..' || is_dir("$oldThumbnailDir/$file")) {
                    continue;
                }
                
                $sourcePath = "$oldThumbnailDir/$file";
                $targetPath = "$thumbnailDir/$file";
                
                if (copy($sourcePath, $targetPath)) {
                    echo "Copied thumbnail: $file\n";
                    $migratedCount++;
                } else {
                    echo "Failed to copy thumbnail: $file\n";
                    $failedCount++;
                }
            }
        }
        
        echo "\n✅ Migration complete: $migratedCount files copied, $failedCount failed.\n";
    } else {
        echo "Migration skipped.\n";
    }
} else {
    echo "No existing uploads directory found at $oldUploadsDir. No files to migrate.\n";
}

// Set a constant for auto-migration in config
echo "\nDo you want to automatically migrate files when they're accessed? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) === 'y') {
    // Try to update config.php to add AUTO_MIGRATE_FILES constant
    $configFile = __DIR__ . '/config.php';
    $configContent = file_get_contents($configFile);
    
    if (strpos($configContent, 'AUTO_MIGRATE_FILES') === false) {
        // Add constant just before the error_reporting line
        $newContent = str_replace(
            "error_reporting(E_ALL);",
            "// Auto-migrate files from old to new location when accessed\ndefine('AUTO_MIGRATE_FILES', true);\n\nerror_reporting(E_ALL);",
            $configContent
        );
        
        file_put_contents($configFile, $newContent);
        echo "✅ Added AUTO_MIGRATE_FILES constant to config.php\n";
    } else {
        echo "AUTO_MIGRATE_FILES constant already exists in config.php\n";
    }
} else {
    echo "Auto-migration disabled.\n";
}

echo "\nSetup completed successfully!\n";