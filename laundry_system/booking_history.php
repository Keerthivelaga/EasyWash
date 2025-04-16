<?php
session_start();
include('db.php');

if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}

$regno = $_SESSION['regno'];

// Handle cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $booking_id = $_GET['cancel'];
    $stmt = $conn->prepare("UPDATE bookings SET status='Cancelled' WHERE id=? AND user_regno=? AND status IN ('booked', 'in_use')");
    $stmt->bind_param("is", $booking_id, $regno);
    $stmt->execute();
    header("Location: booking_history.php");
    exit();
}

// Filtering logic
$filter = $_GET['filter'] ?? 'all';
$filter_sql = "";

if ($filter === 'completed') {
    $filter_sql = "AND status = 'Completed'";
} elseif ($filter === 'upcoming') {
    $filter_sql = "AND status IN ('booked', 'in_use')";
} elseif ($filter === 'cancelled') {
    $filter_sql = "AND status = 'Cancelled'";
} elseif ($filter === 'last7') {
    $filter_sql = "AND booking_date >= CURDATE() - INTERVAL 7 DAY";
}

$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_regno=? $filter_sql ORDER BY booking_date DESC");
$stmt->bind_param("s", $regno);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking History</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('images/bg.png') no-repeat center center/cover;
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: center center;
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: rgba(255, 248, 240, 0.85);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .filters {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .filters a {
            padding: 8px 15px;
            border: 1px solid #4a90e2;
            border-radius: 5px;
            color: #4a90e2;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        .filters a:hover {
            background: #4a90e2;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e2e2e2;
        }
        th {
            background-color: #4a90e2;
            color: white;
        }
        .status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            color: white;
        }
        .booked, .in_use { background-color: #f0ad4e; }
        .completed { background-color: #5cb85c; }
        .cancelled { background-color: #d9534f; }

        .cancel-btn {
            padding: 6px 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .cancel-btn:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: center;
            }
            th, td {
                padding: 10px;
                font-size: 14px;
            }
            .cancel-btn {
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Your Wash History</h2>

    <div class="filters">
        <a href="booking_history.php" class="<?= $filter === 'all' ? 'active' : '' ?>">All</a>
        <a href="?filter=completed" class="<?= $filter === 'completed' ? 'active' : '' ?>">Completed</a>
        <a href="?filter=upcoming" class="<?= $filter === 'upcoming' ? 'active' : '' ?>">Upcoming</a>
        <a href="?filter=cancelled" class="<?= $filter === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
        <a href="?filter=last7" class="<?= $filter === 'last7' ? 'active' : '' ?>">Last 7 Days</a>
    </div>

    <table>
        <tr>
            <th>Booking ID</th>
            <th>Machine ID</th>
            <th>Booking Date</th>
            <th>Booking Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['machine_id']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= htmlspecialchars($row['booking_time']) ?></td>
                    <td><span class="status <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <?php if (in_array($row['status'], ['booked', 'in_use'])): ?>
                            <a href="?cancel=<?= $row['id'] ?>" onclick="return confirm('Cancel this booking?');">
                                <button class="cancel-btn">Cancel</button>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No bookings found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>