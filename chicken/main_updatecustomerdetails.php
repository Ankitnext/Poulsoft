<?php
//inv_updateitemcategory.php
session_start(); include "newConfig.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

$sql='SHOW COLUMNS FROM `main_contactdetails`'; $query = mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("fixed_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `fixed_qty` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Min Quantity' AFTER `active`"; mysqli_query($conn,$sql); }
if(in_array("pan_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `pan_no` VARCHAR(300) NULL DEFAULT NULL COMMENT 'PAN No' AFTER `active`"; mysqli_query($conn,$sql); }
if(in_array("aadhar_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `aadhar_no` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Aadhar No' AFTER `active`"; mysqli_query($conn,$sql); }
if(in_array("area_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `area_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Salesman Code' AFTER `groupcode`"; mysqli_query($conn,$sql); }
if(in_array("sman_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `sman_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Salesman Code' AFTER `area_code`"; mysqli_query($conn,$sql); }
if(in_array("supr_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `supr_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supervisor Code' AFTER `sman_code`"; mysqli_query($conn,$sql); }

if($_GET['submittrans'] == "addpage" && $_GET['cname'] != NULL || $_GET['submittrans'] == "updatepage" && $_GET['cname'] != NULL){
	$cus_code = $_GET['cus_code'];
	$name = $_GET['cname'];
	$mobileno = $_GET['mobile'];
	$pan_no = $_GET['pan_no'];
	$aadhar_no = $_GET['aadhar_no'];
	$contacttype = $_GET['stype'];
	if($contacttype == "S"){ $prefix = "CRT"; } else if($contacttype == "C"){ $prefix = "DBT"; } else { $prefix = "CDT"; }
	$gstinno = $_GET['cgstin'];
	$groupcode = $_GET['sgrp'];
	$area_code = $_GET['area_code']; if($area_code == NULL || $area_code == "select" || $area_code == ""){ $area_code = ""; }
	$address = $_GET['saddress'];
	$sman_code = $_GET['sman_code'];
	$supr_code = $_GET['supr_code'];
	//$fixed_qty = $_GET['fixed_qty']; if($fixed_qty == "" || $fixed_qty == 0 || $fixed_qty == 0.00 || $fixed_qty == "0" || $fixed_qty == "0.00"){ $fixed_qty = 0; }
	$creditamt = $_GET['climit'];
	$creditdays = $_GET['cterms'];
	$obdate = $_GET['crdrdate'];
	if($creditamt == "" || $creditamt == NULL){ $creditamt = "0"; } else{ }
	if($creditdays == "" || $creditdays == NULL){ $creditdays = "0"; } else{ }
	if($obdate == "" || $obdate == NULL){ $obdate = date("Y-m-d"); } else{ }
	$obdate = date("Y-m-d",strtotime($obdate));
	$obtype = $_GET['obtype'];
	$obamt = $_GET['crdramt'];
	if($obamt == "" || $obamt == NULL){ $obamt = "0"; } else{ }
	$obremarks = $_GET['obremarks'];
	$bank = $_GET['bname'];
	$branch = $_GET['branch'];
	$accno = $_GET['accno'];
	$ifsc = $_GET['ifsccode'];
	$micr = $_GET['micrno'];
	if($_GET['submittrans'] == "addpage"){
		$sql ="SELECT * FROM `main_contactdetails` WHERE `name` = '$name'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ 
			header('location:main_displaycustomers.php');
		}
		else {
			$sql = "SELECT MAX(incr) as incr FROM `main_contactdetails` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$incrs;
			} else {
				$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$incrs;
			}
			$sql = "INSERT INTO `main_contactdetails` (incr,prefix,code,name,address,pan_no,aadhar_no,contacttype,mobileno,creditamt,creditdays,obdate,obtype,obamt,obremarks,groupcode,area_code,sman_code,supr_code,gstinno,bank,branch,accno,ifsc,micr,fixed_qty,addedemp,addedtime,client) VALUES 
			('$incrs','$prefix','$code','$name','$address','$pan_no','$aadhar_no','$contacttype','$mobileno','$creditamt','$creditdays','$obdate','$obtype','$obamt','$obremarks','$groupcode','$area_code','$sman_code','$supr_code','$gstinno','$bank','$branch','$accno','$ifsc','$micr','$gstinno','$addedemp','$addedtime','$client')";
			
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycustomers.php'); }
		}
	
	}
	else if($_GET['submittrans'] == "updatepage"){
		$id = $_GET['idvalue'];
		$sql ="SELECT * FROM `main_contactdetails` WHERE `name` = '$name' AND `id` NOT IN ('$id')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			header('location:main_displaycustomers.php');
		}
		else {
			$sql = "UPDATE `main_contactdetails` SET `name` = '$name',`place` = '$place',`address` = '$address',`pan_no` = '$pan_no',`aadhar_no` = '$aadhar_no',`contacttype` = '$contacttype',`mobileno` = '$mobileno',`creditamt` = '$creditamt',`creditdays` = '$creditdays',`obdate` = '$obdate',`obtype` = '$obtype',`obamt` = '$obamt',`obremarks` = '$obremarks',`groupcode` = '$groupcode',`area_code` = '$area_code',`sman_code` = '$sman_code',`supr_code` = '$supr_code',`gstinno` = '$gstinno',`bank` = '$bank',`branch` = '$branch',`accno` = '$accno',`ifsc` = '$ifsc',`micr` = '$micr',`fixed_qty` = '$gstinno',`updated` = '$addedtime',`client` = '$client' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else {
				$cus_access_db_name = $_SESSION['dbase'];
				$sql ="SELECT * FROM `common_customeraccess` WHERE `ccode` = '$cus_code' AND `db_name` IN ('$cus_access_db_name') ORDER BY `id` ASC";
				$query = mysqli_query($conns,$sql); $ccount = mysqli_num_rows($query);
				if($ccount > 0){
					while($row = mysqli_fetch_assoc($query)){ $exist_mobile = $row['mobile']; }
					if($exist_mobile != $mobileno){
						$sql ="UPDATE `common_customeraccess` SET `mobile` = '$mobileno' WHERE `ccode` = '$cus_code' AND `db_name` IN ('$cus_access_db_name')";
						mysqli_query($conns,$sql);
					}
				}
				else{ }
				header('location:main_displaycustomers.php');
			}
		}
	}
	else {
		
	}
}
else if($_POST['submittrans'] == "addmpage"){
	$contacttype = $_POST['stype'];
	$groupcode = $_POST['sgrp'];
	$obdate = date("Y-m-d",strtotime($_POST['crdrdate']));
	$obtype = $_POST['obtype'];
	$i = 0; foreach($_POST['cname'] as $cname){ $i = $i + 1; $name[$i] = $cname; }
	$i = 0; foreach($_POST['mobile'] as $mobile){ $i = $i + 1; $mobileno[$i] = $mobile; }
	//$i = 0; foreach($_POST['stype'] as $stype){ $i = $i + 1; $contacttype[$i] = $stype; }
	//$i = 0; foreach($_POST['sgrp'] as $sgrp){ $i = $i + 1; $groupcode[$i] = $sgrp; }
	$i = 0; foreach($_POST['saddress'] as $saddress){ $i = $i + 1; $address[$i] = $saddress; }
	//$i = 0; foreach($_POST['crdrdate'] as $crdrdate){ $i = $i + 1; $obdate[$i] = date("Y-m-d",strtotime($crdrdate)); }
	//$i = 0; foreach($_POST['obtype'] as $obtypes){ $i = $i + 1; $obtype[$i] = $obtypes; }
	$i = 0; foreach($_POST['crdramt'] as $crdramt){ $i = $i + 1; $obamt[$i] = $crdramt; }
	$i = 0; foreach($_POST['remark'] as $remark){ $i = $i + 1; $obremarks[$i] = $remark; }
		
	$csize = sizeof($name);
		
	for($i = 1;$i <= $csize; $i++){
		if($name[$i] != ""){
			if($contacttype[$i] == "S"){ $prefix = "CRT"; } else if($contacttype[$i] == "C"){ $prefix = "DBT"; } else { $prefix = "CDT"; }
			$sql = "SELECT MAX(incr) as incr FROM `main_contactdetails` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$incrs;
			} else {
				$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$incrs;
			}
			if($obdate == "" || $obdate == NULL){ $obdate = date("Y-m-d"); }
			if($obamt[$i] == "" || $obamt[$i] == NULL){ $obamt[$i] = "0.00"; }
			if($obtype == "" || $obtype == NULL || $obtype == "select"){ $obtype = ""; }
			$sql = "INSERT INTO `main_contactdetails` (incr,prefix,code,name,address,contacttype,mobileno,creditamt,creditdays,obdate,obtype,obamt,obremarks,groupcode,area_code,sman_code,supr_code,gstinno,bank,branch,accno,ifsc,micr,addedemp,addedtime,client) VALUES 
			('$incrs','$prefix','$code','$name[$i]','$address[$i]','$contacttype','$mobileno[$i]','0.00','0','$obdate','$obtype','$obamt[$i]','$obremarks[$i]','$groupcode','$area_code','$sman_code','$supr_code','','','','','','','$addedemp','$addedtime','$client')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
	}
	header('location:main_displaycustomers.php');
}
else {
	$id = $_GET['id'];
	$updatetype = $_GET['page'];
	
	if($updatetype == "edit"){ header('location:main_editcustomermasters.php?id='.$id); }
	
	else if($updatetype == "delete"){
		$sql ="SELECT * FROM `main_contactdetails` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['name']; }
			$sql = "INSERT INTO `main_deletiondetails` (type,pcode,description,empcode) VALUES('contacttype','$catcode','$catdesc','$addedemp')";
			if(!mysqli_query($conn,$sql)){
				die("Error:-".mysqli_error($conn));
			}
			else {
				$sql = "DELETE FROM `main_contactdetails` WHERE `id` = '$id'";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					header('location:main_displaycustomers.php');
				}
			}
		}
		else {
			header('location:main_displaycustomers.php');
		}
	}
	else if($updatetype == "activate"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_contactdetails` SET `active` = '1' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycustomers.php'); }
	}
	else if($updatetype == "pause"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_contactdetails` SET `active` = '0' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycustomers.php'); }
	}
	else if($updatetype == "authorize"){
		$id = $_GET['id'];
		$sql = "UPDATE `main_contactdetails` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaycustomers.php'); }
	}
	else { }
	}
	
?>