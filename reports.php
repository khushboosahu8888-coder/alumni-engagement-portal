<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

/* DATA */
$total_users     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users"))['total'];
$total_students  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='student'"))['total'];
$total_alumni    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='alumni'"))['total'];
$total_admin     = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='admin'"))['total'];
$total_events    = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM events"))['total'];
$total_jobs      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM posts WHERE post_type='job'"))['total'];
$total_news      = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM posts WHERE post_type='news'"))['total'];
$total_messages  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS total FROM messages"))['total'];
$total_registrations = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM event_registrations
    WHERE status = 'registered'
"))['total'];

$total_student_participants = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM event_registrations er
    INNER JOIN users u ON er.user_id = u.id
    WHERE er.status = 'registered'
    AND u.role = 'student'
"))['total'];

$total_alumni_participants = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total
    FROM event_registrations er
    INNER JOIN users u ON er.user_id = u.id
    WHERE er.status = 'registered'
    AND u.role = 'alumni'
"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>System Reports</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg, #14b8a6 0%, #ffffff 50%, #ccfbf1 100%);
    min-height:100vh;
    padding:40px;
}

h1{
    text-align:center;
    font-weight:600;
    color:#0f172a;
    margin-bottom:30px;
}

/* PRINT BUTTON */
.print-area{
    text-align:center;
    margin-bottom:40px;
}
.print-btn{
    padding:14px 26px;
    background:#dc2626;
    color:#fff;
    border:none;
    border-radius:14px;
    font-size:15px;
    cursor:pointer;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}
.print-btn:hover{
    background:#b91c1c;
}

/* GRID */
.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:25px;
}

/* CARDS */
.card{
    position:relative;
    background:rgba(255,255,255,0.85);
    border-radius:18px;
    padding:25px;
    box-shadow:0 15px 35px rgba(0,0,0,0.15);
}

.icon{
    font-size:32px;
    color:#0d9488;
    margin-bottom:15px;
}

.card h3{
    margin:0;
    font-size:15px;
    font-weight:400;
    color:#475569;
}

.card p{
    margin-top:8px;
    font-size:34px;
    font-weight:600;
    color:#0f766e;
}

/* PRINT CLEANUP */
@media print{
    body{
        background:white;
        padding:0;
    }
    .print-btn{
        display:none;
    }
    .card{
        box-shadow:none;
        page-break-inside:avoid;
    }
}
</style>
</head>

<body>

<h1>System Reports & Analytics</h1>

<div class="print-area">
    <button class="print-btn" onclick="window.print()">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </button>
</div>

<div class="dashboard">

    <div class="card">
        <div class="icon"><i class="fa-solid fa-users"></i></div>
        <h3>Total Users</h3>
        <p><?= $total_users ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
        <h3>Students</h3>
        <p><?= $total_students ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
        <h3>Alumni</h3>
        <p><?= $total_alumni ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-user-shield"></i></div>
        <h3>Admins</h3>
        <p><?= $total_admin ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
        <h3>Total Events</h3>
        <p><?= $total_events ?></p>
    </div>

    <div class="card">
    <div class="icon"><i class="fa-solid fa-ticket"></i></div>
    <h3>Total Registrations</h3>
    <p><?= $total_registrations ?></p>
</div>

<div class="card">
    <div class="icon"><i class="fa-solid fa-user-graduate"></i></div>
    <h3>Student Participants</h3>
    <p><?= $total_student_participants ?></p>
</div>

<div class="card">
    <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
    <h3>Alumni Participants</h3>
    <p><?= $total_alumni_participants ?></p>
</div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-briefcase"></i></div>
        <h3>Job Posts</h3>
        <p><?= $total_jobs ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-newspaper"></i></div>
        <h3>News Posts</h3>
        <p><?= $total_news ?></p>
    </div>

    <div class="card">
        <div class="icon"><i class="fa-solid fa-comments"></i></div>
        <h3>Total Messages</h3>
        <p><?= $total_messages ?></p>
    </div>

</div>

</body>
</html>
