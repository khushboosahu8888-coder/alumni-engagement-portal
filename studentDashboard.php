<?php
session_start();

// 🔥 PREVENT BACK BUTTON CACHE + FORM RESUBMISSION
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only students allowed
if (!is_logged_in() || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// LOGOUT HANDLING
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

/* Fetch user info */
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Fetch profile */
$stmt2 = $conn->prepare("SELECT study_year, course FROM student_profile WHERE user_id = ?");
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$profile = $stmt2->get_result()->fetch_assoc();

/* 🔥 PROFILE UPDATE (POST → REDIRECT → GET) */
if (isset($_POST['update_profile'])) {

    $course = $_POST['course'];
    $study_year = $_POST['study_year'];

    $update = $conn->prepare("UPDATE student_profile SET course=?, study_year=? WHERE user_id=?");
    $update->bind_param("ssi", $course, $study_year, $student_id);
    $update->execute();

    // Redirect with success message
    header("Location: studentDashboard.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>

<style>
:root { --header-height: 95px; }

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #14b8a6, #ffffff 50%, #ccfbf1);
    color: #0f172a;
    min-height: 100vh;
}

/* HEADER */
.header {
    height: var(--header-height);
    padding: 20px 40px;
    display: flex; justify-content: space-between; align-items: center;
    background: rgba(255,255,255,0.95);
    border-bottom: 2px solid #14b8a6;
    box-shadow: 0 4px 20px rgba(20,184,166,0.15);
    position: sticky; top: 0; z-index: 100;
}

/* Animated Logo */
.logo {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    border-radius: 12px;
    display: flex; align-items:center; justify-content:center;
    font-size: 24px; font-weight:bold; color:white;
    box-shadow: 0 4px 15px rgba(20,184,166,0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%,100% { transform: scale(1); box-shadow: 0 4px 15px rgba(20,184,166,0.4); }
    50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(20,184,166,0.6); }
}

.logout-btn {
    padding:10px 18px;
    background:linear-gradient(135deg,#14b8a6,#0d9488);
    color:#fff; border:none; border-radius:10px; cursor:pointer;
}

/* SIDEBAR */
.sidebar {
    width: 260px; position: fixed; top: var(--header-height); left: 0;
    height: calc(100vh - var(--header-height));
    background: rgba(255,255,255,0.95);
    border-right: 2px solid #14b8a6; padding-top: 20px;
}

.sidebar a {
    display:flex; gap:12px; padding:16px 24px;
    text-decoration:none; color:#0f172a; font-size:15px;
}

.sidebar a:hover {
    border-left:3px solid #14b8a6; padding-left:32px; color:#0d9488;
    background:rgba(20,184,166,0.1);
}

/* CONTENT */
.content { margin-left: 260px; padding: 40px; }

.page-title {
    font-size: 32px; color: #0d9488; font-weight:800; margin-bottom:20px;
}

/* PROFILE BOX */
.profile-box {
    background:white; border:2px solid #14b8a6;
    display:flex; gap:25px; align-items:center;
    padding:30px; border-radius:16px; box-shadow:0 4px 20px rgba(20,184,166,0.15);
}

.profile-avatar {
    width:80px; height:80px; border-radius:50%;
    background:linear-gradient(135deg,#14b8a6,#0d9488);
    display:flex; justify-content:center; align-items:center;
    color:white; font-size:36px;
}

/* CARDS */
.card-container {
    margin-top:30px;
    display:grid; gap:25px;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
}

.big-card {
    background:white; border:2px solid #14b8a6; border-radius:16px;
    padding:40px 30px; text-align:center;
    font-size:20px; font-weight:600; color:#0f172a;
    text-decoration:none;
    box-shadow:0 4px 20px rgba(20,184,166,0.15);
    transition:.3s;
}

.big-card:hover {
    background:linear-gradient(135deg,#14b8a6,#0d9488);
    color:white; transform:translateY(-8px) scale(1.02);
}

/* MODAL */
.modal-bg {
    position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.45); display:none;
    justify-content:center; align-items:center; z-index:200;
}

.modal-box {
    background:white; padding:30px; width:420px;
    border-radius:15px; border:2px solid #14b8a6;
}

.modal-box h2 { color:#0d9488; margin-bottom:15px; }

input {
    width:100%; padding:12px; margin-bottom:15px;
    border-radius:8px; border:1px solid #14b8a6;
}

.save-btn {
    padding:12px 18px; background:#14b8a6; color:white;
    border:none; border-radius:8px; cursor:pointer; font-weight:bold;
}

.close-btn {
    padding:12px 18px; background:#d92f2f; color:white;
    border:none; border-radius:8px; cursor:pointer; font-weight:bold;
}
</style>

<script>
function openModal() { document.getElementById("modal-bg").style.display = "flex"; }
function closeModal() { document.getElementById("modal-bg").style.display = "none"; }
</script>

</head>
<body>

<!-- HEADER -->
<div class="header">
    <div style="display:flex;gap:16px;align-items:center;">
        <div class="logo">🎓</div>
        <div>
            <h1 style="font-size:22px;color:#0d9488;">COLLEGE ALUMNI ENGAGEMENT PORTAL</h1>
            <div class="tagline">Connect. Share. Grow.</div>
        </div>
    </div>

    <form method="post">
        <button name="logout" class="logout-btn">Logout</button>
    </form>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="studentDashboard.php">📊 Dashboard</a>
    <a href="#" onclick="openModal()">📝 Update Profile</a>
    <a href="view_students.php">👥 Students List</a>
    <a href="index.php">🏠 Home</a>
    <a href="about.html">ℹ️ About Us</a>
</div>

<!-- CONTENT -->
<div class="content">

    <div class="page-title">Student Dashboard</div>
    <div class="page-subtitle">Welcome back — Explore opportunities & connect with alumni.</div>

    <!-- Success Message -->
    <?php if (isset($_GET['updated'])): ?>
        <p style="color:green; font-weight:bold; margin-bottom:15px;">Profile updated successfully!</p>
    <?php endif; ?>

    <!-- PROFILE BOX -->
    <div class="profile-box">
        <div class="profile-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>

        <div>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($profile['course']) ?></p>
            <p><strong>Study Year:</strong> <?= htmlspecialchars($profile['study_year']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>

    <!-- CARDS -->
    <div class="card-container">

        <a href="view_posts.php" class="big-card">
            📰<br>View Jobs & News
            <div style="font-size:14px;">Browse job opportunities & updates</div>
        </a>

        <a href="view_events.php" class="big-card">
           📅<br>View Events
            <div style="font-size:14px;">See upcoming events</div>
        </a>

        <a href="my_events.php" class="big-card">
           ✅<br>My Events
            <div style="font-size:14px;">
            View events you registered for
            </div>
        </a>

        <a href="view_alumni.php" class="big-card">
            🎓<br>View Alumni List
            <div style="font-size:14px;">See all registered alumni</div>
        </a>

        <a href="chat_with_alumni.php" class="big-card">
            💬<br>Chat With Alumni
            <div style="font-size:14px;">Seek guidance by messaging alumni</div>
        </a>
    </div>
</div>

<!-- UPDATE PROFILE MODAL -->
<div class="modal-bg" id="modal-bg">
    <div class="modal-box">
        <h2>Update Profile</h2>

        <form method="post">
            <label><strong>Course</strong></label>
            <input type="text" name="course" value="<?= htmlspecialchars($profile['course']) ?>" required>

            <label><strong>Study Year</strong></label>
            <input type="text" name="study_year" value="<?= htmlspecialchars($profile['study_year']) ?>" required>

            <button class="save-btn" name="update_profile">Save Changes</button>
            <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

</body>
</html>
