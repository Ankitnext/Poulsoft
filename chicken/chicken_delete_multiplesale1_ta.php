<?php
//chicken_delete_multiplesale1_ta.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['multiplesale1_ta'];

$utype = $_GET['page'];
$ids = $_GET['id'];

if($utype == "pause"){
    $sql = "UPDATE `customer_sales` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:chicken_display_multiplesale1_ta.php?ccid='.$cid); }
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `customer_sales` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:chicken_display_multiplesale1_ta.php?ccid='.$cid); }
    }
}
else if($utype == "delete"){

    include_once("poulsoft_store_chngmaster.php");

    $chng_type = "Delete";
    $edit_file = "chicken_delete_multiplesale1_ta.php";
    $mtbl_name = "customer_sales";
    $tno_cname = "invoice";
    $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
    $message = json_encode($msg1);
    store_modified_details($message);

    $chng_type = "Delete";
    $edit_file = "chicken_delete_multiplesale1_ta.php";
    $mtbl_name = "customer_receipts";
    $tno_cname = "link_trnum";
    $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
    $message = json_encode($msg1);
    store_modified_details($message);

    $sql = "UPDATE `customer_sales` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:chicken_display_multiplesale1_ta.php?ccid='.$cid); }
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `customer_sales` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{ header('location:chicken_display_multiplesale1_ta.php?ccid='.$cid); }
    }
}

?>
