<?php
//breeder_delete_standards1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['standards1'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `breeder_breed_standards` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:breeder_display_standards1.php?ccid='.$ccid); }
}

 ?>
