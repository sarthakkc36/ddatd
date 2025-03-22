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

// Placeholder for inquiries functionality
$message = '';
$messageType = '';

// Sample inquiries for demonstration
$inquiries = [
    [
        'id' => 1,
        'name' => 'Amit Kumar',
        'email' => 'amit.kumar@example.com',
        'subject' => 'Question about home visit services',
        'message' => 'I would like to know more about your home visit services. Do you provide services in the Lalitpur area? What are your operating hours?',
        'created_at' => '2025-03-18 14:30:00',
        'status' => 'new'
    ],
    [
        'id' => 2,
        'name' => 'Sunita Rai',
        'email' => 'sunita.rai@example.com',
        'subject' => 'Elderly care inquiry',
        'message' => 'My father is 78 years old and needs regular medical check-ups. Can you provide information about your elderly care services and pricing?',
        'created_at' => '2025-03-17 10:15:00',
        'status' => 'read'
    ],
    [
        'id' => 3,
        'name' => 'Rajan Shrestha',
        'email' => 'rajan.shrestha@example.com',
        'subject' => 'Availability of specialists',
        'message' => 'Do you have cardiologists available for home visits? I need a consultation for my heart condition but cannot travel to the hospital.',
        'created_at' => '2025-03-16 16:45:00',
        'status' => 'replied'
    ]
];

// Determine if we're viewing or listing inquiries
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$inquiryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get inquiry data for viewing
$inquiryData = [];
if ($action === 'view' && $inquiryId > 0) {
    foreach ($inquiries as $inquiry) {
        if ($inquiry['id'] === $inquiryId) {
            $inquiryData = $inquiry;
            break;
        }
    }
    
    if (empty($inquiryData)) {
        $message = 'Inquiry not found!';
        $messageType = 'error';
        $action = 'list';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Doctors At Door Step</title>
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
            --success-color: #10B981;
            --error-color: #EF4444;
            --warning-color: #F59E0B;
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
        
        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
        }
        
        /* Inquiries Table */
        .inquiries-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .inquiries-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inquiries-table th, .inquiries-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .inquiries-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .inquiries-table tr:last-child td {
            border-bottom: none;
        }
        
        .inquiries-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .inquiry-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-new {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .status-read {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .status-replied {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-view, .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-view {
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
        }
        
        .btn-view:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .btn-delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .btn-delete:hover {
            background-color: var(--error-color);
            color: var(--white);
        }
        
        /* Inquiry Detail */
        .inquiry-detail {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .inquiry-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .inquiry-subject {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .inquiry-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 14px;
            color: var(--secondary-color);
        }
        
        .inquiry-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .inquiry-content {
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .reply-form {
            margin-top: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            min-height: 150px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-back {
            padding: 10px 20px;
            background-color: var(--light-color);
            color: var(--dark-color);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background-color: #e0e0e0;
        }
        
        .btn-reply {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-reply:hover {
            background-color: var(--dark-color);
        }
        
        /* Form Helpers */
        .text-muted {
            color: var(--secondary-color);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .text-center {
            text-align: center;
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
            
            .inquiries-table {
                overflow-x: auto;
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
            <a href="dashboard.php" class="menu-item">
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
            <a href="inquiries.php" class="menu-item active">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
            <a href="bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
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
            <h1 class="page-title">
                <?php if ($action === 'view'): ?>
                    View Inquiry
                <?php else: ?>
                    Manage Inquiries
                <?php endif; ?>
            </h1>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Inquiries List -->
            <div class="inquiries-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inquiries)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No inquiries found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></td>
                                    <td>
                                        <span class="inquiry-status status-<?php echo $inquiry['status']; ?>">
                                            <?php echo ucfirst($inquiry['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="inquiries.php?action=view&id=<?php echo $inquiry['id']; ?>" class="btn-view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $inquiry['id']; ?>, '<?php echo htmlspecialchars($inquiry['subject']); ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Inquiry Detail -->
            <div class="inquiry-detail">
                <div class="inquiry-header">
                    <h2 class="inquiry-subject"><?php echo htmlspecialchars($inquiryData['subject']); ?></h2>
                    <div class="inquiry-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($inquiryData['name']); ?></span>
                        <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($inquiryData['email']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M j, Y H:i', strtotime($inquiryData['created_at'])); ?></span>
                        <span>
                            <i class="fas fa-tag"></i>
                            <span class="inquiry-status status-<?php echo $inquiryData['status']; ?>">
                                <?php echo ucfirst($inquiryData['status']); ?>
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="inquiry-content">
                    <p><?php echo nl2br(htmlspecialchars($inquiryData['message'])); ?></p>
                </div>
                
                <div class="reply-form">
                    <h3>Reply to this inquiry</h3>
                    <form method="POST" action="inquiries.php">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="id" value="<?php echo $inquiryData['id']; ?>">
                        
                        <div class="form-group">
                            <label for="reply">Your Reply</label>
                            <textarea id="reply" name="reply" required></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <a href="inquiries.php" class="btn-back">Back to List</a>
                            <button type="submit" class="btn-reply">Send Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
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
        
        // Confirm delete
        function confirmDelete(id, subject) {
            if (confirm(`Are you sure you want to delete the inquiry "${subject}"?`)) {
                window.location.href = `inquiries.php?action=delete&id=${id}`;
            }
        }
    </script>
</body>
</html>
