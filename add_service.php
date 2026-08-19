<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$salon_id = intval($_POST['salon_id'] ?? 0);
$service_name = trim($_POST['service_name'] ?? '');
$price = $_POST['price'] ?? '';
$duration = intval($_POST['duration'] ?? 0);
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'active';


/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

if ($salon_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid salon_id"
    ]);

    exit;
}


if ($service_name === '') {

    echo json_encode([
        "success" => false,
        "message" => "Service name is required"
    ]);

    exit;
}


if ($price === '' || !is_numeric($price)) {

    echo json_encode([
        "success" => false,
        "message" => "Valid price is required"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Insert service
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO services
    (
        salon_id,
        service_name,
        price,
        duration,
        description,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isdiss",
    $salon_id,
    $service_name,
    $price,
    $duration,
    $description,
    $status
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Service added successfully",
        "service_id" => $stmt->insert_id
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to add service"
    ]);
}

$stmt->close();
$conn->close();

?>
