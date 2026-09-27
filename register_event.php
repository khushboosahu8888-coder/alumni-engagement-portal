<?php

session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only logged-in students and alumni can register
if (
    !is_logged_in() ||
    !in_array($_SESSION['role'], ['student', 'alumni'])
) {
    header("Location: login.php");
    exit;
}

// Only accept POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_events.php");
    exit;
}

// Check CSRF token
if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
) {
    die("Invalid CSRF token.");
}

$event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

if (!$event_id) {
    header("Location: view_events.php?error=invalid_event");
    exit;
}

// --------------------------------------------------
// Check whether event exists
// --------------------------------------------------

$stmt = $conn->prepare(
    "SELECT id, event_date, event_time
     FROM events
     WHERE id = ?"
);

$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

$stmt->close();

if (!$event) {
    header("Location: view_events.php?error=event_not_found");
    exit;
}

// --------------------------------------------------
// Check whether event has already happened
// --------------------------------------------------

$event_datetime = $event['event_date'] . ' ' . $event['event_time'];

if (strtotime($event_datetime) <= time()) {
    header("Location: view_events.php?error=event_completed");
    exit;
}

// --------------------------------------------------
// Check existing registration
// --------------------------------------------------

$stmt = $conn->prepare(
    "SELECT id, status
     FROM event_registrations
     WHERE event_id = ?
     AND user_id = ?"
);

$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$registration = $result->fetch_assoc();

$stmt->close();

// --------------------------------------------------
// If already registered
// --------------------------------------------------

if ($registration && $registration['status'] === 'registered') {
    header("Location: view_events.php?error=already_registered");
    exit;
}

// --------------------------------------------------
// If previously cancelled → register again
// --------------------------------------------------

if ($registration && $registration['status'] === 'cancelled') {

    $stmt = $conn->prepare(
        "UPDATE event_registrations
         SET status = 'registered',
             registered_at = CURRENT_TIMESTAMP
         WHERE id = ?"
    );

    $stmt->bind_param("i", $registration['id']);
    $stmt->execute();

    $stmt->close();

    header("Location: view_events.php?success=registered");
    exit;
}

// --------------------------------------------------
// New registration
// --------------------------------------------------

$stmt = $conn->prepare(
    "INSERT INTO event_registrations
     (event_id, user_id, status)
     VALUES (?, ?, 'registered')"
);

$stmt->bind_param("ii", $event_id, $user_id);

if ($stmt->execute()) {
    $stmt->close();

    header("Location: view_events.php?success=registered");
    exit;
}

$stmt->close();

header("Location: view_events.php?error=registration_failed");
exit;
?>