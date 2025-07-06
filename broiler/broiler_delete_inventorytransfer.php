<?php
//broiler_delete_inventorytransfer.php
/*
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['inventorytransfer'];
$table_session = $ccid."tbl_access";
$table_name = $_SESSION[$table_session];
$utype = $_GET['utype'];
$id = $_GET['id'];
$stk_itemid = $_GET['stk_itemid'];

$sql = "SELECT trnum FROM `".$table_name."` WHERE `id` = '$id' and `stk_itemid` = '$stk_itemid'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $trnum = $row['trnum']; }

if($utype == "delete"){
    $sql = "UPDATE `".$table_name."` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id' and `stk_itemid` = '$stk_itemid'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum' and `stk_itemid` = '$stk_itemid'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else { header('location:broiler_display_inventorytransfer.php?ccid='.$ccid); }
    }
}
*/
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['inventorytransfer'];
$table_session = $ccid."tbl_access";
$table_name = $_SESSION[$table_session];
$utype = $_GET['utype'];
$id = $_GET['id'];

$sql = "SELECT trnum FROM `".$table_name."` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $trnum = $row['trnum']; }

if($utype == "delete"){

      //Store Previous Data before change
    include_once("poulsoft_store_chngmaster.php");
    $chng_type = "Delete";
    $edit_file = "broiler_delete_inventorytransfer.php";
    $mtbl_name = "$table_name";
    $tno_cname = "id";
    $msg1 = array("file"=>$edit_file, "trnum"=>$id, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
    $message = json_encode($msg1);
    store_modified_details($message);

    $sql = "UPDATE `".$table_name."` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `account_summary` SET `active` = '0',`dflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else { header('location:broiler_display_inventorytransfer.php?ccid='.$ccid); }
    }
}
?>
