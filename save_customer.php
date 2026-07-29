<?php

header("Content-Type:application/json");

//include "../database.php";
include("database.php");

 $phone=$_POST['phone'];
//$phone = $_POST['phone'] ?? '';

$sql="INSERT INTO customers(phone)
VALUES('$phone')";


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
