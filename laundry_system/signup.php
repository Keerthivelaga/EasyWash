<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $regno = trim($_POST['regno']);  // Primary key
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
    $year_of_study = $_POST['year_of_study'];
    $hostel_block = trim($_POST['hostel_block']);
    $room_number = trim($_POST['room_number']);

    // Check if the registration number already exists
    $check_query = "SELECT registration_number FROM users WHERE registration_number = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $regno);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: 'Registration number already exists! Please log in.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        window.location.href = 'llogin.php';
                    });
                });
              </script>";
        exit();
    }
    $stmt->close();

    // Check if the email already exists
    $check_email_query = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: 'Email is already registered! Please log in.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        window.location.href = 'llogin.php';
                    });
                });
              </script>";
        exit();
    }
    $stmt->close();

    // Insert new user into the database
    $insert_query = "INSERT INTO users (registration_number, username, email, password, year_of_study, hostel_block, room_number) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("sssssss", $regno, $username, $email, $password, $year_of_study, $hostel_block, $room_number);

    if ($stmt->execute()) {
        // Redirect to the login page after successful signup
        header("Location: llogin.php");
        exit();
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not register. Please try again later.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                });
              </script>";
    }

    // Close connections
    $stmt->close();
}
$conn->close();
?>