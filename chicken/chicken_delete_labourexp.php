<?php
//chicken_delete_labourexp.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['labourexp'];

$utype = $_GET['page'];
$id = $_GET['id'];

$sql = "SELECT * FROM `chicken_labveh_expenses` WHERE `code` = '$id' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $ids = $row['code']; }

if($utype == "pause"){
    $sql = "UPDATE `chicken_labveh_expenses` SET `active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
       header('location:chicken_display_labourexp.php?ccid='.$cid); 
    }
}
else if($utype == "activate"){
    $sql = "UPDATE `chicken_labveh_expenses` SET `active` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_labourexp.php?ccid='.$cid); 
    }
}
else if($utype == "delete"){
    $sql = "UPDATE `chicken_labveh_expenses` SET `active` = '0',`dflag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_labourexp.php?ccid='.$cid); 
    }
}
else if($utype == "authorize"){
    $sql = "UPDATE `chicken_labveh_expenses` SET `flag` = '1',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `code` = '$ids'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else{
         header('location:chicken_display_labourexp.php?ccid='.$cid); 
    }
}

?>
