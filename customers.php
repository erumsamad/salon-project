<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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
        customer_name,
        phone,
        email,
        gender,
        date_of_birth,
        last_visit,
        created_at,
        total_visits,
        preferred_service,
        preferred_time
    FROM customers
    WHERE salon_id = ?
    ORDER BY id DESC
");

$stmt->bind_param("i", $salon_id);

$stmt->execute();

$result = $stmt->get_result();

$customers = [];

while ($row = $result->fetch_assoc()) {

    $customers[] = $row;
}

echo json_encode([
    "success" => true,
    "customers" => $customers
]);

$stmt->close();
$conn->close();

?>
