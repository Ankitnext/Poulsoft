<?php //acc_updatecategory.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$empcode = $addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	$d = date('Y-m-d');
	if($_POST['submittrans'] == "addpage"){
		$sdesc = $_POST['cdesc']; $stype = $_POST['ctype']; $sptype = $_POST['cptype'];
		$sql ="SELECT MAX(incr) as incr FROM `acc_schedules` WHERE `subtype` = '$stype'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; } else { $incrs = 1; }
		if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
		
		if($stype =="COA-0001"){ $prefix = "AS"; $code = "ASC-".$incrs; }
		else if($stype =="COA-0002"){ $prefix = "CA"; $code = "CPT-".$incrs; }
		else if($stype =="COA-0003"){ $prefix = "EX"; $code = "EXP-".$incrs; }
		else if($stype =="COA-0004"){ $prefix = "LI"; $code = "LIA-".$incrs; }
		else if($stype =="COA-0005"){ $prefix = "RV"; $code = "RVN-".$incrs; }
		else { $prefix = "OT"; $code = "OTH-".$incrs; }
		
		$sql = "INSERT INTO `acc_schedules` (incr,prefix,code,description,subtype,pstype,addedemp,addeddate,client) VALUES ('$incrs','$prefix','$code','$sdesc','$stype','$sptype','$addedemp','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); } 
	}
	else if($_POST['submittrans'] == "updatepage"){
		$cdesc = $_POST['cdesc']; $stypes = explode("@",$_POST['ctype']); $stype = $stypes[0]; $cptype = $_POST['cptype']; $mainid = $stypes[1];
		$sql ="SELECT MAX(incr) as incr,subtype FROM `acc_schedules` WHERE `subtype` = '$stype'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; $oldcode = $row['subtype']; } $incrs = $incrs + 1; } else { $incrs = 1; }
		
		$sql ="SELECT subtype FROM `acc_schedules` WHERE `id` = '$mainid'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $oldcode = $row['subtype']; } } else { }
		
		
		if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
		
		if($stype =="COA-0001"){ $prefix = "AS"; $code = "ASC-".$incrs; }
		else if($stype =="COA-0002"){ $prefix = "CA"; $code = "CPT-".$incrs; }
		else if($stype =="COA-0003"){ $prefix = "EX"; $code = "EXP-".$incrs; }
		else if($stype =="COA-0004"){ $prefix = "LI"; $code = "LIA-".$incrs; }
		else if($stype =="COA-0005"){ $prefix = "RV"; $code = "RVN-".$incrs; }
		else { $prefix = "OT"; $code = "OTH-".$incrs; }
		if($oldcode === $stype){ $sql = "UPDATE `acc_schedules` SET `description` = '$cdesc',`pstype` = '$cptype',`updated` = '$addedtime' WHERE `id` = '$mainid'"; }
		else { $sql = "UPDATE `acc_schedules` SET `prefix` = '$prefix',`incr` = '$incrs',`code` = '$code',`description` = '$cdesc',`subtype` = '$stype',`pstype` = '$cptype',`updated` = '$addedtime' WHERE `id` = '$mainid'"; }
		
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); }
	}
	else if($_GET['page'] == "delete"){
		$id = $_GET['id'];
		$sql ="SELECT * FROM `acc_schedules` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
			$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('coaschedule','$catcode','$catdesc','$empcode')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $sql = "DELETE FROM `acc_schedules` WHERE `id` = '$id'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); } }
		}
		else { ?> <script> var x = alert("This transaction is already approved or used, \n kindly check the transaction"); if(x == true){ window.location.href = "acc_displayschedule.php"; } else if(x == false { window.location.href = "acc_displayschedule.php"; } else { window.location.href = "acc_displayschedule.php"; } </script> <?php }
	}
	else if($_GET['page'] == "activate"){
		$id = $_GET['id'];
		$sql = "UPDATE `acc_schedules` SET `active` = '1' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); }
	}
	else if($_GET['page'] == "pause"){
		$id = $_GET['id'];
		$sql = "UPDATE `acc_schedules` SET `active` = '0' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); }
	}
	else if($_GET['page'] == "authorize"){
		$id = $_GET['id'];
		$sql = "UPDATE `acc_schedules` SET `flag` = '1',`approvedemp` = '$empcode',`approveddate` = '$d' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displayschedule.php'); }
	}
	else {}
?>