<?php
//broiler_delete_feedsale5.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedsale4'];
$utype = $_GET['utype'];
$trnum = $_GET['trnum'];

if($utype == "delete"){
    $sql = "UPDATE `broiler_sales` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else { header('location:broiler_display_feedsale4.php?ccid='.$ccid); }
    }
}

?>
