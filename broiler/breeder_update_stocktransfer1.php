<?php
//breeder_update_stocktransfer1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['stocktransfer1'];

$utype = $_GET['utype'];
$trnum = $_GET['trnum'];
if($utype == "pause"){
    $sql = "UPDATE `item_stocktransfers` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:breeder_display_stocktransfer1.php?ccid='.$ccid); }
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `item_stocktransfers` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:breeder_display_stocktransfer1.php?ccid='.$ccid); }
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `item_stocktransfers` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:breeder_display_stocktransfer1.php?ccid='.$ccid); }
    }
}

?>
