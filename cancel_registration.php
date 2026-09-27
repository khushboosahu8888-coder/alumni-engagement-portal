<?php

session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Only logged-in students and alumni
if (
    !is_logged_in() ||
    !in_array($_SESSION['role'], ['student', 'alumni'])
) {
    header("Location: login.php");
    exit;
}

// Only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_events.php");
    exit;
}

// CSRF protection
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

// Cancel only the current user's registration
$stmt = $conn->prepare(
    "UPDATE event_registrations
     SET status = 'cancelled'
     WHERE event_id = ?
     AND user_id = ?
     AND status = 'registered'"
);

$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: view_events.php?success=cancelled");
} else {
    header("Location: view_events.php?error=not_registered");
}

$stmt->close();
exit;
?>