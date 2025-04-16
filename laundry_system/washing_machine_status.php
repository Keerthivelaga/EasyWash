<?php
session_start();
include('db.php');

// Automatically mark past bookings as completed and free the machine
$now = date('Y-m-d H:i:s');
$update = $conn->prepare("UPDATE bookings b 
  JOIN washing_machines wm ON b.machine_id = wm.id 
  SET b.status = 'Completed', wm.status = 'Free' 
  WHERE CONCAT(b.booking_date, ' ', b.booking_time) < ? 
    AND b.status = 'booked'");
$update->bind_param("s", $now);
$update->execute();

// Filtering
$filter_in_use = isset($_GET['filter']) && $_GET['filter'] === 'inuse';
$machine_query = "SELECT wm.*, (
    SELECT MIN(CONCAT(booking_date, ' ', booking_time)) 
    FROM bookings 
    WHERE machine_id = wm.id AND status = 'booked' AND CONCAT(booking_date, ' ', booking_time) > NOW()
) AS next_booking_time, (
    SELECT CONCAT(booking_date, ' ', booking_time) 
    FROM bookings 
    WHERE machine_id = wm.id AND status = 'booked' AND CONCAT(booking_date, ' ', booking_time) > NOW()
    ORDER BY CONCAT(booking_date, ' ', booking_time) ASC LIMIT 1
) AS countdown_time 
FROM washing_machines wm";
if ($filter_in_use) {
  $machine_query .= " WHERE wm.status = 'in_use'";
}
$result = $conn->query($machine_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="60"> <!-- refreshes every 60 sec -->
  <title>Machine Tracker</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 20px;
      background: url('images/bg.png') no-repeat center center fixed;
      background-size: cover;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background-color: rgba(255,255,255,0.9);
      padding: 30px;
      border-radius: 15px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .filters {
      text-align: center;
      margin-bottom: 20px;
    }
    .filters a {
      margin: 0 10px;
      text-decoration: none;
      color: #007bff;
      font-weight: bold;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .machine {
      position: relative;
      background: #fff url('images/mbg.png') no-repeat center 10px;
      background-size: 60px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 80px 20px 20px;
      cursor: pointer;
      transition: transform 0.3s;
      overflow: hidden;
    }
    .machine:hover {
      transform: scale(1.02);
    }
    .machine.expanded {
      background-color: #f0f8ff;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    .status {
      font-weight: bold;
      margin-bottom: 5px;
    }
    .Free { color: green; }
    .In\ Use { color: orange; }
    .Out\ of\ Service { color: red; }
    .details {
      display: none;
      margin-top: 10px;
    }
    .machine.expanded .details {
      display: block;
    }
    .countdown {
      font-weight: bold;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Machine Tracker</h2>
    <div class="filters">
      <a href="?">Show All</a> |
      <a href="?filter=inuse">Show Only In Use</a>
    </div>
    <div class="grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="machine" onclick="toggleExpand(this)">
          <h4>Machine <?= htmlspecialchars($row['id']) ?></h4>
          <p class="status <?= htmlspecialchars($row['status']) ?>">Status: <?= htmlspecialchars($row['status']) ?></p>
          <p>Next Booking: <?= $row['next_booking_time'] ? $row['next_booking_time'] : 'None' ?></p>
          <p class="countdown" id="countdown-<?= $row['id'] ?>"></p>
          <div class="details">
            <p><strong>Machine ID:</strong> <?= htmlspecialchars($row['id']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
            <p><strong>Next Booking:</strong> <?= $row['next_booking_time'] ? $row['next_booking_time'] : 'None' ?></p>
          </div>
          <script>
            (function() {
              const countdownElem = document.getElementById('countdown-<?= $row['id'] ?>');
              const targetTime = '<?= $row['countdown_time'] ?>';
              if (!targetTime) return;

              const updateCountdown = () => {
                const now = new Date().getTime();
                const countDownDate = new Date(targetTime).getTime();
                const distance = countDownDate - now;

                if (distance <= 0) {
                  countdownElem.innerHTML = 'Currently booked';
                  return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                countdownElem.innerHTML = `Starts in: ${hours}h ${minutes}m ${seconds}s`;
              };

              updateCountdown();
              setInterval(updateCountdown, 1000);
            })();
          </script>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
  <script>
    function toggleExpand(card) {
      card.classList.toggle('expanded');
    }
  </script>
</body>
</html>
