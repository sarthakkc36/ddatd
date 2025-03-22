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

// Placeholder for bookings functionality
$message = '';
$messageType = '';

// Sample bookings for demonstration
$bookings = [
    [
        'id' => 1,
        'client_name' => 'Ramesh Thapa',
        'email' => 'ramesh.thapa@example.com',
        'phone' => '+977 9801234567',
        'service_id' => 1,
        'service_name' => 'General Health Check-up',
        'booking_date' => '2025-03-25',
        'booking_time' => '10:00:00',
        'address' => 'Baluwatar, Kathmandu',
        'notes' => 'First-time check-up, need comprehensive health assessment.',
        'status' => 'pending',
        'created_at' => '2025-03-18 09:30:00'
    ],
    [
        'id' => 2,
        'client_name' => 'Sita Gurung',
        'email' => 'sita.gurung@example.com',
        'phone' => '+977 9802345678',
        'service_id' => 3,
        'service_name' => 'Pediatric Care',
        'booking_date' => '2025-03-24',
        'booking_time' => '14:30:00',
        'address' => 'Lazimpat, Kathmandu',
        'notes' => 'Regular check-up for 5-year-old daughter with mild fever.',
        'status' => 'confirmed',
        'created_at' => '2025-03-17 15:45:00'
    ],
    [
        'id' => 3,
        'client_name' => 'Hari Bahadur',
        'email' => 'hari.bahadur@example.com',
        'phone' => '+977 9803456789',
        'service_id' => 2,
        'service_name' => 'Elderly Care',
        'booking_date' => '2025-03-23',
        'booking_time' => '11:15:00',
        'address' => 'Patan, Lalitpur',
        'notes' => 'Regular check-up for 75-year-old with diabetes and hypertension.',
        'status' => 'completed',
        'created_at' => '2025-03-16 12:20:00'
    ]
];

// Determine if we're viewing or listing bookings
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get booking data for viewing
$bookingData = [];
if (($action === 'view' || $action === 'edit') && $bookingId > 0) {
    foreach ($bookings as $booking) {
        if ($booking['id'] === $bookingId) {
            $bookingData = $booking;
            break;
        }
    }
    
    if (empty($bookingData)) {
        $message = 'Booking not found!';
        $messageType = 'error';
        $action = 'list';
    }
}

// Sample services for dropdown
$services = [
    ['id' => 1, 'title' => 'General Health Check-up'],
    ['id' => 2, 'title' => 'Elderly Care'],
    ['id' => 3, 'title' => 'Pediatric Care'],
    ['id' => 4, 'title' => 'Physiotherapy'],
    ['id' => 5, 'title' => 'Lab Sample Collection']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Doctors At Door Step</title>
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
            --info-color: #3B82F6;
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
        
        .add-new-btn {
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }
        
        .add-new-btn:hover {
            background-color: var(--dark-color);
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
        
        /* Bookings Table */
        .bookings-table {
            width: 100%;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .bookings-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bookings-table th, .bookings-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .bookings-table th {
            background-color: var(--light-color);
            font-weight: 600;
        }
        
        .bookings-table tr:last-child td {
            border-bottom: none;
        }
        
        .bookings-table tr:hover {
            background-color: rgba(44, 123, 229, 0.05);
        }
        
        .booking-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }
        
        .status-confirmed {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--info-color);
        }
        
        .status-completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .status-cancelled {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-view, .btn-edit, .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-view {
            background-color: rgba(107, 122, 153, 0.1);
            color: var(--secondary-color);
        }
        
        .btn-view:hover {
            background-color: var(--secondary-color);
            color: var(--white);
        }
        
        .btn-edit {
            background-color: rgba(44, 123, 229, 0.1);
            color: var(--primary-color);
        }
        
        .btn-edit:hover {
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
        
        /* Booking Detail */
        .booking-detail {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .booking-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .booking-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .booking-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 14px;
            color: var(--secondary-color);
        }
        
        .booking-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .booking-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-group {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }
        
        .info-value {
            font-size: 16px;
        }
        
        .booking-notes {
            margin-top: 20px;
            padding: 15px;
            background-color: var(--light-color);
            border-radius: 5px;
        }
        
        .booking-notes h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .booking-actions {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Booking Form */
        .booking-form {
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            padding: 20px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-cancel {
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
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
        
        .btn-submit {
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
        
        .btn-submit:hover {
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
            
            .bookings-table {
                overflow-x: auto;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
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
            <a href="inquiries.php" class="menu-item">
                <i class="fas fa-envelope"></i> Inquiries
            </a>
            <a href="bookings.php" class="menu-item active">
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
                    View Booking
                <?php elseif ($action === 'edit'): ?>
                    Edit Booking
                <?php elseif ($action === 'add'): ?>
                    Add New Booking
                <?php else: ?>
                    Manage Bookings
                <?php endif; ?>
            </h1>
            
            <?php if ($action === 'list'): ?>
                <a href="bookings.php?action=add" class="add-new-btn">
                    <i class="fas fa-plus"></i> Add New Booking
                </a>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
            <!-- Bookings List -->
            <div class="bookings-table">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No bookings found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($booking['client_name']); ?></strong>
                                            <div><?php echo htmlspecialchars($booking['phone']); ?></div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['service_name']); ?></td>
                                    <td>
                                        <div>
                                            <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?>
                                            <div><?php echo date('g:i A', strtotime($booking['booking_time'])); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="booking-status status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="bookings.php?action=view&id=<?php echo $booking['id']; ?>" class="btn-view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="bookings.php?action=edit&id=<?php echo $booking['id']; ?>" class="btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn-delete" onclick="confirmDelete(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['client_name']); ?>')">
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
        <?php elseif ($action === 'view'): ?>
            <!-- Booking Detail -->
            <div class="booking-detail">
                <div class="booking-header">
                    <h2 class="booking-title">Booking #<?php echo $bookingData['id']; ?></h2>
                    <div class="booking-meta">
                        <span><i class="fas fa-calendar"></i> Created: <?php echo date('M j, Y H:i', strtotime($bookingData['created_at'])); ?></span>
                        <span>
                            <i class="fas fa-tag"></i>
                            <span class="booking-status status-<?php echo $bookingData['status']; ?>">
                                <?php echo ucfirst($bookingData['status']); ?>
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="booking-info">
                    <div>
                        <div class="info-group">
                            <div class="info-label">Client Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($bookingData['client_name']); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($bookingData['email']); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($bookingData['phone']); ?></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="info-group">
                            <div class="info-label">Service</div>
                            <div class="info-value"><?php echo htmlspecialchars($bookingData['service_name']); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Date</div>
                            <div class="info-value"><?php echo date('F j, Y', strtotime($bookingData['booking_date'])); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Time</div>
                            <div class="info-value"><?php echo date('g:i A', strtotime($bookingData['booking_time'])); ?></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="info-group">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($bookingData['address']); ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($bookingData['notes'])): ?>
                    <div class="booking-notes">
                        <h3>Notes</h3>
                        <p><?php echo nl2br(htmlspecialchars($bookingData['notes'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="booking-actions">
                    <a href="bookings.php" class="btn-cancel">Back to List</a>
                    <a href="bookings.php?action=edit&id=<?php echo $bookingData['id']; ?>" class="btn-submit">Edit Booking</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Booking Form -->
            <div class="booking-form">
                <form method="POST" action="bookings.php">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="id" value="<?php echo $bookingData['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" id="client_name" name="client_name" value="<?php echo $action === 'edit' ? htmlspecialchars($bookingData['client_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo $action === 'edit' ? htmlspecialchars($bookingData['email']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="<?php echo $action === 'edit' ? htmlspecialchars($bookingData['phone']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="service_id">Service</label>
                            <select id="service_id" name="service_id" required>
                                <option value="">Select a service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>" <?php echo ($action === 'edit' && $bookingData['service_id'] == $service['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($service['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="booking_date">Date</label>
                            <input type="date" id="booking_date" name="booking_date" value="<?php echo $action === 'edit' ? $bookingData['booking_date'] : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="booking_time">Time</label>
                            <input type="time" id="booking_time" name="booking_time" value="<?php echo $action === 'edit' ? $bookingData['booking_time'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" required><?php echo $action === 'edit' ? htmlspecialchars($bookingData['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes"><?php echo $action === 'edit' ? htmlspecialchars($bookingData['notes']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?php echo ($action === 'edit' && $bookingData['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($action === 'edit' && $bookingData['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo ($action === 'edit' && $bookingData['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo ($action === 'edit' && $bookingData['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="bookings.php" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <?php echo $action === 'add' ? 'Add Booking' : 'Update Booking'; ?>
                        </button>
                    </div>
                </form>
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
        function confirmDelete(id, name) {
            if (confirm(`Are you sure you want to delete the booking for "${name}"?`)) {
                window.location.href = `bookings.php?action=delete&id=${id}`;
            }
        }
    </script>
</body>
</html>
