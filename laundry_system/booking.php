<?php
session_start();
include('db.php');

if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}

$regno = $_SESSION['regno'];

// Cancel booking
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_regno=?");
    $stmt->bind_param("is", $cancel_id, $regno);
    $stmt->execute();
    $stmt2 = $conn->prepare("UPDATE washing_machines SET status='Free' WHERE id=(SELECT machine_id FROM bookings WHERE id=?)");
    $stmt2->bind_param("i", $cancel_id);
    $stmt2->execute();
    echo "<script>alert('Booking cancelled successfully!'); window.location='booking.php';</script>";
    exit();
}

// Booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $machine_id = $_POST['machine_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];

    // Check for double booking
    $check = $conn->prepare("SELECT * FROM bookings WHERE machine_id = ? AND booking_date = ? AND booking_time = ? AND status = 'booked'");
    $check->bind_param("iss", $machine_id, $booking_date, $booking_time);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('This machine is already booked for the selected time. Please choose another.');</script>";
    } else {
        // Book
        $stmt = $conn->prepare("INSERT INTO bookings (user_regno, machine_id, booking_date, booking_time, status) VALUES (?, ?, ?, ?, 'booked')");
        $stmt->bind_param("siss", $regno, $machine_id, $booking_date, $booking_time);
        $stmt->execute();

        // Update machine status
        $stmt2 = $conn->prepare("UPDATE washing_machines SET status='in_use' WHERE id = ?");
        $stmt2->bind_param("i", $machine_id);
        $stmt2->execute();

        echo "<script>alert('Booking successful!'); window.location='booking.php';</script>";
        exit();
    }
}

// Fetch all machines
$machine_stmt = $conn->prepare("SELECT wm.*, COUNT(b.id) AS usage_count FROM washing_machines wm LEFT JOIN bookings b ON wm.id = b.machine_id GROUP BY wm.id");
$machine_stmt->execute();
$machines = $machine_stmt->get_result();

// Fetch current user's upcoming bookings
$booking_stmt = $conn->prepare("SELECT * FROM bookings WHERE user_regno=? AND status='booked' ORDER BY booking_date, booking_time");
$booking_stmt->bind_param("s", $regno);
$booking_stmt->execute();
$user_bookings = $booking_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Schedule Wash</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: url('images/bg.png') no-repeat center center/cover;
      padding: 30px;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background: rgba(255, 255, 255, 0.8);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    .machine-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      margin-bottom: 30px;
      justify-content: center; 
    }
    .machine-card {
      border: 2px solid #ccc;
      border-radius: 10px;
      width: 200px;
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      background-color: #fff;
    }
    .machine-card img {
      width: 60px;
      height: 60px;
    }
    .machine-card:hover {
      border-color: #4a90e2;
      background: #f0f8ff;
    }
    .machine-card.selected {
      border-color: #4a90e2;
      background: #d0e9ff;
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .machine-card p {
      margin: 5px 0;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      max-width: 400px;
      margin: auto;
    }
    label {
      font-weight: bold;
      color: #333;
    }
    input, button {
      padding: 12px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    input[type="date"], input[type="time"] {
      cursor: pointer;
    }
    button {
      background-color: #4a90e2;
      color: white;
      cursor: pointer;
    }
    button:hover {
      background-color: #357abd;
    }
    .bookings {
      margin-top: 40px;
    }
    .bookings table {
      width: 100%;
      border-collapse: collapse;
    }
    .bookings th, .bookings td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .bookings th {
      background: #f2f2f2;
    }
    @media (max-width: 768px) {
      .machine-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 480px) {
      .machine-grid {
        grid-template-columns: 1fr;
      }
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 30px;
    }
    .back-link a {
      text-decoration: none;
      color: #1E3A5F;
      font-weight: bold;
      font-size: 18px;
      transition: color 0.3s ease;
    }
    .back-link a:hover {
      color: #5aa9e6;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Schedule a Wash</h2>

  <form method="POST">
    <input type="hidden" name="machine_id" id="selectedMachineId" required>

    <div class="machine-grid">
      <?php while ($row = $machines->fetch_assoc()): ?>
        <div class="machine-card" onclick="selectMachine(this, <?= $row['id'] ?>)">
          <img src="images/mbg.png" alt="Machine Icon">
          <h4>Machine <?= htmlspecialchars($row['id']) ?></h4>
          <p>Status: <?= htmlspecialchars($row['status']) ?></p>
          <p>Usage Count: <?= $row['usage_count'] ?></p>
        </div>
      <?php endwhile; ?>
    </div>

    <label for="booking_date">Booking Date:</label>
    <input type="date" name="booking_date" id="booking_date" required>

    <label for="booking_time">Booking Time:</label>
    <input type="time" name="booking_time" id="booking_time" required>

    <button type="submit">Confirm Booking</button>
  </form>

  <div class="bookings">
    <h3>Your Upcoming Bookings</h3>
    <?php if ($user_bookings->num_rows > 0): ?>
      <table>
        <tr>
          <th>Machine ID</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($b = $user_bookings->fetch_assoc()): ?>
          <tr>
            <td><?= $b['machine_id'] ?></td>
            <td><?= $b['booking_date'] ?></td>
            <td><?= $b['booking_time'] ?></td>
            <td><?= $b['status'] ?></td>
            <td><a href="?cancel_id=<?= $b['id'] ?>" onclick="return confirm('Are you sure to cancel?')">Cancel</a></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No upcoming bookings.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  function selectMachine(card, machineId) {
    document.querySelectorAll('.machine-card').forEach(el => el.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('selectedMachineId').value = machineId;
  }

  // Restrict time range
  const bookingTimeInput = document.getElementById("booking_time");
  bookingTimeInput.addEventListener("change", () => {
    const time = bookingTimeInput.value;
    const [hour] = time.split(":");
    if (hour < 6 || hour > 22) {
      alert("Booking time must be between 6:00 AM and 10:00 PM.");
      bookingTimeInput.value = "";
    }
  });
</script>
</body>
</html>
