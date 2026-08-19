<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$service_id = intval($_POST['service_id'] ?? 0);
$salon_id = intval($_POST['salon_id'] ?? 0);

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


/*
|--------------------------------------------------------------------------
| Delete only this salon's service
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM services
    WHERE id = ? AND salon_id = ?
");

$stmt->bind_param(
    "ii",
    $service_id,
    $salon_id
);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "message" => "Service deleted successfully"
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Service not found"
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete service"
    ]);

}


$stmt->close();
$conn->close();

?>
