<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only alumni allowed
if (!is_logged_in() || $_SESSION['role'] !== 'alumni') {
    header('Location: login.php');
    exit;
}

$uid = $_SESSION['user_id'];
$msg = "";

// ------------------ DELETE POST ------------------
if (isset($_GET['delete'])) {
    $pid = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND posted_by = ?");
    $stmt->bind_param("ii", $pid, $uid);
    $stmt->execute();

    header("Location: posts.php?msg=deleted");
    exit;
}

// ------------------ EDIT POST (update) ------------------
if (isset($_POST['edit_post'])) {
    $pid = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE posts SET title=?, description=? WHERE id=? AND posted_by=?");
    $stmt->bind_param("ssii", $title, $desc, $pid, $uid);
    $stmt->execute();

    header("Location: posts.php?msg=updated");
    exit;
}

// ------------------ CREATE POST (Job or News) ------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["post_type"])) {
    $post_type = $_POST["post_type"];
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);

    if ($title === "" || $description === "") {
        $msg = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (post_type, title, description, posted_by, posted_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssi", $post_type, $title, $description, $uid);
        $stmt->execute();
        $msg = ucfirst($post_type) . " posted successfully!";
    }
}

// Fetch user's posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE posted_by = ? ORDER BY posted_at DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Post Jobs & News</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #14b8a6, #ffffff, #ccfbf1);
    padding: 40px;
}

/* Main split section */
.split-container {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

/* Card style */
.form-card {
    flex: 1;
    min-width: 340px;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    border-radius: 14px;
    padding: 25px;
    border: 2px solid #14b8a6;
    box-shadow: 0 10px 25px rgba(20, 184, 166, 0.20);
    transition: 0.3s;
}

.form-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(20, 184, 166, 0.30);
}

.form-card h2 {
    margin-top: 0;
    color: #0d9488;
}

input, textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    border-radius: 8px;
    border: 1px solid #0d9488;
    background: #ffffff;
}

textarea {
    height: 120px;
}

button {
    margin-top: 12px;
    padding: 12px 20px;
    background: #0d9488;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #14b8a6;
}

/* Posts list */
.post-card {
    background: white;
    padding: 18px;
    border-radius: 12px;
    border-left: 5px solid #14b8a6;
    margin-top: 18px;
    transition: 0.2s;
}

.post-card:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    color: white;
    font-size: 12px;
}

.badge-job { background: #0d9488; }
.badge-news { background: #f59e0b; }

/* Modal Styling */
.modal-bg {
    display: none;
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
}
.modal-box {
    background: white;
    padding: 25px;
    border-radius: 10px;
    width: 350px;
    border: 2px solid #0d9488;
}
.close-btn {
    margin-top:10px;
    background: #999;
}
.close-btn:hover {
    background: #777;
}

@media(max-width: 768px) {
    .split-container { flex-direction: column; }
}
</style>
</head>

<body>

<h1 style="color:#0d9488; text-align:center; margin-bottom:30px;">Post Jobs & News</h1>

<?php if($msg !== ""): ?>
<div style="padding:12px; background:#d1fae5; border-left:4px solid #0d9488; margin-bottom:20px; border-radius:8px;">
    <?= htmlspecialchars($msg) ?>
</div>
<?php endif; ?>

<!-- Split layout -->
<div class="split-container">

    <!-- JOB POSTING -->
    <div class="form-card">
        <h2>💼 Post a Job</h2>
        <form method="POST">
            <input type="hidden" name="post_type" value="job">
            
            <label>Job Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" required></textarea>

            <button type="submit">Post Job</button>
        </form>
    </div>

    <!-- NEWS POSTING -->
    <div class="form-card">
        <h2>📰 Post News / Announcement</h2>
        <form method="POST">
            <input type="hidden" name="post_type" value="news">
            
            <label>News Title</label>
            <input type="text" name="title" required>

            <label>Details</label>
            <textarea name="description" required></textarea>

            <button type="submit">Post News</button>
        </form>
    </div>

</div>

<!-- Your posts -->
<h2 style="margin-top:40px; color:#0d9488;">Your Posts</h2>

<?php while($p = $posts->fetch_assoc()) : ?>
<div class="post-card">
    <span class="type-badge <?= $p['post_type'] == 'job' ? 'badge-job' : 'badge-news' ?>">
        <?= strtoupper($p['post_type']) ?>
    </span>

    <h3><?= htmlspecialchars($p['title']) ?></h3>
    <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
    <small style="color:#555;">Posted on: <?= $p['posted_at'] ?></small>

    <div style="margin-top:10px;">
        <!-- Edit Button -->
        <button style="background:#0d9488; padding:6px 12px; border-radius:6px; cursor:pointer;"
            onclick="openEdit('<?= $p['id'] ?>', '<?= htmlspecialchars($p['title']) ?>', `<?= htmlspecialchars($p['description']) ?>`)">
            ✏ Edit
        </button>

        <!-- Delete Button -->
        <a href="posts.php?delete=<?= $p['id'] ?>"
           onclick="return confirm('Delete this post?');">
            <button style="background:#e11d48; padding:6px 12px; border-radius:6px; cursor:pointer;">
                🗑 Delete
            </button>
        </a>
    </div>
</div>
<?php endwhile; ?>

<!-- EDIT MODAL -->
<div id="editModal" class="modal-bg">
    <div class="modal-box">
        <h3>Edit Post</h3>
        <form method="POST">
            <input type="hidden" name="post_id" id="edit_id">
            
            <label>Title</label>
            <input type="text" name="title" id="edit_title" required>

            <label>Description</label>
            <textarea name="description" id="edit_desc" required></textarea>

            <button type="submit" name="edit_post">Update Post</button>
            <button type="button" class="close-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openEdit(id, title, desc) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_title").value = title;
    document.getElementById("edit_desc").value = desc;
    document.getElementById("editModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}
</script>

</body>
</html>
