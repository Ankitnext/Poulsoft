<?php
//broiler_delete_companydetails.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['companydetails'];

$utype = $_GET['utype'];
$id = $_GET['id'];
if($utype == "delete"){
    $sql = "UPDATE `emp_daily_works` SET `dflag` = '1',`active` = '0',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:admin_display_dayworks.php?ccid='.$ccid); }
}

 ?>
