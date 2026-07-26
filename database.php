<?php

$servername="fdb1030.awardspace.net";

$username="4777716_aireceptionist";

$password="Digitonics_26";

$dbname="4777716_aireceptionist";


$conn=new mysqli(
$servername,
$username,
$password,
$dbname
);


if($conn->connect_error){

die("Connection Failed");

}

?>