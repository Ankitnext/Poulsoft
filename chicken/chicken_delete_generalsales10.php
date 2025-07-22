<?php
//chicken_delete_generalsales10.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['generalsales10'];

$utype = $_GET['utype'];
$id = $_GET['trnum'];

if($utype == "pause"){
    $sql = "UPDATE `customer_sales` SET `active` = '0',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            header('location:chicken_display_generalsales10.php?ccid='.$cid);
        }
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `customer_sales` SET `active` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            header('location:chicken_display_generalsales10.php?ccid='.$cid);
        }
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `customer_sales` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            header('location:chicken_display_generalsales10.php?ccid='.$cid);
        }
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `customer_sales` SET `flag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
        $sql = "UPDATE `customer_receipts` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$id'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        else{
            header('location:chicken_display_generalsales10.php?ccid='.$cid);
        }
    }
}

?>
