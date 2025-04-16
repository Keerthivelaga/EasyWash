<?php
session_start();
if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}

include 'db.php';

$regno = $_SESSION['regno'];
$username = $_SESSION['username'];
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

// Fetch machine counts
$availableMachines = 0;
$occupiedMachines = 0;
$maintenanceMachines = 0;

$sql = "SELECT status, COUNT(*) as count FROM washing_machines GROUP BY status";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    switch ($row['status']) {
        case 'free':
            $availableMachines = $row['count'];
            break;
        case 'in_use':
            $occupiedMachines = $row['count'];
            break;
        case 'out_of_service':
            $maintenanceMachines = $row['count'];
            break;
    }
}

// Fetch next booking
$nextBooking = null;
$hasUpcomingBooking = false;

$bookingQuery = "
    SELECT b.*, w.machine_name 
    FROM bookings b
    JOIN washing_machines w ON b.machine_id = w.id
    WHERE b.user_regno = '$regno' AND b.booking_date >= CURDATE()
    ORDER BY b.booking_date ASC, b.booking_time ASC
    LIMIT 1
";
$bookingResult = mysqli_query($conn, $bookingQuery);
if ($bookingResult && mysqli_num_rows($bookingResult) > 0) {
    $nextBooking = mysqli_fetch_assoc($bookingResult);
    $hasUpcomingBooking = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Easy Wash</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Embedded CSS -->
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: flex; flex-direction: column;
            background: url('https://img.freepik.com/premium-vector/laundry-room-interior-with-washing-machine-household-chemistry-cleaning-washing-powder-towels_529344-718.jpg') no-repeat center center/cover;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 250px;
            background: #1E3A5F;
            color: white;
            position: fixed;
            height: 100%;
            padding-top: 20px;
            transition: width 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .sidebar.collapsed { width: 60px; }
        .sidebar h2 {
            text-align: center; margin-bottom: 20px; font-size: 18px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: opacity 0.3s ease;
        }
        .sidebar.collapsed h2 { opacity: 0; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li {
            padding: 15px; cursor: pointer;
            transition: background 0.2s;
        }
        .sidebar ul li:hover { background: rgba(255, 255, 255, 0.2); }
        .sidebar ul li a {
            text-decoration: none; color: white; display: block;
            font-size: 16px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
            transition: opacity 0.3s ease;
        }
        .sidebar.collapsed ul li a { opacity: 0; }
        .sidebar .toggle-btn {
            position: absolute; top: 15px; right: -35px;
            background: #1E3A5F; color: white;
            padding: 10px; border-radius: 5px;
            cursor: pointer; transition: 0.3s; font-size: 20px;
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .sidebar.collapsed + .main-content {
            margin-left: 60px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-left: 40px;
        }
        .header h2 { font-size: 24px; }
        .logout-btn {
            background: #1E3A5F; color: white;
            padding: 10px 15px; border: none;
            cursor: pointer; border-radius: 5px;
            text-decoration: none;
        }

        .status-section {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .status-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px; border-radius: 10px;
            text-align: center; flex: 1; margin: 0 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .status-card h3 { margin-bottom: 15px; }
        .status-card span {
            font-size: 18px; font-weight: bold;
            display: block; margin-top: 10px;
        }

        .next-booking {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px; border-radius: 10px;
            text-align: center; margin: 20px auto;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .next-booking h3 { margin-bottom: 15px; }
        .next-booking p { font-size: 16px; color: #555; }

        #toast {
            position: fixed;
            top: 15px; right: 250px;
            background-color: #1E3A5F;
            color: white; padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            font-size: 14px;
            display: none;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>Easy Wash</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="booking.php"> Schedule Wash </a></li>
        <li><a href="washing_machine_status.php">Machine Tracker</a></li>
        <li><a href="booking_history.php">Wash History</a></li>
        <li><a href="notifications.php">Notifications</a></li>
        <?php if ($is_admin): ?>
            <li><a href="admin_panel.php">Admin Panel</a></li>
        <?php endif; ?>
        <li><a href="user_profile.php">Profile</a></li>
        <li><a href="help_faq.php">Help/FAQ</a></li>
    </ul>
    <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <a href="llogin.php" class="logout-btn">Logout</a>
    </div>

    <!-- Machine Status -->
    <div class="status-section">
        <div class="status-card">
            <h3>Available Machines</h3>
            <span><?php echo $availableMachines; ?></span>
        </div>
        <div class="status-card">
            <h3>Occupied Machines</h3>
            <span><?php echo $occupiedMachines; ?></span>
        </div>
        <div class="status-card">
            <h3>Under Maintenance</h3>
            <span><?php echo $maintenanceMachines; ?></span>
        </div>
    </div>

    <!-- Next Booking -->
    <?php if ($hasUpcomingBooking): ?>
        <div class="next-booking">
            <h3>Your Next Booking</h3>
            <p>Date: <?php echo $nextBooking['booking_date']; ?></p>
            <p>Time: <?php echo $nextBooking['booking_time']; ?></p>
            <p>Machine: <?php echo $nextBooking['machine_name']; ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Toast -->
<div id="toast"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("collapsed");
    }

    window.onload = function () {
        const hasBooking = <?php echo $hasUpcomingBooking ? 'true' : 'false'; ?>;
        const toast = document.getElementById('toast');
        const bookingMessage = `📅 You have a booking on <?php echo $nextBooking['booking_date'] ?? ''; ?> at <?php echo $nextBooking['booking_time'] ?? ''; ?> (<?php echo $nextBooking['machine_name'] ?? ''; ?>)`;

        if (hasBooking) {
            setTimeout(() => {
                toast.innerText = bookingMessage;
                toast.style.display = 'block';
                toast.style.opacity = '1';

                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 500);
                }, 5000);
            }, 800);
        }
    }
</script>

</body>
</html>
