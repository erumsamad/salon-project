<?php

header("Content-Type: application/json");

include("database.php");

$phone = $_GET['phone'] ?? '';

if ($phone === '') {
    echo json_encode([
        "success" => false,
        "error" => "Phone is missing"
    ]);
    exit;
}

$sql = "SELECT current_step
        FROM customer_states
        WHERE phone='$phone'
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    $row = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "state" => $row['current_step']
    ]);

} else {

    echo json_encode([
        "success" => false
    ]);
}

$conn->close();

?>
