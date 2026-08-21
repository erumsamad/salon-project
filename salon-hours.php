<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");


/*
|--------------------------------------------------------------------------
| Input
|--------------------------------------------------------------------------
*/

$salon_id = intval($_GET["salon_id"] ?? 0);


/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

if ($salon_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid salon ID"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get Weekly Hours
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        salon_id,
        day_of_week,
        is_open,
        opening_time,
        closing_time,
        created_at,
        updated_at
    FROM salon_hours
    WHERE salon_id = ?
    ORDER BY day_of_week ASC
");

$stmt->bind_param(
    "i",
    $salon_id
);

$stmt->execute();

$result = $stmt->get_result();

$hours = [];


while ($row = $result->fetch_assoc()) {

    $hours[] = $row;

}


/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "hours" => $hours

]);


$stmt->close();

$conn->close();

?>
