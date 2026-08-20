<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$customer_id = intval($_GET["id"] ?? 0);
$salon_id = intval($_GET["salon_id"] ?? 0);

if ($customer_id <= 0 || $salon_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid customer ID or salon ID"
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
    WHERE id = ?
      AND salon_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $customer_id,
    $salon_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Customer not found"
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


$customer = $result->fetch_assoc();


echo json_encode([
    "success" => true,
    "customer" => $customer
]);


$stmt->close();
$conn->close();

?>
