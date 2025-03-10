<?php
//broiler_delete_feedproduction2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedproduction2'];
$table_session = $ccid."tbl_access";
$table_name = $_SESSION[$table_session];
$utype = $_GET['utype'];
$trnum = $_GET['trnum'];

if($utype == "delete"){
    $sql = "UPDATE `".$table_name."` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `broiler_feed_consumed` SET `dflag` = '1',`active` = '0' WHERE `link_trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
            if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
            else { header('location:broiler_display_feedproduction2.php?ccid='.$ccid); }
        }
    }
}

?>
