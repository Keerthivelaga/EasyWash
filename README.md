## 🮚 EasyWash- Hostel Laundry Management System

**EasyWash** is a web-based application designed to streamline and automate laundry scheduling in hostel environments. It enables students to book washing machines effortlessly, track machine status in real time, and manage their laundry history. Admins can oversee machine usage, manage users, post notifications, and handle maintenance schedules with ease.

---

### 📋 Overview

The project aims to provide an efficient and user-friendly digital solution for managing laundry services in hostels. It eliminates conflicts caused by manual bookings and offers a centralized platform for both students and administrators. The system supports real-time status updates, user role management, and alert notifications.

---

### 🌟 Features

#### 👥 User Roles and Permissions:
- **Students:** Can book laundry slots, view their history, and receive notifications.
- **Admins:** Can manage users, washing machines, bookings, and post system-wide notifications.

#### 🗓️ Wash Booking System:
- Students can schedule a wash by selecting a machine, date, and time.
- Double bookings are prevented through server-side validation.

#### 📊 Real-Time Machine Tracker:
- Displays washing machine availability: Free, In Use, Out of Service.
- Includes countdown timers, filters, and the next available booking slot.

#### 📒 Booking History:
- Users and admins can view, search, and filter booking records.
- Users can cancel or modify upcoming bookings.

#### 👤 User Profile Management:
- Update profile details, upload profile picture, and change password.
- Shows last updated timestamp from the database.

#### 🔔 Notification System:
- Admins can create and post announcements.
- Users are notified and their notification-read status is tracked.

#### 🛠️ Admin Panel:
- Add/edit/delete washing machines and their status.
- View all users, bookings, and manage maintenance schedules.

---

### 🧑‍💻 Technologies Used

- **PHP:** Backend scripting
- **MySQL:** Database management
- **HTML/CSS:** UI layout and styling
- **JavaScript:** Client-side interactivity
- **XAMPP:** Local testing environment

---

### 📁 File Structure

- `index.php` – Landing/Login Page  
- `signup.php` – User registration  
- `dashboard.php` – Main user/admin dashboard  
- `schedule_wash.php` – Booking page  
- `machine_tracker.php` – Real-time machine status view  
- `booking_history.php` – Booking history & actions  
- `user_profile.php` – Profile edit & image upload  
- `admin_panel.php` – Admin management interface  
- `notifications.php` – View & manage alerts  
- `db_connect.php` – DB connection setup  

---

### ⚙️ Installation

1. **Clone the repository**
2. **Set up the MySQL database** using the provided table structures
3. **Update `db_connect.php`** with your DB credentials
4. **Move the project folder** to your `htdocs` directory (XAMPP)
5. **Launch your browser** and visit `http://localhost/laundryease`

---

### 🚀 Usage

- **Admin:** Login through `admin_login.php` to access full dashboard and controls  
- **Student:** Register or login to manage laundry bookings  
- **Machine Tracker:** View current status and next available time  
- **Notifications:** Users receive real-time alerts posted by admin  
- **History Page:** Track past & upcoming bookings with status and filters


