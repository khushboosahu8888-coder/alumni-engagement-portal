<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only admin access
if (!is_logged_in() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Initialize variables
$edit_mode = false;
$edit_event = null;

// HANDLE CREATE EVENT
if (isset($_POST['create_event'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];

    // If editing, run UPDATE
    if (isset($_POST['event_id']) && !empty($_POST['event_id'])) {
        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $description, $event_date, $event_time, $location, $_POST['event_id']);
        $stmt->execute();
    } else {
        // Create new event
        $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, event_time, location) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $event_date, $event_time, $location);
        $stmt->execute();
    }
}

// HANDLE DELETE EVENT
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: events.php"); // redirect to avoid resubmission
    exit;
}

// HANDLE EDIT EVENT
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_mode = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_event = $result->fetch_assoc();
}

// Fetch all events
$result = $conn->query("
    SELECT
        e.*,
        COUNT(
            CASE
                WHEN er.status = 'registered'
                THEN er.id
            END
        ) AS participant_count

    FROM events e

    LEFT JOIN event_registrations er
        ON e.id = er.event_id

    GROUP BY e.id

    ORDER BY e.event_date DESC
");

$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Events</title>
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0fdfa; color: #0f172a; margin: 0; padding: 0; }
    .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
    h1 { color: #0d9488; font-size: 32px; margin-bottom: 20px; }

    /* Form */
    .event-form { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(20, 184, 166, 0.15); margin-bottom: 40px; }
    .event-form input, .event-form textarea { width: 100%; padding: 12px 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 15px; }
    .event-form button { padding: 12px 28px; background: #14b8a6; color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
    .event-form button:hover { background: #0d9488; transform: translateY(-2px); }

    /* Table */
    table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(20, 184, 166, 0.15); }
    table th, table td { padding: 15px 20px; text-align: left; }
    table th { background: #14b8a6; color: white; }
    table tr:nth-child(even) { background: #f0fdfa; }
    table tr:hover { background: #ccfbf1; }
    .action-links a { margin-right: 10px; color: #0d9488; font-weight: 600; text-decoration: none; }
    .action-links a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="container">
    <h1><?= $edit_mode ? "Edit Event" : "New Event" ?></h1>

    <!-- CREATE/EDIT FORM -->
    <div class="event-form">
        <form method="post">
            <input type="hidden" name="event_id" value="<?= $edit_mode ? $edit_event['id'] : '' ?>">
            <input type="text" name="title" placeholder="Event Title" required value="<?= $edit_mode ? htmlspecialchars($edit_event['title']) : '' ?>">
            <textarea name="description" rows="4" placeholder="Event Description"><?= $edit_mode ? htmlspecialchars($edit_event['description']) : '' ?></textarea>
            <input type="date" name="event_date" required value="<?= $edit_mode ? $edit_event['event_date'] : '' ?>">
            <input type="time" name="event_time" required value="<?= $edit_mode ? $edit_event['event_time'] : '' ?>">
            <input type="text" name="location" placeholder="Location" value="<?= $edit_mode ? htmlspecialchars($edit_event['location']) : '' ?>">
            <button type="submit" name="create_event"><?= $edit_mode ? "Update Event" : "Create Event" ?></button>
        </form>
    </div>
      
    <h3> --Manage Events-- </h3>

    <!-- EVENTS TABLE -->
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Participants</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['description']) ?></td>
                <td><?= $event['event_date'] ?></td>
                <td><?= $event['event_time'] ?></td>
                <td><?= htmlspecialchars($event['location']) ?></td>

                <td>
                    <strong>
                            <?= (int)$event['participant_count'] ?>
                    </strong>
                </td>

                <td class="action-links">
                    <a href="events.php?action=edit&id=<?= $event['id'] ?>">
    Edit
</a>

<a href="event_participants.php?event_id=<?= $event['id'] ?>">
    Participants
</a>

<a href="events.php?action=delete&id=<?= $event['id'] ?>"
   onclick="return confirm('Delete this event?')">
    Delete
</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
