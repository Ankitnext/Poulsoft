<?php
//breeder_delete_unitmap1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['unitmap1'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `broiler_secunit_mapping` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:breeder_display_unitmap1.php?ccid='.$ccid); }
}

 ?>
