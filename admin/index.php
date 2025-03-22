<?php
define('ALLOWED_ACCESS', true);
session_start();
require_once '../includes/config.php';
require_once '../includes/Database.php';

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $db = Database::getInstance();
        $user = $db->selectOne(
            "SELECT * FROM users WHERE username = :username AND role = 'admin'",
            ['username' => $username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['csrf_token'] = generate_csrf_token();
            
            // Log successful login
            log_error("Admin login successful: {$username}");
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
            // Log failed attempt
            log_error("Failed admin login attempt for username: {$username}");
        }
    } catch (Exception $e) {
        $error = 'An error occurred. Please try again later.';
        log_error("Login error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Doctors At Door Step</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C7BE5;
            --secondary-color: #6B7A99;
            --dark-color: #1A2B3C;
            --light-color: #F8FAFC;
            --white: #FFFFFF;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: var(--primary-color);
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 5px;
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .login-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .login-form button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-form button:hover {
            background-color: var(--dark-color);
        }
        
        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>                <img src="../images/logo.png" alt="Doctors At Door Step"width="100px" height="100px"  />
            </h1>
            <p>Admin Panel Login</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form class="login-form" method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <a href="../index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Website
        </a>
    </div>
</body>
</html>
