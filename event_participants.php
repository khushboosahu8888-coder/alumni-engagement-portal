<?php

session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only admin
if (
    !is_logged_in() ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

$event_id = filter_input(
    INPUT_GET,
    'event_id',
    FILTER_VALIDATE_INT
);

if (!$event_id) {
    header("Location: events.php");
    exit;
}

// --------------------------------------------------
// Get event
// --------------------------------------------------

$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE id = ?
");

$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();

$event = $result->fetch_assoc();

$stmt->close();

if (!$event) {
    die("Event not found.");
}

// --------------------------------------------------
// Get participants
// --------------------------------------------------

$stmt = $conn->prepare("
    SELECT
        u.id,
        u.name,
        u.email,
        u.role,
        er.status,
        er.registered_at

    FROM event_registrations er

    INNER JOIN users u
        ON er.user_id = u.id

    WHERE er.event_id = ?
      AND er.status IN ('registered', 'attended')

    ORDER BY er.registered_at ASC
");

$stmt->bind_param("i", $event_id);
$stmt->execute();

$participants = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Event Participants</title>

<style>

body {
    font-family: 'Segoe UI', sans-serif;
    background: #f0fdfa;
    margin: 0;
    padding: 30px;
}

.container {
    max-width: 1100px;
    margin: auto;
}

h1 {
    color: #0d9488;
}

.event-info {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

th,
td {
    padding: 14px 16px;
    text-align: left;
}

th {
    background: #14b8a6;
    color: white;
}

tr:nth-child(even) {
    background: #f0fdfa;
}

.back-btn {
    display: inline-block;
    padding: 9px 15px;
    background: #0d9488;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    margin-bottom: 20px;
}

</style>

</head>

<body>

<div class="container">

<a href="events.php" class="back-btn">
    ← Back to Events
</a>

<h1>
    Event Participants
</h1>

<div class="event-info">

    <h2>
        <?= htmlspecialchars($event['title']) ?>
    </h2>

    <p>
        <strong>Date:</strong>
        <?= htmlspecialchars($event['event_date']) ?>
    </p>

    <p>
        <strong>Time:</strong>
        <?= htmlspecialchars($event['event_time']) ?>
    </p>

    <p>
        <strong>Location:</strong>
        <?= htmlspecialchars($event['location']) ?>
    </p>

</div>


<table>

<thead>

<tr>

    <th>#</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Registered At</th>

</tr>

</thead>

<tbody>

<?php if ($participants->num_rows === 0): ?>

<tr>

    <td colspan="6">
        No participants registered yet.
    </td>

</tr>

<?php else: ?>

<?php $count = 1; ?>

<?php while ($participant = $participants->fetch_assoc()): ?>

<tr>

    <td>
        <?= $count++ ?>
    </td>

    <td>
        <?= htmlspecialchars($participant['name']) ?>
    </td>

    <td>
        <?= htmlspecialchars($participant['email']) ?>
    </td>

    <td>
        <?= htmlspecialchars(ucfirst($participant['role'])) ?>
    </td>

    <td>
        <?= htmlspecialchars(ucfirst($participant['status'])) ?>
    </td>

    <td>
        <?= htmlspecialchars($participant['registered_at']) ?>
    </td>

</tr>

<?php endwhile; ?>

<?php endif; ?>

</tbody>

</table>

</div>

</body>

</html>