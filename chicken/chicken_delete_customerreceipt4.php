<?php
//chicken_delete_customerreceipt4.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['customerreceipt1'];

$utype = $_GET['page'];
$id = $_GET['id'];

if($utype == "pause"){
    $sql = "UPDATE `customer_receipts` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_customerreceipt4.php?ccid='.$cid); }
}
else if($utype == "activate"){
    $sql = "UPDATE `customer_receipts` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_customerreceipt4.php?ccid='.$cid); }
}
else if($utype == "delete"){
    include_once("poulsoft_store_chngmaster.php");
$chng_type = "Delete";
$edit_file = "chicken_delete_customerreceipt4.php";
$mtbl_name = "customer_receipts";
$tno_cname = "trnum";
$msg1 = array("file"=>$edit_file, "trnum"=>$id, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
$message = json_encode($msg1);
store_modified_details($message);

    $sql = "UPDATE `customer_receipts` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_customerreceipt4.php?ccid='.$cid); }
}
else if($utype == "authorize"){
    $sql = "UPDATE `customer_receipts` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{ header('location:chicken_display_customerreceipt4.php?ccid='.$cid); }
}

?>
