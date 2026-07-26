<?php

$host = "fdb1030.awardspace.net";
$user = "4777716_aireceptionist";
$pass = "YOUR_PASSWORD";
$db   = "4777716_aireceptionist";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

echo "DB Connected";
