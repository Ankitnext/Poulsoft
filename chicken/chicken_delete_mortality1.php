<?php
//chicken_delete_mortality1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['mortality1'];

$utype = $_GET['page'];
$id = $_GET['id'];

if($utype == "pause"){
    $sql = "UPDATE `item_closingstock` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_mortality1.php?ccid='.$cid); }
}
else if($utype == "activate"){
    $sql = "UPDATE `item_closingstock` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_mortality1.php?ccid='.$cid); }
}
else if($utype == "delete"){
    $sql = "UPDATE `item_closingstock` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_mortality1.php?ccid='.$cid); }
}
else if($utype == "authorize"){
    $sql = "UPDATE `item_closingstock` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_mortality1.php?ccid='.$cid); }
}

?>
