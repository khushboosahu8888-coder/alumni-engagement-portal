<?php
session_start();
require_once "includes/db.php";
require_once "includes/helpers.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

/* ---------------------------------------------------
   SEARCH & SORT
--------------------------------------------------- */
$search = $_GET['search'] ?? "";
$sort = $_GET['sort'] ?? "users.id DESC";

$allowed_sorts = [
    "id_asc"   => "users.id ASC",
    "id_desc"  => "users.id DESC",
    "name_asc" => "users.name ASC",
    "name_desc"=> "users.name DESC",
    "year_asc" => "student_profile.study_year ASC",
    "year_desc"=> "student_profile.study_year DESC",
    "created_asc" => "users.created_at ASC",
    "created_desc"=> "users.created_at DESC"
];

$order_by = $allowed_sorts[$sort] ?? "users.id DESC";

/* ---------------------------------------------------
   MAIN QUERY
--------------------------------------------------- */
$query = "
    SELECT 
        users.id,
        users.name,
        users.email,
        users.created_at AS account_created,
        users.updated_at AS account_updated,
        student_profile.study_year,
        student_profile.course,
        student_profile.updated_at AS profile_updated
    FROM users
    LEFT JOIN student_profile 
        ON users.id = student_profile.user_id
    WHERE users.role = 'student'
      AND (
            users.id LIKE ? OR
            users.name LIKE ? OR
            users.email LIKE ? OR
            student_profile.course LIKE ? OR
            student_profile.study_year LIKE ?
          )
    ORDER BY $order_by
";

$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Students List</title>

<style>
body {
    font-family: Arial;
    background:#eff6ff;
    padding:20px;
}
h1 {
    color:#1d4ed8;
    margin-bottom:15px;
    text-align:center;
}

/* TABLE */
table {
    width:100%;
    border-collapse: collapse;
    margin-top:20px;
    background:white;
}
th, td {
    padding:10px;
    border:1px solid #000;
    text-align:left;
}
th {
    background:#1d4ed8;
    color:white;
}

/* SEARCH + BUTTONS */
.search-box {
    margin-bottom: 15px;
}
.search-box input,
.sort-select {
    padding:8px;
    border-radius:5px;
    border:1px solid #1d4ed8;
}
.search-btn,
.print-btn {
    padding:8px 15px;
    background:#1d4ed8;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
.search-btn:hover,
.print-btn:hover {
    background:#1e40af;
}

/* PRINT SETTINGS */
@media print {
    .search-box,
    .print-btn {
        display: none;
    }
    body {
        background:white;
    }
}
</style>
</head>

<body>

<h1>📘 Students List Report</h1>

<!-- Search + Sort + PDF -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search students..."
           value="<?= htmlspecialchars($search) ?>">

    <select name="sort" class="sort-select">
        <option value="id_desc">Latest ID</option>
        <option value="id_asc" <?= ($sort=="id_asc"?"selected":"") ?>>ID Asc</option>
        <option value="name_asc" <?= ($sort=="name_asc"?"selected":"") ?>>Name A-Z</option>
        <option value="name_desc" <?= ($sort=="name_desc"?"selected":"") ?>>Name Z-A</option>
        <option value="year_asc" <?= ($sort=="year_asc"?"selected":"") ?>>Study Year ↑</option>
        <option value="year_desc" <?= ($sort=="year_desc"?"selected":"") ?>>Study Year ↓</option>
    </select>

    <button class="search-btn">Search</button>
    <button type="button" class="print-btn" onclick="window.print()">🖨 Download PDF</button>
</form>

<table>
<tr>
    <th>ID</th>
    <th>Name & Email</th>
    <th>Study Year</th>
    <th>Course</th>
    <th>Created</th>
    <th>Updated</th>
</tr>

<?php while ($row = $result->fetch_assoc()) : ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td>
        <strong><?= htmlspecialchars($row['name']) ?></strong><br>
        <?= htmlspecialchars($row['email']) ?>
    </td>
    <td><?= $row['study_year'] ?: "-" ?></td>
    <td><?= $row['course'] ?: "-" ?></td>
    <td><?= $row['account_created'] ?></td>
    <td><?= $row['profile_updated'] ?: $row['account_updated'] ?></td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
