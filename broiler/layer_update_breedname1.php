<?php
//layer_update_breedname1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['breedname1'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "pause"){
    $sql = "UPDATE `layer_breed_details` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:layer_display_breedname1.php?ccid='.$ccid); }
}
else if($utype == "activate"){
    $sql = "UPDATE `layer_breed_details` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:layer_display_breedname1.php?ccid='.$ccid); }
}
else if($utype == "authorize"){
    $sql = "UPDATE `layer_breed_details` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:layer_display_breedname1.php?ccid='.$ccid); }
}

?>
