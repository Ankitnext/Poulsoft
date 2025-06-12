<?php
//chicken_delete_generalpurchase8.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['generalpurchase8'];

$utype = $_GET['utype'];
$id = $_GET['trnum'];

if($utype == "pause"){
    $sql = "UPDATE `pur_purchase` SET `active` = '0',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_generalpurchase8.php?ccid='.$cid); }
}
else if($utype == "activate"){
    $sql = "UPDATE `pur_purchase` SET `active` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_generalpurchase8.php?ccid='.$cid); }
}
else if($utype == "delete"){
    $sql = "UPDATE `pur_purchase` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_generalpurchase8.php?ccid='.$cid); }
}
else if($utype == "authorize"){
    $sql = "UPDATE `pur_purchase` SET `flag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_generalpurchase8.php?ccid='.$cid); }
}

?>
