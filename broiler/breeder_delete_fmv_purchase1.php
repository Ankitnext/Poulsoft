<?php
//breeder_delete_fmv_purchase1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['generalpurchase2'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `broiler_purchases` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:breeder_display_fmv_purchase1.php?ccid='.$ccid); }
}

 ?>
