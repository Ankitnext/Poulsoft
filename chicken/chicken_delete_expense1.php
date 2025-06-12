<?php
//chicken_delete_expense1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['expense1'];

$utype = $_GET['page'];
$id = $_GET['id'];

$sql = "SELECT * FROM `acc_vouchers` WHERE `trnum` = '$id' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['trnum']; }

if($utype == "pause"){
    $sql = "UPDATE `acc_vouchers` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
       header('location:chicken_display_expense1.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `acc_vouchers` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_expense1.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `acc_vouchers` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_expense1.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `acc_vouchers` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_expense1.php?ccid='.$cid); 
    }
}

?>
