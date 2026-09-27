# College Alumni Engagement Portal

A web-based platform designed to connect **college students, alumni, and administrators** through events, posts, communication, and user management.

## 📌 Project Overview

The College Alumni Engagement Portal provides a centralized platform where students and alumni can interact, participate in events, share posts, and communicate with each other.

Administrators can manage users, events, posts, and view system-related information through an admin dashboard.

## ✨ Key Features

### 👨‍💼 Admin

* Admin dashboard
* Manage students and alumni
* View and manage users
* Create & Manage events
* View event participants
* Manage posts
* View reports

### 🎓 Alumni

* Alumni dashboard
* View students and other alumni
* Participate in events
* Create and view posts
* Communicate with students
* View events

### 👨‍🎓 Student

* Student dashboard
* View alumni
* Register for events
* View posts
* Communicate with alumni
* View available events

### 🔐 Authentication

* User registration and login
* Role-based access for Admin, Alumni, and Student
* Session-based authentication
* CSRF token protection

## 🛠️ Technologies Used

| Technology | Purpose                          |
| ---------- | -------------------------------- |
| HTML       | Structure of web pages           |
| CSS        | Styling and responsive interface |
| JavaScript | Client-side functionality        |
| PHP        | Backend/server-side logic        |
| MySQL      | Database management              |
| XAMPP      | Local development environment    |

## 🗄️ Database

The project uses **MySQL** to store:

* User information
* Student and alumni profiles
* Events
* Event registrations
* Posts
* Communication/chat data
* Other portal-related information

The database structure and demo data are provided in:

`alumni_portal.sql`

## 🔑 Demo Accounts

The following accounts are provided for demonstration purposes.

> **Note:** All preloaded information is demo data.

| Role    | Email              | Password    |
| ------- | ------------------ | ----------- |
| Admin   | `admin1@gmail.com` | `admin@123` |
| Alumni  | `raj@gmail.com`    | `raj@123`   |
| Student | `rahul@gmail.com`  | `rahul@123` |

You can also **register a new account** through the registration page.

## 💻 Running the Project Locally

### 1. Install XAMPP

Install XAMPP with **Apache** and **MySQL**.

### 2. Copy the project

Place the project folder inside:

```text
C:\xampp\htdocs\
```

The folder should be:

```text
C:\xampp\htdocs\alumni-engagement-portal
```

### 3. Start XAMPP

Start:

* Apache
* MySQL

### 4. Create the database

Open phpMyAdmin and create a database named:

```text
alumni_portal
```

Import:

```text
alumni_portal.sql
```

into the database.

### 5. Configure the database connection

Create your local configuration file:

```text
includes/config.php
```

The repository does **not** include the actual `config.php` because it contains local database configuration.

A sample configuration is provided as:

```text
config.example.php
```

Update the database settings according to your local XAMPP/MySQL setup.

### 6. Open the project

Visit:

```text
http://localhost/alumni-engagement-portal/
```

## 📂 Project Structure

```text
alumni-engagement-portal/
│
├── index.php
├── login.php
├── register.php
├── adminDashboard.php
├── alumniDashboard.php
├── studentDashboard.php
├── events.php
├── posts.php
├── chat_with_alumni.php
├── chat_with_students.php
├── manage_users.php
├── reports.php
├── alumni_portal.sql
├── config.example.php
│
├── includes/
│   ├── config.php
│   ├── db.php
│   └── helpers.php
│
└── .gitignore
```

## 🔒 Security Note

Sensitive local configuration files such as `includes/config.php` are excluded from the Git repository using `.gitignore`.

The repository contains only the example configuration required to understand the setup.

## 🚀 Future Improvements

* Online deployment
* Email notifications
* Advanced alumni search and filtering
* Improved real-time communication
* Event reminders
* Enhanced analytics and reporting
* Cloud database integration
* Improved mobile responsiveness

## 👩‍💻 Developer

**Khushboo Sahu**

MCA Student | Web Development & Data Analytics

---

⭐ If you find this project useful, feel free to explore the repository.
