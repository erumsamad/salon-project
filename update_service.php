<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$service_id = intval($_POST['service_id'] ?? 0);
$salon_id = intval($_POST['salon_id'] ?? 0);

$service_name = trim($_POST['service_name'] ?? '');
$price = $_POST['price'] ?? '';
$duration = intval($_POST['duration'] ?? 0);
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'active';


if ($service_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid service_id"
    ]);

    exit;
}


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
| Update only this salon's service
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE services
    SET
        service_name = ?,
        price = ?,
        duration = ?,
        description = ?,
        status = ?
    WHERE
        id = ?
        AND salon_id = ?
");

$stmt->bind_param(
    "sdissii",
    $service_name,
    $price,
    $duration,
    $description,
    $status,
    $service_id,
    $salon_id
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Service updated successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update service"
    ]);

}


$stmt->close();
$conn->close();

?>
