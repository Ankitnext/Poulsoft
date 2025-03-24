<?php //main_updatefinancialyear.php
$days = $_POST['days'];
$ctype = $_POST['ctype'];

session_start(); include "newConfig.php";
//$fyear = date("y",strtotime($fdate))."".date("y",strtotime($tdate));
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

if(isset($_POST['submittrans']) == "addpage"){
	$sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = '$ctype' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){
		$sql = "UPDATE `dataentry_daterange` SET `type` = '$ctype',`days` = '$days',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime',`client` = '$client' WHERE `type` = '$ctype' AND `tdflag` = '0' AND `pdflag` = '0'";
		if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
			header('location:main_displaydatantrydaterange.php');
		}
	}
	else {
		$sql = "INSERT INTO `dataentry_daterange` (type,days,active,addedemp,addedtime,updatedtime,client) VALUES ('$ctype','$days','1','$addedemp','$addedtime','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
			header('location:main_displaydatantrydaterange.php');
		}
	}
}
else if(isset($_POST['submittrans']) == "updatepage"){
	$id = $_POST['id'];
	$sql = "UPDATE `dataentry_daterange` SET `type` = '$ctype',`days` = '$days',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime',`client` = '$client' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
		header('location:main_displaydatantrydaterange.php');
	}
}
else if(isset($_GET['page']) == "delete"){
	$id = $_GET['id'];
	$sql = "UPDATE `dataentry_daterange` SET `tdflag` = '1',`pdflag` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime',`client` = '$client' WHERE `id` = '$id'";
	if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
		header('location:main_displaydatantrydaterange.php');
	}
}
?>