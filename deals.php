<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include("database.php");


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
| GET — Fetch deals
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $salon_id = intval($_GET["salon_id"] ?? 0);

    if ($salon_id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid salon ID"
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        SELECT
            id,
            salon_id,
            title,
            description,
            price,
            currency,
            image_url,
            valid_from,
            valid_until,
            status,
            created_at,
            updated_at
        FROM salon_deals
        WHERE salon_id = ?
        ORDER BY created_at DESC
    ");


    $stmt->bind_param(
        "i",
        $salon_id
    );


    $stmt->execute();

    $result = $stmt->get_result();

    $deals = [];


    while ($row = $result->fetch_assoc()) {

        $deals[] = $row;

    }


    echo json_encode([

        "success" => true,

        "deals" => $deals

    ]);


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| POST — Create deal
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );


    $salon_id = intval(
        $input["salon_id"] ?? 0
    );

    $title =
        trim($input["title"] ?? "");

    $description =
        trim($input["description"] ?? "");

    $price =
        $input["price"] ?? null;

    $currency =
        trim($input["currency"] ?? "PKR");

    $image_url =
        trim($input["image_url"] ?? "");

    $valid_from =
        $input["valid_from"] ?? null;

    $valid_until =
        $input["valid_until"] ?? null;

    $status =
        $input["status"] ?? "active";


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $salon_id <= 0 ||
        empty($title)
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Salon ID and deal title are required"
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Status validation
    |--------------------------------------------------------------------------
    */

    if (
        $status !== "active" &&
        $status !== "inactive"
    ) {

        $status = "active";

    }


    /*
    |--------------------------------------------------------------------------
    | Insert deal
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO salon_deals
        (
            salon_id,
            title,
            description,
            price,
            currency,
            image_url,
            valid_from,
            valid_until,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");


    $stmt->bind_param(
        "issdsssss",
        $salon_id,
        $title,
        $description,
        $price,
        $currency,
        $image_url,
        $valid_from,
        $valid_until,
        $status
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Deal created successfully",

            "id" =>
                $stmt->insert_id

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Unable to create deal"

        ]);

    }


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE — Delete deal
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );


    $salon_id = intval(
        $input["salon_id"] ?? 0
    );

    $id = intval(
        $input["id"] ?? 0
    );


    if (
        $salon_id <= 0 ||
        $id <= 0
    ) {

        echo json_encode([
            "success" => false,
            "message" =>
                "Invalid salon ID or deal ID"
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        DELETE FROM salon_deals
        WHERE id = ?
          AND salon_id = ?
    ");


    $stmt->bind_param(
        "ii",
        $id,
        $salon_id
    );


    $stmt->execute();


    if ($stmt->affected_rows > 0) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Deal deleted successfully"

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Deal not found"

        ]);

    }


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| Unsupported method
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => false,

    "message" =>
        "Unsupported request method"

]);

?>
