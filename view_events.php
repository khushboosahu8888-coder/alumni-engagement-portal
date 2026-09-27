<?php

session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

// Allow only students and alumni
if (
    !is_logged_in() ||
    !in_array($_SESSION['role'], ['student', 'alumni'])
) {
    header("Location: login.php");
    exit;
}

// --------------------------------------------------
// FILTER VALUES
// --------------------------------------------------

$filter = $_GET['filter'] ?? "all";
$search = trim($_GET['search'] ?? "");

if (!in_array($filter, ['all', 'upcoming', 'past'])) {
    $filter = 'all';
}

$today = date("Y-m-d");

$user_id = $_SESSION['user_id'];

// --------------------------------------------------
// BUILD QUERY
// --------------------------------------------------

$query = "
    SELECT
        e.id,
        e.title,
        e.description,
        e.event_date,
        e.event_time,
        e.location,

        er.status AS registration_status

    FROM events e

    LEFT JOIN event_registrations er
        ON e.id = er.event_id
        AND er.user_id = ?

    WHERE 1=1
";

$types = "i";
$params = [$user_id];

// --------------------------------------------------
// UPCOMING FILTER
// --------------------------------------------------

if ($filter === "upcoming") {

    $query .= " AND e.event_date >= ?";

    $types .= "s";
    $params[] = $today;
}

// --------------------------------------------------
// PAST FILTER
// --------------------------------------------------

if ($filter === "past") {

    $query .= " AND e.event_date < ?";

    $types .= "s";
    $params[] = $today;
}

// --------------------------------------------------
// SEARCH
// --------------------------------------------------

if ($search !== "") {

    $query .= "
        AND (
            e.title LIKE ?
            OR e.location LIKE ?
            OR e.description LIKE ?
        )
    ";

    $searchTerm = "%" . $search . "%";

    $types .= "sss";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// --------------------------------------------------
// ORDER
// --------------------------------------------------

if ($filter === "past") {
    $query .= " ORDER BY e.event_date DESC, e.event_time DESC";
} else {
    $query .= " ORDER BY e.event_date ASC, e.event_time ASC";
}

// --------------------------------------------------
// PREPARE QUERY
// --------------------------------------------------

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Database query preparation failed.");
}

// Dynamic bind_param
$stmt->bind_param($types, ...$params);

$stmt->execute();

$events = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>View Events</title>

<style>

body {
    font-family: Poppins, sans-serif;
    background: #f0fdfa;
    padding: 20px;
    margin: 0;
}

h1 {
    color: #0d9488;
    margin-bottom: 20px;
}

.filter-box {
    background: white;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #14b8a6;
    margin-bottom: 20px;
}

.event-card {
    padding: 20px;
    background: white;
    border-radius: 12px;
    border-left: 5px solid #14b8a6;
    margin-bottom: 18px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.event-card:hover {
    transform: scale(1.01);
    transition: 0.2s;
}

input,
select {
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #0d9488;
}

button,
.action-btn {
    padding: 9px 15px;
    background: #0d9488;
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

button:hover,
.action-btn:hover {
    background: #14b8a6;
}

.cancel-btn {
    background: #dc2626;
}

.cancel-btn:hover {
    background: #b91c1c;
}

.registered-btn {
    background: #16a34a;
    cursor: default;
}

.completed-btn {
    background: #64748b;
    cursor: default;
}

.message {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.success {
    background: #dcfce7;
    color: #166534;
}

.error {
    background: #fee2e2;
    color: #991b1b;
}

.event-actions {
    margin-top: 18px;
}

</style>

</head>

<body>

<h1>Events</h1>

<!-- --------------------------------------------------
     SUCCESS / ERROR MESSAGES
--------------------------------------------------- -->

<?php if (isset($_GET['success'])): ?>

    <?php if ($_GET['success'] === 'registered'): ?>

        <div class="message success">
            ✅ You have successfully registered for this event.
        </div>

    <?php elseif ($_GET['success'] === 'cancelled'): ?>

        <div class="message success">
            ✅ Your event registration has been cancelled.
        </div>

    <?php endif; ?>

<?php endif; ?>


<?php if (isset($_GET['error'])): ?>

    <div class="message error">

        <?php

        switch ($_GET['error']) {

            case 'already_registered':
                echo "You are already registered for this event.";
                break;

            case 'event_completed':
                echo "Registration is closed because this event has already taken place.";
                break;

            case 'event_not_found':
                echo "Event not found.";
                break;

            case 'not_registered':
                echo "You are not registered for this event.";
                break;

            case 'registration_failed':
                echo "Registration failed. Please try again.";
                break;

            case 'invalid_event':
                echo "Invalid event.";
                break;

            default:
                echo "Something went wrong.";
        }

        ?>

    </div>

<?php endif; ?>


<!-- --------------------------------------------------
     FILTER BOX
--------------------------------------------------- -->

<div class="filter-box">

<form method="GET"
      style="display:flex; flex-wrap:wrap; gap:10px;">

    <input
        type="text"
        name="search"
        placeholder="Search by title/location/description"
        value="<?= htmlspecialchars($search) ?>"
    >

    <select name="filter">

        <option
            value="all"
            <?= $filter === "all" ? "selected" : "" ?>
        >
            All Events
        </option>

        <option
            value="upcoming"
            <?= $filter === "upcoming" ? "selected" : "" ?>
        >
            Upcoming Only
        </option>

        <option
            value="past"
            <?= $filter === "past" ? "selected" : "" ?>
        >
            Past Events
        </option>

    </select>

    <button type="submit">
        Apply
    </button>

</form>

</div>


<!-- --------------------------------------------------
     EVENTS
--------------------------------------------------- -->

<?php if ($events->num_rows === 0): ?>

    <p style="color:#555;">
        No events found.
    </p>

<?php else: ?>

    <?php while ($e = $events->fetch_assoc()): ?>

        <div class="event-card">

            <h2 style="color:#0d9488; margin-bottom:5px;">

                <?= htmlspecialchars($e['title']) ?>

            </h2>


            <p>

                <?= nl2br(
                    htmlspecialchars($e['description'])
                ) ?>

            </p>


            <p>
                <strong>Date:</strong>

                <?= htmlspecialchars($e['event_date']) ?>

            </p>


            <p>
                <strong>Time:</strong>

                <?= htmlspecialchars($e['event_time']) ?>

            </p>


            <p>
                <strong>Location:</strong>

                <?= htmlspecialchars($e['location']) ?>

            </p>


            <!-- -----------------------------------------
                 REGISTRATION BUTTON
            ------------------------------------------ -->

            <div class="event-actions">

            <?php

            $event_datetime =
                $e['event_date'] . ' ' . $e['event_time'];

            $event_completed =
                strtotime($event_datetime) <= time();

            ?>


            <?php if ($event_completed): ?>

                <button
                    type="button"
                    class="completed-btn"
                    disabled
                >
                    Event Completed
                </button>


            <?php elseif ($e['registration_status'] === 'registered'): ?>

                <button
                    type="button"
                    class="registered-btn"
                    disabled
                >
                    ✓ Registered
                </button>


                <form
                    method="POST"
                    action="cancel_registration.php"
                    style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to cancel your registration?');"
                >

                    <input
                        type="hidden"
                        name="event_id"
                        value="<?= (int)$e['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrf_token()) ?>"
                    >

                    <button
                        type="submit"
                        class="cancel-btn"
                    >
                        Cancel Registration
                    </button>

                </form>


            <?php else: ?>

                <form
                    method="POST"
                    action="register_event.php"
                    style="display:inline;"
                >

                    <input
                        type="hidden"
                        name="event_id"
                        value="<?= (int)$e['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(csrf_token()) ?>"
                    >

                    <button type="submit">
                        Register Now
                    </button>

                </form>

            <?php endif; ?>

            </div>

        </div>

    <?php endwhile; ?>

<?php endif; ?>

</body>

</html>