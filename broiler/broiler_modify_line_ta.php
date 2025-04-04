<?php
//broiler_modify_line.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['loc_line'];

$region_code = $_POST['region'];
$branch_code = $_POST['branch'];
$description = $_POST['line'];
$id = $_POST['idvalue'];
$sql = "UPDATE `location_line` SET `description` = '$description',`region_code` = '$region_code',`branch_code` = '$branch_code',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_line.php?ccid='.$ccid); }

?>