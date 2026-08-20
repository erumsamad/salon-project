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
        salon_name,
        owner_name,
        phone,
        email,
        address,
        opening_time,
        closing_time,
        status,
        created_at
    FROM salons
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $salon_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Salon not found"
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

$salon = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "salon" => $salon
]);

$stmt->close();
$conn->close();

?>
