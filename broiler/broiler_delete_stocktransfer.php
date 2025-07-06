<?php
//broiler_delete_itemdetails.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['itemdetails'];

$utype = $_GET['utype'];
$trnum = $_GET['trnum'];
if($utype == "delete"){
    $sql = "UPDATE `item_details` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        header('location:broiler_display_itemdetails.php?ccid='.$ccid); 
    }
}