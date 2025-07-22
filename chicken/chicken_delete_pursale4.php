<?php
//chicken_delete_pursale4.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['pursale4'];

$utype = $_GET['page'];
$trnum = $_GET['trnum']; $ptrnum = $strnum = "";
$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$trnum' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $strnum = $row['invoice']; $ptrnum = $row['link_trnum']; }

if($ptrnum != "" && $strnum != ""){
    if($utype == "pause"){
        $sql = "UPDATE `customer_sales` SET `active` = '0',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        $sql = "UPDATE `pur_purchase` SET `active` = '0',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        header('location:chicken_display_pursale4.php?ccid='.$cid);
    }
    else if($utype == "activate"){
        $sql = "UPDATE `customer_sales` SET `active` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        $sql = "UPDATE `pur_purchase` SET `active` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        header('location:chicken_display_pursale4.php?ccid='.$cid);
    }
    else if($utype == "delete"){
        include_once("poulsoft_store_chngmaster.php");
        $chng_type = "Delete";
        $edit_file = "chicken_delete_pursale4.php";
        $mtbl_name = "customer_sales";
        $tno_cname = "link_trnum";
        $msg1 = array("file"=>$edit_file, "trnum"=>$ptrnum, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
        $message = json_encode($msg1);
        store_modified_details($message);

        include_once("poulsoft_store_chngmaster.php");
        $chng_type = "Delete";
        $edit_file = "chicken_delete_pursale4.php";
        $mtbl_name = "pur_purchase";
        $tno_cname = "invoice";
        $msg1 = array("file"=>$edit_file, "trnum"=>$ptrnum, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
        $message = json_encode($msg1);
        store_modified_details($message);

        $sql = "UPDATE `customer_sales` SET `flag` = '0',`active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        $sql = "UPDATE `pur_purchase` SET `flag` = '0',`active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        header('location:chicken_display_pursale4.php?ccid='.$cid);
    }
    else if($utype == "authorize"){
        $sql = "UPDATE `customer_sales` SET `flag` = '2',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        $sql = "UPDATE `pur_purchase` SET `flag` = '2',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `invoice` = '$ptrnum'";
        if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
        header('location:chicken_display_pursale4.php?ccid='.$cid);
    }
}

?>
