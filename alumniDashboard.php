<?php
session_start();

// 🔥 PREVENT BACK BUTTON CACHE + FORM RESUBMISSION
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// only alumni allowed
if (!is_logged_in() || $_SESSION['role'] !== 'alumni') {
    header('Location: login.php');
    exit;
}

// logout handling
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// 🔥 PROFILE UPDATE — PRG PATTERN (POST → REDIRECT → GET)
if (isset($_POST['update_profile'])) {
    $batch_year = $_POST['batch_year'] ?? '';
    $company = $_POST['company'] ?? '';
    $position = $_POST['position'] ?? '';
    $bio = $_POST['bio'] ?? '';

    $uid = $_SESSION['user_id'];

    // check if profile exists
    $stmtCheck = $conn->prepare("SELECT 1 FROM alumni_profile WHERE user_id = ?");
    $stmtCheck->bind_param("i", $uid);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // update existing profile
        $stmt = $conn->prepare("UPDATE alumni_profile 
            SET batch_year=?, company=?, position=?, bio=?, updated_at=NOW() 
            WHERE user_id=?");
        $stmt->bind_param("ssssi", $batch_year, $company, $position, $bio, $uid);
        $stmt->execute();

        // redirect to avoid resubmission
        header("Location: alumniDashboard.php?profile_updated=1");
        exit;
    } else {
        // insert new profile
        $stmt = $conn->prepare("INSERT INTO alumni_profile 
            (user_id, batch_year, company, position, bio, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("issss", $uid, $batch_year, $company, $position, $bio);
        $stmt->execute();

        header("Location: alumniDashboard.php?profile_created=1");
        exit;
    }
}

// fetch profile: users + alumni_profile
$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.created_at AS account_created, u.updated_at AS account_updated,
           ap.batch_year, ap.company, ap.position, ap.bio, 
           ap.created_at AS profile_created, ap.updated_at AS profile_updated
    FROM users u
    LEFT JOIN alumni_profile ap ON u.id = ap.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Alumni Dashboard</title>

<style>
body { margin:0; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:linear-gradient(135deg,#14b8a6 0%,#ffffff 50%,#ccfbf1 100%); color:#0f172a; min-height:100vh;}
.header { padding:20px 40px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:100; background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); border-bottom:2px solid #14b8a6; box-shadow:0 4px 20px rgba(20,184,166,0.15);}
/* Animated Logo */
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
    color: white;
    box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4);
    animation: pulse 2s infinite;
}
/* Pulse Animation */
@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4); 
    }
    50% { 
        transform: scale(1.05); 
        box-shadow: 0 6px 20px rgba(20, 184, 166, 0.6); 
    }
}
.title-group h1 { font-size:22px;color:#0d9488;margin:0; }
.tagline { font-size:13px;margin-top:2px;color:#0f172a; }
.logout-btn { padding:10px 18px;background:linear-gradient(135deg,#14b8a6,#0d9488);color:#fff;border:none;border-radius:10px;cursor:pointer; }

.sidebar { width:260px; position:fixed; top:91px; left:0; height:calc(100vh - 91px); background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); border-right:2px solid #14b8a6; padding:20px 0;}
.sidebar a { display:flex; align-items:center; gap:12px; padding:12px 24px; color:#0f172a; text-decoration:none; font-weight:500;}
.sidebar a:hover { background:rgba(20,184,166,0.06); border-left:3px solid #14b8a6; padding-left:20px; color:#0d9488;}

.content { margin-left:260px; padding:40px; z-index:1; }
.page-title { font-size:36px; color:#0d9488; font-weight:800; margin-bottom:6px; }
.page-subtitle { color:#0f172a; margin-bottom:24px; }

.profile-box { background:white; border:2px solid #14b8a6; padding:20px; border-radius:12px; display:flex; gap:20px; align-items:center; }
.profile-avatar { width:80px;height:80px;border-radius:50%; background:linear-gradient(135deg,#14b8a6,#0d9488); color:white; display:flex; align-items:center; justify-content:center; font-size:36px; }

.card-container { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-top:24px; }
.big-card { background:white; border-radius:12px; padding:26px; border:2px solid #14b8a6; text-align:center; cursor:pointer; transition:all .25s; font-weight:700; }
.big-card:hover { transform:translateY(-6px); background:linear-gradient(135deg,#14b8a6,#0d9488 100%); color:white; box-shadow:0 15px 40px rgba(20,184,166,0.25); }
a.card-link { text-decoration:none; color:inherit; display:block; }

#updateProfileForm { display:none; background:white; border:2px solid #14b8a6; padding:20px; border-radius:12px; margin-top:20px; max-width:500px; }
#updateProfileForm label { display:block; margin:10px 0 4px; font-weight:500; }
#updateProfileForm input, #updateProfileForm textarea { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #ccc; }
#updateProfileForm button { margin-top:12px; padding:10px 18px; background:linear-gradient(135deg,#14b8a6,#0d9488); color:white; border:none; border-radius:8px; cursor:pointer; }
.success-msg { color:green; margin-bottom:10px; font-weight:600; }

@media(max-width:768px){ .sidebar{width:70px} .content{margin-left:70px} .sidebar a span{display:none} }
</style>

<script>
function toggleUpdateProfile(){
    const form = document.getElementById('updateProfileForm');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

</head>
<body>

<!-- header -->
<div class="header">
    <div style="display:flex;align-items:center;gap:15px;">
        <div class="logo">🎓</div>
        <div class="title-group">
            <h1>COLLEGE ALUMNI ENGAGEMENT PORTAL</h1>
            <div class="tagline">Connect. Share. Grow.</div>
        </div>
    </div>

    <form method="post" style="display:inline;">
        <button name="logout" class="logout-btn">Logout</button>
    </form>
</div>

<!-- sidebar -->
<div class="sidebar">
    <a href="alumniDashboard.php"><span>📊</span><span>Dashboard</span></a>
    <a href="#" onclick="toggleUpdateProfile()"><span>✏️</span><span>Update Profile</span></a>
    <a href="view_alumni.php"><span>👥</span><span>Alumni List</span></a>
    <a href="index.php"><span>🏠</span><span>Home</span></a>
    <a href="about.html"><span>ℹ️</span><span>About Us</span></a>
</div>

<!-- content -->
<div class="content">
    <div class="page-title">Alumni Dashboard</div>
    <div class="page-subtitle">Welcome back — Connect with your college community and share opportunities</div>

    <!-- PROFILE -->
    <div class="profile-box">
        <div class="profile-avatar"><?= strtoupper(substr($profile['name'],0,1)) ?></div>
        <div class="profile-info">
            <p><strong><?= htmlspecialchars($profile['name']) ?></strong></p>
            <p>Email: <?= htmlspecialchars($profile['email']) ?></p>
            <p>Batch: <?= htmlspecialchars($profile['batch_year'] ?? 'Not set') ?> • Position: <?= htmlspecialchars($profile['position'] ?? '—') ?></p>
            <p>Company: <?= htmlspecialchars($profile['company'] ?? '—') ?></p>
            <p style="font-size:13px;color:#334155;margin-top:6px;">
                Account created: <?= htmlspecialchars($profile['account_created']) ?> | 
                Profile updated: <?= htmlspecialchars($profile['profile_updated'] ?? $profile['account_updated']) ?>
            </p>
        </div>
    </div>

    <!-- UPDATE PROFILE FORM -->
    <div id="updateProfileForm">
        <?php
        if (isset($_GET['profile_updated'])) {
            echo "<div class='success-msg'>Profile updated successfully!</div>";
        }
        if (isset($_GET['profile_created'])) {
            echo "<div class='success-msg'>Profile created successfully!</div>";
        }
        ?>
        <form method="post">
            <label>Batch Year</label>
            <input type="text" name="batch_year" value="<?= htmlspecialchars($profile['batch_year'] ?? '') ?>">

            <label>Company</label>
            <input type="text" name="company" value="<?= htmlspecialchars($profile['company'] ?? '') ?>">

            <label>Position</label>
            <input type="text" name="position" value="<?= htmlspecialchars($profile['position'] ?? '') ?>">

            <label>Bio</label>
            <textarea name="bio" rows="4"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>

            <button type="submit" name="update_profile">Save Changes</button>
        </form>
    </div>

    <!-- CARDS -->
    <div class="card-container">

        <a class="card-link" href="posts.php">
            <div class="big-card">
                <div>📢</div>
                <div style="margin-top:10px;">Post Jobs & News</div>
                <div style="font-size:13px;">Share openings or announcements</div>
            </div>
        </a>

        <a class="card-link" href="view_students.php">
            <div class="big-card">
                <div>👥</div>
                <div style="margin-top:10px;">View Students List</div>
                <div style="font-size:13px;">See all registered students</div>
            </div>
        </a>

        <a class="card-link" href="view_events.php">
            <div class="big-card">
                <div>📅</div>
                <div style="margin-top:10px;">View Events</div>
                <div style="font-size:13px;">See upcoming events</div>
            </div>
        </a>

        <a class="card-link" href="my_events.php">
    <div class="big-card">
        <div>✅</div>
        <div style="margin-top:10px;">My Events</div>
        <div style="font-size:13px;">
            View registered events
        </div>
    </div>
</a>

        <a class="card-link" href="chat_with_students.php">
            <div class="big-card">
                <div>💬</div>
                <div style="margin-top:10px;">Chat With Students</div>
                <div style="font-size:13px;">Messaging interface</div>
            </div>
        </a>

        <a class="card-link" href="#" onclick="toggleUpdateProfile()">
            <div class="big-card">
                <div>✏️</div>
                <div style="margin-top:10px;">Update Profile</div>
                <div style="font-size:13px;">Edit your details</div>
            </div>
        </a>

    </div>
</div>

</body>
</html>
