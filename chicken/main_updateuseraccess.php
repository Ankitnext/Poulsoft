<?php //main_updateuseraccess.php
session_start(); include "newConfig.php";
$fyear = date("y",strtotime($fdate))."".date("y",strtotime($tdate));
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$dbase = $_SESSION['dbase'];

/*Fetch Column Availability*/
$sql='SHOW COLUMNS FROM `main_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cash_coa", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `cash_coa` VARCHAR(500) NULL DEFAULT NULL AFTER `cgroup_access`"; mysqli_query($conn,$sql); }
if(in_array("bank_coa", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `bank_coa` VARCHAR(500) NULL DEFAULT NULL AFTER `cash_coa`"; mysqli_query($conn,$sql); }

if(isset($_POST['submittrans']) == "addpage"){
	$lnames = $dlinks = $alinks = $elinks = $ulinks = $screens = $screenstwo = $cgroups = ""; $uname = $_POST['uname'];
	//$upass = password_hash($_POST['upass'], PASSWORD_DEFAULT);
	$upass = $_POST['upass'];
	$umobile = $_POST['umobile'];
	$slae_rate_edit_flag = $_POST['spe_flag'];
	$utype = $_POST['uaccess'];
	$cash_coa = $_POST['cash_coa'];
	$bank_coa = $_POST['bank_coa'];
	$misr_flag = $_POST['misr_flag'];
	$misr_flag = $_POST['misr_flag'];
	$logintype = $_POST['logintype'];
	if($utype == "S"){ $sa = 1; $aa = 0; $na = 0; }
	else if($utype == "A"){ $sa = 0; $aa = 1; $na = 0; }
	else { $sa = 0; $aa = 0; $na = 1; }

	foreach($_POST['lname'] as $ln){ if($lnames ==""){ $lnames = $ln; } else { $lnames = $lnames.",".$ln; } }
	foreach($_POST['cgroup'] as $ln){ if($cgroups ==""){ $cgroups = $ln; } else { $cgroups = $cgroups.",".$ln; } }
	foreach($_POST['displays'] as $ln){ if($dlinks == ""){ $dlinks = $ln; } else { $dlinks = $dlinks.",".$ln; } }
	foreach($_POST['adds'] as $ln){ if($alinks == ""){ $alinks = $ln; } else { $alinks = $alinks.",".$ln; } }
	foreach($_POST['edits'] as $ln){ if($elinks == ""){ $elinks = $ln; } else { $elinks = $elinks.",".$ln; } }
	foreach($_POST['updates'] as $ln){ if($ulinks == ""){ $ulinks = $ln; } else { $ulinks = $ulinks.",".$ln; } }
	foreach($_POST['transaction_access'] as $ln){ if($screens == ""){ $screens = $ln; } else { $screens = $screens.",".$ln; } }
	foreach($_POST['report_access'] as $ln){ if($screenstwo == ""){ $screenstwo = $ln; } else { $screenstwo = $screenstwo.",".$ln; } }
	foreach($_POST['ios_transaction_access'] as $ln){ if($ios_screens == ""){ $ios_screens = $ln; } else { $ios_screens = $ios_screens.",".$ln; } }
	foreach($_POST['ios_report_access'] as $ln){ if($ios_screens == ""){ $ios_screens = $ln; } else { $ios_screens = $ios_screens.",".$ln; } }
	$expdate = $_SESSION['expdate'];

	$sql = "SELECT MAX(incr) as incr FROM `log_useraccess` WHERE `client` = '$client'"; $query = mysqli_query($conns,$sql); while($row = mysqli_fetch_assoc($query)) { $incr = $row['incr']; $incr = $incr + 1; }
	if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { } $empcode = $client."-".$incr;
	$ip = $_SERVER['REMOTE_ADDR'];
	$sql = "INSERT INTO `log_useraccess` (incr,username,password,dblist,ios_screens,screens,screenstwo,iaddress,mobileno,expdate,empcode,account_access,logintype,flag,mobileflag,ipflag,addedempcode,createddatetime,updatedempcode,updateddatetime,client) 
	VALUES ('$incr','$uname','$upass','$dbase','$ios_screens','$screens','$screenstwo','$ip','$umobile','$expdate','$empcode','CTS','$logintype','1','0','0','$addedemp','$addedtime',NULL,'$addedtime','$client')";
	if(!mysqli_query($conns,$sql)) { echo die("Error Log user:- ".mysqli_error($conns)); } else {
		$sql = "INSERT INTO `main_access` (empcode,displayaccess,addaccess,editaccess,otheraccess,supadmin_access,admin_access,normal_access,misr_flag,loc_access,cgroup_access,cash_coa,bank_coa,slae_rate_edit_flag,addedemp,addedtime,updatedemp,updatedtime,client) 
		VALUES ('$empcode','$dlinks','$alinks','$elinks','$ulinks','$sa','$aa','$na','$misr_flag','$lnames','$cgroups','$cash_coa','$bank_coa','$slae_rate_edit_flag','$addedemp','$addedtime',NULL,'$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ echo die("Error Access:- ".mysqli_error($conn)); } else { header('Location:main_useraccess.php'); }
	}
}

else if(isset($_POST['submit']) == "updatepage"){
	$lnames = $dlinks = $alinks = $elinks = $ulinks = $screens = $screenstwo = $cgroups = ""; $uname = $_POST['uname']; $umobile = $_POST['umobile']; $empcodes = $_POST['empcodes'];
	$slae_rate_edit_flag = $_POST['spe_flag'];
	$utype = $_POST['uaccess'];
	$cash_coa = $_POST['cash_coa'];
	$bank_coa = $_POST['bank_coa'];
	$misr_flag = $_POST['misr_flag'];
	$logintype = $_POST['logintype'];
	if($utype == "S"){ $sa = 1; $aa = 0; $na = 0; }
	else if($utype == "A"){ $sa = 0; $aa = 1; $na = 0; }
	else { $sa = 0; $aa = 0; $na = 1; }
	foreach($_POST['lname'] as $ln){ if($lnames ==""){ $lnames = $ln; } else { $lnames = $lnames.",".$ln; } }
	foreach($_POST['cgroup'] as $ln){ if($cgroups ==""){ $cgroups = $ln; } else { $cgroups = $cgroups.",".$ln; } }
	foreach($_POST['displays'] as $ln){ if($dlinks == ""){ $dlinks = $ln; } else { $dlinks = $dlinks.",".$ln; } }
	foreach($_POST['adds'] as $ln){ if($alinks == ""){ $alinks = $ln; } else { $alinks = $alinks.",".$ln; } }
	foreach($_POST['edits'] as $ln){ if($elinks == ""){ $elinks = $ln; } else { $elinks = $elinks.",".$ln; } }
	foreach($_POST['updates'] as $ln){ if($ulinks == ""){ $ulinks = $ln; } else { $ulinks = $ulinks.",".$ln; } }
	foreach($_POST['transaction_access'] as $ln){ if($screens == ""){ $screens = $ln; } else { $screens = $screens.",".$ln; } }
	foreach($_POST['report_access'] as $ln){ if($screenstwo == ""){ $screenstwo = $ln; } else { $screenstwo = $screenstwo.",".$ln; } }
	foreach($_POST['ios_transaction_access'] as $ln){ if($ios_screens == ""){ $ios_screens = $ln; } else { $ios_screens = $ios_screens.",".$ln; } }
	foreach($_POST['ios_report_access'] as $ln){ if($ios_screens == ""){ $ios_screens = $ln; } else { $ios_screens = $ios_screens.",".$ln; } }

	$sql = "UPDATE `log_useraccess` SET `username` = '$uname',`mobileno` = '$umobile',`ios_screens` = '$ios_screens',`screens` = '$screens',`screenstwo` = '$screenstwo',`account_access` = 'CTS',`logintype` = '$logintype',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `empcode` = '$empcodes'";
	if(!mysqli_query($conns,$sql)) { echo die("Error:- ".mysqli_error($conns)); } else {
		$sql = "UPDATE `main_access` SET `empcode` = '$empcodes',`displayaccess` = '$dlinks',`addaccess` = '$alinks',`editaccess` = '$elinks',`otheraccess` = '$ulinks',`supadmin_access` = '$sa',`admin_access` = '$aa',`normal_access` = '$na',`misr_flag` = '$misr_flag',`loc_access` = '$lnames',`cgroup_access` = '$cgroups',`cash_coa` = '$cash_coa',`bank_coa` = '$bank_coa',`slae_rate_edit_flag` = '$slae_rate_edit_flag',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `empcode` = '$empcodes'";
		if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else { header('Location:main_useraccess.php'); }
	}
}

else if($_GET['page'] == "delete"){
	echo "delete";
}
else if(isset($_POST['submitpass']) == "updpass"){
	$id = $_POST['ids'];
	//$upass = password_hash($_POST['upass'], PASSWORD_DEFAULT);
	$upass = $_POST['upass'];
	$sql = "UPDATE `log_useraccess` SET `password` = '$upass',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `empcode` = '$id'";
	if(!mysqli_query($conns,$sql)){ echo die("Error:- ".mysqli_error($conns)); } else { header('Location:main_useraccess.php'); }
}
else if($_GET['page'] == "activate"){
	$id = $_GET['id']; $sql = "UPDATE `log_useraccess` SET `flag` = '1',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `empcode` = '$id'";
	if(!mysqli_query($conns,$sql)){ echo die("Error:- ".mysqli_error($conns)); } else { header('Location:main_useraccess.php'); }
}
else if($_GET['page'] == "deactivate"){
	$id = $_GET['id']; $sql = "UPDATE `log_useraccess` SET `flag` = '0',`updatedempcode` = '$addedemp',`updateddatetime` = '$addedtime' WHERE `empcode` = '$id'";
	if(!mysqli_query($conns,$sql)){ echo die("Error:- ".mysqli_error($conns)); } else { header('Location:main_useraccess.php'); }
}

else if($_GET['page'] == "edit"){ $id = $_GET['id']; header('Location:main_edituseraccess.php?id='.$id); }

else {

	echo "Other";

}

?>