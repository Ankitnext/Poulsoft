<?php
//broiler_delete_sectormapping.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['sectormapping'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `broiler_secbrch_mapping` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_sectormapping.php?ccid='.$ccid); }
}

 ?>
