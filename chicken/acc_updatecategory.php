<?php //acc_updatecategory.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$empcode = $addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	$d = date('Y-m-d');
	if($_POST['submittrans'] == "addpage"){
		$sql = "SELECT MAX(incr) as maxno FROM `acc_category`"; $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $incrs = $row['maxno']; }
		$incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
		$codes = "CAT-".$incrs; $typesub = $_POST['ctype']; $cdesc = $_POST['cdesc'];
		$sql = "INSERT INTO `acc_category` (incr,prefix,code,description,addedemp,addedtime,approveddate,subtype,client) VALUES ('$incrs','CAT','$codes','$cdesc','$empcode','$d','$d','$typesub','$clients')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } 
	}
	else if($_POST['submittrans'] == "updatepage"){
		$typesub = $_POST['ctype']; $arrtypes = explode("@",$typesub); $types = $arrtypes[0]; $id = $arrtypes[1]; $cdesc = $_POST['cdesc'];
		$sql = "UPDATE `acc_category` SET `description` = '$cdesc',`subtype` = '$types',`updated` = '$addedtime' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } 
	}
	else if($_GET['page'] == "delete"){
		$id = $_GET['id']; $sql = "SELECT * FROM `acc_category` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
			$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('coacat','$catcode','$catdesc','$empcode')";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
			else { $sql = "DELETE FROM `acc_category` WHERE `id` = '$id'"; if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } }
		}
		else { ?> <script> var x = alert("This transaction is already approved or used, \n kindly check the transaction"); if(x == true){ window.location.href = "acc_displayschedule.php"; } else if(x == false { window.location.href = "acc_displayschedule.php"; } else { window.location.href = "acc_displayschedule.php"; } </script> <?php }
	}
	else if($_GET['page'] == "activate"){
		$id = $_GET['id']; $sql = "UPDATE `acc_category` SET `active` = '1' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } 
	}
	else if($_GET['page'] == "deactivate"){
		$id = $_GET['id']; $sql = "UPDATE `acc_category` SET `active` = '0' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } 
	}
	else if($_GET['page'] == "authorize"){
		$id = $_GET['id']; $sql = "UPDATE `acc_category` SET `flag` = '1',`approvedemp` = '$empcode',`approveddate` = '$d' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:acc_displaycategory.php'); } 
	}
	else {}
?>