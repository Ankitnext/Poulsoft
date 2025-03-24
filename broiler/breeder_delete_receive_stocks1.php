<?php
//breeder_delete_receive_stocks1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['receive_stocks1'];

$utype = $_GET['utype'];
$trnum = $_GET['trnum'];
if($utype == "delete"){
    $sql = "UPDATE `item_stocktransfers` SET `quantity` = '0',`short_qty` = '0',`excess_qty` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum' AND `etype` NOT IN ('Breeder-Send Stock')";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:breeder_display_receive_stocks1.php?ccid='.$ccid); }
    }
}