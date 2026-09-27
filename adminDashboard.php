<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/helpers.php';
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
// Only allow admin to access
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// Fetch admin info dynamically
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #14b8a6 0%, #ffffff 50%, #ccfbf1 100%);
            color: #0f172a;
            min-height: 100vh;
            overflow-x: hidden;
        }
        /* Header */
        .header {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid #14b8a6;
            box-shadow: 0 4px 20px rgba(20, 184, 166, 0.15);
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(20, 184, 166, 0.6); }
        }
        .title-group h1 {
            font-size: 22px;
            font-weight: 700;
            color: #0d9488;
        }
        .tagline {
            font-size: 13px;
            color: #0f172a;
            margin-top: 2px;
        }
        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .logout-btn {
            padding: 12px 28px;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
        }
       .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.5);
        }
        /* Sidebar */
        .sidebar {
            width: 260px;
            position: fixed;
            top: 91px;
            left: 0;
            height: calc(100vh - 91px);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 2px solid #14b8a6;
            padding: 20px 0;
            z-index: 50;
            box-shadow: 4px 0 15px rgba(20, 184, 166, 0.1);
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            color: #0f172a;
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s;
            font-weight: 500;
            border-left: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .sidebar a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(20, 184, 166, 0.15) 0%, transparent 100%);
            transition: width 0.3s;
        }
        .sidebar a:hover::before {
            width: 100%;
        }
        .sidebar a:hover {
            color: #0d9488;
            border-left-color: #14b8a6;
            padding-left: 32px;
        }
        .sidebar a .icon {
            font-size: 18px;
        }
        /* Main Content */
        .content {
            margin-left: 260px;
            padding: 40px;
            position: relative;
            z-index: 1;
        }
        .page-title {
            font-size: 36px;
            margin-bottom: 10px;
            color: #0d9488;
            font-weight: 800;
            display: inline-block;
        }
        .page-subtitle {
            color: #0f172a;
            font-size: 16px;
            margin-bottom: 35px;
        }
        /* Profile Box */
        .profile-box {
            background: white;
            backdrop-filter: blur(20px);
            border: 2px solid #14b8a6;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 25px;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(20, 184, 166, 0.15);
        }
        .profile-box:hover {
            border-color: #0d9488;
            box-shadow: 0 10px 30px rgba(20, 184, 166, 0.25);
            transform: translateY(-3px);
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4);
            color: white;
        }
        .profile-info p {
            margin: 8px 0;
            font-size: 15px;
            color: #0f172a;
        }
        .profile-info strong {
            color: #0d9488;
        }
        /* Function Cards */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        .big-card {
            background: white;
            backdrop-filter: blur(20px);
            padding: 40px 30px;
            text-align: center;
            border-radius: 16px;
            font-size: 20px;
            cursor: pointer;
            border: 2px solid #14b8a6;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            color: #0f172a;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(20, 184, 166, 0.15);
        }
        .big-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(20, 184, 166, 0.2), transparent);
            transition: left 0.5s;
        }
        .big-card:hover::before {
            left: 100%;
        }
        .big-card:hover {
            transform: translateY(-8px) scale(1.02);
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            color: white;
            border-color: #5eead4;
            box-shadow: 0 15px 40px rgba(20, 184, 166, 0.35);
        }
        .card-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .card-desc {
            font-size: 13px;
            opacity: 0.8;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar a span {
                display: none;
            }
            .content {
                margin-left: 70px;
            }
        }
    </style>  </head>
<body>
    <!-- HEADER -->
    <div class="header">
        <div class="logo-section">
            <div class="logo">🎓</div>
            <div class="title-group">
                <h1>COLLEGE ALUMNI ENGAGEMENT PORTAL</h1>
                <div class="tagline">Connect. Share. Grow.</div>
            </div>
        </div>
        <div class="header-right">
    <form method="post" style="display:inline;">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
       </div>
    </div>
    <!-- SIDEBAR -->
    <div class="sidebar">
    <a href="adminDashboard.php"><span class="icon">📊</span> <span>Dashboard</span></a>
    <a href="manage_users.php"><span class="icon">👤</span> <span>Manage Users</span></a>
    <a href="index.php"><span class="icon">🏠</span> <span>Home</span></a>
    <a href="about.html"><span class="icon">ℹ️</span> <span>About Us</span></a>
    </div>
    <!-- CONTENT -->
    <div class="content">
        <div class="page-title">Admin Dashboard</div>
        <div class="page-subtitle">Welcome back - Manage your alumni portal easily with Admin Dashboard</div>
       <!-- PROFILE -->
    <div class="profile-box">
    <div class="profile-avatar">
        <?= strtoupper(substr($admin['name'], 0, 1)) ?>
    </div>
    <div class="profile-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($admin['name']) ?></p>
        <p><strong>Role:</strong> System Administrator</p>
        <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
    </div>
    </div>
    <!-- FUNCTION BUTTONS -->
        <div class="card-container">
        <a href="reports.php" style="text-decoration:none;">
         <div class="big-card">
        <span class="card-icon">📊</span>
        <div class="card-title">Reports</div>
        <div class="card-desc">View analytics & system summary</div>
         </div>
         </a>
       <a href="manage_users.php" style="text-decoration:none;">
        <div class="big-card">
            <span class="card-icon">👤</span>
            <div class="card-title">Manage Users</div>
            <div class="card-desc">Edit and remove users</div>
        </div>
        </a>
        <a href="view_alumni.php" style="text-decoration:none;">
        <div class="big-card">
            <span class="card-icon">🎓</span>
            <div class="card-title">View Alumni List</div>
            <div class="card-desc">See all registered alumni</div>
        </div>
        </a>
        <a href="view_students.php" style="text-decoration:none;">
        <div class="big-card">
            <span class="card-icon">👥</span>
            <div class="card-title">View Students List</div>
            <div class="card-desc">See all registered students</div>
        </div>
        </a>
        <a href="events.php" style="text-decoration:none;">
        <div class="big-card">
            <span class="card-icon">📅</span>
            <div class="card-title">Manage Events</div>
            <div class="card-desc">Create and organize events</div>
        </div>
        </a>
        <a href="view_posts.php" style="text-decoration:none;">
        <div class="big-card">
            <span class="card-icon">📰</span>
            <div class="card-title">View Jobs & News</div>
            <div class="card-desc">See job opportunities & updates posted</div>
        </div>
        </a>
        </div>
    </div>
</body> </html>

