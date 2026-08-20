<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$salon_id = intval($_POST['salon_id'] ?? 0);

$salon_name = trim($_POST['salon_name'] ?? '');
$owner_name = trim($_POST['owner_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');
$opening_time = trim($_POST['opening_time'] ?? '');
$closing_time = trim($_POST['closing_time'] ?? '');
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

if ($salon_name === '') {

    echo json_encode([
        "success" => false,
        "message" => "Salon name is required"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Update salon
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE salons
    SET
        salon_name = ?,
        owner_name = ?,
        phone = ?,
        email = ?,
        address = ?,
        opening_time = ?,
        closing_time = ?,
        status = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssssi",
    $salon_name,
    $owner_name,
    $phone,
    $email,
    $address,
    $opening_time,
    $closing_time,
    $status,
    $salon_id
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Salon updated successfully"
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update salon"
    ]);

}


$stmt->close();
$conn->close();

?>
