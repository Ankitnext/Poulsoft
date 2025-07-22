<?php
//chicken_delete_generalpurchase11.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['generalpurchase11'];

$utype = $_GET['page'];
$id = $_GET['id'];

$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$id' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['link_trnum']; }

if($utype == "pause"){
    $sql = "UPDATE `pur_purchase` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_generalpurchase11.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `pur_purchase` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_generalpurchase11.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){

    include_once("poulsoft_store_chngmaster.php");
    $chng_type = "Delete";
    $edit_file = "chicken_delete_generalpurchase11.php";
    $mtbl_name = "pur_purchase";
    $tno_cname = "link_trnum";
    $msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
    $message = json_encode($msg1);
    store_modified_details($message);

    $sql = "UPDATE `pur_purchase` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updated` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_generalpurchase11.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `pur_purchase` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `link_trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_generalpurchase11.php?ccid='.$cid); 
    }
}

?>
