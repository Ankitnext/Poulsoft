<?php
//layer_update_mvconsumed1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['mvconsumed1'];

$utype = $_GET['utype'];
$id = $_GET['trnum'];
if($utype == "pause"){
    $sql = "UPDATE `layer_medicine_consumed` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ header('location:layer_display_mvconsumed1.php?ccid='.$ccid); }
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `layer_medicine_consumed` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ header('location:layer_display_mvconsumed1.php?ccid='.$ccid); }
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `layer_medicine_consumed` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else{ header('location:layer_display_mvconsumed1.php?ccid='.$ccid); }
    }
}

?>
