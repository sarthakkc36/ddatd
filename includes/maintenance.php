<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settingsHandler->get('site_name', 'Doctors At Door Step')); ?> - Maintenance</title>
    
    <?php if (!empty($siteSettings['favicon'])): ?>
    <link rel="icon" href="<?php echo htmlspecialchars($siteSettings['favicon']); ?>" type="image/x-icon">
    <?php endif; ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C7BE5;
            --secondary-color: #6B7A99;
            --dark-color: #1A2B3C;
            --light-color: #F8FAFC;
            --white: #FFFFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            padding: 20px;
        }
        
        .maintenance-container {
            max-width: 600px;
            padding: 40px;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .maintenance-icon {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--dark-color);
        }
        
        p {
            font-size: 18px;
            line-height: 1.6;
            color: var(--secondary-color);
            margin-bottom: 30px;
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .contact-info p {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .contact-info a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-info a:hover {
            text-decoration: underline;
        }
        
        .back-soon {
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>
        
        <h1>We're Under Maintenance</h1>
        
        <p>
            We're currently performing scheduled maintenance on our website 
            to improve your experience. We'll be back online shortly.
        </p>
        
        <p class="back-soon">Thank you for your patience!</p>
        
        <div class="contact-info">
            <p>If you need immediate assistance, please contact us:</p>
            <p>Email: <a href="mailto:<?php echo htmlspecialchars($settingsHandler->get('contact_email', 'doctorsatdoorstep@gmail.com')); ?>"><?php echo htmlspecialchars($settingsHandler->get('contact_email', 'doctorsatdoorstep@gmail.com')); ?></a></p>
            <p>Phone: <a href="tel:<?php echo htmlspecialchars($settingsHandler->get('contact_phone', '+977 986-0102404')); ?>"><?php echo htmlspecialchars($settingsHandler->get('contact_phone', '+977 986-0102404')); ?></a></p>
        </div>
    </div>
</body>
</html>