<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

header("Content-Type:application/json");

//include "../database.php";
include("database.php");

$phone=$_GET['phone'];


$sql="SELECT * FROM customer_states
WHERE phone='$phone'";


$result=$conn->query($sql);


if($result->num_rows>0){

$row=$result->fetch_assoc();


echo json_encode([

"success"=>true,

"state"=>$row['current_step']

]);

}


else{

echo json_encode([

"success"=>false

]);

}


$conn->close();

?>
