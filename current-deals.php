<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


/*
|--------------------------------------------------------------------------
| OPTIONS
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

    echo json_encode([
        "success" => true
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Only GET allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    echo json_encode([
        "success" => false,
        "message" => "Only GET request is allowed"
    ]);

    exit;
}


include("database.php");


/*
|--------------------------------------------------------------------------
| Salon ID
|--------------------------------------------------------------------------
*/

$salon_id =
    intval($_GET["salon_id"] ?? 0);


if ($salon_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid salon ID"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get current active deals
|--------------------------------------------------------------------------
|
| A deal is shown only when:
|
| status = active
|
| AND valid_from is empty OR today >= valid_from
|
| AND valid_until is empty OR today <= valid_until
|
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        title,
        description,
        price,
        currency,
        image_url,
        valid_from,
        valid_until
    FROM salon_deals
    WHERE salon_id = ?
      AND status = 'active'
      AND (
            valid_from IS NULL
            OR valid_from = '0000-00-00'
            OR valid_from <= CURDATE()
          )
      AND (
            valid_until IS NULL
            OR valid_until = '0000-00-00'
            OR valid_until >= CURDATE()
          )
    ORDER BY created_at DESC
");


$stmt->bind_param(
    "i",
    $salon_id
);


$stmt->execute();


$result =
    $stmt->get_result();


$deals = [];


while ($row = $result->fetch_assoc()) {

    $deals[] = [

        "id" =>
            intval($row["id"]),

        "title" =>
            $row["title"],

        "description" =>
            $row["description"],

        "price" =>
            $row["price"] !== null
                ? (float) $row["price"]
                : null,

        "currency" =>
            $row["currency"] ?? "PKR",

        "image_url" =>
            $row["image_url"] ?? "",

        "valid_from" =>
            $row["valid_from"],

        "valid_until" =>
            $row["valid_until"]

    ];

}


echo json_encode([

    "success" => true,

    "count" =>
        count($deals),

    "deals" =>
        $deals

]);


$stmt->close();

$conn->close();

?>
