<?php
//broiler_delete_placementplanning2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['placementplanning2'];

$utype = $_GET['utype'];
$trnum = $_GET['trnum'];
if($utype == "delete"){
    $sql = "UPDATE `broiler_placementplan` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else { }
}
header('location:broiler_display_placementplanning2.php?ccid='.$ccid);
 ?>
