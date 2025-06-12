<?php
//chicken_delete_stocktransfer1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['stocktransfer1'];

$utype = $_GET['page'];
$id = $_GET['id'];

$sql = "SELECT * FROM `item_stocktransfers` WHERE `trnum` = '$id' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['trnum']; }

if($utype == "pause"){
    $sql = "UPDATE `item_stocktransfers` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
       header('location:chicken_display_stocktransfer1.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `item_stocktransfers` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_stocktransfer1.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `item_stocktransfers` SET `active` = '0',`tdflag` = '1',`pdflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_stocktransfer1.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `item_stocktransfers` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_stocktransfer1.php?ccid='.$cid); 
    }
}

?>
