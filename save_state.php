<?php

header("Content-Type: application/json");

include("database.php");

$phone = $_GET['phone'] ?? '';
$step  = $_GET['step'] ?? '';

if ($phone === '' || $step === '') {
    echo json_encode([
        "success" => false,
        "error" => "Phone or step is missing"
    ]);
    exit;
}

$sql = "INSERT INTO customer_states (phone, current_step)
        VALUES ('$phone', '$step')
        ON DUPLICATE KEY UPDATE
        current_step = '$step'";

if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "phone" => $phone,
        "state" => $step
    ]);

} else {

    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
}

$conn->close();

?>
