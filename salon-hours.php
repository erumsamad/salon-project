<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

//header("Content-Type: application/json");

include("database.php");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {

    echo json_encode([
        "success" => true
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| GET — Fetch weekly hours
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


    echo json_encode([

        "success" => true,

        "hours" => $hours

    ]);


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| POST — Update weekly hours
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

    $day_of_week = intval(
        $input["day_of_week"] ?? 0
    );

    $is_open = intval(
        $input["is_open"] ?? 0
    );

    $opening_time =
        $input["opening_time"] ?? null;

    $closing_time =
        $input["closing_time"] ?? null;


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $salon_id <= 0 ||
        $day_of_week < 1 ||
        $day_of_week > 7
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid data"
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Closed day
    |--------------------------------------------------------------------------
    */

    if ($is_open === 0) {

        $opening_time = null;

        $closing_time = null;

    }


    /*
    |--------------------------------------------------------------------------
    | Insert / Update
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO salon_hours
        (
            salon_id,
            day_of_week,
            is_open,
            opening_time,
            closing_time
        )
        VALUES (?, ?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE

            is_open = VALUES(is_open),

            opening_time = VALUES(opening_time),

            closing_time = VALUES(closing_time)
    ");


    $stmt->bind_param(
        "iiiss",
        $salon_id,
        $day_of_week,
        $is_open,
        $opening_time,
        $closing_time
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Salon hours updated successfully"

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Unable to update salon hours"

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
