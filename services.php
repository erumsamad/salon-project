<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$salon_id = intval($_GET['salon_id'] ?? 0);

if ($salon_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid salon_id"
    ]);

    exit;
}

$stmt = $conn->prepare("
    SELECT
        id,
        salon_id,
        service_name,
        price,
        duration,
        description,
        status,
        created_at
    FROM services
    WHERE salon_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $salon_id);

$stmt->execute();

$result = $stmt->get_result();

$services = [];

while ($row = $result->fetch_assoc()) {

    $services[] = $row;

}

echo json_encode([
    "success" => true,
    "services" => $services
]);

$stmt->close();
$conn->close();

?>
