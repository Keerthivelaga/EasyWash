<?php
session_start();

if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}

include('db.php');

$regno = $_SESSION['regno'];

// Handle form submission
$update_success = false;
$update_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $year_of_study = $_POST['year_of_study'];
    $hostel_block = $_POST['hostel_block'];
    $room_number = $_POST['room_number'];
    $password = $_POST['password'];

    $profile_picture = $user['profile_picture'] ?? null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, year_of_study=?, hostel_block=?, room_number=?, profile_picture=?, password=?, created_at=NOW() WHERE registration_number=?");
    $stmt->bind_param("ssssssss", $username, $email, $year_of_study, $hostel_block, $room_number, $profile_picture, $password, $regno);

    if ($stmt->execute()) {
        $update_success = true;
    } else {
        $update_error = true;
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE registration_number=?");
$stmt->bind_param("s", $regno);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$default_img = 'https://cdn-icons-png.flaticon.com/512/3135/3135789.png';
$profile_img = (!empty($user['profile_picture'])) ? htmlspecialchars($user['profile_picture']) : $default_img;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('images/bg.png') no-repeat center center/cover;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            padding: 40px 30px;
            background: rgba(255, 248, 240, 0.85);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        h2 {
            text-align: center;
            color: #1E3A5F;
            font-size: 28px;
            font-weight: bold;
        }

        .feedback {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .success { color: green; }
        .error { color: red; }

        form { margin-top: 30px; }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }

        input {
            width: 96%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        select {
            width: 99.5%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #5AA9E6;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3d8fcb;
        }

        .reset-btn {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background-color: #ccc;
            color: black;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .reset-btn:hover {
            background-color: #bbb;
        }

        .profile-picture {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .profile-picture img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .last-updated {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
            color: #666;
        }
    </style>
    <script>
        function resetForm() {
            document.getElementById("profile-form").reset();
            document.getElementById("preview-img").src = "<?= $profile_img ?>";
        }

        function previewImage(event) {
            const imgElement = document.getElementById("preview-img");
            imgElement.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Your Profile</h2>

    <?php if ($update_success): ?>
        <p class="feedback success">Profile updated successfully!</p>
    <?php elseif ($update_error): ?>
        <p class="feedback error">An error occurred. Please try again!</p>
    <?php endif; ?>

    <div class="profile-picture">
        <img id="preview-img" src="<?= $profile_img ?>" alt="Profile Picture">
    </div>

    <form method="POST" enctype="multipart/form-data" id="profile-form">
        <label>Registration Number</label>
        <input type="text" value="<?= htmlspecialchars($user['registration_number']) ?>" readonly>

        <label>Profile Picture</label>
        <input type="file" name="profile_picture" onchange="previewImage(event)">

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Password</label>
        <input type="password" name="password" value="<?= htmlspecialchars($user['password']) ?>" required>

        <label>Year of Study</label>
        <select name="year_of_study" required>
            <option value="1" <?= $user['year_of_study'] === '1' ? 'selected' : '' ?>>1</option>
            <option value="2" <?= $user['year_of_study'] === '2' ? 'selected' : '' ?>>2</option>
            <option value="3" <?= $user['year_of_study'] === '3' ? 'selected' : '' ?>>3</option>
            <option value="4" <?= $user['year_of_study'] === '4' ? 'selected' : '' ?>>4</option>
        </select>

        <label>Hostel Block</label>
        <input type="text" name="hostel_block" value="<?= htmlspecialchars($user['hostel_block']) ?>">

        <label>Room Number</label>
        <input type="text" name="room_number" value="<?= htmlspecialchars($user['room_number']) ?>">

        <button type="submit">Update Profile</button>
        <button type="button" class="reset-btn" onclick="resetForm()">Reset Form</button>
    </form>

    <div class="last-updated">
        Last Updated: <?= htmlspecialchars($user['created_at']) ?>
    </div>
</div>
</body>
</html>
