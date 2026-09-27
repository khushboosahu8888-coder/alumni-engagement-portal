<?php
require_once __DIR__ . "/config.php";

$ports = [3307, 3306, 3308, 3309, 3310];  // Try all common ports

$conn = null;
$last_error = "";

foreach ($ports as $p) {

    $test = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, $p);

    if ($test) {
        $conn = $test; // Success!
        break;
    } else {
        $last_error = mysqli_connect_error();
    }
}

if (!$conn) {
    die("<b>FAILED:</b> MySQL not reachable on any port.<br>Error: " . $last_error);
}
?>
