<?php
//chicken_save_daterange1.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "daterange1";
$trlink = "chicken_display_daterange1.php";


$database_name = $_SESSION['dbase'];
$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
$query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

$user_code = $file_name = $min_days = $max_days = array();
$i = 0; foreach($_POST['user_code'] as $ucode){ $user_code[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['file_name'] as $ucode){ $file_name[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['min_days'] as $ucode){ $min_days[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['max_days'] as $ucode){ $max_days[$i] = $ucode; $i++; }
$flag = $dflag = 0; $active = 1;

$dsize = sizeof($user_code);
for($i = 0;$i < $dsize;$i++){
	if($min_days[$i] == ""){ $min_days[$i] = 0; }
	if($max_days[$i] == ""){ $max_days[$i] = 0; }
	if($user_code[$i] == "" || $user_code[$i] == "select" || $file_name[$i] == "" || $file_name[$i] == "select" || $min_days[$i] == "" || $max_days[$i] == ""){ }
	else{
		$emp_alist = array(); if($user_code[$i] == "all"){ foreach($emp_code as $ecode){ $emp_alist[$ecode] = $ecode; } } else{ $emp_alist[$user_code[$i]] = $user_code[$i]; }

		foreach($emp_alist as $ecode){
			$sql1 = "SELECT * FROM `dataentry_daterange_master` WHERE `user_code` = '$ecode' AND `file_name` = '$file_name[$i]' AND `dflag` = '0'";
			$query1 = mysqli_query($conn,$sql1); $u_cnt = mysqli_num_rows($query1);
			if($u_cnt > 0){
				$sql2 = "UPDATE `dataentry_daterange_master` SET `min_days` = '$min_days[$i]',`max_days` = '$max_days[$i]',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `user_code` = '$ecode' AND `file_name` = '$file_name[$i]' AND `dflag` = '0'";
				if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
			}
			else{
				$sql2 = "INSERT INTO `dataentry_daterange_master` (`file_name`,`user_code`,`min_days`,`max_days`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
				VALUES ('$file_name[$i]','$ecode','$min_days[$i]','$max_days[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime');";
				if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
			}
		}
	}
}
       
header('location:chicken_display_daterange1.php');

