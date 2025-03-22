<?php
session_start();
require_once 'includes/auth.php';
require_admin();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Get real statistics from database
$stats = get_dashboard_stats();
$recent_activity = get_recent_activity(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>                <img src="images/logo.png" alt="Doctors At Door Step"width="90px" height="80px"  />
    </title>
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
            --sidebar-width: 250px;
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
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--dark-color);
            color: var(--white);
            padding: 20px 0;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .sidebar-header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: rgba(44, 123, 229, 0.2);
            color: var(--primary-color);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .logout-btn {
            display: block;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info span {
            margin-right: 10px;
        }
        
        .user-info .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 20px;
        }
        
        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        /* Quick Actions */
        .quick-actions {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-card {
            background-color: var(--light-color);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow);
        }
        
        .action-card a {
            color: var(--dark-color);
            text-decoration: none;
            display: block;
        }
        
        .action-card i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .action-card h4 {
            margin-bottom: 5px;
        }
        
        .action-card p {
            font-size: 12px;
            color: var(--secondary-color);
        }
        
        /* Recent Activity */
        .recent-activity {
            background-color: var(--white);
            border-radius: 10px;
            padding: 20px;
            box-shadow: var(--box-shadow);
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: flex-start;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex-grow: 1;
        }
        
        .activity-content h4 {
            margin-bottom: 5px;
        }
        
        .activity-content p {
            font-size: 14px;
            color: var(--secondary-color);
        }
        
        .activity-time {
            font-size: 12px;
            color: var(--secondary-color);
            margin-left: 15px;
            white-space: nowrap;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>                <img src="../images/logo.png" alt="Doctors At Door Step"width="100px" height="100px"  />
            </h1>
            <p>Admin Panel</p>
        </div>
        
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="services.php" class="menu-item">
                <i class="fas fa-hand-holding-medical"></i> Services
            </a>
            <a href="team.php" class="menu-item">
                <i class="fas fa-user-md"></i> Team Members
            </a>
            <a href="testimonials.php" class="menu-item">
                <i class="fas fa-quote-right"></i> Testimonials
            </a>
            <a href="inquiries.php" class="menu-item">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
            <a href="bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="blog-management.php" class="menu-item">
                <i class="fas fa-blog"></i> Blog
            </a>
            <a href="blog-categories.php" class="menu-item">
                <i class="fas fa-folder"></i> Blog Categories
            </a>
            <a href="blog-tags.php" class="menu-item">
                <i class="fas fa-tags"></i> Blog Tags
            </a>
        </div>
        
        <div class="sidebar-footer">
            <a href="dashboard.php?logout=true" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <div class="user-info">
                <span>Welcome, Admin</span>
                <div class="user-avatar">A</div>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-medical"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_services']; ?></h3>
                    <p>Services</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_team_members']; ?></h3>
                    <p>Team Members</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_testimonials']; ?></h3>
                    <p>Testimonials</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_inquiries']; ?></h3>
                    <p>Inquiries</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_bookings']; ?></h3>
                    <p>Bookings</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <div class="action-card">
                    <a href="services.php?action=add">
                        <i class="fas fa-plus-circle"></i>
                        <h4>Add Service</h4>
                        <p>Create a new service</p>
                    </a>
                </div>
                
                <div class="action-card">
                    <a href="team.php?action=add">
                        <i class="fas fa-user-plus"></i>
                        <h4>Add Team Member</h4>
                        <p>Add a new doctor or staff</p>
                    </a>
                </div>
                <div class="action-card">
                    <a href="blog-edit.php?action=add">
                        <i class="fas fa-pen-fancy"></i>
                        <h4>Write Blog Post</h4>
                        <p>Create a new blog article</p>
                    </a>
                </div>
                <div class="action-card">
                    <a href="testimonials.php?action=add">
                        <i class="fas fa-comment-medical"></i>
                        <h4>Add Testimonial</h4>
                        <p>Add a new client review</p>
                    </a>
                </div>
                
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2 class="section-title">Recent Activity</h2>
            <ul class="activity-list">
                <?php if (empty($recent_activity)): ?>
                <li class="activity-item">
                    <div class="activity-content">
                        <p>No recent activity</p>
                    </div>
                </li>
                <?php else: ?>
                    <?php foreach ($recent_activity as $activity): ?>
                        <li class="activity-item">
                            <div class="activity-icon">
                                <?php if ($activity['type'] === 'booking'): ?>
                                    <i class="fas fa-calendar-check"></i>
                                <?php else: ?>
                                    <i class="fas fa-envelope"></i>
                                <?php endif; ?>
                            </div>
                            <div class="activity-content">
                                <h4>
                                    <?php if ($activity['type'] === 'booking'): ?>
                                        New Booking
                                    <?php else: ?>
                                        New Inquiry
                                    <?php endif; ?>
                                </h4>
                                <p>
                                    <?php if ($activity['type'] === 'booking'): ?>
                                        <?php echo htmlspecialchars($activity['data']['client_name']); ?> booked 
                                        <?php echo htmlspecialchars($activity['data']['service_name']); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($activity['data']['name']); ?> sent an inquiry: 
                                        <?php echo htmlspecialchars(substr($activity['data']['subject'], 0, 50)); ?>...
                                    <?php endif; ?>
                                </p>
                            </div>
                            <span class="activity-time">
                                <?php echo time_elapsed_string($activity['type'] === 'booking' ? 
                                    $activity['data']['created_at'] : 
                                    $activity['data']['created_at']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
