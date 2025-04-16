<?php
session_start();
include 'db.php'; // Include database connection

// Initialize error variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($email) || empty($password)) {
        $error_message = 'Both fields are required.';
    } else {
        // ✅ Updated query to include role
        $query = "SELECT registration_number, username, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Authentication successful, store user details in session variables
                $_SESSION['regno'] = $user['registration_number'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role']; // ✅ Store role in session

                // ✅ Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_panel.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error_message = 'Incorrect password.';
            }
        } else {
            $error_message = 'No account found with this email.';
        }
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Easy Wash</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://img.freepik.com/premium-vector/laundry-room-interior-with-washing-machine-household-chemistry-cleaning-washing-powder-towels_529344-718.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-align: center;
            width: 350px;
        }
        h2 {
            margin-bottom: 20px;
            color: #1E3A5F;
        }
        .input-box {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            background: #1E3A5F;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #5aa9e6;
        }
        .signup-link {
            display: block;
            margin-top: 15px;
            color: #1E3A5F;
            text-decoration: none;
        }
        .signup-link:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            font-size: 14px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Easy Wash</h2>
        <?php
        // Display error message if it exists
        if (!empty($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
        <form action="llogin.php" method="POST">
            <input type="email" name="email" class="input-box" placeholder="Enter your email" required>
            <input type="password" name="password" class="input-box" placeholder="Enter your password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="lsignup.php" class="signup-link">Don't have an account? Sign Up</a>
    </div>
</body>
</html>