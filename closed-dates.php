<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include("database.php");


/*
|--------------------------------------------------------------------------
| GET — Get closed dates
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
            closed_date,
            reason,
            created_at
        FROM salon_closed_dates
        WHERE salon_id = ?
        ORDER BY closed_date ASC
    ");

    $stmt->bind_param(
        "i",
        $salon_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $closed_dates = [];


    while ($row = $result->fetch_assoc()) {

        $closed_dates[] = $row;

    }


    echo json_encode([

        "success" => true,

        "closed_dates" => $closed_dates

    ]);


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| POST — Add closed date
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

    $closed_date =
        trim($input["closed_date"] ?? "");

    $reason =
        trim($input["reason"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $salon_id <= 0 ||
        empty($closed_date)
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Salon ID and closed date are required"
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Validate date
    |--------------------------------------------------------------------------
    */

    $date = DateTime::createFromFormat(
        "Y-m-d",
        $closed_date
    );


    if (
        !$date ||
        $date->format("Y-m-d") !== $closed_date
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid date format. Use YYYY-MM-DD"
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Add closed date
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO salon_closed_dates
        (
            salon_id,
            closed_date,
            reason
        )
        VALUES (?, ?, ?)
    ");


    $stmt->bind_param(
        "iss",
        $salon_id,
        $closed_date,
        $reason
    );


    if ($stmt->execute()) {

        echo json_encode([

            "success" => true,

            "message" =>
                "Closed date added successfully",

            "id" =>
                $stmt->insert_id

        ]);

    } else {

        /*
        | Duplicate date
        */

        if ($stmt->errno == 1062) {

            echo json_encode([

                "success" => false,

                "message" =>
                    "This date is already closed"

            ]);

        } else {

            echo json_encode([

                "success" => false,

                "message" =>
                    "Unable to add closed date"

            ]);

        }

    }


    $stmt->close();

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE — Remove closed date
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
            "message" => "Invalid salon ID or closed date ID"
        ]);

        exit;
    }


    $stmt = $conn->prepare("
        DELETE FROM salon_closed_dates
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
                "Closed date removed successfully"

        ]);

    } else {

        echo json_encode([

            "success" => false,

            "message" =>
                "Closed date not found"

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
