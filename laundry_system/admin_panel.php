<?php
session_start();
include 'db.php';

// Redirect non-admin users
if (!isset($_SESSION['regno']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle add machine
if (isset($_POST['add_machine'])) {
    $name = $_POST['machine_name'];
    $status = $_POST['status'];
    $last_maintenance = $_POST['last_maintenance'] ?? NULL;

    $stmt = $conn->prepare("INSERT INTO washing_machines (machine_name, status, last_maintenance) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $status, $last_maintenance);
    $stmt->execute();
}

// Handle delete machine
if (isset($_POST['delete_machine'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM washing_machines WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Handle edit machine
if (isset($_POST['edit_machine'])) {
    $id = $_POST['id'];
    $name = $_POST['machine_name'];
    $status = $_POST['status'];
    $last_maintenance = $_POST['last_maintenance'] ?? NULL;

    $stmt = $conn->prepare("UPDATE washing_machines SET machine_name=?, status=?, last_maintenance=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $status, $last_maintenance, $id);
    $stmt->execute();
}

// Handle post notice
if (isset($_POST['post_notice'])) {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $message);
    $stmt->execute();
}

// Handle delete notice
if (isset($_POST['delete_notice'])) {
    $notice_id = $_POST['notice_id'];
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $notice_id);
    $stmt->execute();
}

$machines = $conn->query("SELECT * FROM washing_machines");
$notices = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Easy Wash</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
            display: flex;
        }
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #1E3A5F;
            color: white;
            padding: 30px 20px;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar h2 { font-size: 24px; margin-bottom: 30px; }
        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 12px 0;
            font-size: 18px;
            display: block;
            transition: all 0.3s;
        }
        .sidebar a i { margin-right: 10px; }
        .sidebar a:hover {
            color: #f9d342;
            transform: translateX(5px);
        }
        .main {
            margin-left: 280px;
            padding: 40px;
            width: 100%;
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .card h3 { margin-bottom: 20px; font-size: 22px; font-weight: 600; }
        .machine-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .machine {
            background: #e3edf8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            width: 280px;
            transition: all 0.3s ease-in-out;
        }
        .machine:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .machine h4 { margin-bottom: 10px; font-size: 18px; }
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
        }
        .status.free { background: #cde6d0; color: #2d6a4f; }
        .status.in_use { background: #ffe0b3; color: #b08968; }
        .status.out_of_service { background: #ffcccc; color: #8a0c0c; }
        .notif {
            padding: 15px;
            border-left: 5px solid #1E3A5F;
            margin-bottom: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            text-align: left;
        }
        .notif strong { font-size: 18px; display: block; }
        .notif p { font-size: 16px; margin-top: 8px; }
        .notif time { display: block; font-size: 13px; color: gray; margin-top: 5px; }
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        input, select, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 90%;
            font-size: 16px;
        }
        button {
            background: #1E3A5F;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        button:hover {
            background: #5aa9e6;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#manage-machines"><i class="fas fa-cogs"></i> Manage Machines</a>
        <a href="#post-notice"><i class="fas fa-bullhorn"></i> Post Notice</a>
        <a href="llogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main">
        <div class="card" id="manage-machines">
            <h3>Manage Washing Machines</h3>
            <form method="POST">
                <input type="text" name="machine_name" placeholder="Machine Name" required>
                <select name="status">
                    <option value="free">Free</option>
                    <option value="in_use">In Use</option>
                    <option value="out_of_service">Out of Service</option>
                </select>
                <input type="date" name="last_maintenance">
                <button type="submit" name="add_machine">Add Machine</button>
            </form>
            <br>
            <div class="machine-list">
                <?php while ($row = $machines->fetch_assoc()): ?>
                    <div class="machine">
                        <h4><?= htmlspecialchars($row['machine_name']) ?></h4>
                        <div class="status <?= htmlspecialchars($row['status']) ?>">
                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($row['status']))) ?>
                        </div>
                        <?php if ($row['last_maintenance']): ?>
                            <p style="margin-top:10px;">Last Maintained: <?= htmlspecialchars($row['last_maintenance']) ?></p>
                        <?php endif; ?>
                        <form method="POST" style="margin-top:10px;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button name="delete_machine">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="card" id="post-notice">
            <h3>Post Notice</h3>
            <form method="POST">
                <input type="text" name="title" placeholder="Notice Title" required>
                <textarea name="message" placeholder="Notice Message" required></textarea>
                <button type="submit" name="post_notice">Post Notice</button>
            </form>
            <br>
            <?php while ($notice = $notices->fetch_assoc()): ?>
                <div class="notif">
                    <strong><?= htmlspecialchars($notice['title']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($notice['message'])) ?></p>
                    <time><?= htmlspecialchars(date("d M Y, h:i A", strtotime($notice['created_at']))) ?></time>
                    <form method="POST" style="margin-top:10px;">
                        <input type="hidden" name="notice_id" value="<?= $notice['id'] ?>">
                        <button name="delete_notice" onclick="return confirm('Are you sure you want to delete this notice?');">Delete Notice</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
