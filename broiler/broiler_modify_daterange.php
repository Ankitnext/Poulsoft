<?php
//broiler_modify_daterange.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['daterange'];

$user_code = $_POST['user_code'];
$file_name = $min_days = $max_days = $id_alist = array();
$i = 0; foreach($_POST['file_name'] as $ucode){ $file_name[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['min_days'] as $ucode){ $min_days[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['max_days'] as $ucode){ $max_days[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['id_alist'] as $ucode){ $id_alist[$i] = $ucode; $i++; }
$flag = $dflag = 0; $active = 1;

$dsize = sizeof($file_name);
for($i = 0;$i < $dsize;$i++){
	if($min_days[$i] == ""){ $min_days[$i] = 0; }
	if($max_days[$i] == ""){ $max_days[$i] = 0; }
	
	$sql2 = "UPDATE `dataentry_daterange_master` SET `min_days` = '$min_days[$i]',`max_days` = '$max_days[$i]',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `user_code` = '$user_code' AND `file_name` = '$file_name[$i]' AND `id` = '$id_alist[$i]' AND `dflag` = '0'";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
}
header('location:broiler_display_daterange.php?ccid='.$ccid);