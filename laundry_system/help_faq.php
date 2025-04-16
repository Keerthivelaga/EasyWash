<?php
session_start();
if (!isset($_SESSION['regno'])) {
    header("Location: llogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help & FAQ - Easy Wash</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://img.freepik.com/free-vector/faq-concept-flat-background_23-2148146430.jpg?t=st=1744112809~exp=1744116409~hmac=b07e74d76747e443315f3592c1ca0430c1644d377198c80523b4a79291de1b6f&w=900') no-repeat center center/cover;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        h1 {
            text-align: center;
            color: #1E3A5F;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
        }

        .faq {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            transition: background 0.3s ease;
        }

        .faq:last-child {
            border-bottom: none;
        }

        .faq-question {
            cursor: pointer;
            padding: 15px;
            color: #1E3A5F;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s ease;
        }

        .faq-answer {
            display: none;
            padding: 0 15px 15px;
            color: #333;
            font-size: 16px;
            line-height: 1.5;
        }

        .faq:hover {
            background: #f9f9f9;
            border-radius: 8px;
        }

        .faq.active .faq-answer {
            display: block;
        }

        .icon {
            font-size: 18px;
            color: #1E3A5F;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .faq.active .icon {
            transform: rotate(180deg);
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
        <h1>Help & Frequently Asked Questions</h1>

        <div class="faq">
            <div class="faq-question">
                Q: How do I book a washing machine?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: Navigate to the Booking page, select a machine, date, and time, and confirm your booking.</div>
        </div>

        <div class="faq">
            <div class="faq-question">
                Q: Can I cancel or modify my booking?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: Yes, go to "My Bookings" and select the booking you want to cancel or edit.</div>
        </div>

        <div class="faq">
            <div class="faq-question">
                Q: What does “Out of Service” mean for a machine?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: It means the machine is temporarily not available due to maintenance.</div>
        </div>

        <div class="faq">
            <div class="faq-question">
                Q: How do I know if my booking is confirmed?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: After booking, you will see a confirmation on the screen and can also view it in "My Bookings".</div>
        </div>

        <div class="faq">
            <div class="faq-question">
                Q: What are the timings for booking a machine?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: Bookings can be made between 6:00 AM and 10:00 PM daily. Please ensure you complete your laundry within this time frame.</div>
        </div>

        <div class="faq">
            <div class="faq-question">
                Q: Who can I contact for support?
                <span class="icon">➕</span>
            </div>
            <div class="faq-answer">A: Please reach out to the hostel office or email: support@easywash.com (example).</div>
        </div>

        <div class="back-link">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </div>
    </div>

    <script>
        const questions = document.querySelectorAll('.faq-question');

        questions.forEach(q => {
            q.addEventListener('click', () => {
                const faq = q.parentElement;
                faq.classList.toggle('active');

                const icon = q.querySelector('.icon');
                icon.textContent = faq.classList.contains('active') ? '➖' : '➕';
            });
        });
    </script>
</body>
</html>
