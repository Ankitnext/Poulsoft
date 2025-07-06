<?php
//main_savegroups.php
	session_start(); include "newConfig.php";
	$client = $_SESSION['client'];
	$addedemp = $_SESSION['userid'];
	date_default_timezone_set("Asia/Kolkata");
	$addedtime = date('Y-m-d H:i:s');
	
	if($_GET['submittrans'] == "addpage"){
		$gtype = $_GET['gtype'];
		$smtype = $_GET['smtype'];
		$gdesc = $_GET['gdesc'];
		$adesc = $gdesc." - Asset";
		$ldesc = $gdesc." - Liability";
		$aprefix = "ASG";
		$atype = "COA-0001";
		$ltype = "COA-0004";
		$istat = $bstat = $cptype = NUll;
		if($bstat == ''){
			$bstat = 0;
		}
		if($istat == ''){
			$istat = 0;
		}
		if($gtype == "S"){ $lsche = "LIA-0002"; $asche = "ASC-0006"; $lctype = "Vendor Prepayment A/c"; $actype = "Vendor A/c"; } else if($gtype == "C"){ $lsche = "LIA-0004"; $asche = "ASC-0005"; $actype = "Customer A/c"; $lctype = "Customer Advance A/c"; } else { $sasche = "LIA-0002"; $slsche = "ASC-0006"; $casche = "LIA-0004"; $clsche = "ASC-0005"; }
		$sql = "SELECT MAX(incr) as incr FROM `acc_coa` WHERE `prefix` = '$aprefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){ $aincrs = $row['incr']; } $aincrs = $aincrs + 1; if($aincrs < 10){ $aincrs = '000'.$aincrs; } else if($aincrs >= 10 && $aincrs < 100){ $aincrs = '00'.$aincrs; } else if($aincrs >= 100 && $aincrs < 1000){ $aincrs = '0'.$aincrs; } else { }
				$acode = $aprefix."-".$aincrs;
		}
		else {
			$aincrs = 1; if($aincrs < 10){ $aincrs = '000'.$aincrs; } else if($aincrs >= 10 && $aincrs < 100){ $aincrs = '00'.$aincrs; } else if($aincrs >= 100 && $aincrs < 1000){ $aincrs = '0'.$aincrs; } else { }
			$acode = $aprefix."-".$aincrs;
		}
		$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,ctype,schedules,categories,bs,`is`,flag,active,addedemp,addedtime,client) VALUES ('$aincrs','$aprefix','$acode','$adesc','$atype','$actype','$asche','$cptype','$bstat','$istat','1','1','$addedemp','$addedtime','$client')";
		mysqli_query($conn,$sql);
		$lprefix = "LIG";
		$sql = "SELECT MAX(incr) as incr FROM `acc_coa` WHERE `prefix` = '$lprefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){ $lincrs = $row['incr']; } $lincrs = $lincrs + 1; if($lincrs < 10){ $lincrs = '000'.$lincrs; } else if($lincrs >= 10 && $lincrs < 100){ $lincrs = '00'.$lincrs; } else if($lincrs >= 100 && $lincrs < 1000){ $lincrs = '0'.$lincrs; } else { }
				$lcode = $lprefix."-".$lincrs;
		}
		else {
			$lincrs = 1; if($lincrs < 10){ $lincrs = '000'.$lincrs; } else if($lincrs >= 10 && $lincrs < 100){ $lincrs = '00'.$lincrs; } else if($lincrs >= 100 && $lincrs < 1000){ $lincrs = '0'.$lincrs; } else { }
			$lcode = $lprefix."-".$lincrs;
		}
		$sql = "INSERT INTO `acc_coa` (incr,prefix,code,description,type,ctype,schedules,categories,bs,`is`,flag,active,addedemp,addedtime,client) VALUES ('$lincrs','$lprefix','$lcode','$ldesc','$ltype','$lctype','$lsche','$cptype','$bstat','$istat','1','1','$addedemp','$addedtime','$client')";
		mysqli_query($conn,$sql);
		
		if($gtype == "S"){ $prefix = "SSG"; } else if($gtype == "C"){ $prefix = "SCG"; } else { $prefix = "SJG"; }
		
		$sql = "SELECT MAX(incr) as incr FROM `main_groups` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
		if($ccount > 0){
			while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
				$code = $prefix."-".$incrs;
		}
		else {
			$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
			$code = $prefix."-".$incrs;
		}
		$sql = "INSERT INTO `main_groups` (incr,prefix,code,description,sm_code,controlaccount,prepayaccount,obaccount,gtype,addedemp,addedtime,client) VALUES ('$incrs','$prefix','$code','$gdesc','$smtype','$lcode','$acode','00000','$gtype','$addedemp','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaygroups.php'); }
	}
	else if($_GET['submittrans'] == "updatepage"){
		$gdesc = $_GET['gdesc'];
		$id = $_GET['id'];
		$sql = "SELECT * FROM `main_groups` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$olddesc  = $row['description'];
		}
		$oldadesc = $olddesc." - Asset";
		$oldldesc = $olddesc." - Liability";
		$adesc = $gdesc." - Asset";
		$ldesc = $gdesc." - Liability";
		$sql = "UPDATE `acc_coa` SET `description` = '$adesc',`updated` = '$addedtime' WHERE `description` = '$oldadesc'"; mysqli_query($conn,$sql);
		$sql = "UPDATE `acc_coa` SET `description` = '$ldesc',`updated` = '$addedtime' WHERE `description` = '$oldldesc'"; mysqli_query($conn,$sql);
		$sql = "UPDATE `main_groups` SET `description` = '$gdesc',`sm_code` = '$smtype',`updated` = '$addedtime' WHERE `id` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaygroups.php'); }
	}
	else {
		$id = $_GET['id'];
		$updatetype = $_GET['page'];
		
		if($updatetype == "edit"){ header('location:main_editgroups.php?id='.$id); }
		
		else if($updatetype == "delete"){
			$sql ="SELECT * FROM `main_groups` WHERE `id` = '$id' AND `flag` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
				while($row = mysqli_fetch_assoc($query)){ $catcode = $row['code']; $catdesc = $row['description']; }
				$sql = "INSERT INTO `main_deletiondetails` (type,transactionno,description,empcode) VALUES('scgroup','$catcode','$catdesc','$addedemp')";
				if(!mysqli_query($conn,$sql)){
					die("Error:-".mysqli_error($conn));
				}
				else {
					$sql = "DELETE FROM `main_groups` WHERE `id` = '$id'";
					if(!mysqli_query($conn,$sql)){
						die("Error:-".mysqli_error($conn));
					}
					else {
						header('location:main_displaygroups.php');
					}
				}
			}
			else {
			?>
				<script>
				var x = alert("This transaction is already approved or used, \n kindly check the transaction");
				if(x == true){
					window.location.href = "main_displaygroups.php";
				}
				else if(x == false {
					window.location.href = "main_displaygroups.php";
				}
				else {
					window.location.href = "main_displaygroups.php";
				}
				</script>
			<?php
			}
		}
		else if($updatetype == "activate"){
			$sql = "UPDATE `main_groups` SET `active` = '1' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaygroups.php'); }
		}
		else if($updatetype == "pause"){
			$sql = "UPDATE `main_groups` SET `active` = '0' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaygroups.php'); }
		}
		else if($updatetype == "authorize"){
			$sql = "UPDATE `main_groups` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `id` = '$id'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:main_displaygroups.php'); }
		}
		else {}
	}
	
	
	
?>