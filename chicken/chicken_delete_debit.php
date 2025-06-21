<?php
//chicken_delete_debit.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['debit'];

$utype = $_GET['page'];
 
$id = $_GET['id'];

$sql = "SELECT * FROM `main_mortality` WHERE `code` = '$id' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['trnum']; }

if($utype == "pause"){
    $sql = "UPDATE `main_mortality` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
       header('location:chicken_display_debit.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `main_mortality` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_debit.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `main_mortality` SET `active` = '0',`dflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_debit.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `main_mortality` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_debit.php?ccid='.$cid); 
    }
}

?>
