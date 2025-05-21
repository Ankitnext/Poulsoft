<?php
//chicken_delete_daterange1.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['daterange1'];

$utype = $_GET['page'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `dataentry_daterange_master` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:chicken_display_daterange1.php?ccid='.$ccid); }
}

?>
