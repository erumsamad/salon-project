<?php

header("Content-Type:application/json");

include "../database.php";


$phone=$_GET['phone'];


$sql="SELECT * FROM customers
WHERE phone='$phone'";


$result=$conn->query($sql);


if($result->num_rows>0){

$row=$result->fetch_assoc();


echo json_encode([

"exists"=>true,

"name"=>$row['customer_name']

]);

}

else{

echo json_encode([

"exists"=>false

]);

}


$conn->close();

?>