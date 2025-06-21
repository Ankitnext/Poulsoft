<?php
//chicken_delete_saleorder.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['saleorder'];

$utype = $_GET['page'];
$id = $_GET['id'];

if($utype == "pause"){
    $sql = "UPDATE `salesorder` SET `active` = '0',`updatetime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_saleorder.php?ccid='.$cid); }
}
else if($utype == "activate"){
    $sql = "UPDATE `salesorder` SET `active` = '1',`updatetime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_saleorder.php?ccid='.$cid); }
}
else if($utype == "delete"){
    $sql = "UPDATE `salesorder` SET `active` = '0',`isDelete` = '1',`updatetime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_saleorder.php?ccid='.$cid); }
}
else if($utype == "authorize"){
    $sql = "UPDATE `salesorder` SET `flag` = '1',`updatetime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_saleorder.php?ccid='.$cid); }
}

?>
