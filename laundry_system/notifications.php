<?php
session_start();
include('db.php');

if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}

$regno = $_SESSION['regno'];

// Optional: Handle marking as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $nid = $_GET['read'];
    $check = $conn->prepare("SELECT * FROM notification_reads WHERE regno = ? AND notification_id = ?");
    $check->bind_param("si", $regno, $nid);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO notification_reads (regno, notification_id, read_at) VALUES (?, ?, NOW())");
        $insert->bind_param("si", $regno, $nid);
        $insert->execute();
    }

    header("Location: notifications.php");
    exit();
}

// Get all notifications
$notif_sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$notifications = $conn->query($notif_sql);

// Get read notifications for this user
$read_sql = "SELECT notification_id FROM notification_reads WHERE regno = ?";
$read_stmt = $conn->prepare($read_sql);
$read_stmt->bind_param("s", $regno);
$read_stmt->execute();
$read_result = $read_stmt->get_result();

$read_ids = [];
while ($row = $read_result->fetch_assoc()) {
    $read_ids[] = $row['notification_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('images/bg.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: rgba(255, 255, 255, 0.85);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .notification {
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: relative;
            border-left: 6px solid #4a90e2;
        }

        .notification.new {
            background-color: #eaf4ff;
        }

        .notification .badge {
            background: #d9534f;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 20px;
            position: absolute;
            top: 15px;
            right: 20px;
        }

        .notification h4 {
            margin: 0 0 5px;
            font-size: 18px;
            color: #2c3e50;
        }

        .notification p {
            margin: 0;
            color: #555;
        }

        .timestamp {
            text-align: right;
            font-size: 12px;
            color: #888;
            margin-top: 8px;
        }

        .type-label {
            display: inline-block;
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 4px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .info { background-color: #3498db; color: white; }
        .reminder { background-color: #f1c40f; color: black; }
        .alert { background-color: #e74c3c; color: white; }

        .read-link {
            display: inline-block;
            margin-top: 6px;
            font-size: 13px;
            color: #007bff;
            text-decoration: none;
        }

        .read-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Notifications</h2>

    <?php if ($notifications->num_rows > 0): ?>
        <?php while ($row = $notifications->fetch_assoc()): 
            $is_read = in_array($row['id'], $read_ids);
            $type = strtolower($row['title']); // Assuming title indicates type like "Info", "Alert"
        ?>
            <div class="notification <?= !$is_read ? 'new' : '' ?>">
                <span class="type-label <?= $type ?>"><?= ucfirst($type) ?></span>
                <?php if (!$is_read): ?>
                    <span class="badge">New</span>
                <?php endif; ?>
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                <div class="timestamp"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></div>
                <?php if (!$is_read): ?>
                    <a href="?read=<?= $row['id'] ?>" class="read-link">Mark as read</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>
</body>
</html>
