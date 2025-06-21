<?php
//main_updatemortality.php
session_start(); include "newConfig.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['dispmort'];
if($_POST['submittrans'] == "addpage"){
	$i = 0; foreach($_POST['pdate'] as $pdate){ $i = $i + 1; $date[$i] = date("Y-m-d",strtotime($pdate)); }
	$i = 0; foreach($_POST['item'] as $item){ $i = $i + 1; $itemcode[$i] = $item; }
	$i = 0; foreach($_POST['birds'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }
	$i = 0; foreach($_POST['quantity'] as $qty){ $i = $i + 1; $quantity[$i] = $qty; }
	$i = 0; foreach($_POST['price'] as $prices){ $i = $i + 1; $price[$i] = $prices; }
	$i = 0; foreach($_POST['amount'] as $amounts){ $i = $i + 1; $amount[$i] = $amounts; }
	$i = 0; foreach($_POST['mtype'] as $mtypes){ $i = $i + 1; $mtype[$i] = $mtypes; }
	$i = 0; foreach($_POST['ccode'] as $ccodes){ $i = $i + 1; $ccode[$i] = $ccodes; }
	$i = 0; foreach($_POST['warehouse'] as $warehouses){ $i = $i + 1; $warehouse[$i] = $warehouses; }
	$i = 0; foreach($_POST['remark'] as $remark){ $i = $i + 1; $remarks[$i] = $remark; }
		
	$csize = sizeof($itemcode);
	$prefix = "M";
	for($i = 1;$i <= $csize; $i++){
		if($itemcode[$i] != ""){
			$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date[$i]' AND `tdate` >= '$date[$i]'"; $query = mysqli_query($conn,$sql);
			while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
			
			$sql = "SELECT MAX(incr) as incr FROM `main_mortality`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$pfx."".$incrs;
			} else {
				$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$pfx."".$incrs;
			}
			if($date[$i] == "" || $date[$i] == NULL){ $date[$i] = date("Y-m-d"); }
			if($birds[$i] == "" || $birds[$i] == NULL){ $birds[$i] = "0.00"; }
			if($quantity[$i] == "" || $quantity[$i] == NULL){ $quantity[$i] = "0.00"; }
			if($price[$i] == "" || $price[$i] == NULL){ $price[$i] = "0.00"; }
			if($amount[$i] == "" || $amount[$i] == NULL){ $amount[$i] = $price[$i] * $quantity[$i]; }
			if($mtype[$i] == "" || $mtype[$i] == NULL || $mtype[$i] == "select"){ $mtype[$i] = ""; }
			if($ccode[$i] == "" || $ccode[$i] == NULL || $ccode[$i] == "select"){ $ccode[$i] = ""; }
			
			$sql = "INSERT INTO `main_mortality` (incr,prefix,code,mtype,date,ccode,itemcode,birds,quantity,price,amount,warehouse,remarks,addedemp,addedtime) VALUES 
			('$incrs','$prefix','$code','$mtype[$i]','$date[$i]','$ccode[$i]','$itemcode[$i]','$birds[$i]','$quantity[$i]','$price[$i]','$amount[$i]','$warehouse[$i]','$remarks[$i]','$addedemp','$addedtime')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		}
	}
	header('location:main_displaymortality.php?cid='.$cid);
}
if($_POST['submittrans'] == "updatepage"){
	$code = $_POST['code'];
	$date = date("Y-m-d",strtotime($_POST['pdate']));
	$itemcode = $_POST['item'];
	$birds = $_POST['birds'];
	$quantity = $_POST['quantity'];
	$price = $_POST['price'];
	$amount = $_POST['amount'];
	$mtype = $_POST['mtype'];
	$ccode = $_POST['ccode'];
	$warehouse = $_POST['warehouse'];
	$remarks = $_POST['remark'];
	
	if($date == "" || $date == NULL){ $date = date("Y-m-d"); }
	if($birds == "" || $birds == NULL){ $birds = "0.00"; }
	if($quantity == "" || $quantity == NULL){ $quantity = "0.00"; }
	if($price == "" || $price == NULL){ $price = "0.00"; }
	if($amount == "" || $amount == NULL){ $amount = $price * $quantity; }
	if($mtype == "" || $mtype == NULL || $mtype == "select"){ $mtype = ""; }
	if($ccode == "" || $ccode == NULL || $ccode == "select"){ $ccode = ""; }

	include_once("poulsoft_store_chngmaster.php");
	$chng_type = "Edit";
	$edit_file = "main_updatemortality.php";
	$mtbl_name = "main_mortality";
	$tno_cname = "code";
	$msg1 = array("file"=>$edit_file, "trnum"=>$code, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
	$message = json_encode($msg1);
	store_modified_details($message);
	
	$sql = "UPDATE `main_mortality` SET `mtype` = '$mtype',`date` = '$date',`itemcode` = '$itemcode',`birds` = '$birds',`quantity` = '$quantity',`price` = '$price',`amount` = '$amount',`ccode` = '$ccode',`warehouse` = '$warehouse',`remarks` = '$remarks' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaymortality.php?cid='.$cid); }
}
else if($_GET['type'] == "delete"){
	$code = $_GET['code'];
	
	include_once("poulsoft_store_chngmaster.php");
	$chng_type = "Delete";
	$edit_file = "main_updatemortality.php";
	$mtbl_name = "main_mortality";
	$tno_cname = "code";
	$msg1 = array("file"=>$edit_file, "trnum"=>$code, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
	$message = json_encode($msg1);
	store_modified_details($message);

	$sql = "UPDATE `main_mortality` SET `dflag` = '1',`active` = '0',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ $status = "failed"; } else { $status = "success"; }
	echo $status;
}
else if($_GET['type'] == "authorize"){
	$code = $_GET['code'];
	$sql = "UPDATE `main_mortality` SET `flag` = '1',`active` = '1',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ $status = "failed"; } else { $status = "success"; }
	echo $status;
}
?>