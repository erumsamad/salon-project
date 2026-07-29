<?php

header("Content-Type:application/json");

//include "../database.php";
include("database.php");

//$phone=$_POST['phone'];

//$step=$_POST['step'];

$phone = $_GET['phone'] ?? '';
$step = $_GET['step'] ?? '';

$sql="REPLACE INTO customer_states
(phone,current_step)

VALUES

('$phone','$step')";


if($conn->query($sql)){

echo json_encode([

"success"=>true

]);

}

else{

echo json_encode([

"success"=>false

]);

}


$conn->close();

?>
