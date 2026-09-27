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
    "batch_asc"=> "alumni_profile.batch_year ASC",
    "batch_desc"=> "alumni_profile.batch_year DESC",
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
        alumni_profile.batch_year,
        alumni_profile.company,
        alumni_profile.position,
        alumni_profile.bio,
        alumni_profile.updated_at AS profile_updated
    FROM users
    LEFT JOIN alumni_profile 
        ON users.id = alumni_profile.user_id
    WHERE users.role = 'alumni'
      AND (
            users.id LIKE ? OR
            users.name LIKE ? OR
            users.email LIKE ? OR
            alumni_profile.company LIKE ? OR
            alumni_profile.batch_year LIKE ? OR
            alumni_profile.position LIKE ? 
          )
    ORDER BY $order_by
";

$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("ssssss", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Alumni List</title>

<style>
body {
    font-family: Arial;
    background:#ecfeff;
    padding:20px;
}
h1 {
    color:#0d9488;
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
    vertical-align: top;
}
th {
    background:#14b8a6;
    color:white;
}

/* SEARCH */
.search-box {
    margin-bottom: 15px;
}
.search-box input,
.sort-select {
    padding:8px;
    border-radius:5px;
    border:1px solid #14b8a6;
}
.search-btn,
.print-btn {
    padding:8px 15px;
    background:#0d9488;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
.search-btn:hover,
.print-btn:hover {
    background:#0f766e;
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

<h1>📋 Alumni List Report</h1>

<!-- Search + Sort -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search alumni..." value="<?= htmlspecialchars($search) ?>">

    <select name="sort" class="sort-select">
        <option value="id_desc">Latest ID</option>
        <option value="id_asc" <?= ($sort=="id_asc"?"selected":"") ?>>ID Asc</option>
        <option value="name_asc" <?= ($sort=="name_asc"?"selected":"") ?>>Name A-Z</option>
        <option value="name_desc" <?= ($sort=="name_desc"?"selected":"") ?>>Name Z-A</option>
        <option value="batch_asc" <?= ($sort=="batch_asc"?"selected":"") ?>>Batch ↑</option>
        <option value="batch_desc" <?= ($sort=="batch_desc"?"selected":"") ?>>Batch ↓</option>
    </select>

    <button class="search-btn">Search</button>
    <button type="button" class="print-btn" onclick="window.print()">🖨 Download PDF</button>
</form>

<table>
<tr>
    <th>ID</th>
    <th>Name & Email</th>
    <th>Batch</th>
    <th>Company</th>
    <th>Position</th>
    <th>Bio</th>
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
    <td><?= $row['batch_year'] ?: "-" ?></td>
    <td><?= $row['company'] ?: "-" ?></td>
    <td><?= $row['position'] ?: "-" ?></td>
    <td><?= $row['bio'] ?: "-" ?></td>
    <td><?= $row['account_created'] ?></td>
    <td><?= $row['profile_updated'] ?: $row['account_updated'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
