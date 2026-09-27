<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only logged-in users (students/alumni/admin) can view posts
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// Fetch all posts, join with users to show who posted
$stmt = $conn->prepare("
    SELECT p.*, u.name AS posted_by_name
    FROM posts p
    JOIN users u ON p.posted_by = u.id
    ORDER BY p.posted_at DESC
");
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Posts - Jobs & News</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #14b8a6, #ffffff, #ccfbf1);
    padding: 40px;
}
h1 { text-align:center; color:#0d9488; margin-bottom:30px; }
.posts-container { display:flex; gap:20px; flex-wrap:wrap; justify-content:center; }
.post-card {
    background:white; border-radius:12px; padding:20px; border-left:5px solid #14b8a6;
    margin-bottom:20px; width:320px; box-shadow:0 6px 18px rgba(0,0,0,0.1); transition:0.2s;
}
.post-card:hover { transform:scale(1.02); box-shadow:0 10px 25px rgba(0,0,0,0.15); }
.type-badge {
    display:inline-block; padding:6px 12px; border-radius:20px; color:white; font-size:12px; margin-bottom:8px;
}
.badge-job { background:#0d9488; }
.badge-news { background:#f59e0b; }
.post-title { font-size:18px; font-weight:600; color:#0d9488; margin:6px 0; }
.post-desc { font-size:14px; color:#334155; margin:8px 0; white-space:pre-wrap; }
.post-footer { font-size:12px; color:#555; }
.section-title { width:100%; font-size:24px; color:#0d9488; margin:20px 0 10px; border-bottom:2px solid #14b8a6; padding-bottom:4px; }
</style>
</head>
<body>

<h1>All Alumni Posts</h1>

<?php
// Separate posts by type
$jobs = [];
$news = [];

while($p = $posts->fetch_assoc()) {
    if($p['post_type'] === 'job') $jobs[] = $p;
    else $news[] = $p;
}

// Display Jobs
if(count($jobs) > 0){
    echo "<div class='section-title'>💼 Job Posts</div><div class='posts-container'>";
    foreach($jobs as $j){
        echo "<div class='post-card'>
            <span class='type-badge badge-job'>JOB</span>
            <div class='post-title'>".htmlspecialchars($j['title'])."</div>
            <div class='post-desc'>".nl2br(htmlspecialchars($j['description']))."</div>
            <div class='post-footer'>Posted by: ".htmlspecialchars($j['posted_by_name'])." on ".$j['posted_at']."</div>
        </div>";
    }
    echo "</div>";
}

// Display News
if(count($news) > 0){
    echo "<div class='section-title'>📰 News / Announcements</div><div class='posts-container'>";
    foreach($news as $n){
        echo "<div class='post-card'>
            <span class='type-badge badge-news'>NEWS</span>
            <div class='post-title'>".htmlspecialchars($n['title'])."</div>
            <div class='post-desc'>".nl2br(htmlspecialchars($n['description']))."</div>
            <div class='post-footer'>Posted by: ".htmlspecialchars($n['posted_by_name'])." on ".$n['posted_at']."</div>
        </div>";
    }
    echo "</div>";
}
?>

</body>
</html>
