<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Not authenticated'
    ]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$current_role = $_SESSION['role'] ?? '';

function json_resp($data)
{
    echo json_encode($data);
    exit;
}

/*
|--------------------------------------------------------------------------
| Determine who the current user is allowed to chat with
|--------------------------------------------------------------------------
*/

if ($current_role === 'student') {
    $allowed_other_role = 'alumni';
} elseif ($current_role === 'alumni') {
    $allowed_other_role = 'student';
} else {
    json_resp([
        'success' => false,
        'error' => 'Chat is not available for this account'
    ]);
}

$action = $_REQUEST['action'] ?? '';

/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/

if ($action === 'send_message') {

    $other = isset($_POST['other_id'])
        ? (int) $_POST['other_id']
        : 0;

    $msg = trim($_POST['message'] ?? '');

    if ($other <= 0 || $msg === '') {
        json_resp([
            'success' => false,
            'error' => 'Invalid parameters'
        ]);
    }

    // Limit message length
    if (mb_strlen($msg) > 2000) {
        json_resp([
            'success' => false,
            'error' => 'Message is too long'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verify that the selected person has the correct opposite role
    |--------------------------------------------------------------------------
    */

    $check = $conn->prepare(
        "SELECT id, name
         FROM users
         WHERE id = ?
         AND role = ?
         LIMIT 1"
    );

    $check->bind_param(
        "is",
        $other,
        $allowed_other_role
    );

    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows === 0) {
        json_resp([
            'success' => false,
            'error' => 'Invalid chat participant'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Insert message
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare(
        "INSERT INTO messages
        (sender_id, receiver_id, message)
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iis",
        $user_id,
        $other,
        $msg
    );

    if ($stmt->execute()) {

        $message_id = $conn->insert_id;

        json_resp([
            'success' => true,
            'message_id' => $message_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);

    } else {

        json_resp([
            'success' => false,
            'error' => 'Message could not be sent'
        ]);
    }
}


/*
|--------------------------------------------------------------------------
| GET MESSAGES
|--------------------------------------------------------------------------
*/

if ($action === 'get_messages') {

    $other = isset($_GET['other_id'])
        ? (int) $_GET['other_id']
        : 0;

    if ($other <= 0) {
        json_resp([
            'success' => false,
            'error' => 'Invalid user'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verify participant
    |--------------------------------------------------------------------------
    */

    $check = $conn->prepare(
        "SELECT id
         FROM users
         WHERE id = ?
         AND role = ?
         LIMIT 1"
    );

    $check->bind_param(
        "is",
        $other,
        $allowed_other_role
    );

    $check->execute();

    if ($check->get_result()->num_rows === 0) {
        json_resp([
            'success' => false,
            'error' => 'Invalid chat participant'
        ]);
    }

    $since_id = isset($_GET['since_id'])
        ? (int) $_GET['since_id']
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Fetch messages
    |--------------------------------------------------------------------------
    */

    if ($since_id > 0) {

        $stmt = $conn->prepare(
            "SELECT
                id,
                sender_id,
                receiver_id,
                message,
                is_read,
                created_at
             FROM messages
             WHERE
                (
                    (sender_id = ? AND receiver_id = ?)
                    OR
                    (sender_id = ? AND receiver_id = ?)
                )
                AND id > ?
             ORDER BY id ASC"
        );

        $stmt->bind_param(
            "iiiii",
            $user_id,
            $other,
            $other,
            $user_id,
            $since_id
        );

    } else {

        $stmt = $conn->prepare(
            "SELECT
                id,
                sender_id,
                receiver_id,
                message,
                is_read,
                created_at
             FROM messages
             WHERE
                (sender_id = ? AND receiver_id = ?)
                OR
                (sender_id = ? AND receiver_id = ?)
             ORDER BY id ASC"
        );

        $stmt->bind_param(
            "iiii",
            $user_id,
            $other,
            $other,
            $user_id
        );
    }

    $stmt->execute();

    $res = $stmt->get_result();

    $messages = [];

    while ($row = $res->fetch_assoc()) {

        /*
        Do NOT htmlspecialchars() here.
        The frontend will safely insert the text using textContent.
        */

        $messages[] = $row;
    }

    /*
    |--------------------------------------------------------------------------
    | Mark received messages as read
    |--------------------------------------------------------------------------
    */

    $mark = $conn->prepare(
        "UPDATE messages
         SET is_read = 1
         WHERE receiver_id = ?
         AND sender_id = ?
         AND is_read = 0"
    );

    $mark->bind_param(
        "ii",
        $user_id,
        $other
    );

    $mark->execute();

    json_resp([
        'success' => true,
        'messages' => $messages
    ]);
}


/*
|--------------------------------------------------------------------------
| GET CONTACTS
|--------------------------------------------------------------------------
*/

if ($action === 'get_contacts') {

    $role = $_GET['role'] ?? '';

    if ($role !== $allowed_other_role) {

        json_resp([
            'success' => false,
            'error' => 'Invalid contact role'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get users + latest conversation message
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            u.id,
            u.name,
            u.email,

            (
                SELECT m.message
                FROM messages m
                WHERE
                    (m.sender_id = u.id AND m.receiver_id = ?)
                    OR
                    (m.sender_id = ? AND m.receiver_id = u.id)
                ORDER BY m.id DESC
                LIMIT 1
            ) AS last_message,

            (
                SELECT m.created_at
                FROM messages m
                WHERE
                    (m.sender_id = u.id AND m.receiver_id = ?)
                    OR
                    (m.sender_id = ? AND m.receiver_id = u.id)
                ORDER BY m.id DESC
                LIMIT 1
            ) AS last_time

        FROM users u

        WHERE u.role = ?

        ORDER BY
            CASE
                WHEN last_time IS NULL THEN 1
                ELSE 0
            END,
            last_time DESC,
            u.name ASC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiiss",
        $user_id,
        $user_id,
        $user_id,
        $user_id,
        $role
    );

    $stmt->execute();

    $res = $stmt->get_result();

    $contacts = [];

    while ($row = $res->fetch_assoc()) {

        $row['last_message'] = $row['last_message']
            ? mb_substr($row['last_message'], 0, 80)
            : '';

        $contacts[] = $row;
    }

    json_resp([
        'success' => true,
        'contacts' => $contacts
    ]);
}


/*
|--------------------------------------------------------------------------
| UNKNOWN ACTION
|--------------------------------------------------------------------------
*/

json_resp([
    'success' => false,
    'error' => 'Unknown action'
]);
?>