<?php
session_start();
require_once "includes/db.php";
require_once "includes/helpers.php";

// Allow only admin
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

/* ----------------------------------------------------------
   UPDATE USER
---------------------------------------------------------- */
if (isset($_POST['update_user'])) {
    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $role  = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=? AND role!='admin'");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

/* ----------------------------------------------------------
   DELETE USER
---------------------------------------------------------- */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: manage_users.php");
    exit;
}

/* ----------------------------------------------------------
   GET ALL STUDENTS + ALUMNI
---------------------------------------------------------- */
$result = $conn->query("SELECT * FROM users WHERE role IN ('student','alumni') ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>

<style>
    body { font-family: Arial; background: #f0fdfa; padding: 20px; }
    h1 { color:#0d9488; margin-bottom:15px; }

    table { width: 100%; border-collapse: collapse; margin-top:20px; }
    th, td { padding: 12px; border: 1px solid #ccc; }
    th { background:#14b8a6;color:white; }

    .btn {
        padding: 8px 14px;
        border-radius: 6px;
        background:#0d9488;
        color:white;
        text-decoration:none;
        cursor:pointer;
        border:none;
    }
    .btn:hover { background:#0f766e; }

    /* Popup Modal */
    .modal {
        display:none;
        position:fixed;
        top:0; left:0;
        width:100%; height:100%;
        background:rgba(0,0,0,0.6);
        justify-content:center;
        align-items:center;
    }
    .modal-content {
        background:white;
        padding:20px;
        border-radius:10px;
        width:350px;
    }
    .close { float:right; cursor:pointer; font-size:20px; }
</style>

<script>
function openEditModal(id, name, email, role) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_name").value = name;
    document.getElementById("edit_email").value = email;
    document.getElementById("edit_role").value = role;
    document.getElementById("editModal").style.display = "flex";
}
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
}
</script>

</head>
<body>

<h1>Manage Users</h1>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= ucfirst($row['role']) ?></td>

        <td>
            <button class="btn"
                onclick="openEditModal('<?= $row['id'] ?>','<?= htmlspecialchars($row['name']) ?>','<?= htmlspecialchars($row['email']) ?>','<?= $row['role'] ?>')">
                Edit
            </button>

            <a class="btn" 
               href="manage_users.php?delete=<?= $row['id'] ?>" 
               onclick="return confirm('Delete this user?');">
               Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- EDIT USER MODAL -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">×</span>
        <h3>Edit User</h3>

        <form method="post">
            <input type="hidden" id="edit_id" name="id">

            <label>Name:</label><br>
            <input type="text" id="edit_name" name="name" required><br><br>

            <label>Email:</label><br>
            <input type="email" id="edit_email" name="email" required><br><br>

            <label>Role:</label><br>
            <select name="role" id="edit_role">
                <option value="student">Student</option>
                <option value="alumni">Alumni</option>
            </select><br><br>

            <button type="submit" class="btn" name="update_user">Update User</button>
        </form>
    </div>
</div>

</body>
</html>
