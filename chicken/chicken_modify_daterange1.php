<?php
//chicken_modify_daterange1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];


// $ids = $_POST['idvalue'];
// $type = $_POST['type'];
// $days = $_POST['days'];
// $users = $_POST['users'];

$user_code = $_POST['user_code'];
$file_name = $min_days = $max_days = $id_alist = array();
$i = 0; foreach($_POST['file_name'] as $ucode){ $file_name[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['min_days'] as $ucode){ $min_days[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['max_days'] as $ucode){ $max_days[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['id_alist'] as $ucode){ $id_alist[$i] = $ucode; $i++; }

$userList = isset($users) ? implode(",", $users) : "None";
$active = 1;
$flag = $dflag = 0;

$trtype = "daterange1";
$trlink = "chicken_display_daterange1.php";
//echo $ids."@".$type."@".$days."@".$userList;
//Modify Transaction
// $sql = "UPDATE `dataentry_daterange` SET `type`='$type', `days`='$days', `users`='$userList' WHERE `id`='$ids';";
// if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$dsize = sizeof($file_name);
for($i = 0;$i < $dsize;$i++){
	if($min_days[$i] == ""){ $min_days[$i] = 0; }
	if($max_days[$i] == ""){ $max_days[$i] = 0; }
	
	$sql2 = "UPDATE `dataentry_daterange_master` SET `min_days` = '$min_days[$i]',`max_days` = '$max_days[$i]',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `user_code` = '$user_code' AND `file_name` = '$file_name[$i]' AND `id` = '$id_alist[$i]' AND `dflag` = '0'";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
}

header('location:chicken_display_daterange1.php');

