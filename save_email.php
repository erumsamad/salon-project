<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

header("Content-Type:application/json");

include "../database.php";


$phone=$_POST['phone'];

$email=$_POST['email'];


$sql="UPDATE customers

SET email='$email'

WHERE phone='$phone'";


if($conn->query($sql)){

echo json_encode([

"success"=>true

]);

}


else{

echo json_encode([

"success"=>false,

"error"=>$conn->error

]);

}


$conn->close();

?>