<?php

session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only students and alumni
if (
    !is_logged_in() ||
    !in_array($_SESSION['role'], ['student', 'alumni'])
) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        e.id,
        e.title,
        e.description,
        e.event_date,
        e.event_time,
        e.location,
        er.status,
        er.registered_at

    FROM event_registrations er

    INNER JOIN events e
        ON er.event_id = e.id

    WHERE er.user_id = ?
      AND er.status = 'registered'

    ORDER BY e.event_date ASC, e.event_time ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$events = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>My Events</title>

<style>

body {
    font-family: Poppins, sans-serif;
    background: #f0fdfa;
    padding: 20px;
    margin: 0;
}

h1 {
    color: #0d9488;
}

.event-card {
    background: white;
    padding: 20px;
    margin-bottom: 18px;
    border-radius: 12px;
    border-left: 5px solid #14b8a6;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.event-card h2 {
    color: #0d9488;
}

.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 9px 15px;
    background: #0d9488;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}

.status {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 13px;
}

</style>

</head>

<body>

<a href="view_events.php" class="back-btn">
    ← Back to Events
</a>

<h1>My Registered Events</h1>

<?php if ($events->num_rows === 0): ?>

    <p>
        You have not registered for any upcoming events yet.
    </p>

<?php else: ?>

    <?php while ($event = $events->fetch_assoc()): ?>

        <div class="event-card">

            <h2>
                <?= htmlspecialchars($event['title']) ?>
            </h2>

            <p>
                <?= nl2br(
                    htmlspecialchars($event['description'])
                ) ?>
            </p>

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

            <p>
                <strong>Registration Status:</strong>

                <span class="status">
                    ✓ Registered
                </span>
            </p>

            <p style="font-size:13px;color:#64748b;">
                Registered on:
                <?= htmlspecialchars($event['registered_at']) ?>
            </p>

        </div>

    <?php endwhile; ?>

<?php endif; ?>

</body>

</html>