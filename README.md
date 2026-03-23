🧺 EasyWash — Laundry Management System

A web-based application to simplify and automate laundry bookings in hostel environments. Students can schedule washing machine usage, track machine availability in real-time, and manage bookings through a user-friendly dashboard.

🔗 **Live Demo:** [easywash-production.up.railway.app](https://easywash-production.up.railway.app)

---

## ✨ Features

- 🔐 **User Authentication** — Secure signup and login with bcrypt password hashing
- 📅 **Machine Booking** — Book washing machines by date and time with double-booking prevention
- 📊 **Real-time Dashboard** — View available, occupied, and under-maintenance machines instantly
- 🕒 **Machine Tracker** — Live countdown timers for upcoming bookings
- 📜 **Booking History** — Filter bookings by status (completed, upcoming, cancelled)
- 🔔 **Notifications** — Admin can post notices; users can mark them as read
- 👤 **User Profile** — Update personal details and profile picture
- 🛠️ **Admin Panel** — Manage machines, update statuses, and post announcements
- ❓ **Help & FAQ** — Accordion-style FAQ page for common questions
- 📱 **Responsive Design** — Works across desktop and mobile devices

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Hosting | Railway |
| Auth | PHP Sessions + bcrypt |

---

## 🚀 Getting Started (Local Setup)

### Prerequisites
- XAMPP (Apache + MySQL)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/Keerthivelaga/EasyWash.git
cd EasyWash
```

2. **Move to XAMPP folder**
```
Copy the project to: C:\xampp\htdocs\EasyWash\
```

3. **Import the database**
- Open `http://localhost/phpmyadmin`
- Create a new database called `laundry_db`
- Click **Import** and select `laundry_db.sql`

4. **Configure database connection**

Edit `db.php`:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "laundry_db";
```

5. **Start XAMPP**
- Start **Apache** and **MySQL** in XAMPP Control Panel

6. **Open in browser**
```
http://localhost/EasyWash/
```

---

## 👤 Default Admin Credentials

To access the admin panel, create a user in the database and set their role to `admin` in the `users` table.

---

## 📁 Project Structure

```
EasyWash/
├── images/              # Background and machine images
├── admin_panel.php      # Admin dashboard
├── booking.php          # Machine booking page
├── booking_history.php  # User booking history
├── dashboard.php        # Main user dashboard
├── db.php               # Database connection
├── help_faq.php         # FAQ page
├── index.php            # Entry point redirect
├── lhome.php            # Landing/home page
├── lhome1.css           # Home page styles
├── llogin.php           # Login page
├── lsignup.php          # Signup page
├── notifications.php    # Notifications page
├── signup.php           # Signup handler
├── user_profile.php     # User profile page
├── washing_machine_status.php  # Machine tracker
├── laundry_db.sql       # Database schema and seed data
├── composer.json        # PHP dependencies
├── railway.toml         # Railway deployment config
└── nixpacks.toml        # Nixpacks build config
```

---

## 🌐 Deployment
This project is deployed on **Railway** with a MySQL database.

## 🙋‍♀️ Author
**Keerthi Sri Velaga**  
📧 keerthichowdary.v1355@gmail.com  
🔗 [LinkedIn](https://linkedin.com/in/your-profile) | [GitHub](https://github.com/Keerthivelaga)

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).
