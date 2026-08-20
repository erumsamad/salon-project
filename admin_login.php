<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {

    echo json_encode([
        "success" => false,
        "message" => "Email and password are required"
    ]);

    exit;
}


$stmt = $conn->prepare("
    SELECT
        id,
        salon_id,
        name,
        email,
        password,
        status
    FROM admin_users
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


$user = $result->fetch_assoc();


if (($user["status"] ?? "active") !== "active") {

    echo json_encode([
        "success" => false,
        "message" => "Account is inactive"
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| Verify password
|--------------------------------------------------------------------------
*/

if (!password_verify($password, $user["password"])) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);

    $stmt->close();
    $conn->close();

    exit;
}


echo json_encode([
    "success" => true,
    "admin" => [
        "id" => $user["id"],
        "salon_id" => $user["salon_id"],
        "name" => $user["name"],
        "email" => $user["email"]
    ]
]);


$stmt->close();
$conn->close();

?>
