<?php
//chicken_delete_vehfuel_fills.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['vehfuel_fills'];

$utype = $_GET['page'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `vehicle_fuelfilling` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_vehfuel_fills.php?ccid='.$ccid); }
}

?>
