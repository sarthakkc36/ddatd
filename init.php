<?php
// Initialization script for HomeCare website
define('ALLOWED_ACCESS', true);

// Include configuration
require_once 'includes/config.php';

// Function to create directory if it doesn't exist
function createDirectory($path) {
    if (!file_exists($path)) {
        if (mkdir($path, 0755, true)) {
            echo "Created directory: $path\n";
            return true;
        } else {
            echo "Failed to create directory: $path\n";
            return false;
        }
    } else {
        echo "Directory already exists: $path\n";
        return true;
    }
}

// Function to set file permissions
function setPermissions($path, $dirPermissions = 0755, $filePermissions = 0644) {
    if (!file_exists($path)) {
        echo "Path does not exist: $path\n";
        return false;
    }

    if (is_file($path)) {
        if (chmod($path, $filePermissions)) {
            echo "Set file permissions for: $path\n";
            return true;
        }
        return false;
    }

    if (is_dir($path)) {
        if (chmod($path, $dirPermissions)) {
            echo "Set directory permissions for: $path\n";
            
            $items = new DirectoryIterator($path);
            foreach ($items as $item) {
                if ($item->isDot()) continue;
                
                $itemPath = $path . DIRECTORY_SEPARATOR . $item->getFilename();
                setPermissions($itemPath, $dirPermissions, $filePermissions);
            }
            return true;
        }
        return false;
    }
}

// Required directories
$directories = [
    'logs',
    'uploads',
    'uploads/documents',
    'uploads/images',
    'uploads/temp'
];

// Create directories
echo "Creating required directories...\n";
foreach ($directories as $dir) {
    createDirectory(__DIR__ . DIRECTORY_SEPARATOR . $dir);
}

// Set permissions for key files and directories
echo "\nSetting permissions...\n";
$permissions = [
    [__DIR__ . DIRECTORY_SEPARATOR . 'logs', 0755],
    [__DIR__ . DIRECTORY_SEPARATOR . 'uploads', 0755],
    [__DIR__ . DIRECTORY_SEPARATOR . '.htaccess', 0644],
    [__DIR__ . DIRECTORY_SEPARATOR . 'includes/config.php', 0644]
];

foreach ($permissions as $item) {
    setPermissions($item[0], $item[1]);
}

// Create log files if they don't exist
echo "\nInitializing log files...\n";
$logFiles = [
    ERROR_LOG_FILE,
    ACCESS_LOG_FILE
];

foreach ($logFiles as $logFile) {
    if (!file_exists($logFile)) {
        if (file_put_contents($logFile, "# Log initialized on " . date('Y-m-d H:i:s') . "\n")) {
            echo "Created log file: $logFile\n";
            chmod($logFile, 0644);
        } else {
            echo "Failed to create log file: $logFile\n";
        }
    }
}

// Create or update .htaccess files in sensitive directories
echo "\nSecuring sensitive directories...\n";
$secureDirectories = [
    'logs',
    'includes',
    'uploads'
];

$htaccessContent = "Order deny,allow\nDeny from all\n";

foreach ($secureDirectories as $dir) {
    $htaccessFile = __DIR__ . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . '.htaccess';
    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "Created/Updated .htaccess in: $dir\n";
        chmod($htaccessFile, 0644);
    } else {
        echo "Failed to create .htaccess in: $dir\n";
    }
}

// Verify critical files exist
echo "\nVerifying critical files...\n";
$criticalFiles = [
    'includes/config.php',
    'includes/header.php',
    'includes/footer.php',
    'css/styles.css',
    'js/script.js',
    'index.php'
];

foreach ($criticalFiles as $file) {
    if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $file)) {
        echo "Found: $file\n";
    } else {
        echo "Missing critical file: $file\n";
    }
}

// Test write permissions
echo "\nTesting write permissions...\n";
$testFile = __DIR__ . DIRECTORY_SEPARATOR . 'logs/write_test.tmp';
if (file_put_contents($testFile, 'Test')) {
    echo "Write test successful\n";
    unlink($testFile);
} else {
    echo "Write test failed\n";
}

echo "\nInitialization complete!\n";
echo "Please review any errors above and configure your settings in includes/config.php\n";
